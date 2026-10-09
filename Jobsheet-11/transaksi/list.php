<?php
$page_title = "Riwayat Transaksi";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare(
        "SELECT COUNT(*) FROM transaksi t
         JOIN target tg ON t.target_id = tg.id
         WHERE tg.nama ILIKE :kw OR CAST(t.id AS TEXT) ILIKE :kw"
    );
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT t.*, tg.nama as target_nama FROM transaksi t
         JOIN target tg ON t.target_id = tg.id
         WHERE tg.nama ILIKE :kw OR CAST(t.id AS TEXT) ILIKE :kw
         ORDER BY t.id DESC LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
    $stmt = $pdo->prepare(
        "SELECT t.*, tg.nama as target_nama FROM transaksi t
         JOIN target tg ON t.target_id = tg.id
         ORDER BY t.id DESC LIMIT :limit OFFSET :offset"
    );
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarTransaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Riwayat Setoran & Penarikan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <form method="get" action="list.php">
                    <span>
                        <label for="search-input">Cari Transaksi / Target</label><br>
                        <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Ketik ID transaksi atau nama target...">
                    </span>
                    <button type="submit">Cari</button>
                </form>
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Target</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarTransaksi)): ?>
                    <tr>
                        <td colspan="7">Tidak ada data transaksi yang cocok.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarTransaksi as $transaksi): ?>
                        <?php $isSetor = $transaksi['jenis_transaksi'] === 'setor'; ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($transaksi['id']); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($transaksi['tanggal_transaksi'])); ?></td>
                            <td><?php echo e($transaksi['target_nama']); ?></td>
                            <td>
                                <span style="color: <?php echo $isSetor ? '#059669' : '#dc2626'; ?>; font-weight: bold;">
                                    <?php echo $isSetor ? '➕ Setor' : '➖ Tarik'; ?>
                                </span>
                            </td>
                            <td style="color: <?php echo $isSetor ? '#059669' : '#dc2626'; ?>; font-weight: bold;">
                                <?php echo $isSetor ? '+' : '-'; ?> Rp <?php echo number_format($transaksi['jumlah'], 0, ',', '.'); ?>
                            </td>
                            <td><?php echo htmlspecialchars($transaksi['catatan'] ?? '-'); ?></td>
                            <td>
                                <form class="form-hapus" method="post" action="hapus.php">
                                    <input type="hidden" name="id" value="<?php echo $transaksi['id']; ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn-hapus">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>

            <nav class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
                   class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </nav>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
