<template>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/index.php">Tagihan Internet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/users/index.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/paket/index.php">Paket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pelanggan/index.php">Pelanggan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tagihan/index.php">Tagihan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/pembayaran/index.php">Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/rekap/index.php">Rekap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
		<login-form />
	</div>
</template>

<script>
import LoginForm from './components/LoginForm.vue';
export default { components: { LoginForm } };
</script>
