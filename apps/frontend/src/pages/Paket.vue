<template>
  <div>
    <h2>Daftar Paket Internet</h2>
    <button class="btn btn-primary mb-3">Tambah Paket</button>
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
            <button class="btn btn-warning">Edit</button>
            <button class="btn btn-danger">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'Paket',
  data() {
    return {
      pakets: [
        { id: 1, nama: 'Basic', kecepatan: '10 Mbps', harga: 150000, deskripsi: 'Paket dasar' },
        { id: 2, nama: 'Premium', kecepatan: '50 Mbps', harga: 350000, deskripsi: 'Paket cepat' }
      ]
    }
  }
}
</script>

<style scoped>
</style> 