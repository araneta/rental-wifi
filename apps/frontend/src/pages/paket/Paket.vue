<template>
  <div>
    <h2>Daftar Paket Internet</h2>
    <a class="btn btn-primary mb-3" href="/pakets/create">Tambah Paket</a>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Paket</th>
          <th>Kecepatan</th>
          <th>Harga</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="paket in pakets" :key="paket.id">
          <td>{{ paket.id }}</td>
          <td>{{ paket.nama }}</td>
          <td>{{ paket.kecepatan }}</td>
          <td>Rp{{ paket.harga.toLocaleString('id-ID') }}</td>
          <td>{{ paket.deskripsi }}</td>
          <td>
            <a class="btn btn-warning" :href="`/pakets/${paket.id}`">Edit</a>&nbsp;
            <button class="btn btn-danger" @click="deletePaket(paket)">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { apiFetch } from '../../api'; // adjust as needed
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  name: 'Paket',
  data() {
    return {
      pakets: []
    };
  },
  methods: {
    async deletePaket(paket) {
      const confirmed = confirm(`Yakin ingin menghapus paket "${paket.nama}"?`);
      if (!confirmed) return;

      try {
        await apiFetch(`/pakets/${paket.id}`, { method: 'DELETE' });
        this.pakets = this.pakets.filter(p => p.id !== paket.id);
        toast.success(`Paket "${paket.nama}" berhasil dihapus.`);
      } catch (err) {
        console.error(err);
        toast.error('Gagal menghapus paket.');
      }
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/pakets', { method: 'GET' });
      this.pakets = data.pakets;
    } catch (e) {
      console.error('Fetch failed', e);
      toast.error('Gagal memuat data paket.');
    }
  }
};
</script>

<style scoped>
</style>
