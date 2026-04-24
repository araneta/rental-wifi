import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'tagihan_page.dart';
import 'Config.dart';

class PembayaranPage extends StatefulWidget {
  final String? tagihanId;
  const PembayaranPage({super.key, this.tagihanId});

  @override
  State<PembayaranPage> createState() => _PembayaranPageState();
}

class _PembayaranPageState extends State<PembayaranPage> {
  List<dynamic> pelangganList = [];
  List<dynamic> tagihanList = [];

  final _formKey = GlobalKey<FormState>();

  Map<String, dynamic> form = {
    "petugas_id": 6,
    "pelanggan_id": '',
    "tagihan_id": '',
    "jumlah": '',
    "metode_pembayaran": 'tunai',
    "tanggal_pembayaran": '',
  };

  String alertMessage = '';
  String alertType = 'success';
  bool isLoading = false;
  bool isSubmitting = false;

  @override
  void initState() {
    super.initState();
    fetchPelanggan();
    if (widget.tagihanId != null) {
      fetchTagihanById(widget.tagihanId!);
    }
  }

  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  Future<void> fetchPelanggan() async {
    final token = await getToken();
    setState(() => isLoading = true);
    try {
      final res = await http.get(
        Uri.parse(Config.API_HOST+'/api/pelanggans'),
        headers: {'Authorization': 'Bearer $token'},
      );
      if (res.statusCode == 200) {
        final data = json.decode(res.body);
        setState(() => pelangganList = data['pelanggans'] ?? []);
      } else {
        setState(() {
          alertMessage = 'Gagal memuat daftar pelanggan.';
          alertType = 'danger';
        });
      }
    } catch (e) {
      setState(() {
        alertMessage = 'Gagal memuat daftar pelanggan.';
        alertType = 'danger';
      });
    } finally {
      setState(() => isLoading = false);
    }
  }

  Future<void> fetchTagihan(String pelangganId) async {
    final token = await getToken();
    setState(() {
      isLoading = true;
      tagihanList = [];
      form['tagihan_id'] = '';
      form['jumlah'] = '';
    });
    try {
      final res = await http.get(
        Uri.parse(Config.API_HOST+'/api/tagihans/by-pelanggan/$pelangganId'),
        headers: {'Authorization': 'Bearer $token'},
      );
      if (res.statusCode == 200) {
        final data = json.decode(res.body);
        setState(() => tagihanList = data['tagihans'] ?? []);
      } else {
        setState(() {
          alertMessage = 'Gagal memuat tagihan.';
          alertType = 'danger';
        });
      }
    } catch (e) {
      setState(() {
        alertMessage = 'Gagal memuat tagihan.';
        alertType = 'danger';
      });
    } finally {
      setState(() => isLoading = false);
    }
  }

  Future<void> fetchTagihanById(String tagihanId) async {
    final token = await getToken();
    setState(() => isLoading = true);
    try {
      final res = await http.get(
        Uri.parse(Config.API_HOST+'/api/tagihans/$tagihanId'),
        headers: {'Authorization': 'Bearer $token'},
      );
      if (res.statusCode == 200) {
        final data = json.decode(res.body);
        final tagihan = data['tagihan'];
        setState(() {
          tagihanList = [tagihan];
          form['tagihan_id'] = tagihan['id'].toString();
          form['pelanggan_id'] = tagihan['pelanggan_id'].toString();
          form['jumlah'] = tagihan['jumlah'].toString();
        });
      } else {
        setState(() {
          alertMessage = 'Tagihan tidak ditemukan';
          alertType = 'danger';
        });
      }
    } catch (e) {
      setState(() {
        alertMessage = 'Gagal memuat tagihan.';
        alertType = 'danger';
      });
    } finally {
      setState(() => isLoading = false);
    }
  }

  void updateJumlah(String? tagihanId) {
    final selected = tagihanList.firstWhere((t) => t['id'].toString() == tagihanId, orElse: () => null);
    if (selected != null) {
      setState(() => form['jumlah'] = selected['jumlah'].toString());
    }
  }

