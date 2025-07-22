<template>
  <div>
    <h2>Daftar Tagihan</h2>
    <!-- Form Filter -->
    <form class="mb-3" @submit.prevent>
      <div class="row">
        <div class="col-md-3">
          <label>Status Pembayaran:</label>
          <select v-model="filterStatus" class="form-control">
            <option value="">-- Semua --</option>
            <option value="belum bayar">Belum Bayar</option>
            <option value="dibayar">Lunas</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>Bulan/Tahun:</label>
          <input type="month" v-model="filterBulanTahun" class="form-control" />
        </div>
      </div>
    </form>
    <!-- Tombol Tambah -->
    <a class="btn btn-primary mb-3" href="/tagihans/create">Tambah Manual</a>&nbsp;
    <a class="btn btn-warning mb-3" href="/tagihans/create-mass">Tambah Keseluruhan</a>&nbsp;
    <button class="btn btn-success mb-3">Export Excel</button>&nbsp;
    <div class="table-responsive">
      <table class="table table-bordered" width="100%">
        <thead>
          <tr>
            <th>No</th>
            <th>Pelanggan</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Bulan/Tahun</th>
            <th>Petugas</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(tagihan, idx) in filteredTagihan" :key="tagihan.id">
            <td>{{ idx + 1 }}</td>
            <td>{{ tagihan.pelanggan }}</td>
            <td>Rp{{ tagihan.jumlah.toLocaleString('id-ID') }}</td>
            <td>
              <span v-if="tagihan.status === 'belum bayar'">❌ Belum Bayar</span>
              <span v-else>✅ Lunas</span>
            </td>
            <td>{{ tagihan.bulan_tahun }}</td>
            <td>{{ tagihan.petugas }}</td>
            <td>
              <button class="btn btn-warning">Edit</button>&nbsp;
              <button class="btn btn-danger">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { apiFetch } from '../../api' // Your custom API handler
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  name: 'Tagihan',
  data() {
    return {
      filterStatus: '',
      filterBulanTahun: '',
      tagihans: [
        
      ]
    }
  },
  computed: {
    filteredTagihan() {
      return this.tagihans.filter(t => {
        const statusMatch = !this.filterStatus || t.status === this.filterStatus;
        const bulanTahunMatch = !this.filterBulanTahun || t.bulan_tahun === this.filterBulanTahun;
        return statusMatch && bulanTahunMatch;
      });
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/tagihans?status='+this.filterStatus, { method: 'GET' });
      this.tagihans = data.tagihans;
    } catch (e) {
      console.error('Fetch failed', e);
      toast.error('Gagal memuat data tagihan.');
    }
  }
  
}
</script>

<style scoped>
</style> 
