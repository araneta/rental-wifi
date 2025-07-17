<?php
include '../config/database.php';

header('Content-Type: text/plain');

// Ambil data dari GET
$id         = isset($_GET['id']) ? $_GET['id'] : null;
$metode     = isset($_GET['metode']) ? $_GET['metode'] : null;
$tanggal    = isset($_GET['tanggal']) ? $_GET['tanggal'] : null;
$petugas_id = isset($_GET['petugas_id']) ? $_GET['petugas_id'] : null;

// Validasi input
if (!$id || !$metode || !$tanggal || !$petugas_id) {
    echo "error: data tidak lengkap";
    exit;
}

// Cek apakah petugas_id ada di tabel users
$cekPetugas = $conn->query("SELECT id FROM users WHERE id='$petugas_id'");
if ($cekPetugas->num_rows == 0) {
    echo "error: petugas_id tidak valid";
    exit;
}

// Cek apakah pembayaran sudah dilakukan
$checkPayment = $conn->query("SELECT id FROM pembayaran WHERE tagihan_id='$id'");
if ($checkPayment->num_rows > 0) {
    $conn->query("UPDATE tagihan SET status='dibayar', metode_pembayaran='$metode', tanggal_bayar='$tanggal' WHERE id='$id'");
    echo "error: pembayaran sudah dilakukan untuk tagihan ini";
    exit;
}

// Ambil data tagihan
$query = $conn->query("SELECT pelanggan_id, jumlah FROM tagihan WHERE id='$id'");
if (!$query || $query->num_rows == 0) {
    echo "error: tagihan tidak ditemukan";
    exit;
}
$row = $query->fetch_assoc();
$pelanggan_id = $row['pelanggan_id'];
$jumlah = $row['jumlah'];

// Proses update & insert
$conn->begin_transaction();

try {
    $conn->query("UPDATE tagihan SET status='dibayar', metode_pembayaran='$metode', tanggal_bayar='$tanggal' WHERE id='$id'");

    $conn->query("INSERT INTO pembayaran (tagihan_id, pelanggan_id, jumlah, metode_pembayaran, tanggal_pembayaran, petugas_id)
                  VALUES ('$id', '$pelanggan_id', '$jumlah', '$metode', '$tanggal', '$petugas_id')");

    $conn->commit();
    echo "success";
} catch (Exception $e) {
    $conn->rollback();
    echo "error: " . $e->getMessage();
}
?>
