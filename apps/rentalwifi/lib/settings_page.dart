import 'package:print_bluetooth_thermal/print_bluetooth_thermal.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:esc_pos_utils/esc_pos_utils.dart';
import 'package:flutter/material.dart';
import 'package:app_settings/app_settings.dart' as app_settings;
import 'package:permission_handler/permission_handler.dart' show openAppSettings;
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/printer_service.dart';
import 'widgets/app_drawer.dart';
import 'utils/auth_utils.dart';

class SettingsScreen extends StatefulWidget {
  const SettingsScreen({super.key});

  @override
  State<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends State<SettingsScreen>
    with WidgetsBindingObserver {
  // ── Helpers ───────────────────────────────────────────────────────────────

  void _showSnack(String msg, {bool isError = false}) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor:
          isError ? const Color(0xFFFF3B30) : const Color(0xFF34C759),
    ));
  }

  // ── Section card ──────────────────────────────────────────────────────────

  Widget _sectionCard(
      {required String title, required List<Widget> children}) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Text(title,
                style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF666666))),
          ),
          ...children,
        ],
      ),
    );
  }

  // ── Setting tile (tappable) ───────────────────────────────────────────────

  Widget _settingTile({
    required IconData icon,
    required String title,
    String? subtitle,
    VoidCallback? onTap,
    bool isLast = false,
  }) {
    return Column(
      children: [
        ListTile(
          leading: Icon(icon, color: const Color(0xFF007AFF)),
          title: Text(title, style: const TextStyle(fontSize: 16)),
          subtitle: subtitle != null
              ? Text(subtitle,
                  style: const TextStyle(
                      fontSize: 12, color: Color(0xFF666666)))
              : null,
          trailing: onTap != null
              ? const Icon(Icons.chevron_right, color: Color(0xFF666666))
              : null,
          onTap: onTap,
        ),
        if (!isLast) const Divider(height: 1, indent: 56),
      ],
    );
  }

  // ── Printer tile ──────────────────────────────────────────────────────────

  Widget _printerTile(BuildContext context) {
    return AnimatedBuilder(
      animation: PrinterService(),
      builder: (_, __) {
        final svc = context.read<PrinterService>();
        final conn = svc.connectedDevice;
        final saved = svc.savedPrinter;
        final subtitle = conn != null
            ? conn.name
            : saved != null
                ? '${saved.name} (not connected)'
                : 'No printer configured';
        final subtitleColor =
            conn != null ? const Color(0xFF34C759) : const Color(0xFF666666);

        return Column(children: [
          ListTile(
            leading:
                const Icon(Icons.print_outlined, color: Color(0xFF007AFF)),
            title: const Text('Receipt Printer',
                style: TextStyle(fontSize: 16)),
            subtitle: Text(subtitle,
                style: TextStyle(fontSize: 12, color: subtitleColor)),
            trailing: const Icon(Icons.chevron_right,
                color: Color(0xFF666666)),
            onTap: () => _showPrinterModal(context),
          ),
          const Divider(height: 1, indent: 56),
        ]);
      },
    );
  }

  void _showPrinterModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(16))),
      builder: (_) => const _PrinterModal(),
    );
  }

  // ── Build ─────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),
      appBar: AppBar(
        title: const Text('Settings',
            style: TextStyle(fontWeight: FontWeight.w600)),
        backgroundColor: Colors.white,
        elevation: 1,
      ),
      drawer: const AppDrawer(currentPage: AppPage.settings),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // ── Store Settings ────────────────────────────────────────────────
          _sectionCard(
            title: 'Store Settings',
            children: [
              _printerTile(context),
            ],
          ),
          const SizedBox(height: 16),

          // ── About ─────────────────────────────────────────────────────────
          _sectionCard(
            title: 'About',
            children: [
              ListTile(
                leading: const Icon(Icons.info_outline,
                    color: Color(0xFF007AFF)),
                title: const Text('App Information'),
                trailing: const Text('v1.0.0',
                    style: TextStyle(color: Color(0xFF666666))),
              ),
              _settingTile(
                icon: Icons.description_outlined,
                title: 'Terms of Service',
                onTap: () {},
              ),
              _settingTile(
                icon: Icons.shield_outlined,
                title: 'Privacy Policy',
                onTap: () {},
                isLast: true,
              ),
            ],
          ),
        ],
      ),
    );
  }
}

