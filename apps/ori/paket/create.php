<?php
include '../config/database.php';
include '../views/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $kecepatan = $_POST['kecepatan'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    $query = "INSERT INTO paket (nama, kecepatan, harga, deskripsi) VALUES ('$nama', '$kecepatan', '$harga', '$deskripsi')";
    if ($conn->query($query)) {
        echo "<div class='alert alert-success'>Paket berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan.</div>";
    }
}
?>

<h2>Tambah Paket</h2>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Nama Paket</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Kecepatan</label>
        <input type="text" name="kecepatan" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" name="harga" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<?php include '../views/footer.php'; ?>
