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
        <div class="col-md-3">
          <label>Cari Pelanggan:</label>
          <input type="text" v-model="searchKeyword" class="form-control" placeholder="Nama pelanggan..." />
        </div>
      </div>
    </form>

    <!-- Tombol Tambah -->
    <a class="btn btn-primary mb-3" href="/tagihans/create">Tambah Manual</a>&nbsp;
    <a class="btn btn-warning mb-3" href="/tagihans/create-mass">Tambah Keseluruhan</a>&nbsp;
    <button class="btn btn-success mb-3" @click="btndownloadExcel">Export Excel</button>&nbsp;

    <!-- Table -->
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
              <button class="btn btn-success" @click="bayarTagihan(tagihan)">Bayar</button>&nbsp;
              <a class="btn btn-warning" :href="`/tagihans/${tagihan.id}`">Edit</a>&nbsp;
              <button class="btn btn-danger" @click="deleteTagihan(tagihan)">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { apiFetch, downloadExcel } from '../../api';
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  name: 'Tagihan',
  data() {
    return {
      filterStatus: '',
      filterBulanTahun: '',
      searchKeyword: '',
      tagihans: []
    }
  },
  computed: {
    filteredTagihan() {
      return this.tagihans.filter(t => {
        const statusMatch = !this.filterStatus || t.status === this.filterStatus;
        const bulanTahunMatch = !this.filterBulanTahun || t.bulan_tahun === this.filterBulanTahun;
        const keywordMatch = !this.searchKeyword || t.pelanggan.toLowerCase().includes(this.searchKeyword.toLowerCase());
        return statusMatch && bulanTahunMatch && keywordMatch;
      });
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/tagihans?status=' + this.filterStatus, { method: 'GET' });
      this.tagihans = data.tagihans;
    } catch (e) {
      console.error('Fetch failed', e);
      toast.error('Gagal memuat data tagihan.');
    }
  },
  methods: {
    async btndownloadExcel() {
      downloadExcel(
        `/tagihans/excel?status=${encodeURIComponent(this.filterStatus)}&bulan_tahun=${encodeURIComponent(this.filterBulanTahun)}`,
        `tagihan-${this.filterBulanTahun || 'semua'}`
      );
    },
    async deleteTagihan(tagihan) {
      const confirmed = confirm(`Yakin ingin menghapus tagihan "${tagihan.pelanggan}"?`);
      if (!confirmed) return;

      try {
        await apiFetch(`/tagihans/${tagihan.id}`, { method: 'DELETE' });
        this.tagihans = this.tagihans.filter(p => p.id !== tagihan.id);
        toast.success(`Tagihan "${tagihan.pelanggan}" berhasil dihapus.`);
      } catch (err) {
        console.error(err);
        toast.error('Gagal menghapus tagihan.');
      }
    },
    async bayarTagihan(tagihan) {
		window.location.href = '/pembayarans/create?tagihan_id='+tagihan.id;
		return;
      try {
        const confirmed = confirm(`Tandai tagihan "${tagihan.pelanggan}" sebagai LUNAS?`);
        if (!confirmed) return;

        const data = await apiFetch(`/tagihans/${tagihan.id}/bayar`, { method: 'POST' });
        console.log('data',data);
        tagihan.status = 'dibayar';
        toast.success(`Tagihan "${tagihan.pelanggan}" berhasil ditandai sebagai lunas.`);
      } catch (err) {
        console.error(err);
        toast.error('Gagal membayar tagihan.');
      }
    }
  }
}
</script>
