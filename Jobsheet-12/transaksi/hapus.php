<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

csrf_verify();

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php');
    exit;
}

try {
    // Memulai database transaction untuk membatalkan transaksi dan mereversal saldo secara atomik
    $pdo->beginTransaction();

    // 1. Kunci baris transaksi dan baris target terkait (FOR UPDATE)
    $stmtTrx = $pdo->prepare("SELECT * FROM transaksi WHERE id = :id FOR UPDATE");
    $stmtTrx->execute(['id' => $id]);
    $trx = $stmtTrx->fetch(PDO::FETCH_ASSOC);

    if (!$trx) {
        throw new Exception("Data transaksi tidak ditemukan.");
    }

    $stmtTarget = $pdo->prepare("SELECT id, nama, saldo_sekarang FROM target WHERE id = :id FOR UPDATE");
    $stmtTarget->execute(['id' => $trx['target_id']]);
    $target = $stmtTarget->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        throw new Exception("Data target terkait tidak ditemukan.");
    }

    // 2. Reversal penyesuaian saldo pada target
    if ($trx['jenis_transaksi'] === 'setor') {
        // Jika membatalkan setoran, kurangi saldo target
        if ($target['saldo_sekarang'] < $trx['jumlah']) {
            throw new Exception("Tidak dapat menghapus setoran karena saldo target saat ini (Rp " . 
                number_format($target['saldo_sekarang'], 0, ',', '.') . 
                ") lebih kecil dari nominal setoran yang akan dibatalkan.");
        }
        $newSaldo = $target['saldo_sekarang'] - $trx['jumlah'];
    } else {
        // Jika membatalkan penarikan, pulihkan/tambah kembali saldo target
        $newSaldo = $target['saldo_sekarang'] + $trx['jumlah'];
    }

    $stmtUpdate = $pdo->prepare("UPDATE target SET saldo_sekarang = :saldo WHERE id = :id");
    $stmtUpdate->execute(['saldo' => $newSaldo, 'id' => $target['id']]);

    // 3. Hapus baris transaksi
    $stmtDelete = $pdo->prepare("DELETE FROM transaksi WHERE id = :id");
    $stmtDelete->execute(['id' => $id]);

    // Commit transaksi
    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => "Transaksi #{$id} berhasil dibatalkan dan saldo target telah disesuaikan kembali."
    ];
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => "Gagal membatalkan transaksi: " . $e->getMessage()
    ];
}

header('Location: list.php');
exit;
