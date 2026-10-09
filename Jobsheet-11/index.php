<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$totalTarget = $pdo->query("SELECT COUNT(*) FROM target")->fetchColumn();
$totalSaldo = $pdo->query("SELECT COALESCE(SUM(saldo_sekarang), 0) FROM target")->fetchColumn();
$totalTargetNominal = $pdo->query("SELECT COALESCE(SUM(target_nominal), 0) FROM target")->fetchColumn();
$sisaKebutuhan = max(0, $totalTargetNominal - $totalSaldo);
$percent = $totalTargetNominal > 0 ? min(100, round(($totalSaldo / $totalTargetNominal) * 100)) : 0;
?>

<style>
body {
    background-color: #1a0a0a;
}
</style>

<!-- KOTAK SELAMAT DATANG -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title" style="color:#c41e3a;">Selamat Datang di TabunganKu</h2>
                <p class="card-text mb-0">Aplikasi pengelolaan target tabungan dan pencatatan transaksi setoran/penarikan berbasis web.</p>
            </div>
        </section>

<!-- KOTAK RINGKASAN -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="card-title mb-3" style="color:#c41e3a;">Ringkasan Tabungan</h2>
                <div class="row text-center">

                <!-- KARTU 1: TOTAL SALDO TERKUMPUL -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#5c1a1a; border: 1px solid #5c1a1a;">
                            <h3 class="h6 text-secondary">� Total Saldo Terkumpul</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#c41e3a;">Rp <?php echo number_format($totalSaldo, 0, ',', '.'); ?></p>
                        </div>
                    </div>

                <!-- KARTU 2: TOTAL TARGET -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#5c1a1a; border: 1px solid #5c1a1a;">
                            <h3 class="h6 text-secondary">🎯 Total Target</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#c41e3a;">Rp <?php echo number_format($totalTargetNominal, 0, ',', '.'); ?></p>
                        </div>
                    </div>

                <!-- KARTU 3: SISA KEBUTUHAN -->
                    <div class="col-12 col-md-4">
                        <div class="p-3 rounded-3" style="background-color:#5c1a1a; border: 1px solid #5c1a1a;">
                            <h3 class="h6 text-secondary">⏳ Sisa Kebutuhan</h3>
                            <p class="fs-2 fw-bold mb-0" style="color:#c41e3a;">Rp <?php echo number_format($sisaKebutuhan, 0, ',', '.'); ?></p>
                        </div>
                    </div>

                </div>
                
                <!-- PROGRESS BAR GLOBAL -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-secondary">Progres Keseluruhan</span>
                        <span class="text-secondary"><?php echo $percent; ?>% Tercapai</span>
                    </div>
                    <div class="progress" style="height: 25px; background-color: #2d1515;">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo $percent; ?>%; background-color: #9e0303;" aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo $percent; ?>%
                        </div>
                    </div>
                </div>
            </div>
        </section>
<?php include __DIR__ . '/includes/footer.php'; ?>
