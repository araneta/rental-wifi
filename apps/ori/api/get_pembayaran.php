
<?php
include '../config/database.php';

$result = $conn->query("SELECT p.id, pl.nama AS pelanggan, p.jumlah, p.metode_pembayaran AS metode, p.tanggal_pembayaran AS tanggal 
          FROM pembayaran p 
          JOIN pelanggan pl ON p.pelanggan_id = pl.id 
          ORDER BY p.tanggal_pembayaran DESC");
$pelanggan = [];

while ($row = $result->fetch_assoc()) {
    $pelanggan[] = $row;
}

echo json_encode($pelanggan);
?>
