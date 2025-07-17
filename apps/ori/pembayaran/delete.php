<?php
include '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil tagihan_id sebelum dihapus
    $query = "SELECT tagihan_id FROM pembayaran WHERE id = '$id'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $tagihan_id = $row['tagihan_id'];

    // Hapus pembayaran
    $delete = "DELETE FROM pembayaran WHERE id = '$id'";
    if ($conn->query($delete)) {
        // Set status tagihan menjadi "Belum Bayar" jika pembayaran dihapus
        $conn->query("UPDATE tagihan SET status = 'belum bayar' WHERE id = '$tagihan_id'");

        echo "<script>alert('Pembayaran berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan!'); window.location='index.php';</script>";
    }
} else {
    echo "<script>window.location='index.php';</script>";
}
?>
