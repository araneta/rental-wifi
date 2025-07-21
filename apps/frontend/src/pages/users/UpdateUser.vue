<template>
  <div>
    <h2>Edit User</h2>
    <form @submit.prevent="updateUser">
      <input type="text" v-model="form.name" required class="form-control mb-2" />

      <input type="email" v-model="form.email" required class="form-control mb-2" />

      <input type="password" v-model="form.password" placeholder="Password Baru (Opsional)" class="form-control mb-2" />

      <select v-model="form.role" class="form-control mb-2">
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
      </select>

      <button type="submit" class="btn btn-primary">Update</button>&nbsp;
      <router-link to="/users" class="btn btn-secondary">Batal</router-link>
    </form>
  </div>
</template>

<script>
import { apiFetch } from '../../api';

export default {
  name: 'UpdateUser',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        role: 'admin'
      }
    };
  },
  async mounted() {
    const id = this.$route.params.id;
    try {
      const response = await apiFetch(`/users/${id}`, { method: 'GET' });
      this.form.name = response.user.name;
      this.form.email = response.user.email;
      this.form.role = response.user.role;
    } catch (err) {
      console.error('Gagal memuat data user:', err);
    }
  },
  methods: {
    async updateUser() {
      const id = this.$route.params.id;
      try {
        await apiFetch(`/users/${id}`, {
          method: 'PUT',
          body: JSON.stringify(this.form),
			headers: {
			  'Content-Type': 'application/json'
			}

        });
        this.$router.push('/users');
      } catch (err) {
        console.error('Gagal mengupdate user:', err);
      }
    }
  }
};
</script>

<style scoped>
</style>
