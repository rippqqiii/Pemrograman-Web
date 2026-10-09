<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Setor / Tarik Tabungan";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarTarget = $pdo->query("SELECT * FROM target ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <section class="card shadow-sm border-0 mb-4" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #5c1a1a !important;">
                    <div>
                        <h2 class="h4 mb-1" style="color: #ff6b6b;">💳 Setor / Tarik Tabungan</h2>
                        <p class="text-secondary small mb-0">Catat transaksi penambahan (setoran) atau penarikan saldo pada target tabungan.</p>
                    </div>
                    <a href="list.php" class="btn btn-outline-secondary btn-sm">Riwayat Transaksi</a>
                </div>

                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                        <?php echo e($flash['pesan']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($daftarTarget)): ?>
                    <div class="alert alert-warning" role="alert">
                        ⚠️ Belum ada target tabungan. Silakan buat target terlebih dahulu.
                        <div class="mt-2">
                            <a href="../target/tambah.php" class="btn btn-sm btn-outline-warning">Buat Target Baru</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form id="form-tambah" method="post" action="proses_tambah.php">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="target_id" class="form-label fw-semibold">Pilih Target Tabungan</label>
                            <select id="target_id" name="target_id" class="form-select" required>
                                <option value="">-- Pilih Target --</option>
                                <?php foreach ($daftarTarget as $target): ?>
                                    <option value="<?php echo $target['id']; ?>" data-saldo="<?php echo $target['saldo_sekarang']; ?>">
                                        <?php echo e($target['nama']); ?> &mdash; Saldo: Rp <?php echo number_format($target['saldo_sekarang'], 0, ',', '.'); ?> / Target: Rp <?php echo number_format($target['target_nominal'], 0, ',', '.'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Jenis Transaksi</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="trx-setor" value="setor" checked>
                                <label class="form-check-label text-success fw-semibold" for="trx-setor">➕ Setor (Nabung)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_transaksi" id="trx-tarik" value="tarik">
                                <label class="form-check-label text-danger fw-semibold" for="trx-tarik">➖ Tarik Uang</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label fw-semibold">Nominal (Rp)</label>
                            <input type="number" id="jumlah" name="jumlah" class="form-control" min="1000" required placeholder="Contoh: 50000">
                            <div class="form-text text-secondary">Minimal Rp 1.000.</div>
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-semibold">Catatan / Keterangan</label>
                            <input type="text" id="catatan" name="catatan" class="form-control" placeholder="Contoh: Sisa uang jajan, Bonus proyek, Hadiah ulang tahun">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4" style="background-color: #9e0303; border-color: #9e0303;">
                                Simpan Transaksi
                            </button>
                            <a href="list.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
