<?php
$page_title = "Riwayat Penjualan";
$extra_scripts = ["../assets/js/penjualan.js"];
include __DIR__ . '/../includes/header.php';
?>
        <section>
            <h2>Riwayat Penjualan</h2>
            <div class="search-box">
                <label for="search-input">Cari Transaksi</label>
                <input type="text" id="search-input" placeholder="Ketik ID transaksi atau judul buku...">
            </div>
            <div id="loading-indicator" style="display:none;">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Memuat data...
            </div>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Buku</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris diisi dinamis oleh assets/js/penjualan.js via fetch('../data/penjualan.json') -->
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
