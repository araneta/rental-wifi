// lib/services/printer_service.dart
//
// Modelled directly on the working bluetoothscanner reference app:
//   - Uses esc_pos_utils (not esc_pos_utils_plus)
//   - Singleton ChangeNotifier so connection persists across screens
//   - Permissions: only bluetoothScan + bluetoothConnect (no location)
//   - setGlobalCodeTable('CP1252') before every print job

import 'package:esc_pos_utils/esc_pos_utils.dart';
import 'package:flutter/foundation.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:print_bluetooth_thermal/print_bluetooth_thermal.dart';
import 'package:shared_preferences/shared_preferences.dart';

// ── Simple device model ───────────────────────────────────────────────────────

class PrinterDevice {
  final String name;
  final String macAddress;

  const PrinterDevice({required this.name, required this.macAddress});

  @override
  String toString() => '$name ($macAddress)';
}

// ── Saved preference keys ─────────────────────────────────────────────────────

const _prefMac  = 'printer_mac';
const _prefName = 'printer_name';

// ── Singleton service (ChangeNotifier so UI rebuilds automatically) ───────────

class PrinterService extends ChangeNotifier {
  static final PrinterService _instance = PrinterService._internal();
  factory PrinterService() => _instance;
  PrinterService._internal();

  // ── State ─────────────────────────────────────────────────────────────────

  List<BluetoothInfo> _devices = [];
  List<BluetoothInfo> get devices => List.unmodifiable(_devices);

  bool _isScanning = false;
  bool get isScanning => _isScanning;

  bool _isPrinting = false;
  bool get isPrinting => _isPrinting;
  bool isBluetoothOff = false;
  BluetoothInfo? _connectedDevice;
  BluetoothInfo? get connectedDevice => _connectedDevice;

  PrinterDevice? _savedPrinter;
  PrinterDevice? get savedPrinter => _savedPrinter;

  String _statusMessage = '';
  String get statusMessage => _statusMessage;

  // ── Bluetooth state ───────────────────────────────────────────────────────

  Future<bool> isBluetoothEnabled() async {
    debugPrint('[Printer] Checking if Bluetooth is enabled');
    try { 
      debugPrint('[Printer] Bluetooth enabled: ${await PrintBluetoothThermal.bluetoothEnabled}');
      return await PrintBluetoothThermal.bluetoothEnabled; 
    }
    catch (e) { 
      debugPrint('[Printer] Bluetooth enabled check failed: $e');
      return false;
    }
  }

  /// Call this whenever the app resumes or on startup.
  /// Updates [isBluetoothOff] and notifies listeners so the UI rebuilds.
  Future<void> checkBluetoothState() async {
    final enabled = await isBluetoothEnabled();
    isBluetoothOff = !enabled;
    notifyListeners();
  }

  Future<bool> isConnected() async {
    try { 
      debugPrint('[Printer] Checking connection status: ${await PrintBluetoothThermal.connectionStatus}');
      return await PrintBluetoothThermal.connectionStatus; }
    catch (e) { 
      debugPrint('[Printer] Connection status check failed: $e');
      return false; 
      }
  }

  // ── Permissions ───────────────────────────────────────────────────────────
  // Exactly matches the working reference app: only bluetoothScan + bluetoothConnect

  Future<bool> requestPermissions() async {
    final statuses = await [
      Permission.bluetoothScan,
      Permission.bluetoothConnect,
    ].request();

    debugPrint('[Printer] Permission statuses: $statuses');

    final denied = statuses.values.any(
      (s) => s.isDenied || s.isPermanentlyDenied,
    );
    return !denied;
  }

  Future<bool> hasPermissions() async {
    final scan    = await Permission.bluetoothScan.isGranted;
    final connect = await Permission.bluetoothConnect.isGranted;
    return scan && connect;
  }

  Future<bool> isPermPermanentlyDenied() async {
    return await Permission.bluetoothScan.isPermanentlyDenied ||
           await Permission.bluetoothConnect.isPermanentlyDenied;
  }

