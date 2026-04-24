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

class TagihanItem {
  final String id;
  final String pelanggan;
  final String bulanTahun;
  final String alamat;
  final int jumlah;
  final String status;

  TagihanItem({
    required this.id,
    required this.pelanggan,
    required this.bulanTahun,
    required this.alamat,
    required this.jumlah,
    required this.status,
  });

  factory TagihanItem.fromJson(Map<String, dynamic> json) {
    return TagihanItem(
      id: json['id'].toString(),
      pelanggan: json['pelanggan'] ?? '',
      bulanTahun: json['bulan_tahun'] ?? '',
      alamat: json['alamat'] ?? '',
      jumlah: int.tryParse(json['jumlah']?.split('.')?.first ?? '0') ?? 0,
      status: json['status'] ?? '',
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
                              return ListTile(
                                title: Text(item.pelanggan),
                                subtitle: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(item.alamat),
                                    Text(item.bulanTahun),
                                    Text(
                                      'Rp${item.jumlah.toString().replaceAllMapped(RegExp(r'(\d{3})(?=\d)'), (m) => '${m[1]}.')}',
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
                                    : null,
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
