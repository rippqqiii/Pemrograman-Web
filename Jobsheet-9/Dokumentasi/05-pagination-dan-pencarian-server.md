# 5. Pagination & Pencarian Sisi Server

Ketika data di database sudah berjumlah puluhan hingga ribuan baris, menampilkan semua baris sekaligus akan memperlambat waktu respon dan memenuhi memori browser. Oleh karena itu diterapkan pagination dan pencarian sisi server.

---

## 5.1 Mekanisme Pagination dengan SQL (`LIMIT` & `OFFSET`)
Di PostgreSQL:
- `LIMIT`: Menentukan batas maksimum jumlah baris yang diambil per halaman (misal: 5 baris).
- `OFFSET`: Menentukan berapa baris data awal yang dilewati sebelum mulai mengambil data.

Rumus perhitungan offset:
$$\text{offset} = (\text{page} - 1) \times \text{perPage}$$

```php
$perPage = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
```

Total halaman dihitung dari total baris dibagi baris per halaman:
```php
$totalPages = max(1, (int) ceil($totalRows / $perPage));
```

---

## 5.2 Pencarian Sisi Server Menggunakan Operator `ILIKE`
PostgreSQL menyediakan operator `ILIKE` untuk pencarian string *case-insensitive* (tidak membedakan huruf besar dan kecil):

```php
$keyword = trim($_GET['q'] ?? '');

if ($keyword !== '') {
    $hitung = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE judul ILIKE :kw OR pengarang ILIKE :kw");
    $hitung->execute(['kw' => '%' . $keyword . '%']);
    $totalRows = $hitung->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM produk WHERE judul ILIKE :kw OR pengarang ILIKE :kw ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue('kw', '%' . $keyword . '%');
} else {
    $totalRows = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM produk ORDER BY id DESC LIMIT :limit OFFSET :offset");
}
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
```

---

## 5.3 Mempertahankan Query Parameter pada Link Pagination
Saat berpindah halaman, kata kunci pencarian tidak boleh hilang dari URL. Oleh karena itu, parameter `q` disambungkan kembali pada link navigasi halaman:
```html
<nav class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="list.php?page=<?php echo $i; ?><?php echo $keyword !== '' ? '&q=' . urlencode($keyword) : ''; ?>"
       class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</nav>
```
