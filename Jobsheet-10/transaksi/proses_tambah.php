<?php
require __DIR__ . '/../includes/koneksi.php';

$targetId = trim($_POST['target_id'] ?? '');
$jenisTransaksi = trim($_POST['jenis_transaksi'] ?? '');
$jumlah = trim($_POST['jumlah'] ?? '');
$catatan = trim($_POST['catatan'] ?? '');

$errors = [];
if ($targetId === '') {
    $errors[] = "Target tabungan wajib dipilih.";
}
if ($jenisTransaksi === '' || !in_array($jenisTransaksi, ['setor', 'tarik'])) {
    $errors[] = "Jenis transaksi tidak valid.";
}
if (!is_numeric($jumlah) || $jumlah < 1000) {
    $errors[] = "Nominal minimal Rp 1.000.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

// Get target data
$stmt = $pdo->prepare("SELECT * FROM target WHERE id = :id");
$stmt->execute(['id' => $targetId]);
$target = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$target) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Target tidak ditemukan.'];
    header('Location: tambah.php');
    exit;
}

// Check balance for withdrawal
if ($jenisTransaksi === 'tarik' && $jumlah > $target['saldo_sekarang']) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($target['saldo_sekarang'], 0, ',', '.')];
    header('Location: tambah.php');
    exit;
}

// Update target balance
if ($jenisTransaksi === 'setor') {
    $newSaldo = $target['saldo_sekarang'] + $jumlah;
} else {
    $newSaldo = $target['saldo_sekarang'] - $jumlah;
}

$pdo->beginTransaction();
try {
    // Update target saldo
    $stmt = $pdo->prepare("UPDATE target SET saldo_sekarang = :saldo WHERE id = :id");
    $stmt->execute(['saldo' => $newSaldo, 'id' => $targetId]);

    // Insert transaction
    $stmt = $pdo->prepare(
        "INSERT INTO transaksi (target_id, jenis_transaksi, jumlah, catatan)
         VALUES (:target_id, :jenis_transaksi, :jumlah, :catatan)"
    );
    $stmt->execute([
        'target_id' => $targetId,
        'jenis_transaksi' => $jenisTransaksi,
        'jumlah' => (float) $jumlah,
        'catatan' => $catatan,
    ]);

    $pdo->commit();
    
    $pesan = $jenisTransaksi === 'setor' 
        ? 'Berhasil menabung Rp ' . number_format($jumlah, 0, ',', '.') 
        : 'Berhasil menarik Rp ' . number_format($jumlah, 0, ',', '.');
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => $pesan];
    header('Location: list.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
