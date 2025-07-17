<?php
include '../config/database.php';
include '../views/header.php';

// Ambil daftar pelanggan yang aktif dan memiliki paket
$pelanggan = $conn->query("SELECT pelanggan.*, paket.nama AS paket_nama, paket.harga 
                           FROM pelanggan 
                           JOIN paket ON pelanggan.paket_id = paket.id 
                           WHERE pelanggan.status = 'aktif'");

$petugas = $conn->query("SELECT * FROM users WHERE role = 'petugas'");

if ($_POST) {
    $pelanggan_id = $_POST['pelanggan_id'];
    $jumlah = $_POST['jumlah'];
    $status = $_POST['status'];
    $tanggal_bayar = $_POST['tanggal_bayar'] ?: NULL;
    $bulan_tahun = $_POST['bulan_tahun'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $petugas_id = $_POST['petugas_id'];

    // 🔹 Cek apakah tagihan sudah ada untuk pelanggan dan bulan/tahun yang sama
    $cek = $conn->prepare("SELECT id FROM tagihan WHERE pelanggan_id = ? AND bulan_tahun = ?");
    $cek->bind_param("is", $pelanggan_id, $bulan_tahun);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<div class='alert alert-danger'>Tagihan untuk pelanggan ini pada bulan tersebut sudah ada!</div>";
    } else {
        // 🔹 Jika belum ada, lakukan INSERT
        $sql = $conn->prepare("INSERT INTO tagihan (pelanggan_id, jumlah, status, tanggal_bayar, bulan_tahun, metode_pembayaran, petugas_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("iissssi", $pelanggan_id, $jumlah, $status, $tanggal_bayar, $bulan_tahun, $metode_pembayaran, $petugas_id);

        if ($sql->execute()) {
            header("Location: index.php");
        } else {
            echo "<div class='alert alert-danger'>Gagal menambahkan tagihan.</div>";
        }
    }
}
?>

<h2>Tambah Tagihan</h2>
<form method="POST">
    <div class="mb-3">
        <label for="pelanggan_id" class="form-label">Pilih Pelanggan</label>
        <select name="pelanggan_id" id="pelanggan_id" required class="form-control" onchange="updateJumlah()">
            <option value="">-- Pilih Pelanggan --</option>
            <?php while ($row = $pelanggan->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>" data-harga="<?= $row['harga'] ?>">
                    <?= $row['nama'] ?> (Paket: <?= $row['paket_nama'] ?> - Rp<?= number_format($row['harga'], 0, ',', '.') ?>)
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah Tagihan (Rp)</label>
        <input type="number" id="jumlah" name="jumlah" placeholder="Jumlah Tagihan" required class="form-control" readonly>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="belum bayar">Belum Bayar</option>
            <option value="lunas">Lunas</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
        <input type="date" name="tanggal_bayar" class="form-control">
    </div>

    <div class="mb-3">
        <label for="bulan_tahun" class="form-label">Bulan/Tahun</label>
        <input type="month" name="bulan_tahun" required class="form-control">
    </div>

    <div class="mb-3">
        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
        <select name="metode_pembayaran" class="form-control">
            <option value=""></option>
            <option value="tunai">Tunai</option>
            <option value="transfer">Transfer</option>
            <option value="QRIS">QRIS</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="petugas_id" class="form-label">Pilih Petugas</label>
        <select name="petugas_id" required class="form-control">
            <option value="">-- Pilih Petugas --</option>
            <?php while ($row = $petugas->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
            <?php } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<script>
function updateJumlah() {
    var pelanggan = document.getElementById("pelanggan_id");
    var harga = pelanggan.options[pelanggan.selectedIndex].getAttribute("data-harga");
    document.getElementById("jumlah").value = harga || "";
}
</script>

<?php include '../views/footer.php'; ?>
