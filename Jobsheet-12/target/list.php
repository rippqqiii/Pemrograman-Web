<?php
$page_title = "Daftar Target";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM target WHERE nama ILIKE :kw OR kategori ILIKE :kw");
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = (int) $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM target WHERE nama ILIKE :kw OR kategori ILIKE :kw ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = (int) $pdo->query("SELECT COUNT(*) FROM target")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM target ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarTarget = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>

<section class="card shadow-sm border-0 mb-4" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #5c1a1a !important;">
            <div>
                <h2 class="h4 mb-1" style="color: #ff6b6b;">🎯 Daftar Target Tabungan</h2>
                <p class="text-secondary small mb-0">Kelola impian finansial, pantau persentase capaian saldo, dan riwayat mutasi dana.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                <?php if ($sudahLogin): ?>
                    <a href="tambah.php" class="btn btn-danger btn-sm" style="background-color: #9e0303; border-color: #9e0303;">+ Buat Target Baru</a>
                    <a href="../pindah_saldo/tambah.php" class="btn btn-outline-danger btn-sm" style="border-color: #ff6b6b; color: #ff6b6b;">🔄 Pindah Saldo</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                <?php echo e($flash['pesan']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Search Box -->
        <div class="mb-3">
            <form method="get" action="list.php" class="row g-2">
                <div class="col-12 col-md-5">
                    <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari nama target atau kategori...">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                    <?php if ($keyword !== ''): ?>
                        <a href="list.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                <thead>
                    <tr style="border-bottom: 2px solid #5c1a1a;">
                        <th>Nama Target</th>
                        <th>Kategori</th>
                        <th>Target Dana</th>
                        <th>Saldo Terkumpul</th>
                        <th style="min-width: 140px;">Progres</th>
                        <th>Tenggat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarTarget)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">
                                Tidak ada data target yang cocok.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftarTarget as $target): ?>
                        <?php 
                            $percent = $target['target_nominal'] > 0 ? min(100, round(($target['saldo_sekarang'] / $target['target_nominal']) * 100)) : 0;
                            $isComplete = $target['saldo_sekarang'] >= $target['target_nominal'] && $target['target_nominal'] > 0;
                        ?>
                        <tr style="border-bottom: 1px solid #3b1212;">
                            <td>
                                <a href="../transaksi/riwayat.php?target_id=<?php echo $target['id']; ?>" class="fw-semibold text-decoration-none" style="color: #ff8585;">
                                    <?php echo htmlspecialchars($target['nama']); ?>
                                </a>
                                <?php if (!empty($target['link_barang'])): ?>
                                    <a href="<?php echo htmlspecialchars($target['link_barang']); ?>" target="_blank" rel="noopener noreferrer" class="ms-1 small text-info text-decoration-none" title="Buka tautan barang">🔗</a>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($target['kategori'] ?? 'Umum'); ?></span></td>
                            <td class="text-white">Rp <?php echo number_format($target['target_nominal'], 0, ',', '.'); ?></td>
                            <td class="fw-bold text-success">Rp <?php echo number_format($target['saldo_sekarang'], 0, ',', '.'); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 10px; background-color: #241111;">
                                        <div class="progress-bar <?php echo $isComplete ? 'bg-success' : 'bg-danger'; ?>" 
                                             role="progressbar" 
                                             style="width: <?php echo $percent; ?>%;" 
                                             aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="<?php echo $isComplete ? 'text-success fw-bold' : 'text-secondary'; ?>"><?php echo $percent; ?>%</small>
                                </div>
                            </td>
                            <td class="small text-secondary"><?php echo $target['deadline'] ? date('d-m-Y', strtotime($target['deadline'])) : '-'; ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="../transaksi/riwayat.php?target_id=<?php echo $target['id']; ?>" class="btn btn-outline-info" title="Lihat Riwayat Transaksi">Riwayat</a>
                                    <?php if ($sudahLogin): ?>
                                        <form class="d-inline" method="post" action="hapus.php" onsubmit="return confirm('Apakah Anda yakin ingin menghapus target <?php echo addslashes($target['nama']); ?>? Target hanya bisa dihapus jika saldonya Rp 0.');">
                                            <input type="hidden" name="id" value="<?php echo $target['id']; ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-outline-danger ms-1">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination pagination-sm justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                           style="<?php echo $i === $page ? 'background-color: #9e0303; border-color: #9e0303;' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
