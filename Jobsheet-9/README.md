# Jobsheet 9 — CRUD Penuh (Sistem Informasi Penjualan Gramedia)

Sub-CPMK: Membangun fitur CRUD penuh pada proyek sistem penjualan buku Gramedia.

## Perubahan dari Jobsheet 8
- **Update**:
  - `produk/edit.php` + `produk/proses_edit.php` — mengubah data buku (judul, pengarang, tahun, isbn, stok, harga, kategori).
  - `penjualan/edit.php` + `penjualan/proses_edit.php` — mengubah data transaksi penjualan (tanggal, buku terpilih, jumlah, total harga otomatis).
- **Delete**:
  - `produk/hapus.php` & `penjualan/hapus.php` — menghapus data dengan method `POST` (bukan GET) demi keamanan agar tidak terpemicu tidak sengaja oleh link biasa atau web crawler. Pada buku juga dilengkapi proteksi constraint bila sudah memiliki transaksi.
- **Konfirmasi Hapus (JS)**:
  - Tombol Hapus di `list.php` sekarang berada di dalam `<form class="form-hapus" method="post">`.
  - `assets/js/app.js` (`initHapusConfirm`) menangani event `submit` form untuk konfirmasi (`confirm()`), sehingga dapat dibatalkan dengan `e.preventDefault()`.
- **Pagination & Server-side Search**:
  - `produk/list.php` & `penjualan/list.php`: menambahkan **pagination** (`LIMIT`/`OFFSET`, 5 baris per halaman) dan **pencarian server-side** menggunakan query PostgreSQL (`ILIKE :kw`) lewat form method `GET`.
- **Integrasi Harga & Tampilan Dark Theme**:
  - Sinkronisasi kolom harga pada produk buku dan perhitungan total harga pada form transaksi penjualan.
  - Penyesuaian styling pagination, form cari, dan tombol aksi (Edit & Hapus) dengan Dark Theme Gramedia.

## Struktur Folder Jobsheet 9
```
Jobsheet-9/
├── index.php                         # Dashboard ringkasan total buku, penjualan & pendapatan
├── includes/
│   ├── header.php                    # Header & navigasi Bootstrap 5 tema Gramedia
│   ├── footer.php                    # Footer template
│   └── koneksi.php                   # Koneksi PDO PostgreSQL (db: gramedia)
├── produk/
│   ├── list.php                      # Read + Pagination (5 baris) + Search Server-side + Tombol Form Hapus
│   ├── tambah.php                    # Create (Form tambah buku + harga)
│   ├── proses_tambah.php             # Create (Proses INSERT ke tabel produk)
│   ├── edit.php                      # Update (Form edit buku terisi data lama)
│   ├── proses_edit.php               # Update (Proses UPDATE ke tabel produk)
│   └── hapus.php                     # Delete (Proses DELETE hanya menerima POST)
├── penjualan/
│   ├── list.php                      # Read + Pagination + Search Server-side + Tombol Form Hapus
│   ├── tambah.php                    # Create (Form transaksi + kalkulasi otomatis harga)
│   ├── proses_tambah.php             # Create (Proses INSERT ke tabel penjualan)
│   ├── edit.php                      # Update (Form edit transaksi terisi data lama + kalkulasi harga)
│   ├── proses_edit.php               # Update (Proses UPDATE ke tabel penjualan)
│   └── hapus.php                     # Delete (Proses DELETE hanya menerima POST)
├── assets/
│   ├── css/style.css                 # Dark theme, styling pagination, search box, tombol edit & hapus
│   └── js/app.js                     # JS toggle, konfirmasi hapus on submit, real-time filter & validasi
├── sql/
│   └── 01_produk_penjualan.sql       # DDL skema database PostgreSQL
├── docs/
│   └── wireframe.md                  # Dokumentasi wireframe UI
└── Dokumentasi/                      # Penjelasan modul & materi praktikum CRUD
```

## Cara Menjalankan
1. Pastikan PostgreSQL berjalan dan database `gramedia` telah dibuat dengan tabel `produk` dan `penjualan` sesuai `sql/01_produk_penjualan.sql`.
2. Jalankan PHP built-in server pada folder ini:
   ```bash
   php -S localhost:8000
   ```
3. Buka browser di `http://localhost:8000/index.php`.
4. Uji alur CRUD penuh:
   - **Produk**: Tambah buku baru -> Tampil di daftar -> Edit buku -> Hapus buku.
   - **Penjualan**: Buat transaksi -> Tampil di riwayat -> Edit transaksi -> Hapus transaksi.
   - Uji pencarian dan pagination pada kedua modul.
