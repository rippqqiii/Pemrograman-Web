<?php
$page_title = "Setor / Tarik Tabungan";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarTarget = $pdo->query("SELECT * FROM target ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Setor / Tarik Tabungan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <?php if (empty($daftarTarget)): ?>
                <p style="color: #f87171;">Belum ada target tabungan. Silakan buat target terlebih dahulu.</p>
                <p><a href="../target/tambah.php" style="color: #c41e3a;">Buat Target Baru</a></p>
            <?php else: ?>

            <form id="form-tambah" method="post" action="proses_tambah.php">
                <?php echo csrf_field(); ?>
                <p>
                    <label for="target_id">Pilih Target Tabungan</label><br>
                    <select id="target_id" name="target_id" required>
                        <option value="">Pilih Target</option>
                        <?php foreach ($daftarTarget as $target): ?>
                        <option value="<?php echo $target['id']; ?>" data-saldo="<?php echo $target['saldo_sekarang']; ?>">
                            <?php echo e($target['nama']); ?> - Saldo: Rp <?php echo number_format($target['saldo_sekarang'], 0, ',', '.'); ?> / Target: Rp <?php echo number_format($target['target_nominal'], 0, ',', '.'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label>Jenis Transaksi</label><br>
                    <label>
                        <input type="radio" name="jenis_transaksi" value="setor" checked> ➕ Setor (Nabung)
                    </label><br>
                    <label>
                        <input type="radio" name="jenis_transaksi" value="tarik"> ➖ Tarik Uang
                    </label>
                </p>
                <p>
                    <label for="jumlah">Nominal (Rp)</label><br>
                    <input type="number" id="jumlah" name="jumlah" min="1000" required placeholder="Contoh: 50000">
                </p>
                <p>
                    <label for="catatan">Catatan / Keterangan</label><br>
                    <input type="text" id="catatan" name="catatan" placeholder="Contoh: Sisa uang jajan, Bonus proyek">
                </p>
                <p>
                    <button type="submit">Simpan Transaksi</button>
                </p>
            </form>
            <?php endif; ?>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
