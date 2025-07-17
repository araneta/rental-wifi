<?php
include '../config/database.php';
include '../views/header.php';

// Ambil filter dari URL jika ada, jika tidak set default
$status = isset($_GET['status']) ? $_GET['status'] : '';
$bulan_tahun = isset($_GET['bulan_tahun']) ? $_GET['bulan_tahun'] : '';

// Mulai query
$query = "SELECT tagihan.*, pelanggan.nama AS pelanggan, users.name AS petugas 
          FROM tagihan 
          JOIN pelanggan ON tagihan.pelanggan_id = pelanggan.id 
          JOIN users ON tagihan.petugas_id = users.id 
          WHERE 1=1";  // 1=1 agar memudahkan penambahan kondisi selanjutnya

// Tambahkan filter status
if (!empty($status)) {
    $query .= " AND tagihan.status = ?";
}

// Tambahkan filter bulan/tahun
if (!empty($bulan_tahun)) {
    $query .= " AND tagihan.bulan_tahun = ?";
}

$query .= " ORDER BY created_at DESC";

// Persiapkan statement SQL
$stmt = $conn->prepare($query);

// Bind parameter jika ada filter yang digunakan
if (!empty($status) && !empty($bulan_tahun)) {
    $stmt->bind_param("ss", $status, $bulan_tahun);
} elseif (!empty($status)) {
    $stmt->bind_param("s", $status);
} elseif (!empty($bulan_tahun)) {
    $stmt->bind_param("s", $bulan_tahun);
}

// Eksekusi statement
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Daftar Tagihan</h2>

<!-- Form Filter -->
<form method="GET" action="" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <label>Status Pembayaran:</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">-- Semua --</option>
                <option value="belum bayar" <?= $status == 'belum bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                <!-- <option value="dibayar" <?=($status == '' ) ? 'selected' : '' ?>>Sudah Bayar</option> -->
            </select>
        </div>

        <div class="col-md-3">
            <label>Bulan/Tahun:</label>
            <input type="month" name="bulan_tahun" class="form-control" value="<?= $bulan_tahun ?>" onchange="this.form.submit()">
             
        </div>

    </div>
</form>

<!-- Tombol Tambah -->
<a href="create.php" class="btn btn-primary mb-3">Tambah Manual</a>
<a href="create_masal.php" class="btn btn-warning mb-3">Tambah Keseluruhan</a>
<a href="export_excel.php?status=<?= urlencode($status) ?>&bulan_tahun=<?= urlencode($bulan_tahun) ?>" class="btn btn-success mb-3">Export Excel</a>
<div class="table-responsive">
    <table id="dataTable" class="table table-bordered" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Pelanggan</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Bulan/Tahun</th>
                <th>Petugas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                while ($row = $result->fetch_assoc()) {
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['pelanggan']) ?></td>
                <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['status'] == 'belum bayar' ? '❌ Belum Bayar' : '✅ Lunas' ?></td>
                <td><?= htmlspecialchars($row['bulan_tahun']) ?></td>
                <td><?= htmlspecialchars($row['petugas']) ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>&pelanggan_id=<?= $row['pelanggan_id'] ?>" class="btn btn-warning">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../views/footer.php'; ?>
