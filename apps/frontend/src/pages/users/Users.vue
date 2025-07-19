<template>
  <div>
    <h2>Manajemen Pengguna</h2>
    <a class="btn btn-primary mb-3" href="/users/create">Tambah User</a>
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
            <a class="btn btn-warning" :href="`/users/${user.id}`">Edit</a>

            <!-- <button class="btn btn-danger">Hapus</button> -->
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { apiFetch } from '../../api'; // or your API handler

export default {
  name: 'Users',
  data() {
    return {
      users: [
       
      ]
    }
  },
  async mounted() {
    try {
      const data = await apiFetch('/users', { method: 'GET' });
      console.log('data', data);
      this.users = data.users;      
    } catch (e) {
      console.error('Fetch failed', e);
      this.error = 'Gagal memuat data dashboard.';
    }
  }
}
</script>

<style scoped>
</style> 
