<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalProduk = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalPenjualan = $pdo->query("SELECT COUNT(*) FROM penjualan")->fetchColumn();
?>

<style>
body {
    background-color: #0d0f0c;
}
</style>

<!-- KOTAK SELAMAT DATANG -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title" style="color:#01669c;">Selamat Datang di Sistem Penjualan Buku</h2>
                <p class="card-text mb-0">Aplikasi sederhana untuk mencatat penjualan dan mengelola stok buku.</p>
            </div>
        </section>

<!-- KOTAK RINGKASAN -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3" style="color:#01669C;">Ringkasan</h2>
                <div class="row text-center">

                <!-- KARTU 1: TOTAL BUKU -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#03365B; border: 1px solid #03365B;">
                            <h3 class="h6 text-secondary">📚 Total Buku</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#01669C;">12</p>
                        </div>
                    </div>

                <!-- KARTU 2: TOTAL PENJUALAN -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#03365B; border: 1px solid #03365B;">
                            <h3 class="h6 text-secondary">✚ Total Penjualan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#01669C;">25</p>
                        </div>
                    </div>

                <!-- KARTU 3: TOTAL PENDAPATAN -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#03365B; border: 1px solid #03365B;">
                            <h3 class="h6 text-secondary">� Pendapatan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#01669C;">Rp 1.5jt</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
