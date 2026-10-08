<?php
$page_title = "Edit Transaksi Penjualan";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM penjualan WHERE id = :id");
$stmt->execute(['id' => $id]);
$penjualan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$penjualan) {
    header('Location: list.php');
    exit;
}

$daftarProduk = $pdo->query("SELECT * FROM produk ORDER BY judul ASC")->fetchAll(PDO::FETCH_ASSOC);
$tanggalVal = date('Y-m-d', strtotime($penjualan['tanggal_penjualan']));
?>
        <section>
            <h2>Edit Transaksi Penjualan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_edit.php">
                <input type="hidden" name="id" value="<?php echo $penjualan['id']; ?>">
                <p>
                    <label for="tanggal">Tanggal</label><br>
                    <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($tanggalVal); ?>" required>
                </p>
                <p>
                    <label for="produk_id">Buku</label><br>
                    <select id="produk_id" name="produk_id" required>
                        <option value="">Pilih Buku</option>
                        <?php foreach ($daftarProduk as $produk): ?>
                        <option value="<?php echo $produk['id']; ?>" data-harga="<?php echo $produk['harga']; ?>" <?php echo $produk['id'] == $penjualan['produk_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($produk['judul']); ?> - Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="jumlah">Jumlah</label><br>
                    <input type="number" id="jumlah" name="jumlah" min="1" value="<?php echo htmlspecialchars($penjualan['jumlah']); ?>" required>
                </p>
                <p>
                    <label for="total_harga">Total Harga</label><br>
                    <input type="number" id="total_harga" name="total_harga" value="<?php echo htmlspecialchars($penjualan['total_harga']); ?>" required>
                </p>
                <p>
                    <button type="submit">Update</button>
                </p>
            </form>
        </section>
        <script>
            document.getElementById('produk_id').addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const harga = opt ? opt.getAttribute('data-harga') : 0;
                const jumlah = document.getElementById('jumlah').value;
                if (harga && jumlah) {
                    document.getElementById('total_harga').value = Math.round(harga * jumlah);
                }
            });
            document.getElementById('jumlah').addEventListener('input', function() {
                const sel = document.getElementById('produk_id');
                const opt = sel.options[sel.selectedIndex];
                const harga = opt ? opt.getAttribute('data-harga') : 0;
                const jumlah = this.value;
                if (harga && jumlah) {
                    document.getElementById('total_harga').value = Math.round(harga * jumlah);
                }
            });
        </script>
<?php include __DIR__ . '/../includes/footer.php'; ?>