<?php
include '../config/database.php';

// Ambil filter dari URL jika ada
$status = isset($_GET['status']) ? $_GET['status'] : '';
$bulan_tahun = isset($_GET['bulan_tahun']) ? $_GET['bulan_tahun'] : '';

// Mulai query
$query = "SELECT tagihan.*, pelanggan.nama AS pelanggan, users.name AS petugas 
          FROM tagihan 
          JOIN pelanggan ON tagihan.pelanggan_id = pelanggan.id 
          JOIN users ON tagihan.petugas_id = users.id 
          WHERE 1=1";

// Tambahkan filter status
$params = [];
$types = "";

if (!empty($status)) {
    $query .= " AND tagihan.status = ?";
    $params[] = $status;
    $types .= "s";
}

// Tambahkan filter bulan/tahun
if (!empty($bulan_tahun)) {
    $query .= " AND tagihan.bulan_tahun = ?";
    $params[] = $bulan_tahun;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

// Persiapkan statement SQL
$stmt = $conn->prepare($query);

// Bind parameter jika ada filter yang digunakan
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Eksekusi statement
$stmt->execute();
$result = $stmt->get_result();

// Set header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Daftar_Tagihan.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Buat tabel
echo "No\tPelanggan\tJumlah\tStatus\tBulan/Tahun\tPetugas\n";

$no = 1;
while ($row = $result->fetch_assoc()) {
    $status = ($row['status'] == 'belum bayar') ? 'Belum Bayar' : 'Lunas';
    echo "$no\t{$row['pelanggan']}\t{$row['jumlah']}\t$status\t{$row['bulan_tahun']}\t{$row['petugas']}\n";
    $no++;
}
?>
