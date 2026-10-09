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
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id, nama, saldo_sekarang FROM target WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $id]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        throw new Exception("Target tidak ditemukan.");
    }

    if ($target['saldo_sekarang'] > 0) {
        throw new Exception(
            "Target '{$target['nama']}' masih memiliki saldo Rp " . 
            number_format($target['saldo_sekarang'], 0, ',', '.') . 
            ". Silakan pindahkan atau tarik seluruh saldo terlebih dahulu sebelum menghapus target."
        );
    }

    // Hapus seluruh histori transaksi terkait target ini secara atomik
    $stmtDelTrx = $pdo->prepare("DELETE FROM transaksi WHERE target_id = :id");
    $stmtDelTrx->execute(['id' => $id]);

    // Hapus baris target
    $stmtDel = $pdo->prepare("DELETE FROM target WHERE id = :id");
    $stmtDel->execute(['id' => $id]);

    $pdo->commit();
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => "Target '{$target['nama']}' berhasil dihapus."];
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => "Gagal menghapus target: " . $e->getMessage()];
}

header('Location: list.php');
exit;
