# 7. Rangkuman & Latihan Mandiri

## 7.1 Rangkuman Materi Jobsheet 9
Pada Jobsheet 9 ini, aplikasi Sistem Penjualan Buku Gramedia telah mencapai kemampuan **CRUD Penuh**:
1. **Create**: Menambah data buku baru beserta harganya (`produk/tambah.php`) dan mencatat transaksi penjualan buku (`penjualan/tambah.php`).
2. **Read**: Menampilkan daftar buku dan riwayat transaksi dengan dukungan pagination 5 item per halaman serta pencarian *case-insensitive* `ILIKE`.
3. **Update**: Mengubah rincian buku (`produk/edit.php`) dan merevisi transaksi penjualan (`penjualan/edit.php`) dengan mengisi form secara dinamis dari data database.
4. **Delete**: Menghapus baris data secara aman melalui HTTP POST (`hapus.php`) dengan konfirmasi JavaScript dan penanganan integritas relasi foreign key.

---

## 7.2 Latihan Mandiri
1. **Pembaruan Stok Otomatis**:
   - Ketika transaksi penjualan baru berhasil disimpan di `penjualan/proses_tambah.php`, buat query untuk mengurangi stok buku terkait di tabel `produk` (`UPDATE produk SET stok = stok - :jumlah WHERE id = :produk_id`).
2. **Validasi Ketersediaan Stok**:
   - Pada form transaksi, berikan validasi server-side agar jumlah buku yang dibeli tidak melebihi stok yang tersedia.
3. **Pencarian Berdasarkan Rentang Tanggal**:
   - Tambahkan filter rentang tanggal pada halaman riwayat penjualan (`penjualan/list.php`).
