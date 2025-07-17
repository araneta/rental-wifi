<?php
include '../config/database.php';
include '../views/header.php';

$query = "SELECT pembayaran.*, pelanggan.nama AS nama_pelanggan, users.name AS nama_petugas, tagihan.bulan_tahun 
          FROM pembayaran 
          JOIN pelanggan ON pembayaran.pelanggan_id = pelanggan.id 
          JOIN users ON pembayaran.petugas_id = users.id 
          JOIN tagihan ON pembayaran.tagihan_id = tagihan.id
          ORDER BY pembayaran.tanggal_pembayaran ASC";
$result = $conn->query($query);
?>

<h2>Daftar Pembayaran</h2>
<a href="create.php" class="btn btn-primary mb-3">Tambah Pembayaran</a>

<div class="table-responsive">
    <table id="dataTable" class="table table-bordered" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Petugas</th>
                <th>Pelanggan</th>
                <th>Tagihan Bulan</th>
                <th>Jumlah</th>
                <th>Metode</th>
                <th>Tanggal Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama_petugas'] ?></td>
                <td><?= $row['nama_pelanggan'] ?></td>
                <td><?= $row['bulan_tahun'] ?></td>
                <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= ucfirst($row['metode_pembayaran']) ?></td>
                <td><?= $row['tanggal_pembayaran'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


<?php include '../views/footer.php'; ?>
