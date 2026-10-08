<?php
$page_title = "Buat Transaksi";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarProduk = $pdo->query("SELECT * FROM produk ORDER BY judul ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Buat Transaksi</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <p>
                    <label for="tanggal">Tanggal</label><br>
                    <input type="date" id="tanggal" name="tanggal" required>
                </p>
                <p>
                    <label for="produk_id">Buku</label><br>
                    <select id="produk_id" name="produk_id" required>
                        <option value="">Pilih Buku</option>
                        <?php foreach ($daftarProduk as $produk): ?>
                        <option value="<?php echo $produk['id']; ?>" data-harga="<?php echo $produk['harga']; ?>">
                            <?php echo $produk['judul']; ?> - Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="jumlah">Jumlah</label><br>
                    <input type="number" id="jumlah" name="jumlah" min="1" required>
                </p>
                <p>
                    <label for="total_harga">Total Harga</label><br>
                    <input type="number" id="total_harga" name="total_harga" required>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
        <script>
            document.getElementById('produk_id').addEventListener('change', function() {
                const harga = this.options[this.selectedIndex].getAttribute('data-harga');
                const jumlah = document.getElementById('jumlah').value;
                if (harga && jumlah) {
                    document.getElementById('total_harga').value = harga * jumlah;
                }
            });
            document.getElementById('jumlah').addEventListener('input', function() {
                const harga = document.getElementById('produk_id').options[document.getElementById('produk_id').selectedIndex].getAttribute('data-harga');
                const jumlah = this.value;
                if (harga && jumlah) {
                    document.getElementById('total_harga').value = harga * jumlah;
                }
            });
        </script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
