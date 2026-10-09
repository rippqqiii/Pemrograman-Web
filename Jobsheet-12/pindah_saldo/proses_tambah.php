<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

csrf_verify();

$targetAsalId = (int) ($_POST['target_asal_id'] ?? 0);
$targetTujuanId = (int) ($_POST['target_tujuan_id'] ?? 0);
$jumlah = (float) ($_POST['jumlah'] ?? 0);
$catatan = trim($_POST['catatan'] ?? '');

$errors = [];
if ($targetAsalId <= 0) {
    $errors[] = "Target asal wajib dipilih.";
}
if ($targetTujuanId <= 0) {
    $errors[] = "Target tujuan wajib dipilih.";
}
if ($targetAsalId > 0 && $targetAsalId === $targetTujuanId) {
    $errors[] = "Target asal dan target tujuan tidak boleh sama.";
}
if ($jumlah < 1000) {
    $errors[] = "Nominal pemindahan minimal Rp 1.000.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    // Memulai database transaction atomik (ACID)
    $pdo->beginTransaction();

    // Pencegahan deadlock: urutkan ID baris yang akan dikunci
    $firstId = min($targetAsalId, $targetTujuanId);
    $secondId = max($targetAsalId, $targetTujuanId);

    // Kunci baris target (SELECT ... FOR UPDATE) untuk mencegah race condition.
    // Transaksi lain yang mencoba mengubah kedua target ini akan menunggu
    // hingga transaksi ini selesai (commit atau rollback).
    $stmtLock = $pdo->prepare("SELECT id, nama, saldo_sekarang FROM target WHERE id IN (:id1, :id2) FOR UPDATE");
    $stmtLock->execute(['id1' => $firstId, 'id2' => $secondId]);
    $rows = $stmtLock->fetchAll(PDO::FETCH_ASSOC);

    $targets = [];
    foreach ($rows as $row) {
        $targets[$row['id']] = $row;
    }

    if (!isset($targets[$targetAsalId])) {
        throw new Exception("Target asal tidak ditemukan.");
    }
    if (!isset($targets[$targetTujuanId])) {
        throw new Exception("Target tujuan tidak ditemukan.");
    }

    $asal = $targets[$targetAsalId];
    $tujuan = $targets[$targetTujuanId];

    // Validasi saldo target asal di dalam locking (pasti akurat dan bebas race condition)
    if ($asal['saldo_sekarang'] < $jumlah) {
        throw new Exception(
            "Saldo target asal (" . $asal['nama'] . ") tidak mencukupi! " .
            "Saldo saat ini: Rp " . number_format($asal['saldo_sekarang'], 0, ',', '.')
        );
    }

    // 1. Kurangi saldo target asal
    $stmtUpdateAsal = $pdo->prepare("UPDATE target SET saldo_sekarang = saldo_sekarang - :jumlah WHERE id = :id");
    $stmtUpdateAsal->execute(['jumlah' => $jumlah, 'id' => $targetAsalId]);

    // 2. Tambah saldo target tujuan
    $stmtUpdateTujuan = $pdo->prepare("UPDATE target SET saldo_sekarang = saldo_sekarang + :jumlah WHERE id = :id");
    $stmtUpdateTujuan->execute(['jumlah' => $jumlah, 'id' => $targetTujuanId]);

    // 3. Catat riwayat penarikan dari target asal
    $catatanKeluar = "Pindah saldo ke " . $tujuan['nama'] . ($catatan !== '' ? " (" . $catatan . ")" : "");
    $stmtTrx1 = $pdo->prepare(
        "INSERT INTO transaksi (target_id, jenis_transaksi, jumlah, catatan)
         VALUES (:target_id, 'tarik', :jumlah, :catatan)"
    );
    $stmtTrx1->execute([
        'target_id' => $targetAsalId,
        'jumlah' => $jumlah,
        'catatan' => $catatanKeluar
    ]);

    // 4. Catat riwayat setoran ke target tujuan
    $catatanMasuk = "Pindah saldo dari " . $asal['nama'] . ($catatan !== '' ? " (" . $catatan . ")" : "");
    $stmtTrx2 = $pdo->prepare(
        "INSERT INTO transaksi (target_id, jenis_transaksi, jumlah, catatan)
         VALUES (:target_id, 'setor', :jumlah, :catatan)"
    );
    $stmtTrx2->execute([
        'target_id' => $targetTujuanId,
        'jumlah' => $jumlah,
        'catatan' => $catatanMasuk
    ]);

    // Commit seluruh perubahan secara atomik
    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Berhasil memindahkan saldo Rp ' . number_format($jumlah, 0, ',', '.') . 
                   ' dari ' . $asal['nama'] . ' ke ' . $tujuan['nama'] . '.'
    ];
    header('Location: ../transaksi/list.php');
    exit;
} catch (Exception $e) {
    // Batalkan seluruh perubahan jika terjadi galat di tengah transaksi
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal memproses pindah saldo: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
