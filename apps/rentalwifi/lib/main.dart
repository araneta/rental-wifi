import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/date_symbol_data_local.dart';

import 'package:provider/provider.dart';
import 'login_page.dart';
import 'tagihan_page.dart';
import 'settings_page.dart';
import 'services/printer_service.dart';


Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID', null);
  runApp(MultiProvider(
        providers: [          

          // ✅ ADD THIS
          ChangeNotifierProvider(create: (_) => PrinterService()),
          
        ],
        child: MyApp(),
      )
  ); // Sentry dihapus, langsung jalankan app
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Tagihan Wifi',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: SplashPage(), // Initial page to decide where to go
    );
  }
}

class SplashPage extends StatelessWidget {
  Future<bool> isLoggedIn() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    return prefs.getString('token') != null;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: isLoggedIn(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return Scaffold(
            body: Center(child: CircularProgressIndicator()),
          );
        }

        if (snapshot.hasData && snapshot.data == true) {
          return TagihanPage();
        } else {
          return LoginPage();
        }
      },
    );
  }
}
