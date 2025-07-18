<template>
  <div>
    <h2>Manajemen Pengguna</h2>
    <button class="btn btn-primary mb-3">Tambah User</button>
    <table class="table table-bordered" width="100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.role }}</td>
          <td>
            <button class="btn btn-warning">Edit</button>
            <!-- <button class="btn btn-danger">Hapus</button> -->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'Users',
  data() {
    return {
      users: [
        { id: 1, name: 'Aldo', email: 'aldo@mail.com', role: 'admin' },
        { id: 2, name: 'Budi', email: 'budi@mail.com', role: 'user' },
        { id: 3, name: 'Cici', email: 'cici@mail.com', role: 'user' }
      ]
    }
  }
}
</script>

<style scoped>
</style> 