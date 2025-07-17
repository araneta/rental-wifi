<?php
include '../config/database.php';
include '../views/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $petugas_id = 6; // Sesuaikan dengan login petugas
    $pelanggan_id = $_POST['pelanggan_id'];
    $tagihan_id = $_POST['tagihan_id'];
    $jumlah = $_POST['jumlah'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $tanggal_pembayaran = $_POST['tanggal_pembayaran'];

    // Simpan pembayaran ke database
    $query = "INSERT INTO pembayaran (petugas_id, pelanggan_id, tagihan_id, jumlah, metode_pembayaran, tanggal_pembayaran) 
              VALUES ('$petugas_id', '$pelanggan_id', '$tagihan_id', '$jumlah', '$metode_pembayaran', '$tanggal_pembayaran')";
    
    if ($conn->query($query)) {
        // Ubah status tagihan menjadi "Lunas"
        $conn->query("UPDATE tagihan SET status = 'lunas' WHERE id = '$tagihan_id'");
        echo "<div class='alert alert-success'>Pembayaran berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan.</div>";
    }
}

// Ambil daftar pelanggan
$pelanggan = $conn->query("SELECT * FROM pelanggan");
?>

<h2>Tambah Pembayaran</h2>
<form method="post">
    <div class="mb-3">
        <label for="pelanggan_id" class="form-label">Pilih Pelanggan</label>
        <select name="pelanggan_id" id="pelanggan_id" class="form-control" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php while ($row = $pelanggan->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama'] ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="tagihan_id" class="form-label">Pilih Tagihan</label>
        <select name="tagihan_id" id="tagihan_id" class="form-control" required>
            <option value="">-- Pilih Pelanggan Terlebih Dahulu --</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="jumlah" class="form-label">Jumlah Pembayaran (Rp)</label>
        <input type="number" name="jumlah" id="jumlah" class="form-control" required readonly>
    </div>

    <div class="mb-3">
        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
        <select name="metode_pembayaran" class="form-control" required>
            <option value="tunai">Tunai</option>
            <option value="transfer">Transfer Bank</option>
        <option value="QRIS">QRIS</option>
        <option value="Lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="tanggal_pembayaran" class="form-label">Tanggal Pembayaran</label>
        <input type="date" name="tanggal_pembayaran" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#pelanggan_id').change(function() {
            var pelanggan_id = $(this).val();
            if (pelanggan_id !== '') {
                $.ajax({
                    url: 'get_tagihan.php',
                    type: 'POST',
                    data: { pelanggan_id: pelanggan_id },
                    success: function(response) {
                        $('#tagihan_id').html(response);
                        $('#jumlah').val(''); // Reset jumlah saat pelanggan berubah
                    }
                });
            } else {
                $('#tagihan_id').html('<option value="">-- Pilih Pelanggan Terlebih Dahulu --</option>');
                $('#jumlah').val('');
            }
        });

        $('#tagihan_id').change(function() {
            var jumlah = $(this).find(':selected').data('jumlah');
            $('#jumlah').val(jumlah);
        });
    });
</script>

<?php include '../views/footer.php'; ?>
