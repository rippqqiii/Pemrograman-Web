<?php
$page_title = "Riwayat Penjualan";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarPenjualan = $pdo->query("SELECT p.*, pr.judul FROM penjualan p JOIN produk pr ON p.produk_id = pr.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Riwayat Penjualan</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <label for="search-input">Cari Transaksi</label>
                <input type="text" id="search-input" placeholder="Ketik ID transaksi atau judul buku...">
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
                        <td colspan="6">Belum ada data penjualan. Silakan buat transaksi lewat menu "Buat Transaksi".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarPenjualan as $penjualan): ?>
                        <tr>
                            <td><?php echo $penjualan['id']; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($penjualan['tanggal_penjualan'])); ?></td>
                            <td><?php echo $penjualan['judul']; ?></td>
                            <td><?php echo $penjualan['jumlah']; ?></td>
                            <td>Rp <?php echo number_format($penjualan['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <button type="button">Edit</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
