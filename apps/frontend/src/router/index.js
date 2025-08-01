import { createRouter, createWebHistory } from 'vue-router';
import Home from '../pages/Home.vue';
import Login from '../pages/Login.vue';
import Students from '../pages/Students.vue';
import Dashboard from '../pages/Dashboard.vue';

import Users from '../pages/users/Users.vue';
import CreateUser from '../pages/users/CreateUser.vue';
import UpdateUser from '../pages/users/UpdateUser.vue';

import Paket from '../pages/paket/Paket.vue';
import CreatePaket from '../pages/paket/CreatePaket.vue';
import UpdatePaket from '../pages/paket/UpdatePaket.vue';

import Pelanggan from '../pages/pelanggan/Pelanggan.vue';
import CreatePelanggan from '../pages/pelanggan/CreatePelanggan.vue';
import UpdatePelanggan from '../pages/pelanggan/UpdatePelanggan.vue';

import Tagihan from '../pages/tagihan/Tagihan.vue';
import CreateTagihan from '../pages/tagihan/CreateTagihan.vue';
import MassCreateTagihan from '../pages/tagihan/MassCreateTagihan.vue';
import TagihanEdit from '../pages/tagihan/TagihanEdit.vue';

import Pembayaran from '../pages/pembayaran/Pembayaran.vue';
import CreatePembayaran from '../pages/pembayaran/CreatePembayaran.vue';
import UpdatePembayaran from '../pages/pembayaran/UpdatePembayaran.vue';

import Rekap from '../pages/rekap/Rekap.vue';

const routes = [
	{ path: '/', component: Home },
	{ path: '/login', component: Login },
	{ path: '/students', component: Students, meta: { requiresAuth: true } },
	
	{ path: '/users', component: Users, meta: { requiresAuth: true } },
	{path:'/users/create', component: CreateUser, meta:{requiredAuth: true}},
	{ path: '/users/:id', component: UpdateUser, meta: { requiresAuth: true } },

	{ path: '/pakets', component: Paket, meta: { requiresAuth: true } },
	{path:'/pakets/create', component: CreatePaket, meta:{requiredAuth: true}},
	{ path: '/pakets/:id', component: UpdatePaket, meta: { requiresAuth: true } },
	
  { path: '/pelanggans', component: Pelanggan , meta: { requiresAuth: true }},
  {path:'/pelanggans/create', component: CreatePelanggan, meta:{requiredAuth: true}},
  {path:'/pelanggans/:id', component: UpdatePelanggan, meta:{requiredAuth: true}},
  
  { path: '/tagihans', component: Tagihan, meta: { requiresAuth: true } },
  {path:'/tagihans/create', component: CreateTagihan, meta:{requiredAuth: true}},
  {path:'/tagihans/create-mass', component: MassCreateTagihan, meta:{requiredAuth: true}},
  { path: '/tagihans/:id', component: TagihanEdit, meta: { requiresAuth: true } },
  
  
  { path: '/pembayarans', component: Pembayaran, meta: { requiresAuth: true } },
  {path:'/pembayarans/create', component: CreatePembayaran, meta:{requiredAuth: true}},
  { path: '/pembayarans/:id', component: UpdatePembayaran, meta: { requiresAuth: true } },
  
  { path: '/rekaps', component: Rekap, meta: { requiresAuth: true } },
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
