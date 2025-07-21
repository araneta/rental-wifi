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
            <button class="btn btn-danger">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { apiFetch } from '../../api'; // or your API handler
export default {
  name: 'Paket',
  data() {
    return {
      pakets: [
        
      ]
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/pakets', { method: 'GET' });
      console.log('data', data);
      this.pakets = data.pakets;      
    } catch (e) {
      console.error('Fetch failed', e);
      this.error = 'Gagal memuat data dashboard.';
    }
  }
}
</script>

<style scoped>
</style> 
