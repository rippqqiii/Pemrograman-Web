# Dokumentasi Jobsheet 9 — CRUD Penuh (Sistem Gramedia)

Dokumentasi ini melanjutkan materi dari Jobsheet 8 (Koneksi PostgreSQL). Jobsheet 9 **melengkapi** siklus manajemen data aplikasi penjualan buku Gramedia menjadi **CRUD Penuh**:
- **C**reate (`INSERT`) — Sejak Jobsheet 8
- **R**ead (`SELECT`) — Sejak Jobsheet 8
- **U**pdate (`UPDATE`) — **Fitur Baru Jobsheet 9**
- **D**elete (`DELETE`) — **Fitur Baru Jobsheet 9**

---

## Apa yang Baru di Jobsheet 9?

1. **`edit.php` + `proses_edit.php`** (Produk & Penjualan) — melengkapi **Update**: form yang **sudah terisi** data lama berdasarkan `id` dari URL, lalu menyimpan perubahannya menggunakan prepared statement `UPDATE ... WHERE id = :id`.
2. **`hapus.php`** (Produk & Penjualan) — melengkapi **Delete**, sengaja **hanya menerima method `POST`** (bukan `GET`) supaya tidak terpicu tidak sengaja lewat tautan biasa atau web crawler.
3. Tombol Hapus di `list.php` sekarang berupa **`<form class="form-hapus">` sungguhan** dengan tombol submit — `app.js` diubah menangani konfirmasi di event `submit`, bukan `click`.
4. **Pagination** (`LIMIT`/`OFFSET`, 5 baris per halaman) dan **Pencarian Sisi Server** (`WHERE ... ILIKE :kw`) — menggantikan pencarian client-side murni untuk mencari data lintas seluruh halaman di database.
5. **Kalkulasi & Harga Produk Terintegrasi**: sinkronisasi field harga pada buku dan otomatisasi kalkulasi total transaksi penjualan.

---

## Daftar Isi Modul Dokumentasi

1. [01. Konsep Dasar CRUD](01-konsep-dasar-crud.md)
2. [02. Mengubah Data: `edit.php` & `proses_edit.php`](02-edit-update-data.md)
3. [03. Menghapus Data: `hapus.php`](03-hapus-delete-data.md)
4. [04. JavaScript: Konfirmasi Hapus via Event `submit`](04-js-update-hapus-confirm.md)
5. [05. Pagination & Pencarian Sisi Server](05-pagination-dan-pencarian-server.md)
6. [06. CSS Pendukung Fitur Baru & Dark Theme](06-css-pendukung.md)
7. [07. Rangkuman & Latihan Mandiri](07-rangkuman-latihan.md)