// ── Bluetooth Printer Modal ───────────────────────────────────────────────────

class _PrinterModal extends StatefulWidget {
  const _PrinterModal();
  @override
  State<_PrinterModal> createState() => _PrinterModalState();
}

class _PrinterModalState extends State<_PrinterModal> {
  final PrinterService _service = PrinterService();
  bool _btEnabled = false;
  bool _permPermanentlyDenied = false;
  PaperSize _paperSize = PaperSize.mm58;

  @override
  void initState() {
    super.initState();
    _init();
  }

  Future<void> _init() async {
    final enabled = await _service.isBluetoothEnabled();
    if (mounted) setState(() => _btEnabled = enabled);
    if (enabled) _requestPermissionsAndScan();
  }

  Future<void> _enableBluetooth() async {
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
    await _init();
  }

  // Exact same pattern as the working reference app's scan_screen.dart
  Future<void> _requestPermissionsAndScan() async {
    final granted = await _service.requestPermissions();
    if (!granted) {
      final permanent = await _service.isPermPermanentlyDenied();
      if (mounted) setState(() => _permPermanentlyDenied = permanent);
      if (mounted && !permanent) _showPermDialog();
      return;
    }
    setState(() => _permPermanentlyDenied = false);
    _service.startScan();
  }

  void _showPermDialog() {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Bluetooth Permission Required'),
        content: const Text(
            'Bluetooth permission is needed to scan for printers. '
            'Please allow it when prompted.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Cancel')),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              openAppSettings();
            },
            child: const Text('Open Settings'),
          ),
        ],
      ),
    );
  }

  Future<void> _connect(BluetoothInfo device) async {
    final ok = await _service.connectTo(device);
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(ok
          ? 'Connected to ${device.name}'
          : 'Failed to connect to ${device.name}'),
      backgroundColor: ok ? const Color(0xFF2E7D32) : Colors.red,
      behavior: SnackBarBehavior.floating,
    ));
  }

  Future<void> _testPrint() async {
    final ok = await _service.printTestPage(paperSize: _paperSize);
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(ok ? 'Test page printed!' : _service.statusMessage),
      backgroundColor: ok ? const Color(0xFF2E7D32) : Colors.red,
      behavior: SnackBarBehavior.floating,
    ));
  }

  // Same printer-detection heuristic as reference app
  bool _likelyPrinter(String name) {
    final n = name.toUpperCase();
    return n.contains('POS') ||
        n.contains('PRINT') ||
        n.contains('RPP') ||
        n.contains('MTP') ||
        n.contains('HM-A') ||
        n.contains('THERMAL') ||
        n.contains('RECEIPT') ||
        n.contains('XP-') ||
        n.contains('TSP') ||
        n.contains('TM-') ||
        n.contains('EP') ||
        n.contains('EPPOS');
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _service,
      builder: (_, __) {
        final devices = _service.devices;
        final printers =
            devices.where((d) => _likelyPrinter(d.name)).toList();
        final others =
            devices.where((d) => !_likelyPrinter(d.name)).toList();
        final conn = _service.connectedDevice;

        return Scaffold(
          appBar: AppBar(
            title: const Text('Settings'),
            leading: IconButton(
              icon: const Icon(Icons.arrow_back),
              onPressed: () => Navigator.pop(context),
            ),
          ),
          body: Padding(
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
            child: Column(children: [
              // Handle
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Title + Scan
              Row(children: [
                const Icon(Icons.print_outlined,
                    color: Color(0xFF1565C0), size: 22),
                const SizedBox(width: 10),
                const Text('Receipt Printer',
                    style: TextStyle(
                        fontSize: 20, fontWeight: FontWeight.w700)),
                const Spacer(),
                if (_service.isScanning)
                  const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                        strokeWidth: 2.5, color: Color(0xFF1565C0)),
                  )
                else
                  IconButton(
                    icon: const Icon(Icons.refresh_rounded,
                        color: Color(0xFF1565C0)),
                    tooltip: 'Scan again',
                    onPressed: _requestPermissionsAndScan,
                  ),
              ]),

              // Permanently denied banner
              if (_permPermanentlyDenied) ...[
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFE5E5),
                    borderRadius: BorderRadius.circular(10),
                    border:
                        Border.all(color: const Color(0xFFFFCDD2)),
                  ),
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                            'Bluetooth permission permanently denied.',
                            style: TextStyle(
                                color: Color(0xFFCC0000),
                                fontWeight: FontWeight.w600)),
                        const SizedBox(height: 8),
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            onPressed: () => openAppSettings(),
                            icon: const Icon(Icons.settings, size: 16),
                            label: const Text('Open App Settings'),
                            style: ElevatedButton.styleFrom(
                                backgroundColor:
                                    const Color(0xFFCC0000),
                                foregroundColor: Colors.white),
                          ),
                        ),
                      ]),
                ),
              ],

              // Bluetooth off warning
              if (!_btEnabled) ...[
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF3E0),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFFFFB74D)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(children: const [
                        Icon(Icons.bluetooth_disabled,
                            color: Colors.orange, size: 20),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text('Bluetooth is turned off.',
                              style: TextStyle(
                                  color: Colors.orange,
                                  fontWeight: FontWeight.w600)),
                        ),
                      ]),
                      const SizedBox(height: 8),
                      const Text(
                        'Bluetooth is required to connect to your receipt printer.',
                        style: TextStyle(
                            fontSize: 13, color: Color(0xFF795548)),
                      ),
                      const SizedBox(height: 10),
                      Row(children: [
                        Expanded(
                          child: ElevatedButton.icon(
                            onPressed: _enableBluetooth,
                            icon:
                                const Icon(Icons.bluetooth, size: 16),
                            label:
                                const Text('Enable Bluetooth'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.orange,
                              foregroundColor: Colors.white,
                              shape: RoundedRectangleBorder(
                                  borderRadius:
                                      BorderRadius.circular(8)),
                            ),
                          ),
                        ),
                        const SizedBox(width: 8),
                        OutlinedButton(
                          onPressed: () async {
                            final printer =
                                context.read<PrinterService>();
                            await printer.refreshBluetoothStatus();
                            await printer.autoConnect();
                          },
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(
                                color: Colors.orange),
                            shape: RoundedRectangleBorder(
                                borderRadius:
                                    BorderRadius.circular(8)),
                          ),
                          child: const Text('Refresh',
                              style:
                                  TextStyle(color: Colors.orange)),
                        ),
                      ]),
                    ],
                  ),
                ),
              ],

              // Connected banner
              if (conn != null) ...[
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFE8F5E9),
                    borderRadius: BorderRadius.circular(10),
                    border:
                        Border.all(color: const Color(0xFF81C784)),
                  ),
                  child: Row(children: [
                    const Icon(Icons.check_circle_rounded,
                        color: Color(0xFF2E7D32), size: 20),
                    const SizedBox(width: 8),
                    Expanded(
                        child: Column(
                            crossAxisAlignment:
                                CrossAxisAlignment.start,
                            children: [
                          const Text('Connected',
                              style: TextStyle(
                                  color: Color(0xFF2E7D32),
                                  fontWeight: FontWeight.w700,
                                  fontSize: 12)),
                          Text(conn.name,
                              style: const TextStyle(
                                  fontWeight: FontWeight.w600)),
                          Text(conn.macAdress,
                              style: const TextStyle(
                                  fontSize: 11,
                                  color: Color(0xFF888888))),
                        ])),
                    TextButton(
                      onPressed: _service.disconnectCurrent,
                      child: const Text('Disconnect',
                          style: TextStyle(color: Colors.red)),
                    ),
                  ]),
                ),
              ],

              const SizedBox(height: 10),

              // Paper size + test print row
              Row(children: [
                const Text('Paper:',
                    style: TextStyle(
                        fontWeight: FontWeight.w500, fontSize: 13)),
                const SizedBox(width: 8),
                _paperBtn('58 mm', PaperSize.mm58),
                const SizedBox(width: 6),
                _paperBtn('80 mm', PaperSize.mm80),
                const Spacer(),
                if (conn != null)
                  OutlinedButton.icon(
                    onPressed:
                        _service.isPrinting ? null : _testPrint,
                    icon: _service.isPrinting
                        ? const SizedBox(
                            width: 14,
                            height: 14,
                            child: CircularProgressIndicator(
                                strokeWidth: 2))
                        : const Icon(Icons.receipt_outlined,
                            size: 15),
                    label: const Text('Test Print',
                        style: TextStyle(fontSize: 13)),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 6),
                      foregroundColor: const Color(0xFF1565C0),
                      side: const BorderSide(
                          color: Color(0xFF1565C0)),
                    ),
                  ),
              ]),

              // Status message
              if (_service.statusMessage.isNotEmpty) ...[
                const SizedBox(height: 8),
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(
                      horizontal: 14, vertical: 10),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.grey.shade200),
                  ),
                  child: Row(children: [
                    if (_service.isScanning)
                      const SizedBox(
                          width: 14,
                          height: 14,
                          child: CircularProgressIndicator(
                              strokeWidth: 2,
                              color: Color(0xFF1565C0)))
                    else
                      const Icon(Icons.info_outline,
                          size: 16, color: Colors.grey),
                    const SizedBox(width: 8),
                    Expanded(
                        child: Text(_service.statusMessage,
                            style: const TextStyle(
                                fontSize: 13,
                                color: Colors.black54))),
                  ]),
                ),
              ],

              const SizedBox(height: 10),
              const Divider(height: 1),

              // Section header
              if (devices.isNotEmpty) ...[
                const SizedBox(height: 8),
                if (printers.isNotEmpty)
                  _sectionHeader(Icons.print_rounded, 'ESC/POS Printers',
                      printers.length, const Color(0xFF1565C0)),
              ],

              // Device list
              Expanded(
                child: _service.isScanning
                    ? const Center(
                        child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                          CircularProgressIndicator(
                              color: Color(0xFF1565C0)),
                          SizedBox(height: 12),
                          Text('Scanning…',
                              style: TextStyle(color: Colors.grey)),
                        ]))
                    : devices.isEmpty
                        ? Center(
                            child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                              Icon(Icons.bluetooth_searching_rounded,
                                  size: 56,
                                  color: Colors.grey.shade300),
                              const SizedBox(height: 12),
                              const Text('No paired devices found',
                                  style: TextStyle(
                                      fontWeight: FontWeight.w600,
                                      color: Color(0xFF888888))),
                              const SizedBox(height: 6),
                              const Text(
                                  'Pair your printer in Android Settings → Bluetooth\nthen tap refresh',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                      color: Colors.grey,
                                      fontSize: 12)),
                            ]))
                        : ListView(children: [
                            ...printers.map((d) => _DeviceTile(
                                  device: d,
                                  connectedMac: conn?.macAdress,
                                  onConnect: () => _connect(d),
                                  onDisconnect:
                                      _service.disconnectCurrent,
                                  isPrinter: true,
                                )),
                            if (others.isNotEmpty)
                              _sectionHeader(
                                  Icons.bluetooth_rounded,
                                  'Other Devices',
                                  others.length,
                                  Colors.grey),
                            ...others.map((d) => _DeviceTile(
                                  device: d,
                                  connectedMac: conn?.macAdress,
                                  onConnect: () => _connect(d),
                                  onDisconnect:
                                      _service.disconnectCurrent,
                                  isPrinter: false,
                                )),
                          ]),
              ),
            ]),
          ),
        );
      },
    );
  }

  Widget _paperBtn(String label, PaperSize size) {
    final sel = _paperSize == size;
    return GestureDetector(
      onTap: () => setState(() => _paperSize = size),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: sel ? const Color(0xFF1565C0) : const Color(0xFFEEEEEE),
          borderRadius: BorderRadius.circular(6),
        ),
        child: Text(label,
            style: TextStyle(
                color:
                    sel ? Colors.white : const Color(0xFF333333),
                fontWeight: FontWeight.w500,
                fontSize: 13)),
      ),
    );
  }

  Widget _sectionHeader(
      IconData icon, String label, int count, Color color) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(0, 12, 0, 4),
      child: Row(children: [
        Icon(icon, size: 16, color: color),
        const SizedBox(width: 6),
        Text(label,
            style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: color,
                letterSpacing: 0.4)),
        const SizedBox(width: 6),
        Container(
          padding:
              const EdgeInsets.symmetric(horizontal: 7, vertical: 1),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Text('$count',
              style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                  color: color)),
        ),
      ]),
    );
  }
}

