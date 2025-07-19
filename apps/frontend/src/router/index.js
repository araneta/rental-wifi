import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Students from '../pages/Students.vue';
import Dashboard from '../pages/Dashboard.vue';
import Users from '../pages/Users.vue';
import Paket from '../pages/Paket.vue';
import Pelanggan from '../pages/Pelanggan.vue';
import Tagihan from '../pages/Tagihan.vue';
import Pembayaran from '../pages/Pembayaran.vue';
import Rekap from '../pages/Rekap.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login },
  { path: '/students', component: Students, meta: { requiresAuth: true } },
	{ path: '/users', component: Users, meta: { requiresAuth: true } },
  { path: '/paket', component: Paket, meta: { requiresAuth: true } },
  { path: '/pelanggan', component: Pelanggan , meta: { requiresAuth: true }},
  { path: '/tagihan', component: Tagihan, meta: { requiresAuth: true } },
  { path: '/pembayaran', component: Pembayaran, meta: { requiresAuth: true } },
  { path: '/rekap', component: Rekap, meta: { requiresAuth: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Auth guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.requiresAuth && !token) next('/login');
  else next();
});

export default router;
