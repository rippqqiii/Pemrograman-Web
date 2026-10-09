<?php
$page_title = "Daftar Target";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM target WHERE nama ILIKE :kw OR kategori ILIKE :kw");
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM target WHERE nama ILIKE :kw OR kategori ILIKE :kw ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM target")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM target ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftarTarget = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
?>
        <section>
            <h2>Daftar Target Tabungan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <form method="get" action="list.php">
                    <span>
                        <label for="search-input">Cari Nama Target / Kategori</label><br>
                        <input type="text" id="search-input" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Ketik nama target atau kategori...">
                    </span>
                    <button type="submit">Cari</button>
                </form>
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Target</th>
                        <th>Kategori</th>
                        <th>Target</th>
                        <th>Saldo</th>
                        <th>Progress</th>
                        <th>Deadline</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarTarget)): ?>
                    <tr>
                        <td colspan="7">Tidak ada data target yang cocok.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarTarget as $target): ?>
                        <?php 
                            $percent = $target['target_nominal'] > 0 ? min(100, round(($target['saldo_sekarang'] / $target['target_nominal']) * 100)) : 0;
                            $isComplete = $target['saldo_sekarang'] >= $target['target_nominal'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($target['nama']); ?></td>
                            <td><?php echo htmlspecialchars($target['kategori'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($target['target_nominal'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($target['saldo_sekarang'], 0, ',', '.'); ?></td>
                            <td>
                                <div style="background-color: #2d1515; border-radius: 4px; overflow: hidden;">
                                    <div style="background-color: <?php echo $isComplete ? '#059669' : '#9e0303'; ?>; height: 20px; width: <?php echo $percent; ?>%;"></div>
                                </div>
                                <small><?php echo $percent; ?>%</small>
                            </td>
                            <td><?php echo $target['deadline'] ? date('d-m-Y', strtotime($target['deadline'])) : '-'; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $target['id']; ?>" class="btn-edit">Edit</a>
                                <form class="form-hapus" method="post" action="hapus.php">
                                    <input type="hidden" name="id" value="<?php echo $target['id']; ?>">
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
