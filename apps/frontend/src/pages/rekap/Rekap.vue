<template>
  <div class="container mt-4">
    <h3>Rekap Data Pembayaran</h3>

    <div class="mb-3">
      <label class="form-label">Pilih Periode:</label>
      <select v-model="filter" @change="fetchData" class="form-select">
        <option value="hari">Harian</option>
        <option value="minggu">Mingguan</option>
        <option value="bulan">Bulanan</option>
        <option value="tahun">Tahunan</option>
      </select>
    </div>

    <table class="table table-bordered mt-3" v-if="data.length">
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
        <tr v-for="item in data" :key="item.id">
          <td>{{ item.id }}</td>
          <td>{{ item.pelanggan }}</td>
          <td>{{ item.alamat }}</td>
          <td>{{ formatCurrency(item.jumlah) }}</td>
          <td>{{ item.metode_pembayaran }}</td>
          <td>{{ item.tanggal_pembayaran }}</td>
        </tr>
      </tbody>
    </table>

    <div v-else class="text-muted">Tidak ada data.</div>

    <a
      :href="`/export_excel.php?filter=${filter}`"
      class="btn btn-success mt-3"
      target="_blank"
    >
      Export ke Excel
    </a>
  </div>
</template>

<script>
import { apiFetch } from '../../api'

export default {
  name: 'RekapPembayaran',
  data() {
    return {
      filter: 'hari',
      data: []
    }
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      try {
        const res = await apiFetch(`/rekaps?filter=${this.filter}`)
        this.data = res.pembayarans || []
      } catch (err) {
        console.error('Gagal mengambil data rekap', err)
        this.data = []
      }
    },
    formatCurrency(value) {
      const number = Number(value)
      return number.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      })
    }
  }
}
</script>
