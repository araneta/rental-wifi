import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'Config.dart';
import 'pembayaran_page.dart';
import 'settings_page.dart';
import 'month_picker.dart';
import 'package:provider/provider.dart';
import './services/printer_service.dart';
import 'widgets/app_drawer.dart';
import 'utils/auth_utils.dart';
import 'services/printer_service.dart';
import 'print_helper.dart';

class TagihanItem {
  final String id;
  final String pelanggan;
  final String bulanTahun;
  final String alamat;
  final int jumlah;
  final String status;
  final String tanggalBayar;

  TagihanItem({
    required this.id,
    required this.pelanggan,
    required this.bulanTahun,
    required this.alamat,
    required this.jumlah,
    required this.status,
    this.tanggalBayar = "",
  });

  factory TagihanItem.fromJson(Map<String, dynamic> json) {
    return TagihanItem(
      id: json['id'].toString(),
      pelanggan: json['pelanggan'] ?? '',
      bulanTahun: json['bulan_tahun'] ?? '',
      alamat: json['alamat'] ?? '',
      jumlah: int.tryParse(
        (json['jumlah'] ?? '0').toString().split('.').first,
      ) ?? 0,
      status: json['status'] ?? '',
      tanggalBayar: json['tanggal_bayar'] ?? '',
    );
  }
}
class TagihanPage extends StatefulWidget {
  @override
  State<TagihanPage> createState() => _TagihanPageState();
}

class _TagihanPageState extends State<TagihanPage> {
  List<TagihanItem> allItems = [];
  String searchQuery = '';
  String selectedStatus = 'semua';
  bool isLoading = false;
  String filterBulanTahun = '';
	String? printingId;
	
  final List<String> statusOptions = ['semua', 'dibayar', 'belum bayar'];

  @override
  void initState() {
    super.initState();
    fetchData();
  }

  Future<void> fetchData() async {
    setState(() => isLoading = true);
    SharedPreferences prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final statusParam = selectedStatus == 'semua' ? '' : selectedStatus;
    final url = Uri.parse(Config.API_HOST + '/api/tagihans?status=$statusParam');

    try {
      final response = await http.get(url, headers: {
        'Authorization': 'Bearer $token',
      });

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        print('response.body');
        print(response.body);
        final List items = data['tagihans'] ?? [];
        setState(() {
          allItems = items.map((item) => TagihanItem.fromJson(item)).toList();
        });
      } else {
        ScaffoldMessenger.of(context)
            .showSnackBar(const SnackBar(content: Text('Failed to fetch data')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text('Error: $e')));
    } finally {
      setState(() => isLoading = false);
    }
  }

  List<TagihanItem> get filteredTagihan {
    return allItems.where((t) {
      final bulanTahunMatch =
          filterBulanTahun == '' || t.bulanTahun == filterBulanTahun;
      final keywordMatch = searchQuery == '' ||
          t.pelanggan.toLowerCase().contains(searchQuery.toLowerCase());
      return bulanTahunMatch && keywordMatch;
    }).toList();
  }
	String formatMonthYear(String input) {
		  final date = DateTime.parse("$input-01"); // add day to make it valid
		  return DateFormat('MMMM yyyy', 'id_ID').format(date);
	  }
	  
	  void _showSnack(String msg, {bool isError = false}) {
		if (!mounted) return;
		ScaffoldMessenger.of(context).showSnackBar(
		  SnackBar(
			content: Text(msg),
			backgroundColor: isError ? const Color(0xFFFF3B30) : const Color(0xFF34C759),
		  ),
		);
	  }
	  
