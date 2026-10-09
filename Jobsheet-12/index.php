<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$totalTarget = (int) $pdo->query("SELECT COUNT(*) FROM target")->fetchColumn();
$totalSaldo = (float) $pdo->query("SELECT COALESCE(SUM(saldo_sekarang), 0) FROM target")->fetchColumn();
$totalTargetNominal = (float) $pdo->query("SELECT COALESCE(SUM(target_nominal), 0) FROM target")->fetchColumn();
$totalTransaksi = (int) $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
$targetTercapai = (int) $pdo->query("SELECT COUNT(*) FROM target WHERE saldo_sekarang >= target_nominal AND target_nominal > 0")->fetchColumn();

$sisaKebutuhan = max(0, $totalTargetNominal - $totalSaldo);
$percent = $totalTargetNominal > 0 ? min(100, round(($totalSaldo / $totalTargetNominal) * 100)) : 0;

// Ambil 5 transaksi terbaru untuk ringkasan integrasi dashboard
$transaksiTerbaru = $pdo->query(
    "SELECT t.*, tg.nama AS target_nama 
     FROM transaksi t
     JOIN target tg ON tg.id = t.target_id
     ORDER BY t.tanggal_transaksi DESC, t.id DESC 
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
body {
    background-color: #1a0a0a;
}
</style>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show mb-4" role="alert">
        <?php echo e($flash['pesan']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- KOTAK SELAMAT DATANG -->
<section class="card shadow-sm mb-4 border-0" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
    <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h2 class="card-title h3 mb-1" style="color: #ff6b6b;">Selamat Datang di TabunganKu</h2>
            <p class="card-text text-secondary mb-0">Aplikasi pengelolaan target tabungan, pencatatan transaksi, dan pemindahan saldo terintegrasi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($sudahLogin): ?>
                <a href="<?php echo $base; ?>transaksi/tambah.php" class="btn btn-sm btn-danger" style="background-color: #9e0303; border-color: #9e0303;">➕ Setor / Tarik</a>
                <a href="<?php echo $base; ?>pindah_saldo/tambah.php" class="btn btn-sm btn-outline-danger" style="border-color: #ff6b6b; color: #ff6b6b;">🔄 Pindah Saldo</a>
                <a href="<?php echo $base; ?>target/tambah.php" class="btn btn-sm btn-outline-secondary">+ Buat Target</a>
            <?php else: ?>
                <a href="<?php echo $base; ?>auth/login.php" class="btn btn-sm btn-danger" style="background-color: #9e0303; border-color: #9e0303;">🔐 Login Petugas</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- KOTAK RINGKASAN UTAMA -->
<section class="card shadow-sm mb-4 border-0" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
    <div class="card-body p-4">
        <h2 class="card-title h4 mb-3" style="color: #ff6b6b;">Ringkasan Keuangan</h2>
        <div class="row g-3 text-center">

            <!-- KARTU 1: TOTAL SALDO TERKUMPUL -->
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 h-100" style="background-color: #3b1212; border: 1px solid #5c1a1a;">
                    <h3 class="h6 text-secondary mb-1">💰 Total Saldo Terkumpul</h3>
                    <p class="fs-3 fw-bold mb-0 text-success">Rp <?php echo number_format($totalSaldo, 0, ',', '.'); ?></p>
                </div>
            </div>

            <!-- KARTU 2: TOTAL TARGET DANA -->
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 h-100" style="background-color: #3b1212; border: 1px solid #5c1a1a;">
                    <h3 class="h6 text-secondary mb-1">🎯 Total Target Dana</h3>
                    <p class="fs-3 fw-bold mb-0" style="color: #ff6b6b;">Rp <?php echo number_format($totalTargetNominal, 0, ',', '.'); ?></p>
                </div>
            </div>

            <!-- KARTU 3: SISA KEBUTUHAN -->
            <div class="col-12 col-md-4">
                <div class="p-3 rounded-3 h-100" style="background-color: #3b1212; border: 1px solid #5c1a1a;">
                    <h3 class="h6 text-secondary mb-1">⏳ Sisa Kebutuhan</h3>
                    <p class="fs-3 fw-bold mb-0 text-warning">Rp <?php echo number_format($sisaKebutuhan, 0, ',', '.'); ?></p>
                </div>
            </div>

        </div>

        <!-- STATISTIK TARGET & TRANSAKSI -->
        <div class="row g-3 text-center mt-2">
            <div class="col-6 col-md-4">
                <div class="p-2 rounded-2" style="background-color: #2d1515; border: 1px solid #4a1515;">
                    <small class="text-secondary d-block">Jumlah Target</small>
                    <span class="fw-bold fs-5 text-white"><?php echo $totalTarget; ?> Target</span>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="p-2 rounded-2" style="background-color: #2d1515; border: 1px solid #4a1515;">
                    <small class="text-secondary d-block">Target Tercapai</small>
                    <span class="fw-bold fs-5 text-success"><?php echo $targetTercapai; ?> Selesai</span>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-2 rounded-2" style="background-color: #2d1515; border: 1px solid #4a1515;">
                    <small class="text-secondary d-block">Total Mutasi Transaksi</small>
                    <span class="fw-bold fs-5 text-info"><?php echo $totalTransaksi; ?> Transaksi</span>
                </div>
            </div>
        </div>
        
        <!-- PROGRESS BAR GLOBAL -->
        <div class="mt-4">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-secondary small">Progres Keseluruhan Tabungan</span>
                <span class="text-secondary small fw-semibold"><?php echo $percent; ?>% Tercapai</span>
            </div>
            <div class="progress" style="height: 20px; background-color: #1a0a0a;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $percent; ?>%; background-color: #9e0303;" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo $percent; ?>%
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KOTAK 5 TRANSAKSI TERBARU -->
<section class="card shadow-sm mb-4 border-0" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #5c1a1a !important;">
            <h3 class="h5 mb-0" style="color: #ff6b6b;">⚡ Transaksi Terbaru</h3>
            <div class="d-flex gap-2">
                <a href="<?php echo $base; ?>transaksi/list.php" class="btn btn-outline-secondary btn-sm">Lihat Semua</a>
                <a href="<?php echo $base; ?>transaksi/riwayat.php" class="btn btn-outline-danger btn-sm" style="border-color: #ff6b6b; color: #ff6b6b;">Filter per Target</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                <thead>
                    <tr style="border-bottom: 2px solid #5c1a1a;">
                        <th>Waktu</th>
                        <th>Target</th>
                        <th>Jenis</th>
                        <th>Nominal</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksiTerbaru)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transaksiTerbaru as $t): ?>
                            <?php $isSetor = $t['jenis_transaksi'] === 'setor'; ?>
                            <tr style="border-bottom: 1px solid #3b1212;">
                                <td><?php echo date('d-m-Y H:i', strtotime($t['tanggal_transaksi'])); ?></td>
                                <td><?php echo e($t['target_nama']); ?></td>
                                <td>
                                    <span class="badge <?php echo $isSetor ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $isSetor ? '➕ Setor' : '➖ Tarik'; ?>
                                    </span>
                                </td>
                                <td class="fw-bold <?php echo $isSetor ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $isSetor ? '+' : '-'; ?> Rp <?php echo number_format($t['jumlah'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-secondary"><?php echo e($t['catatan'] ?: '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
