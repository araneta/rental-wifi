<?php
include '../config/database.php';
include '../views/header.php';

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

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rekap Data</title>
</head>
<body>
    <div class="container mt-4">
        <h3>Rekap Data Pembayaran</h3>
        <form method="GET" action="index.php">
            <label>Pilih Periode:</label>
            <select name="filter" class="form-control" onchange="this.form.submit()">
                <option value="hari" <?= $filter == 'hari' ? 'selected' : '' ?>>Harian</option>
                <option value="minggu" <?= $filter == 'minggu' ? 'selected' : '' ?>>Mingguan</option>
                <option value="bulan" <?= $filter == 'bulan' ? 'selected' : '' ?>>Bulanan</option>
                <option value="tahun" <?= $filter == 'tahun' ? 'selected' : '' ?>>Tahunan</option>
            </select>
        </form>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
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

        <a href="export_excel.php?filter=<?= $filter ?>" class="btn btn-success">Export ke Excel</a>
    </div>
</body>
</html>
