# 2. Mengubah Data: `edit.php` & `proses_edit.php`

Fitur update memerlukan dua file yang bekerja sama:
1. `edit.php`: Mengambil data lama dari database dan menampilkannya pada form.
2. `proses_edit.php`: Menerima data hasil pengeditan dari form, memvalidasi, dan mengeksekusi perintah SQL `UPDATE`.

---

## 2.1 Mengambil Data Lama Berdasarkan ID
Pada `produk/edit.php` dan `penjualan/edit.php`, parameter `id` diterima melalui URL superglobal `$_GET['id']`:
```php
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id");
$stmt->execute(['id' => $id]);
$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    header('Location: list.php');
    exit;
}
```

## 2.2 Menyisipkan Primary Key ke Form (`hidden input`)
Agar `proses_edit.php` mengetahui baris mana yang harus di-update, nilai `id` disertakan ke dalam input bertipe `hidden`:
```html
<form method="post" action="proses_edit.php">
    <input type="hidden" name="id" value="<?php echo $buku['id']; ?>">
    <input type="text" name="judul" value="<?php echo htmlspecialchars($buku['judul']); ?>" required>
    ...
</form>
```

## 2.3 Menjalankan Query UPDATE
Pada `proses_edit.php`, validasi server-side dilakukan terlebih dahulu sebelum query dijalankan dengan prepared statement:
```php
$stmt = $pdo->prepare(
    "UPDATE produk SET judul = :judul, pengarang = :pengarang, tahun = :tahun,
     isbn = :isbn, stok = :stok, harga = :harga, kategori = :kategori WHERE id = :id"
);
$stmt->execute([
    'judul'     => $judul,
    'pengarang' => $pengarang,
    'tahun'     => (int) $tahun,
    'isbn'      => $isbn,
    'stok'      => (int) $stok,
    'harga'     => (float) $harga,
    'kategori'  => $kategori,
    'id'        => $id,
]);
```
Setelah berhasil, sistem menyimpan notifikasi sukses ke dalam `$_SESSION['flash']` dan melakukan redirect ke `list.php`.
