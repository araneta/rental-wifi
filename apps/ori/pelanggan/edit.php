<?php
include '../config/database.php';
include '../views/header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM pelanggan WHERE id = $id");
$pelanggan = $result->fetch_assoc();

if ($_POST) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $status = $_POST['status'];
    $paket_id = $_POST['paket_id'];
    $pop = $_POST['pop'];

    $sql = "UPDATE pelanggan SET nama='$nama', alamat='$alamat', no_hp='$no_hp', status='$status', paket_id = '$paket_id', pop = '$pop' WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupdate pelanggan.</div>";
    }
}
?>

<h2>Edit Pelanggan</h2>
<form method="POST">
    <input type="text" name="nama" value="<?= $pelanggan['nama'] ?>" required class="form-control mb-2">
    <textarea name="alamat" required class="form-control mb-2"><?= $pelanggan['alamat'] ?></textarea>
    <input type="text" name="no_hp" value="<?= $pelanggan['no_hp'] ?>" required class="form-control mb-2">
     <select name="paket_id" class="form-control mb-2" required>
        <option value="">-- Pilih Paket --</option>
        <?php
        $paket = $conn->query("SELECT * FROM paket");
        while ($row = $paket->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['nama']} - Rp" . number_format($row['harga'], 0, ',', '.') . "</option>";
        }
        ?>
    </select>
    <select name="status" class="form-control mb-2">
        <option value="aktif" <?= $pelanggan['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
        <option value="nonaktif" <?= $pelanggan['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
    </select>

    <input type="text" name="pop" value="<?= $pelanggan['pop'] ?>" placeholder="POP" class="form-control mb-2">
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
</form>

<?php include '../views/footer.php'; ?>
