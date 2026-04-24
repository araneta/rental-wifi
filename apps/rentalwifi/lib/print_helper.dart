// lib/widgets/print_helper.dart
//
// Single function used by both pos_screen.dart and pos_screen_mobile.dart.
//
// Behaviour:
//   • If printer is configured (savedPrinter != null) → auto-reconnects and prints.
//   • If no printer is configured → shows a dialog telling the user to go to Settings.
//   • If reconnect fails → shows a dialog with the device name and a Settings shortcut.
//   • On any other print error → shows the error message.

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/printer_service.dart';
import 'package:flutter/material.dart';
import 'package:app_settings/app_settings.dart' as app_settings;
import 'package:permission_handler/permission_handler.dart' show openAppSettings;
/// Prints a receipt and handles all error cases with user-friendly dialogs.
///
/// [navigateToSettings] — callback that navigates to the Settings screen.
/// Pass `() => Navigator.pushNamed(context, '/settings')` or equivalent.
Future<void> printReceiptWithFeedback({
  required BuildContext context,   
required String name,
required String month,
required String paymentDate,
required String Function(double) formatPrice,
  required VoidCallback navigateToSettings,
}) async {
  final svc = context.read<PrinterService>(); 

  try {
    
    await svc.printReceipt(
	  storeName: "Perintis",
	  phone: "081235238655",
	  name: name,
	  month: month,
	  paymentDate: paymentDate,
	  formatPrice: formatPrice,
	  
	);
   
  } catch (e) {
     ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(e.toString()),
          duration: const Duration(seconds: 12),
          behavior: SnackBarBehavior.floating, // Makes it look more like a toast
        ),
      );
    if (!context.mounted){
      
      return;
    } 
   
    final msg = e.toString().replaceFirst('Exception: ', '');

    if (msg == 'BLUETOOTH_OFF') {
      _showBluetoothOffDialog(context: context, navigateToSettings: navigateToSettings);
    } else if (msg == 'NO_PRINTER_CONFIGURED') {
      _showPrinterDialog(
        context: context,
        title: 'No Printer Configured',
        body:
            'You haven\'t set up a Bluetooth printer yet.\n\n'
            'Go to Settings → Printer to pair and select your printer.',
        navigateToSettings: navigateToSettings,
      );
    } else if (msg.startsWith('PRINTER_RECONNECT_FAILED:')) {
      final deviceName = msg.replaceFirst('PRINTER_RECONNECT_FAILED:', '');
      _showPrinterDialog(
        context: context,
        title: 'Printer Unavailable',
        body:
            'Could not connect to "$deviceName".\n\n'
            'Make sure the printer is turned on and within range, '
            'then try again from Settings → Printer.',
        navigateToSettings: navigateToSettings,
      );
    } else {
      _showPrinterDialog(
        context: context,
        title: 'Print Failed',
        body: msg,
        navigateToSettings: navigateToSettings,
      );
    }
  }
}
void _showPermDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Bluetooth Permission Required'),
        content: const Text(
            'Bluetooth permission is needed to scan for printers. '
            'Please allow it when prompted.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () { Navigator.pop(context); openAppSettings(); },
            child: const Text('Open Settings'),
          ),
        ],
      ),
    );
  }
void _showBluetoothOffDialog({
  required BuildContext context,
  required VoidCallback navigateToSettings,
}) {
  showDialog(
    context: context,
    builder: (dialogContext) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      icon: const Icon(Icons.bluetooth_disabled,
          color: Colors.orange, size: 36),
      title: const Text('Bluetooth is Off',
          style: TextStyle(fontWeight: FontWeight.w600, fontSize: 17)),
      content: const Text(
        'Bluetooth is required to print receipts.\n\n'
        'Please enable Bluetooth and try again.',
        style: TextStyle(fontSize: 14, color: Color(0xFF555555), height: 1.5),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Cancel'),
        ),
        ElevatedButton.icon(
          onPressed: () async {
              try {
                await app_settings.AppSettings.openAppSettings(
                  type: app_settings.AppSettingsType.bluetooth,
                );
              } catch (e) {
                // fallback to general settings
                await app_settings.AppSettings.openAppSettings();
              }

              // Re-check after user returns
              await Future.delayed(const Duration(milliseconds: 800));
              final svc = dialogContext.read<PrinterService>();
              final now = await svc.isBluetoothEnabled();
              if(now){
                final now = await svc.isBluetoothEnabled();
                if(now){
                  final granted = await svc.requestPermissions();
                  if (!granted) {
                    final permanent = await svc.isPermPermanentlyDenied();                    
                    if (!permanent) _showPermDialog(context);
                    return;
                  }
                  
                  svc.startScan();
                }
              }              
          },
          icon: const Icon(Icons.bluetooth, size: 16),
          label: const Text('Enable Bluetooth'),
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.orange,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8)),
          ),
        ),
      ],
    ),
  );
}

void _showPrinterDialog({
  required BuildContext context,
  required String title,
  required String body,
  required VoidCallback navigateToSettings,
}) {
  showDialog(
    context: context,
    builder: (_) => AlertDialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      icon: const Icon(Icons.print_disabled_outlined,
          color: Color(0xFFFF9500), size: 36),
      title: Text(title,
          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 17)),
      content: Text(body,
          style: const TextStyle(fontSize: 14, color: Color(0xFF555555),
              height: 1.5)),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context),
          child: const Text('Dismiss'),
        ),
        ElevatedButton.icon(
          onPressed: () {
            Navigator.pop(context);
            navigateToSettings();
          },
          icon: const Icon(Icons.settings_outlined, size: 16),
          label: const Text('Go to Settings'),
          style: ElevatedButton.styleFrom(
            backgroundColor: const Color(0xFF007AFF),
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8)),
          ),
        ),
      ],
    ),
  );
}
