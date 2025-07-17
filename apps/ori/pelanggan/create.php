<?php
include '../config/database.php';
include '../views/header.php';

if ($_POST) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $status = $_POST['status'];
    $paket_id = $_POST['paket_id'];
    $pop = $_POST['pop'];

    $sql = "INSERT INTO pelanggan (nama, alamat, no_hp, status,paket_id,pop) VALUES ('$nama', '$alamat', '$no_hp', '$status','$paket_id','$pop')";
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger'>Gagal menambahkan pelanggan.</div>";
    }
}
?>

<h2>Tambah Pelanggan</h2>
<form method="POST">
    <input type="text" name="nama" placeholder="Nama" required class="form-control mb-2">
    <textarea name="alamat" placeholder="Alamat" required class="form-control mb-2"></textarea>
    <input type="text" name="no_hp" placeholder="No HP" required class="form-control mb-2">
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
        <option value="aktif">Aktif</option>
        <option value="nonaktif">Nonaktif</option>
    </select>

    <input type="text" name="pop" placeholder="POP" class="form-control mb-2">
    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<?php include '../views/footer.php'; ?>
