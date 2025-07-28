import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'login_page.dart';
import 'tagihan_page.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  final Future<Widget> _initialPage = _getInitialPage();

  static Future<Widget> _getInitialPage() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('token');
    return token == null ? LoginPage() : TagihanPage();
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Tagihan App',
      theme: ThemeData(primarySwatch: Colors.red),
      home: FutureBuilder<Widget>(
        future: _initialPage,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting)
            return CircularProgressIndicator();
          return snapshot.data!;
        },
      ),
    );
  }
}