  // ── Scan ──────────────────────────────────────────────────────────────────

  Future<void> startScan() async {
    _isScanning = true;
    _statusMessage = 'Scanning for paired Bluetooth printers…';
    notifyListeners();

    try {
      final paired = await PrintBluetoothThermal.pairedBluetooths;
      _devices = paired;
      _statusMessage = paired.isEmpty
          ? 'No paired devices found. Pair your printer in Android Settings → Bluetooth first.'
          : 'Found ${paired.length} device(s). Select your printer below.';
      debugPrint('[Printer] Scan found ${paired.length} device(s)');
      for (final d in paired) {
        debugPrint('[Printer]   ${d.name}  ${d.macAdress}');
      }
    } catch (e) {
      _statusMessage = 'Scan error: $e';
      debugPrint('[Printer] Scan error: $e');
    } finally {
      _isScanning = false;
      notifyListeners();
    }
  }

  // ── Connect / Disconnect ──────────────────────────────────────────────────

  Future<bool> connectTo(BluetoothInfo device) async {
    await disconnectCurrent();

    _statusMessage = 'Connecting to ${device.name}…';
    notifyListeners();

    try {
      final ok = await PrintBluetoothThermal.connect(
        macPrinterAddress: device.macAdress,
      );
      if (ok) {
        _connectedDevice = device;
        _statusMessage = 'Connected to ${device.name}';
        // Persist for auto-reconnect on app restart
        await _persist(PrinterDevice(name: device.name, macAddress: device.macAdress));
      } else {
        _statusMessage = 'Could not connect to ${device.name}.\n'
            'Make sure the printer is powered on and nearby.';
      }
      notifyListeners();
      return ok;
    } catch (e) {
      _statusMessage = 'Connection failed: $e';
      notifyListeners();
      return false;
    }
  }

  Future<void> disconnectCurrent() async {
    if (_connectedDevice != null) {
      try { await PrintBluetoothThermal.disconnect; } catch (_) {}
      _connectedDevice = null;
      _statusMessage = 'Disconnected.';
      notifyListeners();
    }
  }

  // ── Saved printer persistence ─────────────────────────────────────────────

