<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Login Petugas";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <section class="card shadow-sm border-0 mb-4" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
            <div class="card-body p-4">
                <h2 class="h4 mb-1 text-center" style="color: #ff6b6b;">🔐 Login Pengguna / Petugas</h2>
                <p class="text-secondary small text-center mb-4">Masuk untuk mengelola target tabungan dan melakukan transaksi.</p>

                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                        <?php echo e($flash['pesan']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="proses_login.php">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold" style="background-color: #9e0303; border-color: #9e0303;">
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="mt-4 pt-3 border-top text-center text-secondary small" style="border-color: #5c1a1a !important;">
                    Belum punya akun? <a href="register.php" style="color: #ff8585;">Daftar di sini</a>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
