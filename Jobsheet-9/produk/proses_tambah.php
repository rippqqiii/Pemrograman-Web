<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun = $_POST['tahun'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stok = $_POST['stok'] ?? '';
$harga = $_POST['harga'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');

// Validasi server-side
$errors = [];
if ($judul === '') {
    $errors[] = "Judul wajib diisi.";
}
if ($pengarang === '') {
    $errors[] = "Pengarang wajib diisi.";
}
if (!is_numeric($tahun) || $tahun < 1900 || $tahun > 2026) {
    $errors[] = "Tahun harus di antara 1900-2026.";
}
if (!is_numeric($stok) || $stok < 0) {
    $errors[] = "Stok tidak boleh negatif.";
}
if (!is_numeric($harga) || $harga < 0) {
    $errors[] = "Harga tidak boleh negatif.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO produk (judul, pengarang, tahun, isbn, stok, harga, kategori)
     VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :harga, :kategori)
     RETURNING id"
);
$stmt->execute([
    'judul' => $judul,
    'pengarang' => $pengarang,
    'tahun' => (int) $tahun,
    'isbn' => $isbn,
    'stok' => (int) $stok,
    'harga' => (float) $harga,
    'kategori' => $kategori,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil ditambahkan.'];
header('Location: list.php');
exit;
