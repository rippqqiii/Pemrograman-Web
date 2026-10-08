<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = $_POST['id'] ?? null;
$tanggal = trim($_POST['tanggal'] ?? '');
$produkId = trim($_POST['produk_id'] ?? '');
$jumlah = trim($_POST['jumlah'] ?? '');
$totalHarga = trim($_POST['total_harga'] ?? '');

if (!$id) {
    header('Location: list.php');
    exit;
}

$errors = [];
if ($tanggal === '') {
    $errors[] = "Tanggal wajib diisi.";
}
if ($produkId === '') {
    $errors[] = "Buku wajib dipilih.";
}
if ($jumlah === '' || $jumlah < 1) {
    $errors[] = "Jumlah wajib diisi dan minimal 1.";
}
if ($totalHarga === '' || $totalHarga < 0) {
    $errors[] = "Total harga wajib diisi dan tidak boleh negatif.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: edit.php?id=' . urlencode($id));
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE penjualan SET produk_id = :produk_id, jumlah = :jumlah,
     total_harga = :total_harga, tanggal_penjualan = :tanggal WHERE id = :id"
);
$stmt->execute([
    'produk_id' => $produkId,
    'jumlah' => (int) $jumlah,
    'total_harga' => (float) $totalHarga,
    'tanggal' => $tanggal,
    'id' => $id,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Transaksi berhasil diperbarui.'];
header('Location: list.php');
exit;
