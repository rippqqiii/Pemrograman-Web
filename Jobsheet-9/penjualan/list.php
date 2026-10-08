<?php
$page_title = "Riwayat Penjualan";
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
        "SELECT COUNT(*) FROM penjualan p
         JOIN produk pr ON p.produk_id = pr.id
         WHERE pr.judul ILIKE :kw OR CAST(p.id AS TEXT) ILIKE :kw"
    );
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT p.*, pr.judul FROM penjualan p
         JOIN produk pr ON p.produk_id = pr.id
         WHERE pr.judul ILIKE :kw OR CAST(p.id AS TEXT) ILIKE :kw
         ORDER BY p.id DESC LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM penjualan")->fetchColumn();
    $stmt = $pdo->prepare(
        "SELECT p.*, pr.judul FROM penjualan p
         JOIN produk pr ON p.produk_id = pr.id
         ORDER BY p.id DESC LIMIT :limit OFFSET :offset"
    );
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarPenjualan = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Riwayat Penjualan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <form method="get" action="list.php">
                    <span>
                        <label for="search-input">Cari Transaksi / Buku</label><br>
                        <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Ketik ID transaksi atau judul buku...">
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
                        <th>Buku</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarPenjualan)): ?>
                    <tr>
                        <td colspan="6">Tidak ada data transaksi yang cocok.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarPenjualan as $penjualan): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($penjualan['id']); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($penjualan['tanggal_penjualan'])); ?></td>
                            <td><?php echo htmlspecialchars($penjualan['judul']); ?></td>
                            <td><?php echo htmlspecialchars($penjualan['jumlah']); ?></td>
                            <td>Rp <?php echo number_format($penjualan['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $penjualan['id']; ?>" class="btn-edit">Edit</a>
                                <form class="form-hapus" method="post" action="hapus.php">
                                    <input type="hidden" name="id" value="<?php echo $penjualan['id']; ?>">
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
