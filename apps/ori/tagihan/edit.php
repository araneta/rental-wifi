<?php
include '../config/database.php';
include '../views/header.php';

$id = $_GET['id'];
// $pelanggan_id = $_GET['pelanggan_id'];
$tagihan = $conn->query("SELECT * FROM tagihan WHERE id = $id")->fetch_assoc();
$pelanggan = $conn->query("SELECT * FROM pelanggan");
$petugas = $conn->query("SELECT * FROM users WHERE role = 'petugas'");

$jumlah = $tagihan['jumlah'];
// $metode = $tagihan['metode_pembayaran'];
// $tanggal = $tagihan['tanggal_bayar'];
$pelanggan_id = $tagihan['pelanggan_id'];

if ($_POST) {
    $status = $_POST['status'];
    $tanggal_bayar = $_POST['tanggal_bayar'] ?: NULL;
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Cek apakah data pembayaran sudah ada
            $cek = $conn->query("SELECT * FROM pembayaran WHERE tagihan_id = '$id'");

            if ($cek->num_rows > 0) {
                // Jika data sudah ada
                // echo json_encode(['status' => false, 'message' => 'Pembayaran sudah pernah dilakukan.']);
            } else {
                // Jika belum ada, lakukan insert
                $insert = $conn->query("INSERT INTO pembayaran (tagihan_id, pelanggan_id, jumlah, metode_pembayaran, tanggal_pembayaran, petugas_id)
                                        VALUES ('$id', '$pelanggan_id', '$jumlah', '$metode_pembayaran', '$tanggal_bayar', '6')");
                // if ($insert) {
                //     echo json_encode(['status' => true, 'message' => 'Pembayaran berhasil disimpan.']);
                // } else {
                //     echo json_encode(['status' => false, 'message' => 'Gagal menyimpan pembayaran.']);
                // }
            }

    $sql = "UPDATE tagihan SET status='$status', tanggal_bayar='$tanggal_bayar', metode_pembayaran='$metode_pembayaran' WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger'>Gagal mengupdate tagihan.</div>";
    }
}
?>

<h2>Edit Tagihan</h2>
<form method="POST">
    <select name="status" class="form-control mb-2">
        <option value="belum bayar" <?= $tagihan['status'] == 'belum bayar' ? 'selected' : '' ?>>Belum Bayar</option>
        <option value="lunas" <?= $tagihan['status'] == 'lunas' ? 'selected' : '' ?>>Lunas</option>
    </select>
    <input type="date" name="tanggal_bayar" value="<?= $tagihan['tanggal_bayar'] ?>" class="form-control mb-2">
    <select name="metode_pembayaran" class="form-control mb-2">
        <option value="tunai">Tunai</option>
        <option value="transfer">Transfer</option>
        <option value="QRIS">QRIS</option>
        <option value="Lainnya">Lainnya</option>
    </select>
    <button type="submit" class="btn btn-warning">Update</button>
</form>

<?php include '../views/footer.php'; ?>
