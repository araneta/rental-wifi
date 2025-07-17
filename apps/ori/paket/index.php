<?php
include '../config/database.php';
include '../views/header.php';

$result = $conn->query("SELECT * FROM paket");
?>

<h2>Daftar Paket Internet</h2>
<a href="create.php" class="btn btn-primary mb-3">Tambah Paket</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Paket</th>
            <th>Kecepatan</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['kecepatan'] ?></td>
            <td>Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td><?= $row['deskripsi'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Hapus paket ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php include '../views/footer.php'; ?>
