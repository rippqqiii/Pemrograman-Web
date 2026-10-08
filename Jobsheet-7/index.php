<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
?>
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title" style="color:#38bdf8;">Selamat Datang di Sistem Penjualan Buku</h2>
                <p class="card-text mb-0">Aplikasi sederhana untuk mencatat penjualan dan mengelola stok buku.</p>
            </div>
        </section>

        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3" style="color:#38bdf8;">Ringkasan</h2>
                <div class="row g-3 text-center">
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#0f172a; border: 1px solid #334155;">
                            <h3 class="h6 text-secondary">📚 Total Buku</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#38bdf8;">12</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#0f172a; border: 1px solid #334155;">
                            <h3 class="h6 text-secondary">✚ Total Penjualan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#38bdf8;">25</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#0f172a; border: 1px solid #334155;">
                            <h3 class="h6 text-secondary">💲 Pendapatan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#38bdf8;">Rp 1.5jt</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
