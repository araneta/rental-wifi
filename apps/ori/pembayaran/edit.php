<?php
include '../config/database.php';
include '../views/header.php';

$id = $_GET['id'];
$query = "SELECT * FROM pembayaran WHERE id = $id";
$result = $conn->query($query);
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jumlah = $_POST['jumlah'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $tanggal_pembayaran = $_POST['tanggal_pembayaran'];

    $query = "UPDATE pembayaran SET jumlah='$jumlah', metode_pembayaran='$metode_pembayaran', tanggal_pembayaran='$tanggal_pembayaran' WHERE id='$id'";
    if ($conn->query($query)) {
        echo "<div class='alert alert-success'>Pembayaran berhasil diperbarui!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan.</div>";
    }
}

?>

<h2>Edit Pembayaran</h2>
<form method="post">
    <label>Jumlah</label>
    <input type="number" name="jumlah" value="<?= $data['jumlah'] ?>" class="form-control">
    <label>Metode</label>
    <select name="metode_pembayaran" class="form-control mb-2">
        <option value="tunai">Tunai</option>
        <option value="transfer">Transfer</option>
        <option value="QRIS">QRIS</option>
        <option value="Lainnya">Lainnya</option>
    </select>
    <label>Tanggal</label>
    <input type="date" name="tanggal_pembayaran" value="<?= $data['tanggal_pembayaran'] ?>" class="form-control">
    <button type="submit" class="btn btn-primary">Update</button>
</form>

<?php include '../views/footer.php'; ?>