  Future<void> _persist(PrinterDevice d) async {
    _savedPrinter = d;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_prefMac, d.macAddress);
    await prefs.setString(_prefName, d.name);
  }

  Future<void> loadSavedPrinter() async {
    final prefs = await SharedPreferences.getInstance();
    final mac  = prefs.getString(_prefMac);
    final name = prefs.getString(_prefName);
    if (mac != null && mac.isNotEmpty) {
      _savedPrinter = PrinterDevice(name: name ?? mac, macAddress: mac);
      notifyListeners();
    }
  }

  /// Loads the saved printer then immediately tries to connect.
  /// Call this after login and on app resume.
  Future<void> autoConnectSavedPrinter() async {
    await loadSavedPrinter();
    if (_savedPrinter == null) return;
    if (await isConnected()) return; // already connected

    // Need BT on and permissions before connecting
    if (!await isBluetoothEnabled()) return;
    final hasPerms = await hasPermissions();
    if (!hasPerms) {
      await requestPermissions();
      if (!await hasPermissions()) return;
    }

    debugPrint('[Printer] Auto-connecting to ${_savedPrinter!.name}…');
    _statusMessage = 'Connecting to ${_savedPrinter!.name}…';
    notifyListeners();

    try {
      final ok = await PrintBluetoothThermal.connect(
          macPrinterAddress: _savedPrinter!.macAddress);
      if (ok) {
        _connectedDevice = BluetoothInfo(
          name: _savedPrinter!.name,
          macAdress: _savedPrinter!.macAddress,
        );
        _statusMessage = 'Connected to ${_savedPrinter!.name}';
        debugPrint('[Printer] Auto-connect succeeded');
      } else {
        _statusMessage = 'Could not auto-connect to ${_savedPrinter!.name}';
        debugPrint('[Printer] Auto-connect failed');
      }
    } catch (e) {
      _statusMessage = 'Auto-connect error: $e';
      debugPrint('[Printer] Auto-connect error: $e');
    }
    notifyListeners();
  }

  Future<void> clearSavedPrinter() async {
    _savedPrinter = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_prefMac);
    await prefs.remove(_prefName);
    notifyListeners();
  }

  // ── Test page ─────────────────────────────────────────────────────────────

  Future<bool> printTestPage({PaperSize paperSize = PaperSize.mm58}) async {
    if (!await isConnected()) {
      _statusMessage = 'No printer connected.';
      notifyListeners();
      return false;
    }

    _isPrinting = true;
    _statusMessage = 'Printing test page…';
    notifyListeners();

    try {
      final profile = await CapabilityProfile.load();
      final gen = Generator(paperSize, profile);
      List<int> bytes = [];

      bytes += gen.setGlobalCodeTable('CP1252');
      bytes += gen.text('=== TEST PAGE ===',
          styles: const PosStyles(align: PosAlign.center, bold: true,
              height: PosTextSize.size2, width: PosTextSize.size2));
      bytes += gen.text('BoltexPOS',
          styles: const PosStyles(align: PosAlign.center));
      bytes += gen.text('ESC/POS Printer OK',
          styles: const PosStyles(align: PosAlign.center));
      bytes += gen.hr();
      bytes += gen.text('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
      bytes += gen.text('0123456789  !@#\$%^&*()');
      bytes += gen.hr();
      final now = DateTime.now();
      bytes += gen.text(
        '${_p(now.day)}/${_p(now.month)}/${now.year}  ${_p(now.hour)}:${_p(now.minute)}',
        styles: const PosStyles(align: PosAlign.center),
      );
      bytes += gen.feed(2);
      bytes += gen.cut();

      final ok = await PrintBluetoothThermal.writeBytes(bytes);
      _statusMessage = ok ? 'Test page printed!' : 'Print failed.';
      _isPrinting = false;
      notifyListeners();
      return ok;
    } catch (e) {
      _statusMessage = 'Print error: $e';
      _isPrinting = false;
      notifyListeners();
      return false;
    }
  }

  // ── Receipt printing ──────────────────────────────────────────────────────

  Future<bool> printReceipt({
    required Map<String, dynamic> order,
    String storeName = 'BoltexPOS',
    String storeAddress = '',
    String storePhone = '',
    String storeEmail = '',
    String storeWebsite = '',
    String storeTaxId = '',
    required String Function(double) formatPrice,
    PaperSize paperSize = PaperSize.mm58,
  }) async {
    // Check Bluetooth is on first
    debugPrint('[Printer] Starting receipt print');
    if (!await isBluetoothEnabled()) {
      throw Exception('BLUETOOTH_OFF');
    }
    debugPrint('[Printer] Bluetooth is ON');

    if (!await isConnected()) {
      debugPrint('[Printer] Not connected — attempting auto-reconnect to ${_savedPrinter!.name}');
      if (_savedPrinter == null) {
        debugPrint('[Printer] No printer configured — showing settings prompt');
        // No printer configured at all
        throw Exception('NO_PRINTER_CONFIGURED');
      }
      
      // Try auto-reconnect with saved device
      debugPrint('[Printer] Not connected — attempting auto-reconnect to ${_savedPrinter!.name}');
      _statusMessage = 'Reconnecting to ${_savedPrinter!.name}…';
      notifyListeners();
      final ok = await PrintBluetoothThermal.connect(
          macPrinterAddress: _savedPrinter!.macAddress);
      if (!ok) {
        _statusMessage = 'Could not reconnect to ${_savedPrinter!.name}.';
        notifyListeners();
        throw Exception('PRINTER_RECONNECT_FAILED:${_savedPrinter!.name}');
      }
      debugPrint('[Printer] Auto-reconnect successful');
    }

    _isPrinting = true;
    _statusMessage = 'Printing receipt…';
    notifyListeners();

    try {
      final bytes = await _buildReceipt(
        order: order,
        storeName: storeName,
        storeAddress: storeAddress,
        storePhone: storePhone,
        storeEmail: storeEmail,
        storeWebsite: storeWebsite,
        storeTaxId: storeTaxId,
        formatPrice: formatPrice,
        paperSize: paperSize,
      );
      final ok = await PrintBluetoothThermal.writeBytes(bytes);
      _statusMessage = ok ? 'Receipt printed!' : 'Print failed.';
      _isPrinting = false;
      notifyListeners();
      return ok;
    } catch (e) {
      _statusMessage = 'Print error: $e';
      _isPrinting = false;
      notifyListeners();
      return false;
    }
  }


  // ── ESC/POS receipt builder  (mirrors printer.ts) ───────────────────────────

  Future<List<int>> _buildReceipt({
    required Map<String, dynamic> order,
    required String storeName,
    required String storeAddress,
    required String storePhone,
    required String storeEmail,
    required String storeWebsite,
    required String storeTaxId,
    required String Function(double) formatPrice,
    required PaperSize paperSize,
  }) async {
    final profile = await CapabilityProfile.load();
    final gen     = Generator(paperSize, profile);
    List<int> bytes = [];

    bytes += gen.setGlobalCodeTable('CP1252');

    // ── Store header (CENTER, large name) ─────────────────────────────────────
    bytes += gen.text(
      storeName,
      styles: const PosStyles(
        align: PosAlign.center,
        bold: true,
        height: PosTextSize.size2,
        width: PosTextSize.size2,
      ),
    );
    if (storePhone.isNotEmpty)
      bytes += gen.text(storePhone, styles: const PosStyles(align: PosAlign.center));
    if (storeEmail.isNotEmpty)
      bytes += gen.text(storeEmail, styles: const PosStyles(align: PosAlign.center));
    if (storeAddress.isNotEmpty)
      bytes += gen.text(storeAddress, styles: const PosStyles(align: PosAlign.center));
    if (storeWebsite.isNotEmpty)
      bytes += gen.text(storeWebsite, styles: const PosStyles(align: PosAlign.center));
    if (storeTaxId.isNotEmpty)
      bytes += gen.text('Tax ID: $storeTaxId', styles: const PosStyles(align: PosAlign.center));

    bytes += gen.hr();

    // ── Customer info (if present) ────────────────────────────────────────────
    final cust = order['customer'] as Map<String, dynamic>?;
    if (cust != null) {
      final custName    = cust['name']?.toString() ?? '';
      final custEmail   = cust['email']?.toString() ?? '';
      final custPhone   = cust['phone']?.toString() ?? '';
      final custAddress = cust['address']?.toString() ?? '';
      if (custName.isNotEmpty) {
        bytes += gen.hr(ch: '-');
        bytes += gen.text('Customer: $custName');
        if (custEmail.isNotEmpty)   bytes += gen.text('Email: $custEmail');
        if (custPhone.isNotEmpty)   bytes += gen.text('Phone: $custPhone');
        if (custAddress.isNotEmpty) bytes += gen.text('Address: $custAddress');
      }
    }

    // ── Order info ────────────────────────────────────────────────────────────
    bytes += gen.hr(ch: '-');
    final orderId = order['id']?.toString() ?? 'N/A';
    bytes += gen.text('Order #$orderId');

    final rawDate = order['date']?.toString() ??
        order['createdAt']?.toString() ?? '';
    if (rawDate.isNotEmpty) {
      try {
        final dt = DateTime.parse(rawDate).toLocal();
        bytes += gen.text(
            'Date: ${_p(dt.day)}/${_p(dt.month)}/${dt.year}');
        bytes += gen.text(
            'Time: ${_p(dt.hour)}:${_p(dt.minute)}:${_p(dt.second)}');
      } catch (_) {
        bytes += gen.text('Date: $rawDate');
      }
    }

    final empName = (order['employee'] as Map<String, dynamic>?)?['name']
            ?.toString() ??
        '';
    if (empName.isNotEmpty) bytes += gen.text('Cashier: $empName');

    bytes += gen.hr(ch: '-');

    // ── Items ─────────────────────────────────────────────────────────────────
    bytes += gen.text('ITEMS:');

    final items = (order['items'] as List?) ?? [];
    for (final raw in items) {
      final item   = raw as Map<String, dynamic>;
      final name   = item['name']?.toString() ?? '';
      final qty    = (item['quantity'] as num? ?? 1).toInt();
      final price  = double.tryParse(item['price']?.toString() ?? '0') ?? 0;
      final disc   = double.tryParse(
              item['discount_amount']?.toString() ?? '0') ??
          0;
      final discType  = item['discountType']?.toString() ?? '';
      final discValue = item['discountValue']?.toString() ?? '';
      final itemTotal = price * qty;
      final itemSub   = itemTotal - disc;

      // Item name — own line
      bytes += gen.text(name);
      // Qty x price = total
      bytes += gen.text(
          '  $qty x ${formatPrice(price)} = ${formatPrice(itemTotal)}');

      // Discount line
      if (disc > 0) {
        final discLabel = discType == 'percentage'
            ? '$discValue% off'
            : '-${formatPrice(disc)}';
        bytes += gen.text('  Discount: $discLabel');
        bytes += gen.text('  Subtotal: ${formatPrice(itemSub)}');
      }
    }

    bytes += gen.hr(ch: '-');

    // ── Totals (RIGHT aligned) ────────────────────────────────────────────────
    final subtotal   = double.tryParse(order['subtotal']?.toString() ?? '0') ?? 0;
    final totalItemDiscs = items.fold<double>(
        0,
        (s, raw) =>
            s + (double.tryParse(
                    (raw as Map<String, dynamic>)['discount_amount']?.toString() ?? '0') ??
                0));
    final orderDisc  = double.tryParse(
            order['discount_amount']?.toString() ??
                order['discountAmount']?.toString() ??
                '0') ??
        0;
    final discCode   = order['discountCode']?.toString() ?? '';
    final taxAmt     = double.tryParse(
            order['tax_amount']?.toString() ??
                order['taxAmount']?.toString() ??
                '0') ??
        0;
    final total      = double.tryParse(order['total']?.toString() ?? '0') ?? 0;

    _rightRow(gen, bytes, 'Subtotal:', formatPrice(subtotal));
    if (totalItemDiscs > 0)
      _rightRow(gen, bytes, 'Item Discounts:', '-${formatPrice(totalItemDiscs)}');
    if (orderDisc > 0)
      _rightRow(
        gen, bytes,
        'Order Discount${discCode.isNotEmpty ? ' ($discCode)' : ''}:',
        '-${formatPrice(orderDisc)}',
      );
    if (taxAmt > 0) _rightRow(gen, bytes, 'Tax:', formatPrice(taxAmt));

    bytes += gen.hr(ch: '=');
    bytes += gen.text(
      'TOTAL: ${formatPrice(total)}',
      styles: const PosStyles(
        bold: true,
        align: PosAlign.right,
        height: PosTextSize.size2,
        width: PosTextSize.size2,
      ),
    );
    bytes += gen.hr(ch: '=');

    // ── Payment ───────────────────────────────────────────────────────────────
    final payments = (order['paymentDetails'] as List?) ?? [];
    if (payments.isNotEmpty) {
      bytes += gen.feed(1);
      bytes += gen.text('PAYMENT:');
      for (final raw in payments) {
        final pm     = raw as Map<String, dynamic>;
        final method = _humanMethod(pm['method']?.toString() ?? '');
        final amount = double.tryParse(pm['amount']?.toString() ?? '0') ?? 0;
        final change = double.tryParse(pm['change']?.toString() ?? '0') ?? 0;
        final txId   = pm['transactionId']?.toString() ?? '';
        final last4  = pm['cardLast4']?.toString() ?? '';
        final brand  = pm['cardBrand']?.toString() ?? '';

        bytes += gen.text('$method: ${formatPrice(amount)}');
        if (txId.isNotEmpty)
          bytes += gen.text('  Transaction ID: $txId',
              styles: const PosStyles(fontType: PosFontType.fontB));
        if (last4.isNotEmpty)
          bytes += gen.text('  Card: ****$last4${brand.isNotEmpty ? ' ($brand)' : ''}',
              styles: const PosStyles(fontType: PosFontType.fontB));
        if (change > 0)
          bytes += gen.text('  Change: ${formatPrice(change)}');
      }
    } else {
      final method = _humanMethod(order['paymentMethod']?.toString() ?? '');
      if (method.isNotEmpty) bytes += gen.text('Payment: $method');
    }

    // ── Footer ────────────────────────────────────────────────────────────────
    bytes += gen.hr(ch: '-');
    bytes += gen.text('Thank you for your purchase!',
        styles: const PosStyles(align: PosAlign.center, bold: true));
    bytes += gen.text('Please come again',
        styles: const PosStyles(align: PosAlign.center));
    bytes += gen.feed(3);
    bytes += gen.cut();

    return bytes;
  }


  // ── Helpers ───────────────────────────────────────────────────────────────

  String _p(int n) => n.toString().padLeft(2, '0');

  void _rightRow(Generator gen, List<int> bytes, String label, String value) {
    bytes += gen.row([
      PosColumn(text: label, width: 6),
      PosColumn(text: value, width: 6,
          styles: const PosStyles(align: PosAlign.right)),
    ]);
  }
  String _trunc(String s, int max) => s.length > max ? s.substring(0, max) : s;
  String _humanMethod(String m) {
    switch (m) {
      case 'cash': return 'Cash';
      case 'card': return 'Card';
      case 'digital_wallet': return 'Digital Wallet';
      case 'bank_transfer': return 'Bank Transfer';
      default: return m;
    }
  }

  Future<void> autoConnect() async {
    await loadSavedPrinter();

    if (_savedPrinter == null) {
      debugPrint('[Printer] No saved printer');
      return;
    }

    // Check Bluetooth first
    final isOn = await isBluetoothEnabled();
    if (!isOn) {
      isBluetoothOff = true;
      _statusMessage = 'Bluetooth is OFF';
      notifyListeners();
      return;
    } else {
      isBluetoothOff = false;
    }

    // Already connected?
    if (await isConnected()) {
      debugPrint('[Printer] Already connected');
      return;
    }

    debugPrint('[Printer] Auto-connecting to ${_savedPrinter!.name}');

    try {
      final ok = await PrintBluetoothThermal.connect(
        macPrinterAddress: _savedPrinter!.macAddress,
      );

      if (ok) {
        _connectedDevice = BluetoothInfo(
          name: _savedPrinter!.name,
          macAdress: _savedPrinter!.macAddress,
        );
        _statusMessage = 'Auto-connected to ${_savedPrinter!.name}';
      } else {
        _statusMessage = 'Auto-connect failed';
      }
    } catch (e) {
      _statusMessage = 'Auto-connect error: $e';
    }

    notifyListeners();
  }

  Future<void> refreshBluetoothStatus() async {
    final isOn = await isBluetoothEnabled();

    isBluetoothOff = !isOn;

    notifyListeners();
  }

  Future<void> refreshAll() async {
    final isOn = await PrintBluetoothThermal.bluetoothEnabled;

    isBluetoothOff = !isOn;

    if (isOn) {
      // Try reconnect if printer saved
      if (_savedPrinter != null) {
        final connected = await PrintBluetoothThermal.connectionStatus;

        if (!connected) {
          await PrintBluetoothThermal.connect(
            macPrinterAddress: _savedPrinter!.macAddress,
          );
        }
      }
    }

    notifyListeners();
  }
}
