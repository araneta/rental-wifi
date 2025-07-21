<template>
  <div>
    <h2>Daftar Pelanggan</h2>
    <a href="/pelanggans/create" class="btn btn-primary mb-3">Tambah Pelanggan</a>
    <table class="table table-bordered" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Alamat</th>
          <th>No HP</th>
          <th>Paket</th>
          <th>POP</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="pelanggan in pelanggans" :key="pelanggan.id">
          <td>{{ pelanggan.id }}</td>
          <td>{{ pelanggan.nama }}</td>
          <td>{{ pelanggan.alamat }}</td>
          <td>{{ pelanggan.no_hp }}</td>
          <td>{{ pelanggan.paket_nama || '❌ Tidak Berlangganan' }}</td>
          <td>{{ pelanggan.pop }}</td>
          <td>
            <span v-if="pelanggan.status === 'aktif'">✅ Aktif</span>
            <span v-else>❌ Nonaktif</span>
          </td>
          <td>
            <a class="btn btn-warning" :href="`/pelanggans/${pelanggan.id}`">Edit</a>&nbsp;
            <button class="btn btn-danger" @click="deletePelanggan(pelanggan)">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { apiFetch } from '../../api' // Your custom API handler
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  name: 'Pelanggan',
  data() {
    return {
      pelanggans: [
        
      ]
    }
  },
  methods: {
    async deletePelanggan(pelanggan) {
      const confirmed = confirm(`Yakin ingin menghapus pelanggan "${pelanggan.nama}"?`);
      if (!confirmed) return;

      try {
        await apiFetch(`/pelanggans/${pelanggan.id}`, { method: 'DELETE' });
        this.pelanggans = this.pelanggans.filter(p => p.id !== pelanggan.id);
        toast.success(`Pelanggan "${pelanggan.nama}" berhasil dihapus.`);
      } catch (err) {
        console.error(err);
        toast.error('Gagal menghapus pelanggan.');
      }
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/pelanggans', { method: 'GET' });
      this.pelanggans = data.pelanggans;
    } catch (e) {
      console.error('Fetch failed', e);
      toast.error('Gagal memuat data pelanggan.');
    }
  }
}
</script>

<style scoped>
</style> 
