import 'package:flutter/material.dart';
import '../tagihan_page.dart';
import '../settings_page.dart';
import '../utils/auth_utils.dart';

/// Which top-level page is currently active.
/// Used by [AppDrawer] to decide whether to pop (already here)
/// or push-replace (navigate to that page).
enum AppPage { tagihan, settings }

/// A shared navigation drawer used by [TagihanPage] and [SettingsScreen].
///
/// Pass [currentPage] so the drawer knows which item is "active":
///   - Active item  → closes the drawer (`Navigator.pop`)
///   - Inactive item → navigates to that page (`Navigator.pushReplacement`)
class AppDrawer extends StatelessWidget {
  final AppPage currentPage;

  const AppDrawer({super.key, required this.currentPage});

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        children: [
          const DrawerHeader(
            child: Text('Menu', style: TextStyle(fontSize: 24)),
          ),
          ListTile(
            leading: const Icon(Icons.payment),
            title: const Text('Tagihan'),
            onTap: currentPage == AppPage.tagihan
                ? () => Navigator.pop(context)
                : () => Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(builder: (_) => TagihanPage()),
                    ),
          ),
          ListTile(
            leading: const Icon(Icons.settings),
            title: const Text('Settings'),
            onTap: currentPage == AppPage.settings
                ? () => Navigator.pop(context)
                : () => Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(builder: (_) => SettingsScreen()),
                    ),
          ),
          ListTile(
            leading: const Icon(Icons.logout),
            title: const Text('Logout'),
            onTap: () => logout(context),
          ),
        ],
      ),
    );
  }
}