  Future<void> submitForm() async {
    final token = await getToken();
    setState(() => isSubmitting = true);
    try {
      final formattedForm = {
        ...form,
        'pelanggan_id': int.tryParse(form['pelanggan_id'].toString()) ?? 0,
        'tagihan_id': int.tryParse(form['tagihan_id'].toString()) ?? 0,
        'jumlah': int.tryParse(form['jumlah'].toString()) ?? 0,
      };
      final res = await http.post(
        Uri.parse(Config.API_HOST+'/api/pembayarans'),
        headers: {
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: json.encode(formattedForm),
      );
      if (res.statusCode == 200) {
        setState(() {
          alertMessage = 'Pembayaran berhasil ditambahkan!';
          alertType = 'success';
        });
        _formKey.currentState?.reset();
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (_) => TagihanPage()),
        );
      } else {
        setState(() {
          alertMessage = 'Terjadi kesalahan saat menyimpan.';
          alertType = 'danger';
        });
      }
    } catch (e) {
      setState(() {
        alertMessage = 'Terjadi kesalahan saat menyimpan.';
        alertType = 'danger';
      });
    } finally {
      setState(() => isSubmitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Tambah Pembayaran')),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16.0),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (alertMessage.isNotEmpty)
                      Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        padding: const EdgeInsets.all(12),
                        color: alertType == 'danger' ? Colors.red[100] : Colors.green[100],
                        child: Text(alertMessage),
                      ),
                    DropdownButtonFormField(
                      value: form['pelanggan_id'].isEmpty ? null : form['pelanggan_id'],
                      items: pelangganList.map<DropdownMenuItem<String>>((p) {
                        return DropdownMenuItem(
                          value: p['id'].toString(),
                          child: Text(p['nama']),
                        );
                      }).toList(),
                      decoration: InputDecoration(labelText: 'Pilih Pelanggan'),
                      onChanged: (val) {
                        setState(() {
                          form['pelanggan_id'] = val;
                        });
                        fetchTagihan(val as String);
                      },
                      validator: (val) => val == null ? 'Harus dipilih' : null,
                    ),
                    SizedBox(height: 16),
                    DropdownButtonFormField(
                      value: form['tagihan_id'].isEmpty ? null : form['tagihan_id'],
                      items: tagihanList.map<DropdownMenuItem<String>>((t) {
                        return DropdownMenuItem(
                          value: t['id'].toString(),
                          child: Text("[${t['bulan_tahun']}] - Rp${NumberFormat.decimalPattern('id').format(int.parse(t['jumlah'].split('.')[0]))}"),
                        );
                      }).toList(),
                      decoration: InputDecoration(labelText: 'Pilih Tagihan'),
                      onChanged: (val) {
                        setState(() {
                          form['tagihan_id'] = val;
                        });
                        updateJumlah(val  as String);
                      },
                      validator: (val) => val == null ? 'Harus dipilih' : null,
                    ),
                    SizedBox(height: 16),
                    TextFormField(
                      readOnly: true,
                      decoration: InputDecoration(labelText: 'Jumlah Pembayaran (Rp)'),
                      controller: TextEditingController(text: form['jumlah'].toString()),
                    ),
                    SizedBox(height: 16),
                    DropdownButtonFormField(
                      value: form['metode_pembayaran'],
                      decoration: InputDecoration(labelText: 'Metode Pembayaran'),
                      items: ['tunai', 'transfer', 'QRIS', 'Lainnya']
                          .map((m) => DropdownMenuItem(value: m, child: Text(m)))
                          .toList(),
                      onChanged: (val) => setState(() => form['metode_pembayaran'] = val),
                    ),
                    SizedBox(height: 16),
                    TextFormField(
                      decoration: InputDecoration(labelText: 'Tanggal Pembayaran'),
                      keyboardType: TextInputType.datetime,
                      controller: TextEditingController(text: form['tanggal_pembayaran']),
                      onTap: () async {
                        FocusScope.of(context).requestFocus(FocusNode());
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: DateTime.now(),
                          firstDate: DateTime(2020),
                          lastDate: DateTime(2100),
                        );
                        if (picked != null) {
                          setState(() {
                            form['tanggal_pembayaran'] = DateFormat('yyyy-MM-dd').format(picked);
                          });
                        }
                      },
                    ),
                    SizedBox(height: 20),
                    ElevatedButton(
                      onPressed: isSubmitting
                          ? null
                          : () {
                              if (_formKey.currentState?.validate() ?? false) {
                                submitForm();
                              }
                            },
                      child: isSubmitting ? CircularProgressIndicator() : Text('Simpan'),
                    )
                  ],
                ),
              ),
            ),
    );
  }
}