	Future<void> printForm(TagihanItem item) async {		
		setState(() => printingId = item.id);

		print("item");
		print("ID: ${item.id}");
print("Pelanggan: ${item.pelanggan}");
print("Bulan: ${item.bulanTahun}");
print("Alamat: ${item.alamat}");
print("Jumlah: ${item.jumlah}");
print("Status: ${item.status}");
print("Tanggal Bayar: ${item.tanggalBayar}");
		
		final bulanTahun = formatMonthYear(item.bulanTahun);
		
		print("tanggal_pembayaran "+item.tanggalBayar);
		String formattedDate = '-';

		final parsedDate = DateTime.tryParse(item.tanggalBayar);
		if (parsedDate != null) {
		  formattedDate = DateFormat('dd MMMM yyyy', 'id').format(parsedDate);
		}

		print("formattedDate $formattedDate");
				
		print("pelanggan_nama "+item.pelanggan);
		print("bulan_tahun "+bulanTahun);
		
		
		final jumlahText = 'Rp ${item.jumlah.toString().replaceAllMapped(RegExp(r'(\d{3})(?=\d)'), (m) => '${m[1]}.')}';
		print("jumlah "+jumlahText);		
		try {
			
			await printReceiptWithFeedback(
				context: context,
				name: item.pelanggan,
				month: bulanTahun,				
				paymentDate: formattedDate,
				formatPrice:jumlahText,
				navigateToSettings: () => Navigator.pushNamed(context, '/settings'),
			  );
		  } catch (e) {
			_showSnack(e.toString(), isError: true);
		  }finally{
			 setState(() => printingId = null);
 
		  }
  }
  
  @override
  Widget build(BuildContext context) {
    return Consumer<PrinterService>(builder: (_, printer, __) {
      final filteredItems = filteredTagihan;

      return Scaffold(
        appBar: AppBar(
          title: const Text('Daftar Tagihan'),
          actions: [
            IconButton(
              icon: const Icon(Icons.logout),
              onPressed: () => logout(context),
            ),
          ],
        ),
        drawer: const AppDrawer(currentPage: AppPage.tagihan),
        body: RefreshIndicator(
          onRefresh: fetchData,
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.all(8),
                child: TextField(
                  decoration: const InputDecoration(
                    hintText: 'Pencarian',
                    prefixIcon: Icon(Icons.search),
                  ),
                  onChanged: (val) => setState(() => searchQuery = val),
                ),
              ),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8),
                child: DropdownButton<String>(
                  isExpanded: true,
                  value: selectedStatus,
                  items: statusOptions
                      .map((status) => DropdownMenuItem(
                            value: status,
                            child: Text(status.toUpperCase()),
                          ))
                      .toList(),
                  onChanged: (val) {
                    setState(() => selectedStatus = val!);
                    fetchData();
                  },
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(8),
                child: MonthPickerField(
                  onChanged: (date) {
                    setState(() {
                      filterBulanTahun = DateFormat('yyyy-MM').format(date);
                    });
                  },
                ),
              ),
              Expanded(
                child: isLoading
                    ? const Center(child: CircularProgressIndicator())
                    : filteredItems.isEmpty
                        ? const Center(child: Text('Tidak ada data'))
                        : ListView.separated(
                            itemCount: filteredItems.length,
                            separatorBuilder: (_, __) => const Divider(),
                            itemBuilder: (context, index) {
                              final item = filteredItems[index];
                              final isThisPrinting = printingId == item.id;

                              return ListTile(
                                title: Text(item.pelanggan),
                                subtitle: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(item.alamat),
                                    Text(item.bulanTahun),
                                    Text(
                                      'Rp ${item.jumlah.toString().replaceAllMapped(RegExp(r'(\d{3})(?=\d)'), (m) => '${m[1]}.')}',
                                    ),
                                    Text(
                                      item.status,
                                      style: TextStyle(
                                        color: item.status == 'dibayar'
                                            ? Colors.green
                                            : Colors.red,
                                      ),
                                    ),
                                   
                                  ],
                                ),
                                trailing: item.status == 'belum bayar'
                                    ? ElevatedButton(
                                        style: ElevatedButton.styleFrom(
                                            backgroundColor: Colors.red),
                                        onPressed: () {
                                          Navigator.push(
                                            context,
                                            MaterialPageRoute(
                                              builder: (_) => PembayaranPage(
                                                  tagihanId: item.id),
                                            ),
                                          );
                                        },
                                        child: const Text('BAYAR',
                                            style:
                                                TextStyle(color: Colors.white)),
                                      )
                                    :  ElevatedButton(
									  onPressed: isThisPrinting
										  ? null
										  : () async {											  
												await printForm(item);												
											},
									  child: isThisPrinting
										  ? SizedBox(
											  width: 16,
											  height: 16,
											  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
											)
										  : Text('Print'),
									),
                              );
                            },
                          ),
              ),
            ],
          ),
        ),
      );
    });
  }
}
