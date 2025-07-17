<?php
include '../config/database.php';
include '../views/header.php';

// Ambil daftar pelanggan beserta paketnya
$query = "SELECT pelanggan.*, paket.nama AS paket_nama 
          FROM pelanggan 
          LEFT JOIN paket ON pelanggan.paket_id = paket.id";
$result = $conn->query($query);
?>

<h2>Daftar Pelanggan</h2>
<a href="create.php" class="btn btn-primary mb-3">Tambah Pelanggan</a>

<table id="dataTable" class="table table-bordered" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No HP</th>
            <th>Paket</th>
            <th>POP</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['alamat'] ?></td>
            <td><?= $row['no_hp'] ?></td>
            <td><?= $row['paket_nama'] ?: '❌ Tidak Berlangganan' ?></td>
            <td><?= $row['pop'] ?></td>
            <td><?= $row['status'] == 'aktif' ? '✅ Aktif' : '❌ Nonaktif' ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php include '../views/footer.php'; ?>
