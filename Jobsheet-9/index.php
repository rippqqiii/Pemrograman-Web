<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalProduk = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$totalPenjualan = $pdo->query("SELECT COUNT(*) FROM penjualan")->fetchColumn();
$totalPendapatan = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM penjualan")->fetchColumn();
?>

<style>
body {
    background-color: #1a0a0a;
}
</style>

<!-- KOTAK SELAMAT DATANG -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title" style="color:#c41e3a;">Selamat Datang di Sistem Penjualan Buku Gramedia</h2>
                <p class="card-text mb-0">Aplikasi pengelolaan produk dan pencatatan transaksi penjualan buku berbasis web.</p>
            </div>
        </section>

<!-- KOTAK RINGKASAN -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3" style="color:#c41e3a;">Ringkasan</h2>
                <div class="row text-center">

                <!-- KARTU 1: TOTAL BUKU -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#5c1a1a; border: 1px solid #5c1a1a;">
                            <h3 class="h6 text-secondary">📚 Total Buku</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#c41e3a;"><?php echo (int) $totalProduk; ?></p>
                        </div>
                    </div>

                <!-- KARTU 2: TOTAL PENJUALAN -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#5c1a1a; border: 1px solid #5c1a1a;">
                            <h3 class="h6 text-secondary">✚ Total Penjualan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#c41e3a;"><?php echo (int) $totalPenjualan; ?></p>
                        </div>
                    </div>

                <!-- KARTU 3: TOTAL PENDAPATAN -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#5c1a1a; border: 1px solid #5c1a1a;">
                            <h3 class="h6 text-secondary">💰 Pendapatan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#c41e3a;">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
