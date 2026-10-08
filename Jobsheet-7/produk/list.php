<?php
$page_title = "Daftar Buku";
$extra_scripts = ["../assets/js/buku.js"];
include __DIR__ . '/../includes/header.php';
?>
        <section>
            <h2>Daftar Buku</h2>
            <div class="search-box">
                <label for="search-input">Cari Judul Buku</label>
                <input type="text" id="search-input" placeholder="Ketik judul buku...">
            </div>
            <div id="loading-indicator" style="display:none;">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Memuat data...
            </div>
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris diisi dinamis oleh assets/js/buku.js via fetch('../data/buku.json') -->
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
