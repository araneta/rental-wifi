<template>
  <div>
    <h2>Daftar Pembayaran</h2>
    <button class="btn btn-primary mb-3">Tambah Pembayaran</button>
    <div class="table-responsive">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Petugas</th>
            <th>Pelanggan</th>
            <th>Tagihan Bulan</th>
            <th>Jumlah</th>
            <th>Metode</th>
            <th>Tanggal Pembayaran</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pembayaran in pembayarans" :key="pembayaran.id">
            <td>{{ pembayaran.id }}</td>
            <td>{{ pembayaran.nama_petugas }}</td>
            <td>{{ pembayaran.nama_pelanggan }}</td>
            <td>{{ pembayaran.bulan_tahun }}</td>
            <td>Rp{{ pembayaran.jumlah.toLocaleString('id-ID') }}</td>
            <td>{{ pembayaran.metode_pembayaran }}</td>
            <td>{{ pembayaran.tanggal_pembayaran }}</td>
            <td>
              <button class="btn btn-warning">Edit</button>
              <button class="btn btn-danger">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pembayaran',
  data() {
    return {
      pembayarans: [
        { id: 1, nama_petugas: 'Aldo', nama_pelanggan: 'Andi', bulan_tahun: '2024-06', jumlah: 150000, metode_pembayaran: 'transfer', tanggal_pembayaran: '2024-06-01' },
        { id: 2, nama_petugas: 'Budi', nama_pelanggan: 'Budi', bulan_tahun: '2024-05', jumlah: 150000, metode_pembayaran: 'cash', tanggal_pembayaran: '2024-05-01' }
      ]
    }
  }
}
</script>

<style scoped>
</style> 