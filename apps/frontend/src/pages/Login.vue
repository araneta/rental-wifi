<template>
  <div>
    <h2>Login</h2>
    <form @submit.prevent="login">
      <input v-model="form.username" placeholder="Username" required />
      <input type="password" v-model="form.password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <p v-if="error">{{ error }}</p>
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
        const data = await apiFetch('/api/login', {
          method: 'POST',
          body: JSON.stringify(this.form)
        });
        localStorage.setItem('token', data.token);
        this.$router.push('/students');
      } catch (e) {
        this.error = 'Login failed';
      }
    }
  }
};
</script>
