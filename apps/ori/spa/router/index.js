import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../Dashboard.vue';
import Users from '../Users.vue';
import Paket from '../Paket.vue';
import Pelanggan from '../Pelanggan.vue';
import Tagihan from '../Tagihan.vue';
import Pembayaran from '../Pembayaran.vue';
import Rekap from '../Rekap.vue';

const routes = [
  { path: '/', component: Dashboard },
  { path: '/users', component: Users },
  { path: '/paket', component: Paket },
  { path: '/pelanggan', component: Pelanggan },
  { path: '/tagihan', component: Tagihan },
  { path: '/pembayaran', component: Pembayaran },
  { path: '/rekap', component: Rekap }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router; 