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
          </select>
        </div>
        <div class="col-md-3">
          <label>Bulan/Tahun:</label>
          <input type="month" v-model="filterBulanTahun" class="form-control" />
        </div>
      </div>
    </form>
    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3">Tambah Manual</button>
    <button class="btn btn-warning mb-3">Tambah Keseluruhan</button>
    <button class="btn btn-success mb-3">Export Excel</button>
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
  name: 'Tagihan',
  data() {
    return {
      filterStatus: '',
      filterBulanTahun: '',
      tagihans: [
        { id: 1, pelanggan: 'Andi', jumlah: 150000, status: 'belum bayar', bulan_tahun: '2024-06', petugas: 'Aldo' },
        { id: 2, pelanggan: 'Budi', jumlah: 150000, status: 'lunas', bulan_tahun: '2024-05', petugas: 'Budi' }
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
  }
}
</script>

<style scoped>
</style> 