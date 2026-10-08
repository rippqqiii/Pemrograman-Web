# 1. Konsep Dasar CRUD

## 1.1 Apa itu CRUD?
**CRUD** adalah singkatan dari 4 operasi dasar yang menjadi inti pengelolaan data pada aplikasi web:

| Huruf | Operasi | Perintah SQL | Implementasi di Gramedia |
|---|---|---|---|
| **C**reate | Menambah data baru | `INSERT` | `tambah.php` + `proses_tambah.php` |
| **R**ead | Membaca / menampilkan data | `SELECT` | `list.php` (+ pagination & search) |
| **U**pdate | Mengubah data lama | `UPDATE` | `edit.php` + `proses_edit.php` |
| **D**elete | Menghapus data | `DELETE` | `hapus.php` (via method `POST`) |

Pada sistem informasi penjualan Gramedia, siklus CRUD diterapkan pada dua entitas utama:
1. **Produk Buku**: Mengelola inventaris judul, pengarang, tahun, stok, harga, dan kategori.
2. **Penjualan**: Mengelola transaksi pembelian buku pelanggan, mencakup tanggal, buku yang dibeli, kuantitas, dan total harga.

## 1.2 Form Kosong vs Form Terisi (Create vs Update)
- **Create (`tambah.php`)**: Form terbuka dalam keadaan kosong, data baru dimasukkan dan di-insert ke database dengan ID auto-increment (`SERIAL`).
- **Update (`edit.php`)**: Form dibuka dengan parameter `?id=X` pada URL. Sistem melakukan `SELECT * FROM ... WHERE id = :id` lalu mengisi seluruh `value=""` input form dengan data lama, sehingga pengguna hanya perlu mengubah field yang diinginkan.

## 1.3 Aspek Keamanan pada Operasi Delete
Operasi `DELETE` bersifat merusak (irreversible). Oleh karena itu:
- Penghapusan **tidak boleh** menggunakan link `<a>` biasa dengan method `GET`. Jika menggunakan GET, web crawler atau prefetching browser dapat memicu penghapusan tanpa sengaja.
- Penghapusan dibungkus dalam form terpisah dengan method `POST`:
  ```html
  <form class="form-hapus" method="post" action="hapus.php">
      <input type="hidden" name="id" value="...">
      <button type="submit" class="btn-hapus">Hapus</button>
  </form>
  ```
- Dilengkapi konfirmasi JavaScript dan penanganan integritas relasi foreign key pada database.
