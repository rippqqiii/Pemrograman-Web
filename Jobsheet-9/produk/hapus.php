<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

// Sengaja hanya menerima POST (bukan GET) agar penghapusan tidak bisa
// dipicu tanpa sengaja lewat link/preview crawler.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

$id = $_POST['id'] ?? null;
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil dihapus.'];
    } catch (PDOException $e) {
        // Jika ada relasi foreign key ke tabel penjualan
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Buku gagal dihapus karena sudah memiliki riwayat transaksi penjualan.'];
    }
}

header('Location: list.php');
exit;
