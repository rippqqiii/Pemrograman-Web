# Dokumentasi Jobsheet 12 — Integrasi Modul Transaksi & Concurrency Control

Dokumentasi ini menjelaskan integrasi menyeluruh (*end-to-end*) sistem aplikasi **TabunganKu** pada Jobsheet 12, yang mengintegrasikan entitas `target`, `transaksi`, dan `users` dengan dukungan transaksi atomik (ACID) dan *pessimistic locking* (`SELECT ... FOR UPDATE`).

---

## 1. Konsep Utama Jobsheet 12

1. **Integrasi Antar-Entitas (End-to-End Integration)**:
   - Menghubungkan entitas impian/target dengan mutasi keuangan (setor, tarik, dan pindah saldo antar-target).
   - Menampilkan riwayat transaksi per target secara spesifik (`transaksi/riwayat.php`) menggunakan relasi SQL `JOIN`.

2. **Atomic Database Transaction (ACID)**:
   - Operasi multi-tabel harus bersifat *all-or-nothing*.
   - Menggunakan PDO:
     ```php
     $pdo->beginTransaction();
     try {
         // Serangkaian query SQL terkait
         $pdo->commit();
     } catch (Exception $e) {
         $pdo->rollBack();
     }
     ```

3. **Pessimistic Concurrency Control (`SELECT ... FOR UPDATE`)**:
   - Mencegah masalah *race condition* (misalnya: dua permintaan penarikan/pemindahan saldo berjalan serentak saat saldo hanya cukup untuk satu permintaan).
   - Baris tabel dikunci di tingkat database saat pembacaan (`FOR UPDATE`) hingga transaksi selesai (`COMMIT` atau `ROLLBACK`).

4. **Pencegahan Deadlock**:
   - Pada fitur **Pindah Saldo** yang melibatkan dua baris dari tabel yang sama (Target Asal & Target Tujuan), urutan penguncian baris diseragamkan berdasarkan `min(id1, id2)` dan `max(id1, id2)` untuk menghindari kebuntuan (*deadlock*).

5. **Proteksi Akses (Authentication Guard)**:
   - Seluruh halaman transaksi dan pengelolaan data diproteksi dengan `includes/auth.php` sehingga hanya pengguna yang sudah terotentikasi yang dapat melakukan aksi modifikasi data.

---

## 2. Struktur Berkas Jobsheet 12

```
Jobsheet-12/
├── index.php                      # Dashboard ringkasan keuangan & 5 transaksi terbaru
├── README.md                      # Panduan ringkas Jobsheet 12 & skenario pengujian
├── assets/
│   ├── css/style.css              # Custom styling aplikasi
│   └── js/app.js                  # Script validasi & interaktivitas front-end
├── auth/
│   ├── login.php                  # Halaman form login
│   ├── proses_login.php           # Verifikasi password & session regeneration
│   ├── register.php               # Halaman form registrasi
│   ├── proses_register.php        # Hash password & simpan user baru
│   └── logout.php                 # Hapus session & logout aman
├── includes/
│   ├── auth.php                   # Guard clause proteksi halaman login
│   ├── csrf.php                   # Token CSRF generator & verifier
│   ├── footer.php                 # Penutup HTML & script Bootstrap
│   ├── header.php                 # Navbar responsif, menu dinamis, status login
│   ├── helpers.php                # Fungsi sanitasi e()
│   └── koneksi.php                # PDO connection ke PostgreSQL
├── target/
│   ├── list.php                   # Daftar target, progress bar, & aksi
│   ├── tambah.php                 # Form pembuatan target baru
│   ├── proses_tambah.php          # Insert target dengan validasi & auth
│   └── hapus.php                  # Hapus target aman (cek saldo & transaksi)
├── transaksi/
│   ├── list.php                   # Riwayat seluruh transaksi (pagination & search)
│   ├── riwayat.php                # Riwayat transaksi terfilter per target
│   ├── tambah.php                 # Form setor & tarik tabungan
│   ├── proses_tambah.php          # Pemrosesan setor/tarik dengan SELECT ... FOR UPDATE
│   └── hapus.php                  # Pembatalan transaksi & reversal saldo atomik
├── pindah_saldo/
│   ├── tambah.php                 # Form pemindahan saldo antar-target
│   └── proses_tambah.php          # Eksekusi pindah saldo (ACID + deadlock prevention)
├── sql/
│   ├── 01_target_transaksi.sql    # Skema tabel target & transaksi
│   ├── 02_users.sql               # Skema tabel users untuk autentikasi
│   └── 03_pindah_saldo.sql        # Dokumentasi transaksi pindah saldo & verifikasi
├── docs/wireframe.md              # Rancangan UI/UX aplikasi
└── Dokumentasi/                   # Folder dokumentasi detail
```
