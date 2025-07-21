<template>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="">Tagihan Internet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pakets">Paket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pelanggans">Pelanggan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tagihans">Tagihan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pembayarans">Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rekaps">Rekap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <router-view />
    </div>
</template>

<script>
import LoginForm from './components/LoginForm.vue';
export default { components: { LoginForm } };
</script>
