<?php
$page_title = "Riwayat Transaksi per Target";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$targetId = (int) ($_GET['target_id'] ?? 0);
$daftarTarget = $pdo->query("SELECT * FROM target ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);

$targetTerpilih = null;
$riwayat = [];
$totalSetor = 0;
$totalTarik = 0;

if ($targetId > 0) {
    $stmtT = $pdo->prepare("SELECT * FROM target WHERE id = :id");
    $stmtT->execute(['id' => $targetId]);
    $targetTerpilih = $stmtT->fetch(PDO::FETCH_ASSOC);

    if ($targetTerpilih) {
        $stmtR = $pdo->prepare(
            "SELECT t.*, tg.nama AS target_nama 
             FROM transaksi t
             JOIN target tg ON tg.id = t.target_id
             WHERE t.target_id = :id
             ORDER BY t.tanggal_transaksi DESC, t.id DESC"
        );
        $stmtR->execute(['id' => $targetId]);
        $riwayat = $stmtR->fetchAll(PDO::FETCH_ASSOC);

        foreach ($riwayat as $r) {
            if ($r['jenis_transaksi'] === 'setor') {
                $totalSetor += (float) $r['jumlah'];
            } else {
                $totalTarik += (float) $r['jumlah'];
            }
        }
    }
}
?>

<section class="card shadow-sm border-0 mb-4" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #5c1a1a !important;">
            <div>
                <h2 class="h4 mb-1" style="color: #ff6b6b;">📜 Riwayat Transaksi per Target</h2>
                <p class="text-secondary small mb-0">Lihat histori seluruh arus kas (setor, tarik, pindah saldo) untuk target tabungan tertentu.</p>
            </div>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <a href="list.php" class="btn btn-outline-secondary btn-sm">Semua Transaksi</a>
                <?php if ($sudahLogin): ?>
                    <a href="tambah.php" class="btn btn-danger btn-sm" style="background-color: #9e0303; border-color: #9e0303;">+ Transaksi Baru</a>
                <?php endif; ?>
            </div>
        </div>

        <form method="get" action="riwayat.php" class="row g-2 align-items-end mb-4">
            <div class="col-12 col-md-6 col-lg-5">
                <label for="target_id" class="form-label fw-semibold">Pilih Target Tabungan</label>
                <select id="target_id" name="target_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Target Tabungan --</option>
                    <?php foreach ($daftarTarget as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php echo $targetId === (int) $t['id'] ? 'selected' : ''; ?>>
                            <?php echo e($t['nama']); ?> (Saldo: Rp <?php echo number_format($t['saldo_sekarang'], 0, ',', '.'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-danger px-4" style="background-color: #9e0303; border-color: #9e0303;">Tampilkan</button>
            </div>
        </form>

        <?php if ($targetTerpilih): ?>
            <?php 
            $percent = $targetTerpilih['target_nominal'] > 0 
                ? min(100, round(($targetTerpilih['saldo_sekarang'] / $targetTerpilih['target_nominal']) * 100)) 
                : 0;
            ?>
            <div class="card p-3 mb-4 border-0" style="background-color: #3b1212; border: 1px solid #751a1a !important;">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                    <h3 class="h5 mb-0 text-white">🎯 <?php echo e($targetTerpilih['nama']); ?></h3>
                    <span class="badge bg-secondary"><?php echo e($targetTerpilih['kategori'] ?? 'Umum'); ?></span>
                </div>
                <div class="row g-3 text-center my-2">
                    <div class="col-6 col-md-3">
                        <small class="text-secondary d-block">Saldo Saat Ini</small>
                        <span class="fs-5 fw-bold" style="color: #4ade80;">Rp <?php echo number_format($targetTerpilih['saldo_sekarang'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-secondary d-block">Target Nominal</small>
                        <span class="fs-5 fw-bold" style="color: #ff6b6b;">Rp <?php echo number_format($targetTerpilih['target_nominal'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-secondary d-block">Total Disetor</small>
                        <span class="fs-5 fw-bold text-info">+ Rp <?php echo number_format($totalSetor, 0, ',', '.'); ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-secondary d-block">Total Ditarik</small>
                        <span class="fs-5 fw-bold text-warning">- Rp <?php echo number_format($totalTarik, 0, ',', '.'); ?></span>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="d-flex justify-content-between text-secondary small mb-1">
                        <span>Pencapaian: <?php echo $percent; ?>%</span>
                        <?php if (!empty($targetTerpilih['deadline'])): ?>
                            <span>Tenggat: <?php echo date('d-m-Y', strtotime($targetTerpilih['deadline'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="progress" style="height: 12px; background-color: #241111;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent; ?>%;" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                    <thead>
                        <tr style="border-bottom: 2px solid #5c1a1a;">
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">
                                    Belum ada transaksi untuk target tabungan ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $r): ?>
                                <?php $isSetor = $r['jenis_transaksi'] === 'setor'; ?>
                                <tr style="border-bottom: 1px solid #3b1212;">
                                    <td>#<?php echo htmlspecialchars($r['id']); ?></td>
                                    <td><?php echo date('d-m-Y H:i', strtotime($r['tanggal_transaksi'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $isSetor ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $isSetor ? '➕ Setor' : '➖ Tarik'; ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold <?php echo $isSetor ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $isSetor ? '+' : '-'; ?> Rp <?php echo number_format($r['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="text-secondary"><?php echo e($r['catatan'] ?: '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($targetId > 0): ?>
            <div class="alert alert-danger" role="alert">
                Target yang dipilih tidak ditemukan.
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                Silakan pilih salah satu target tabungan di atas untuk melihat riwayat transaksinya.
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
