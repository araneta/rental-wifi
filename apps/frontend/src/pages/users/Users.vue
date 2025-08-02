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
            <a class="btn btn-warning" :href="`/users/${user.id}`">Edit</a>&nbsp;
            <button class="btn btn-danger" @click="deleteUser(user)">Hapus</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { apiFetch } from '../../api';
import { useToast } from 'vue-toastification';
const toast = useToast();

export default {
  name: 'Users',
  data() {
    return {
      users: []
    };
  },
  async mounted() {
    await this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      try {
        const data = await apiFetch('/users', { method: 'GET' });
        this.users = data.users;
      } catch (e) {
        console.error('Fetch failed', e);
        toast.error('Gagal memuat data pengguna.');
      }
    },
    async deleteUser(user) {
      const confirmed = confirm(`Yakin ingin menghapus user "${user.name}"?`);
      if (!confirmed) return;

      try {
        await apiFetch(`/users/${user.id}`, { method: 'DELETE' });
        toast.success(`User "${user.name}" berhasil dihapus.`);
        await this.fetchUsers(); // refetch from API
      } catch (err) {
        console.error(err);
        toast.error('Gagal menghapus user.');
      }
    }
  }
}
</script>
