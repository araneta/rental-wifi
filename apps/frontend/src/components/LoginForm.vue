<template>
  <div class="card mx-auto" style="max-width: 400px;">
    <div class="card-body">
      <h4 class="card-title mb-3">Login</h4>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label>Username</label>
          <input v-model="form.email" class="form-control" required />
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
import { authFetch } from '../api';

export default {
  data() {
    return { form: { email: '', password: '' }, error: '' };
  },
  methods: {
    async login() {
      try {
        const data = await authFetch('/login', {
          method: 'POST',
          body: JSON.stringify(this.form)
        });
        localStorage.setItem('token', data.token);
        console.log('login ok');
        this.$router.push('/dashboard');
      } catch (e) {
        this.error = 'Login failed';
      }
    }
  }
};
</script>