// ── Device tile ───────────────────────────────────────────────────────────────

class _DeviceTile extends StatelessWidget {
  final BluetoothInfo device;
  final String? connectedMac;
  final VoidCallback onConnect;
  final VoidCallback onDisconnect;
  final bool isPrinter;

  const _DeviceTile({
    required this.device,
    required this.connectedMac,
    required this.onConnect,
    required this.onDisconnect,
    required this.isPrinter,
  });

  bool get _isConnected => connectedMac == device.macAdress;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 5),
      decoration: BoxDecoration(
        color: _isConnected ? const Color(0xFFE8F5E9) : Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
            color: _isConnected
                ? const Color(0xFF81C784)
                : Colors.grey.shade200),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 5,
              offset: const Offset(0, 2))
        ],
      ),
      child: ListTile(
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
        leading: Container(
          padding: const EdgeInsets.all(9),
          decoration: BoxDecoration(
            color: isPrinter
                ? const Color(0xFF1565C0).withOpacity(0.1)
                : Colors.grey.withOpacity(0.1),
            shape: BoxShape.circle,
          ),
          child: Icon(
            isPrinter ? Icons.print_rounded : Icons.bluetooth_rounded,
            color: isPrinter ? const Color(0xFF1565C0) : Colors.grey,
            size: 20,
          ),
        ),
        title: Text(device.name,
            style: const TextStyle(
                fontWeight: FontWeight.w600, fontSize: 14)),
        subtitle: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 2),
              Text(device.macAdress,
                  style: TextStyle(
                      fontSize: 11,
                      color: Colors.grey.shade500,
                      fontFamily: 'monospace')),
              const SizedBox(height: 2),
              Row(children: [
                Container(
                    width: 7,
                    height: 7,
                    decoration: BoxDecoration(
                        color: _isConnected
                            ? const Color(0xFF2E7D32)
                            : Colors.grey,
                        shape: BoxShape.circle)),
                const SizedBox(width: 4),
                Text(
                    _isConnected ? 'Connected' : 'Not connected',
                    style: TextStyle(
                        fontSize: 11,
                        color: _isConnected
                            ? const Color(0xFF2E7D32)
                            : Colors.grey,
                        fontWeight: FontWeight.w500)),
              ]),
            ]),
        trailing: _isConnected
            ? OutlinedButton(
                onPressed: onDisconnect,
                style: OutlinedButton.styleFrom(
                  foregroundColor: Colors.red,
                  side: const BorderSide(color: Colors.red),
                  padding: const EdgeInsets.symmetric(
                      horizontal: 10, vertical: 4),
                  minimumSize: Size.zero,
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8)),
                ),
                child: const Text('Disconnect',
                    style: TextStyle(fontSize: 12)),
              )
            : ElevatedButton(
                onPressed: onConnect,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF1565C0),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(
                      horizontal: 10, vertical: 4),
                  minimumSize: Size.zero,
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8)),
                ),
                child: const Text('Connect',
                    style: TextStyle(fontSize: 12)),
              ),
      ),
    );
  }
}
