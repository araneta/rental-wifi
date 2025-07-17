<?php
include '../config/database.php';
include '../views/header.php';

$petugas = $conn->query("SELECT * FROM users WHERE role = 'petugas'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bulan_tahun = $_POST['bulan_tahun'];
    $petugas_id = $_POST['petugas_id'];
    $status = 'belum bayar';
    $metode_pembayaran = 'Belum Ditentukan';

    // Ambil semua pelanggan aktif beserta paketnya
    $query = "SELECT pelanggan.id, paket.harga 
              FROM pelanggan 
              JOIN paket ON pelanggan.paket_id = paket.id 
              WHERE pelanggan.status = 'aktif'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $tagihan_dibuat = 0;
        while ($row = $result->fetch_assoc()) {
            $pelanggan_id = $row['id'];
            $jumlah_tagihan = $row['harga']; // Harga sesuai paket

            // Cek apakah tagihan sudah ada untuk bulan & tahun yang sama
            $cekTagihan = "SELECT id FROM tagihan WHERE pelanggan_id='$pelanggan_id' AND bulan_tahun='$bulan_tahun'";
            $cekResult = $conn->query($cekTagihan);

            if ($cekResult->num_rows == 0) {
                // Jika belum ada, masukkan tagihan
                $insert = "INSERT INTO tagihan (pelanggan_id, jumlah, status, bulan_tahun, metode_pembayaran, petugas_id, created_at)
                           VALUES ('$pelanggan_id', '$jumlah_tagihan', '$status', '$bulan_tahun', '$metode_pembayaran', '$petugas_id', NOW())";
                if ($conn->query($insert)) {
                    $tagihan_dibuat++;
                }
            }
        }

        if ($tagihan_dibuat > 0) {
            echo "<div class='alert alert-success'>Tagihan untuk bulan $bulan_tahun berhasil ditambahkan sebanyak $tagihan_dibuat pelanggan.</div>";
        } else {
            echo "<div class='alert alert-warning'>Semua pelanggan sudah memiliki tagihan untuk bulan $bulan_tahun.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Tidak ada pelanggan aktif ditemukan.</div>";
    }
}
?>

<h2>Tambah Tagihan Massal</h2>
<h6>Menu ini akan menambahkan tagihan ke semua pelanggan berdasarkan paket yang diambil</h6>
<form method="post">
    <div class="mb-3">
        <label for="bulan_tahun" class="form-label">Pilih Bulan & Tahun</label>
        <input type="month" name="bulan_tahun" class="form-control" required>
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

    <button type="submit" class="btn btn-primary">Tambah Tagihan</button>
</form>

<?php include '../views/footer.php'; ?>
