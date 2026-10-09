<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

csrf_verify();

$targetId = (int) ($_POST['target_id'] ?? 0);
$jenisTransaksi = trim($_POST['jenis_transaksi'] ?? '');
$jumlah = (float) ($_POST['jumlah'] ?? 0);
$catatan = trim($_POST['catatan'] ?? '');

$errors = [];
if ($targetId <= 0) {
    $errors[] = "Target tabungan wajib dipilih.";
}
if ($jenisTransaksi === '' || !in_array($jenisTransaksi, ['setor', 'tarik'])) {
    $errors[] = "Jenis transaksi tidak valid.";
}
if ($jumlah < 1000) {
    $errors[] = "Nominal transaksi minimal Rp 1.000.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    // Mulai transaksi atomik
    $pdo->beginTransaction();

    // Kunci baris target (SELECT ... FOR UPDATE) di dalam transaksi
    // agar pembacaan saldo dan modifikasi terisolasi dari race condition.
    $stmt = $pdo->prepare("SELECT id, nama, saldo_sekarang FROM target WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $targetId]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        throw new Exception('Target tabungan tidak ditemukan.');
    }

    // Validasi saldo untuk penarikan dilakukan langsung terhadap data yang terkunci
    if ($jenisTransaksi === 'tarik' && $jumlah > $target['saldo_sekarang']) {
        throw new Exception('Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($target['saldo_sekarang'], 0, ',', '.'));
    }

    $newSaldo = ($jenisTransaksi === 'setor')
        ? $target['saldo_sekarang'] + $jumlah
        : $target['saldo_sekarang'] - $jumlah;

    // 1. Update saldo target
    $stmtUpdate = $pdo->prepare("UPDATE target SET saldo_sekarang = :saldo WHERE id = :id");
    $stmtUpdate->execute(['saldo' => $newSaldo, 'id' => $targetId]);

    // 2. Catat riwayat transaksi
    $stmtInsert = $pdo->prepare(
        "INSERT INTO transaksi (target_id, jenis_transaksi, jumlah, catatan)
         VALUES (:target_id, :jenis_transaksi, :jumlah, :catatan)"
    );
    $stmtInsert->execute([
        'target_id' => $targetId,
        'jenis_transaksi' => $jenisTransaksi,
        'jumlah' => $jumlah,
        'catatan' => $catatan,
    ]);

    // Commit transaksi
    $pdo->commit();
    
    $pesan = $jenisTransaksi === 'setor' 
        ? 'Berhasil menabung Rp ' . number_format($jumlah, 0, ',', '.') . ' ke target ' . $target['nama']
        : 'Berhasil menarik Rp ' . number_format($jumlah, 0, ',', '.') . ' dari target ' . $target['nama'];
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => $pesan];
    header('Location: list.php');
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
