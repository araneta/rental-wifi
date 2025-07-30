<template>
  <div>
    <h2>Daftar Pembayaran</h2>
    <router-link to="/pembayarans/create" class="btn btn-primary mb-3">Tambah Pembayaran</router-link>

    <input
      type="text"
      class="form-control mb-3"
      placeholder="Cari berdasarkan nama pelanggan, petugas, bulan/tahun, metode, tanggal, atau jumlah"
      v-model="searchQuery"
    />

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
          <tr v-for="row in filteredPembayaranList" :key="row.id">
            <td>{{ row.id }}</td>
            <td>{{ row.nama_petugas }}</td>
            <td>{{ row.nama_pelanggan }}</td>
            <td>{{ row.bulan_tahun }}</td>
            <td>Rp{{ formatNumber(row.jumlah) }}</td>
            <td>{{ capitalize(row.metode_pembayaran) }}</td>
            <td>{{ row.tanggal_pembayaran }}</td>
            <td>
              <router-link :to="`/pembayarans/${row.id}`" class="btn btn-warning">Edit</router-link>&nbsp;
              <button class="btn btn-danger" @click="confirmDelete(row.id)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script>
import { apiFetch } from '../../api'
import { useToast } from 'vue-toastification'
const toast = useToast()

export default {
  name: 'Pembayaran',
  data() {
    return {
      pembayaranList: [],
      searchQuery: ''
    }
  },
  computed: {
    filteredPembayaranList() {
      const q = this.searchQuery.toLowerCase()
      return this.pembayaranList.filter(row => {
        return (
          (row.nama_pelanggan || '').toLowerCase().includes(q) ||
          (row.nama_petugas || '').toLowerCase().includes(q) ||
          (row.bulan_tahun || '').toLowerCase().includes(q) ||
          (row.metode_pembayaran || '').toLowerCase().includes(q) ||
          (row.tanggal_pembayaran || '').toLowerCase().includes(q) ||
          String(row.jumlah || '').includes(q)
        )
      })
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/pembayarans')
      this.pembayaranList = data.pembayarans;
    } catch (err) {
      console.error(err)
      toast.error('Gagal mengambil data pembayaran.')
    }
  },
  methods: {
    formatNumber(value) {
      return Number(value).toLocaleString('id-ID')
    },
    capitalize(text) {
      return text.charAt(0).toUpperCase() + text.slice(1)
    },
    async confirmDelete(id) {
      if (confirm('Yakin ingin menghapus?')) {
        try {
          await apiFetch(`/pembayarans/${id}`, { method: 'DELETE' })
          this.pembayaranList = this.pembayaranList.filter(row => row.id !== id)
          toast.success('Data berhasil dihapus.')
        } catch (err) {
          console.error(err)
          toast.error('Gagal menghapus data.')
        }
      }
    }
  }
}
</script>
