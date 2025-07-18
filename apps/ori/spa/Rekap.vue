<template>
  <div class="container mt-4">
    <h3>Rekap Data Pembayaran</h3>
    <form @submit.prevent>
      <label>Pilih Periode:</label>
      <select v-model="filter" class="form-control" style="max-width:200px;display:inline-block;" >
        <option value="hari">Harian</option>
        <option value="minggu">Mingguan</option>
        <option value="bulan">Bulanan</option>
        <option value="tahun">Tahunan</option>
      </select>
    </form>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Pelanggan</th>
          <th>Alamat</th>
          <th>Jumlah</th>
          <th>Metode</th>
          <th>Tanggal Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="rekap in filteredRekap" :key="rekap.id">
          <td>{{ rekap.id }}</td>
          <td>{{ rekap.pelanggan }}</td>
          <td>{{ rekap.alamat }}</td>
          <td>{{ rekap.jumlah.toLocaleString('id-ID') }}</td>
          <td>{{ rekap.metode_pembayaran }}</td>
          <td>{{ rekap.tanggal_pembayaran }}</td>
        </tr>
      </tbody>
    </table>
    <button class="btn btn-success">Export ke Excel</button>
  </div>
</template>

<script>
export default {
  name: 'Rekap',
  data() {
    return {
      filter: 'hari',
      rekaps: [
        { id: 1, pelanggan: 'Andi', alamat: 'Jl. Mawar', jumlah: 150000, metode_pembayaran: 'transfer', tanggal_pembayaran: '2024-06-01', periode: 'hari' },
        { id: 2, pelanggan: 'Budi', alamat: 'Jl. Melati', jumlah: 150000, metode_pembayaran: 'cash', tanggal_pembayaran: '2024-05-01', periode: 'bulan' }
      ]
    }
  },
  computed: {
    filteredRekap() {
      return this.rekaps.filter(r => r.periode === this.filter);
    }
  }
}
</script>

<style scoped>
</style> 