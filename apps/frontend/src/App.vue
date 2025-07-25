<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="#">Tagihan Internet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/users">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="/pakets">Paket</a></li>
            <li class="nav-item"><a class="nav-link" href="/pelanggans">Pelanggan</a></li>
            <li class="nav-item"><a class="nav-link" href="/tagihans">Tagihan</a></li>
            <li class="nav-item"><a class="nav-link" href="/pembayarans">Pembayaran</a></li>
            <li class="nav-item"><a class="nav-link" href="/rekaps">Rekap</a></li>
            <li v-if="isLoggedIn" class="nav-item">
              <a class="nav-link text-warning" href="#" @click.prevent="logout">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-4">
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: 'MainLayout',
  data() {
    return {
      isLoggedIn: false
    };
  },
  mounted() {
    this.checkAuth();
  },
  methods: {
    checkAuth() {
      this.isLoggedIn = !!localStorage.getItem('token');
    },
    logout() {
      localStorage.clear();
      this.isLoggedIn = false;
      this.$router.push('/login');
    }
  },
  watch: {
    '$route'() {
      this.checkAuth(); // update login status on route change
    }
  }
};
</script>
