<?php
include '../config/database.php';
include '../views/header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM paket WHERE id = $id");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $kecepatan = $_POST['kecepatan'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    $query = "UPDATE paket SET nama='$nama', kecepatan='$kecepatan', harga='$harga', deskripsi='$deskripsi' WHERE id=$id";
    if ($conn->query($query)) {
        echo "<div class='alert alert-success'>Paket berhasil diperbarui!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan.</div>";
    }
}
?>

<h2>Edit Paket</h2>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Nama Paket</label>
        <input type="text" name="nama" class="form-control" value="<?= $row['nama'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Kecepatan</label>
        <input type="text" name="kecepatan" class="form-control" value="<?= $row['kecepatan'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control"><?= $row['deskripsi'] ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<?php include '../views/footer.php'; ?>
