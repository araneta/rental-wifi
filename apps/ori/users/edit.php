<?php
include '../config/database.php';
include '../views/header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();

if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $conn->query("UPDATE users SET name='$name', email='$email', password='$password', role='$role' WHERE id=$id");
    } else {
        $conn->query("UPDATE users SET name='$name', email='$email', role='$role' WHERE id=$id");
    }
    
    header("Location: index.php");
}
?>

<h2>Edit User</h2>
<form method="POST">
    <input type="text" name="name" value="<?= $user['name'] ?>" required class="form-control mb-2">
    <input type="email" name="email" value="<?= $user['email'] ?>" required class="form-control mb-2">
    <input type="password" name="password" placeholder="Password Baru (Opsional)" class="form-control mb-2">
    <select name="role" class="form-control mb-2">
        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="petugas" <?= $user['role'] == 'petugas' ? 'selected' : '' ?>>Petugas</option>
    </select>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php include '../views/footer.php'; ?>
