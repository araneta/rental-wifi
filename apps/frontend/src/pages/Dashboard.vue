<template>
  <div>
    <h2 class="mb-4">Dashboard Tagihan Internet</h2>
    <div class="row">
      <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
          <div class="card-header">Pengguna</div>
          <div class="card-body">
            <h4 class="card-title">{{ totalUsers }}</h4>
            <router-link to="/users" class="btn btn-light btn-sm">Kelola</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
          <div class="card-header">Pelanggan</div>
          <div class="card-body">
            <h4 class="card-title">{{ totalPelanggan }}</h4>
            <router-link to="/pelanggan" class="btn btn-light btn-sm">Kelola</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
          <div class="card-header">Tagihan Belum Bayar</div>
          <div class="card-body">
            <h4 class="card-title">{{ totalTagihan }}</h4>
            <router-link to="/tagihan" class="btn btn-light btn-sm">Kelola</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
          <div class="card-header">Pembayaran</div>
          <div class="card-body">
            <h4 class="card-title">{{ totalPembayaran }}</h4>
            <router-link to="/pembayaran" class="btn btn-light btn-sm">Kelola</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { apiFetch } from '../api';

export default {
  name: 'Dashboard',
  data() {
    return {
      totalUsers: 0,
      totalPelanggan: 0,
      totalTagihan: 0,
      totalPembayaran: 0,
      error: null
    };
  },
  async mounted() {
    try {
      const data = await apiFetch('/analytics', { method: 'GET' });
      console.log('data', data);
      this.totalUsers = data.totalUsers;
      this.totalPelanggan = data.totalPelanggan;
      this.totalTagihan = data.totalTagihan;
      this.totalPembayaran = data.totalPembayaran;
    } catch (e) {
      console.error('Fetch failed', e);
      this.error = 'Gagal memuat data dashboard.';
    }
  }
};
</script>

<style scoped>
</style>
