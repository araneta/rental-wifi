import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

// Example providers (adjust to your actual ones)
import '../providers/settings_provider.dart';
import '../providers/auth_provider.dart';

class SettingsPage extends StatelessWidget {
  const SettingsPage({super.key});

  @override
  Widget build(BuildContext context) {
    final settings = context.watch<SettingsProvider>();
    final auth = context.read<AuthProvider>();

    return Scaffold(
      appBar: AppBar(
        title: const Text('Settings'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _buildSectionTitle('General'),
          _buildSwitchTile(
            icon: Icons.dark_mode,
            title: 'Dark Mode',
            value: settings.isDarkMode,
            onChanged: (val) => settings.setDarkMode(val),
          ),

          _buildSectionTitle('Account'),
          _buildTile(
            icon: Icons.person,
            title: 'Profile',
            onTap: () {
              Navigator.pushNamed(context, '/profile');
            },
          ),
          _buildTile(
            icon: Icons.lock,
            title: 'Change Password',
            onTap: () {
              Navigator.pushNamed(context, '/change-password');
            },
          ),

          _buildSectionTitle('App'),
          _buildTile(
            icon: Icons.info,
            title: 'About',
            onTap: () {
              showAboutDialog(
                context: context,
                applicationName: 'Your App Name',
                applicationVersion: '1.0.0',
              );
            },
          ),

          const SizedBox(height: 20),

          _buildDangerZone(
            context,
            onLogout: () async {
              await auth.logout();
              Navigator.pushNamedAndRemoveUntil(
                context,
                '/login',
                (route) => false,
              );
            },
          ),
        ],
      ),
    );
  }

  // =========================
  // UI COMPONENTS
  // =========================

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(top: 16, bottom: 8),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.bold,
          color: Colors.grey,
        ),
      ),
    );
  }

  Widget _buildTile({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return Card(
      elevation: 0,
      margin: const EdgeInsets.symmetric(vertical: 4),
      child: ListTile(
        leading: Icon(icon),
        title: Text(title),
        trailing: const Icon(Icons.chevron_right),
        onTap: onTap,
      ),
    );
  }

  Widget _buildSwitchTile({
    required IconData icon,
    required String title,
    required bool value,
    required Function(bool) onChanged,
  }) {
    return Card(
      elevation: 0,
      margin: const EdgeInsets.symmetric(vertical: 4),
      child: SwitchListTile(
        secondary: Icon(icon),
        title: Text(title),
        value: value,
        onChanged: onChanged,
      ),
    );
  }

  Widget _buildDangerZone(BuildContext context, {required VoidCallback onLogout}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Divider(),
        const SizedBox(height: 8),
        const Text(
          'Danger Zone',
          style: TextStyle(
            color: Colors.red,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 8),
        Card(
          color: Colors.red.shade50,
          child: ListTile(
            leading: const Icon(Icons.logout, color: Colors.red),
            title: const Text(
              'Logout',
              style: TextStyle(color: Colors.red),
            ),
            onTap: () => _confirmLogout(context, onLogout),
          ),
        ),
      ],
    );
  }

  void _confirmLogout(BuildContext context, VoidCallback onLogout) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Confirm Logout'),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel'),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              onLogout();
            },
            child: const Text('Logout'),
          ),
        ],
      ),
    );
  }
}
