<?php
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$nama = trim($_POST['nama'] ?? '');
$targetNominal = $_POST['target_nominal'] ?? '';
$saldoSekarang = $_POST['saldo_sekarang'] ?? 0;
$kategori = trim($_POST['kategori'] ?? '');
$deadline = $_POST['deadline'] ?? '';
$linkBarang = trim($_POST['link_barang'] ?? '');

// Validasi server-side
$errors = [];
if ($nama === '') {
    $errors[] = "Nama target wajib diisi.";
}
if (!is_numeric($targetNominal) || $targetNominal < 1000) {
    $errors[] = "Target nominal minimal Rp 1.000.";
}
if (!is_numeric($saldoSekarang) || $saldoSekarang < 0) {
    $errors[] = "Saldo awal tidak boleh negatif.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO target (nama, target_nominal, saldo_sekarang, kategori, deadline, link_barang)
     VALUES (:nama, :target_nominal, :saldo_sekarang, :kategori, :deadline, :link_barang)
     RETURNING id"
);
$stmt->execute([
    'nama' => $nama,
    'target_nominal' => (float) $targetNominal,
    'saldo_sekarang' => (float) $saldoSekarang,
    'kategori' => $kategori,
    'deadline' => $deadline ?: null,
    'link_barang' => $linkBarang ?: null,
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Target tabungan berhasil dibuat.'];
header('Location: list.php');
exit;
