<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$tanggal = trim($_POST['tanggal'] ?? '');
$produkId = trim($_POST['produk_id'] ?? '');
$jumlah = trim($_POST['jumlah'] ?? '');
$totalHarga = trim($_POST['total_harga'] ?? '');

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
if ($totalHarga === '') {
    $errors[] = "Total harga wajib diisi.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO penjualan (produk_id, jumlah, total_harga, tanggal_penjualan)
     VALUES (:produk_id, :jumlah, :total_harga, :tanggal)"
);
$stmt->execute([
    'produk_id' => $produkId,
    'jumlah' => $jumlah,
    'total_harga' => $totalHarga,
    'tanggal' => $tanggal,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Transaksi berhasil ditambahkan.'];
header('Location: list.php');
exit;
