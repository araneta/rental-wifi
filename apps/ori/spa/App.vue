<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Rental Internet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Dashboard</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/users">Users</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/paket">Paket</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/pelanggan">Pelanggan</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/tagihan">Tagihan</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/pembayaran">Pembayaran</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/rekap">Rekap</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container">
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
body {
  background: #f8f9fa;
}
</style> 