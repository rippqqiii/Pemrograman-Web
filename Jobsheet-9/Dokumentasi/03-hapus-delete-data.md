# 3. Menghapus Data: `hapus.php`

## 3.1 Mengapa Harus Method POST?
Tautan biasa (`<a href="...">`) mengirimkan request menggunakan HTTP method `GET`. Menggunakan GET untuk operasi penghapusan memiliki risiko fatal:
1. **Web Crawler & Bot**: Mesin perayap (Googlebot, web crawlers) otomatis menelusuri seluruh link yang ditemukan di halaman. Jika link hapus menggunakan GET, bot akan menghapus seluruh isi database tanpa sengaja.
2. **Link Prefetching**: Fitur prefetch browser dapat memuat halaman di latar belakang untuk mempercepat navigasi, yang secara tidak sengaja dapat mengeksekusi penghapusan.

Oleh sebab itu, `hapus.php` memeriksa metode request dan langsung menolak request non-POST:
```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}
```

## 3.2 Implementasi di `produk/hapus.php` dengan Integritas Relasi
Pada basis data relasional, sebuah data buku tidak dapat dihapus jika terdapat data transaksi penjualan yang merujuk pada `id` buku tersebut (`FOREIGN KEY`).

Dengan membungkus query dalam blok `try...catch (PDOException $e)`, aplikasi tidak mengalami crash / error 500 fatal, melainkan menampilkan pesan informatif ke pengguna:
```php
$id = $_POST['id'] ?? null;
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Buku berhasil dihapus.'];
    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Buku gagal dihapus karena sudah memiliki riwayat transaksi penjualan.'];
    }
}
```
