<?php
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
    <title>TabunganKu<?php echo isset($page_title) ? ' | ' . $page_title : ''; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <style>
        .navbar {
            background-color: #9e0303 !important;
        }
        .navbar-brand, .nav-link {
            color: #e8d4d4 !important;
        }
        .nav-link:hover {
            color: #ffffff !important;
        }
        .dropdown-menu {
            background-color: #2d1515;
            border: 1px solid #9e0303;
        }
        .dropdown-item {
            color: #e8d4d4;
        }
        .dropdown-item:hover {
            background-color: #9e0303;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <header class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?php echo $base; ?>index.php">TabunganKu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <nav class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base; ?>index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Target</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base; ?>target/list.php">Daftar Target</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>target/tambah.php">Buat Target</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Transaksi</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base; ?>transaksi/list.php">Riwayat Transaksi</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base; ?>transaksi/tambah.php">Setor / Tarik</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-4">
