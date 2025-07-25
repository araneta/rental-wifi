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
        <option value="range">Rentang Tanggal</option>
      </select>
    </div>

    <div v-if="filter === 'range'" class="row mb-3">
      <div class="col-md-5">
        <label for="start" class="form-label">Tanggal Mulai:</label>
        <input type="date" v-model="startDate" class="form-control" @change="fetchData">
      </div>
      <div class="col-md-5">
        <label for="end" class="form-label">Tanggal Akhir:</label>
        <input type="date" v-model="endDate" class="form-control" @change="fetchData">
      </div>
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

    <button class="btn btn-success mb-3" @click="btndownloadExcel">Export Excel</button>
  </div>
</template>

<script>
import { apiFetch, downloadExcel } from '../../api'

export default {
  name: 'RekapPembayaran',
  data() {
    return {
      filter: 'hari',
      data: [],
      startDate: '',
      endDate: ''
    }
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      try {
        let url = `/rekaps?filter=${this.filter}`

        if (this.filter === 'range') {
          if (this.startDate && this.endDate) {
            url += `&start=${this.startDate}&end=${this.endDate}`
          } else {
            this.data = []
            return
          }
        }

        const res = await apiFetch(url)
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
    },
    async btndownloadExcel() {
		if(!this.startDate){
			alert('Pilih tanggal mulai');
			return;
		}
		if(!this.endDate){
			alert('Pilih tanggal akhir');
			return;
		}
      let url = `/rekaps/excel?filter=${encodeURIComponent(this.filter)}`
      if (this.filter === 'range' && this.startDate && this.endDate) {
        url += `&start=${this.startDate}&end=${this.endDate}`
      }
      downloadExcel(url, 'rekap_pembayaran')
    }
  }
}
</script>
