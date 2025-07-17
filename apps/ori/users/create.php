<?php
include '../config/database.php';
include '../views/header.php';

if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
    header("Location: index.php");
}
?>

<h2>Tambah User</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Nama" required class="form-control mb-2">
    <input type="email" name="email" placeholder="Email" required class="form-control mb-2">
    <input type="password" name="password" placeholder="Password" required class="form-control mb-2">
    <select name="role" class="form-control mb-2">
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
    </select>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<?php include '../views/footer.php'; ?>
