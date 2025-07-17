<?php
include 'config/database.php';
include 'views/header.php';

// Ambil jumlah data dari database
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_pelanggan = $conn->query("SELECT COUNT(*) AS total FROM pelanggan")->fetch_assoc()['total'];
$total_tagihan = $conn->query("SELECT COUNT(*) AS total FROM tagihan WHERE status='belum bayar'")->fetch_assoc()['total'];
$total_pembayaran = $conn->query("SELECT COUNT(*) AS total FROM pembayaran")->fetch_assoc()['total'];
?>

<h2 class="mb-4">Dashboard Tagihan Internet</h2>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Pengguna</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_users ?></h4>
                <a href="users/index.php" class="btn btn-light btn-sm">Kelola</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Pelanggan</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_pelanggan ?></h4>
                <a href="pelanggan/index.php" class="btn btn-light btn-sm">Kelola</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Tagihan Belum Bayar</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_tagihan ?></h4>
                <a href="tagihan/index.php" class="btn btn-light btn-sm">Kelola</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Pembayaran</div>
            <div class="card-body">
                <h4 class="card-title"><?= $total_pembayaran ?></h4>
                <a href="pembayaran/index.php" class="btn btn-light btn-sm">Kelola</a>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
