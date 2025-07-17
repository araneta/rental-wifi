<?php
include '../config/database.php';

if (isset($_POST['pelanggan_id'])) {
    $pelanggan_id = $_POST['pelanggan_id'];

    // Ambil tagihan berdasarkan pelanggan yang statusnya "belum bayar"
    $query = "SELECT id, bulan_tahun, jumlah FROM tagihan 
              WHERE pelanggan_id = '$pelanggan_id' AND status IN ('belum bayar','')";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo '<option value="">-- Pilih Tagihan --</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['id'].'" data-jumlah="'.$row['jumlah'].'">['.$row['bulan_tahun'].'] - Rp'.number_format($row['jumlah'], 0, ',', '.').'</option>';
        }
    } else {
        echo '<option value="">Tidak ada tagihan yang belum dibayar</option>';
    }
}
?>
