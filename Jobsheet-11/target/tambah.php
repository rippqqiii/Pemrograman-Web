<?php
$page_title = "Buat Target Baru";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Buat Target Tabungan Baru</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <?php echo csrf_field(); ?>
                <p>
                    <label for="nama">Nama Target / Impian</label><br>
                    <input type="text" id="nama" name="nama" placeholder="Contoh: Beli Laptop Baru, Liburan ke Bali" required>
                </p>
                <p>
                    <label for="target_nominal">Target Dana (Rp)</label><br>
                    <input type="number" id="target_nominal" name="target_nominal" min="1000" placeholder="Contoh: 10000000" required>
                </p>
                <p>
                    <label for="saldo_sekarang">Saldo Awal (Rp)</label><br>
                    <input type="number" id="saldo_sekarang" name="saldo_sekarang" min="0" value="0" placeholder="Contoh: 500000">
                </p>
                <p>
                    <label for="kategori">Kategori</label><br>
                    <select id="kategori" name="kategori">
                        <option value="Gadget & Elektronik">📱 Gadget & Elektronik</option>
                        <option value="Liburan & Hiburan">✈️ Liburan & Hiburan</option>
                        <option value="Pendidikan & Belajar">🎓 Pendidikan & Belajar</option>
                        <option value="Dana Darurat">🛡️ Dana Darurat</option>
                        <option value="Kendaraan">🛵 Kendaraan</option>
                        <option value="Lainnya">📦 Lainnya</option>
                    </select>
                </p>
                <p>
                    <label for="deadline">Target Selesai (Batas Waktu)</label><br>
                    <input type="date" id="deadline" name="deadline">
                </p>
                <p>
                    <label for="link_barang">Link Barang (URL Toko Online)</label><br>
                    <input type="url" id="link_barang" name="link_barang" placeholder="Contoh: https://tokopedia.com/produk/laptop">
                </p>
                <p>
                    <button type="submit">Simpan Target</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
