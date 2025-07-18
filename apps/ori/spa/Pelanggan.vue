<template>
  <div>
    <h2>Daftar Pelanggan</h2>
    <button class="btn btn-primary mb-3">Tambah Pelanggan</button>
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
  name: 'Pelanggan',
  data() {
    return {
      pelanggans: [
        { id: 1, nama: 'Andi', alamat: 'Jl. Mawar', no_hp: '08123456789', paket_nama: 'Basic', pop: 'POP1', status: 'aktif' },
        { id: 2, nama: 'Budi', alamat: 'Jl. Melati', no_hp: '08129876543', paket_nama: '', pop: 'POP2', status: 'nonaktif' }
      ]
    }
  }
}
</script>

<style scoped>
</style> 