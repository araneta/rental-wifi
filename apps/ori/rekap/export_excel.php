<?php
include '../config/database.php';
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=rekap_pembayaran.xls");

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'hari';
$query = "";

if ($filter == 'hari') {
    $query = "SELECT p.id, pl.nama AS pelanggan, pl.alamat, p.jumlah, p.metode_pembayaran, p.tanggal_pembayaran 
              FROM pembayaran p 
              JOIN pelanggan pl ON p.pelanggan_id = pl.id 
              WHERE DATE(p.tanggal_pembayaran) = CURDATE()";
} elseif ($filter == 'minggu') {
    $query = "SELECT p.id, pl.nama AS pelanggan, pl.alamat, p.jumlah, p.metode_pembayaran, p.tanggal_pembayaran 
              FROM pembayaran p 
              JOIN pelanggan pl ON p.pelanggan_id = pl.id 
              WHERE YEARWEEK(p.tanggal_pembayaran, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filter == 'bulan') {
    $query = "SELECT p.id, pl.nama AS pelanggan, pl.alamat, p.jumlah, p.metode_pembayaran, p.tanggal_pembayaran 
              FROM pembayaran p 
              JOIN pelanggan pl ON p.pelanggan_id = pl.id 
              WHERE MONTH(p.tanggal_pembayaran) = MONTH(CURDATE()) 
              AND YEAR(p.tanggal_pembayaran) = YEAR(CURDATE())";
} elseif ($filter == 'tahun') {
    $query = "SELECT p.id, pl.nama AS pelanggan, pl.alamat, p.jumlah, p.metode_pembayaran, p.tanggal_pembayaran 
              FROM pembayaran p 
              JOIN pelanggan pl ON p.pelanggan_id = pl.id 
              WHERE YEAR(p.tanggal_pembayaran) = YEAR(CURDATE())";
}

$result = $conn->query($query);
?>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Pelanggan</th>
            <th>Alamat</th>
            <th>Jumlah</th>
            <th>Metode Pembayaran</th>
            <th>Tanggal Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['pelanggan'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pembayaran'] ?></td>
                <td><?= $row['tanggal_pembayaran'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
