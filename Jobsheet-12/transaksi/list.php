<?php
$page_title = "Riwayat Transaksi";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 10;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare(
        "SELECT COUNT(*) FROM transaksi t
         JOIN target tg ON t.target_id = tg.id
         WHERE tg.nama ILIKE :kw OR CAST(t.id AS TEXT) ILIKE :kw OR t.catatan ILIKE :kw"
    );
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = (int) $hitung->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT t.*, tg.nama as target_nama FROM transaksi t
         JOIN target tg ON t.target_id = tg.id
         WHERE tg.nama ILIKE :kw OR CAST(t.id AS TEXT) ILIKE :kw OR t.catatan ILIKE :kw
         ORDER BY t.tanggal_transaksi DESC, t.id DESC LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = (int) $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
    $stmt = $pdo->prepare(
        "SELECT t.*, tg.nama as target_nama FROM transaksi t
         JOIN target tg ON t.target_id = tg.id
         ORDER BY t.tanggal_transaksi DESC, t.id DESC LIMIT :limit OFFSET :offset"
    );
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarTransaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>

<section class="card shadow-sm border-0 mb-4" style="background-color: #241111; border: 1px solid #5c1a1a !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #5c1a1a !important;">
            <div>
                <h2 class="h4 mb-1" style="color: #ff6b6b;">💳 Riwayat Seluruh Transaksi</h2>
                <p class="text-secondary small mb-0">Semua aktivitas arus kas masuk (setoran), arus kas keluar (penarikan), dan pemindahan saldo.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                <a href="riwayat.php" class="btn btn-outline-info btn-sm">Filter per Target</a>
                <?php if ($sudahLogin): ?>
                    <a href="tambah.php" class="btn btn-danger btn-sm" style="background-color: #9e0303; border-color: #9e0303;">➕ Setor / Tarik</a>
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
                    <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari ID, nama target, atau catatan...">
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
                        <th>ID</th>
                        <th>Waktu Transaksi</th>
                        <th>Target</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Catatan</th>
                        <?php if ($sudahLogin): ?>
                        <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarTransaksi)): ?>
                        <tr>
                            <td colspan="<?php echo $sudahLogin ? '7' : '6'; ?>" class="text-center py-4 text-secondary">
                                Tidak ada data transaksi yang cocok.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($daftarTransaksi as $transaksi): ?>
                            <?php $isSetor = $transaksi['jenis_transaksi'] === 'setor'; ?>
                            <tr style="border-bottom: 1px solid #3b1212;">
                                <td>#<?php echo htmlspecialchars($transaksi['id']); ?></td>
                                <td><?php echo date('d-m-Y H:i', strtotime($transaksi['tanggal_transaksi'])); ?></td>
                                <td>
                                    <a href="riwayat.php?target_id=<?php echo $transaksi['target_id']; ?>" class="text-decoration-none fw-semibold" style="color: #ff8585;">
                                        <?php echo e($transaksi['target_nama']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge <?php echo $isSetor ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $isSetor ? '➕ Setor' : '➖ Tarik'; ?>
                                    </span>
                                </td>
                                <td class="fw-bold <?php echo $isSetor ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $isSetor ? '+' : '-'; ?> Rp <?php echo number_format($transaksi['jumlah'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-secondary small"><?php echo htmlspecialchars($transaksi['catatan'] ?: '-'); ?></td>
                                <?php if ($sudahLogin): ?>
                                <td>
                                    <form class="d-inline" method="post" action="hapus.php" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi #<?php echo $transaksi['id']; ?>? Saldo target akan disesuaikan kembali.');">
                                        <input type="hidden" name="id" value="<?php echo $transaksi['id']; ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Batalkan & Reversal Transaksi">Batal</button>
                                    </form>
                                </td>
                                <?php endif; ?>
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
