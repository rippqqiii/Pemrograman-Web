<?php
session_start();

// Prefix relatif ke root proyek ini (bukan root domain) — supaya
// /assets, /index.php, dst tetap benar walau proyek diakses lewat
// subfolder (mis. dp2026.test/kode-praktikum/jobsheet-07/), bukan cuma
// lewat vhost yang document root-nya langsung folder ini.
$__jobsheetRoot = dirname(__DIR__);
$__scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
$__rel = ltrim(str_replace('\\', '/', substr($__scriptDir, strlen($__jobsheetRoot))), '/');
$base = $__rel === '' ? '' : str_repeat('../', substr_count($__rel, '/') + 1);
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gramedia<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?php echo $base; ?>index.php">Gramedia</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <nav class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Produk</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base; ?>produk/list.php">Daftar Buku</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>produk/tambah.php">Tambah Buku</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Penjualan</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base; ?>penjualan/list.php">Riwayat Penjualan</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>penjualan/tambah.php">Buat Transaksi</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-4">