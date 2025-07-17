<?php
include '../config/database.php';
include '../views/header.php';

$result = $conn->query("SELECT * FROM users");
?>

<h2>Manajemen Pengguna</h2>
<a href="create.php" class="btn btn-primary mb-3">Tambah User</a>
<table id="dataTable" class="table table-bordered" width="100%">
    <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a>
                <!-- <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin?')">Hapus</a> -->
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php include '../views/footer.php'; ?>
