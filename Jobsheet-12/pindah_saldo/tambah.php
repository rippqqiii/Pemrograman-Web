<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Pindah Saldo Antar Target";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Ambil target yang memiliki saldo > 0 untuk target asal (analog dengan buku stok > 0 pada repositori acuan)
$targetAsalList = $pdo->query("SELECT * FROM target WHERE saldo_sekarang > 0 ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua target untuk target tujuan
$targetSemuaList = $pdo->query("SELECT * FROM target ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <section class="card shadow-sm border-0 mb-4" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #5c1a1a !important;">
                    <div>
                        <h2 class="h4 mb-1" style="color: #ff6b6b;">🔄 Pindah Saldo Antar Target</h2>
                        <p class="text-secondary small mb-0">Pindahkan saldo dari satu target tabungan ke target tabungan lain secara aman dan atomik.</p>
                    </div>
                    <a href="<?php echo $base; ?>transaksi/list.php" class="btn btn-outline-secondary btn-sm">Lihat Transaksi</a>
                </div>

                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                        <?php echo e($flash['pesan']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($targetSemuaList) || count($targetSemuaList) < 2): ?>
                    <div class="alert alert-warning" role="alert">
                        ⚠️ Minimal harus ada <strong>2 target tabungan</strong> untuk dapat melakukan pemindahan saldo.
                        <div class="mt-2">
                            <a href="<?php echo $base; ?>target/tambah.php" class="btn btn-sm btn-outline-warning">Buat Target Baru</a>
                        </div>
                    </div>
                <?php elseif (empty($targetAsalList)): ?>
                    <div class="alert alert-warning" role="alert">
                        ⚠️ Belum ada target tabungan yang memiliki saldo (semua saldo masih Rp 0). 
                        Silakan lakukan setoran terlebih dahulu ke salah satu target.
                        <div class="mt-2">
                            <a href="<?php echo $base; ?>transaksi/tambah.php" class="btn btn-sm btn-outline-warning">Setor Saldo Sekarang</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="post" action="proses_tambah.php" id="form-pindah-saldo">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="target_asal_id" class="form-label fw-semibold">Target Asal (Sumber Dana)</label>
                            <select id="target_asal_id" name="target_asal_id" class="form-select" required>
                                <option value="">-- Pilih Target Asal --</option>
                                <?php foreach ($targetAsalList as $t): ?>
                                    <option value="<?php echo $t['id']; ?>" data-saldo="<?php echo $t['saldo_sekarang']; ?>">
                                        <?php echo e($t['nama']); ?> &mdash; Saldo Tersedia: Rp <?php echo number_format($t['saldo_sekarang'], 0, ',', '.'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-secondary">Hanya target dengan saldo &gt; 0 yang dapat dipilih sebagai sumber dana.</div>
                        </div>

                        <div class="mb-3">
                            <label for="target_tujuan_id" class="form-label fw-semibold">Target Tujuan (Penerima Dana)</label>
                            <select id="target_tujuan_id" name="target_tujuan_id" class="form-select" required>
                                <option value="">-- Pilih Target Tujuan --</option>
                                <?php foreach ($targetSemuaList as $t): ?>
                                    <option value="<?php echo $t['id']; ?>">
                                        <?php echo e($t['nama']); ?> &mdash; Saldo Saat Ini: Rp <?php echo number_format($t['saldo_sekarang'], 0, ',', '.'); ?> (Target: Rp <?php echo number_format($t['target_nominal'], 0, ',', '.'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-secondary">Target tujuan tidak boleh sama dengan target asal.</div>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label fw-semibold">Nominal Pemindahan (Rp)</label>
                            <input type="number" id="jumlah" name="jumlah" class="form-control" min="1000" placeholder="Contoh: 100000" required>
                            <div class="form-text text-secondary">Minimal Rp 1.000 dan tidak boleh melebihi saldo target asal.</div>
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-semibold">Catatan / Alasan Pemindahan</label>
                            <input type="text" id="catatan" name="catatan" class="form-control" placeholder="Contoh: Realokasi sisa dana, Pengalihan prioritas belanja">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger px-4" style="background-color: #9e0303; border-color: #9e0303;">
                                🚀 Proses Pindah Saldo
                            </button>
                            <a href="<?php echo $base; ?>index.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                    <script>
                    document.getElementById('form-pindah-saldo')?.addEventListener('submit', function (e) {
                        const asal = document.getElementById('target_asal_id');
                        const tujuan = document.getElementById('target_tujuan_id');
                        const jumlah = parseFloat(document.getElementById('jumlah').value || 0);

                        if (asal.value === tujuan.value) {
                            e.preventDefault();
                            alert('Target asal dan target tujuan tidak boleh sama!');
                            tujuan.focus();
                            return false;
                        }

                        const selectedOption = asal.options[asal.selectedIndex];
                        const saldoAsal = parseFloat(selectedOption.getAttribute('data-saldo') || 0);
                        if (jumlah > saldoAsal) {
                            e.preventDefault();
                            alert('Nominal yang dipindahkan melebihi saldo target asal (Rp ' + saldoAsal.toLocaleString('id-ID') + ')!');
                            document.getElementById('jumlah').focus();
                            return false;
                        }
                    });
                    </script>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
