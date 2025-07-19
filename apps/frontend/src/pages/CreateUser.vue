<template>


<h2>Tambah User</h2>
<form @submit.prevent="createUser">
    <input type="text" name="name" placeholder="Nama" required class="form-control mb-2" v-model="form.name" />
    <input type="email" name="email" placeholder="Email" required class="form-control mb-2" v-model="form.email" />
    <input type="password" name="password" placeholder="Password" required class="form-control mb-2" v-model="form.password" />
    <select name="role" class="form-control mb-2" v-model="form.role">
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
    </select>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>
<p class="text-danger mt-2" v-if="error">{{ error }}</p>
</template>


<script>
import { apiFetch } from '../api';
import { useToast } from 'vue-toastification';
const toast = useToast();


export default {
  data() {
    return { form: { name: '', email: '', password:'', role:'', }, error: '' };
  },
  methods: {
    async createUser() {
      try {
        const data = await apiFetch('/users', {
          method: 'POST',
          body: JSON.stringify(this.form)
        });        
        console.log('data',data);
        this.$router.push('/users');
      } catch (e) {
		toast.error(e.message.error);
		console.log('err',e);
        this.error = 'Add user failed';
      }
    }
  }
};
</script>
