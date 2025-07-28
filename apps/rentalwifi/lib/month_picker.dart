import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class MonthPickerField extends StatefulWidget {
  final Function(DateTime)? onChanged;

  const MonthPickerField({super.key, this.onChanged});

  @override
  State<MonthPickerField> createState() => _MonthPickerFieldState();
}

class _MonthPickerFieldState extends State<MonthPickerField> {
  DateTime? selectedDate;
  final TextEditingController _controller = TextEditingController();

  Future<void> _selectMonth(BuildContext context) async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: selectedDate ?? now,
      firstDate: DateTime(2000),
      lastDate: DateTime(2100),
      initialDatePickerMode: DatePickerMode.year,
      builder: (context, child) {
        return Theme(data: Theme.of(context), child: child!);
      },
    );

    if (picked != null) {
      setState(() {
        selectedDate = picked;
        _controller.text = DateFormat('yyyy-MM').format(picked);
      });
      if (widget.onChanged != null) widget.onChanged!(picked);
    }
  }

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: _controller,
      readOnly: true,
      onTap: () => _selectMonth(context),
      decoration: const InputDecoration(
        labelText: 'Bulan/Tahun',
        border: OutlineInputBorder(),
        suffixIcon: Icon(Icons.calendar_today),
      ),
    );
  }
}
