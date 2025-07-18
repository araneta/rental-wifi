<template>
  <div class="card mx-auto" style="max-width: 400px;">
    <div class="card-body">
      <h4 class="card-title mb-3">Login</h4>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label>Username</label>
          <input v-model="form.username" class="form-control" required />
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" v-model="form.password" class="form-control" required />
        </div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>
      <p class="text-danger mt-2" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import { apiFetch } from '../api';

export default {
  data() {
    return { form: { username: '', password: '' }, error: '' };
  },
  methods: {
    async login() {
      try {
        const data = await apiFetch('/login', {
          method: 'POST',
          body: JSON.stringify(this.form)
        });
        localStorage.setItem('token', data.token);
        this.$router.push('/');
      } catch (e) {
        this.error = 'Login failed';
      }
    }
  }
};
</script>
