<template>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="https://rental.local/index.php">Tagihan Internet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/users/index.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/paket/index.php">Paket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/pelanggan/index.php">Pelanggan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/tagihan/index.php">Tagihan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/pembayaran/index.php">Pembayaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://rental.local/rekap/index.php">Rekap</a>
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
