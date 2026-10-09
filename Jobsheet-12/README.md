# Jobsheet 12 — Integrasi Modul Pindah Saldo & Concurrency Control

**Mata Kuliah:** Desain dan Pemrograman Web (Semester 3)  
**Studi Kasus:** TabunganKu  
**Sub-CPMK:** Mengintegrasikan front-end dan back-end proyek secara utuh serta menerapkan transaksi database atomik (*concurrency control*).

---

## 1. Perubahan dari Jobsheet 11

Pada Jobsheet 12 ini, seluruh komponen yang dibangun dari Jobsheet 8 hingga Jobsheet 11 diintegrasikan secara utuh ke dalam alur bisnis aplikasi:

1. **Skema & Dokumentasi Transaksi (`sql/03_pindah_saldo.sql`)**:
   - Skrip verifikasi tabel `target` dan `transaksi`.
   - Dokumentasi alur transaksi multi-tabel di PostgreSQL.

2. **Modul Pindah Saldo Antar Target (`pindah_saldo/`)**:
   - `pindah_saldo/tambah.php`: Form memilih Target Asal (hanya target dengan `saldo_sekarang > 0`), Target Tujuan (validasi tidak boleh sama dengan asal), nominal, dan catatan.
   - `pindah_saldo/proses_tambah.php`:
     - Menjalankan **transaksi atomik** (`$pdo->beginTransaction()`, `$pdo->commit()`, `$pdo->rollBack()`).
     - Menerapkan **pessimistic locking** (`SELECT ... FOR UPDATE`) pada kedua target yang terlibat agar saldo tidak dimodifikasi oleh proses lain selama transaksi berlangsung (*race condition prevention*).
     - Mengurutkan ID baris sebelum dikunci (`min(id1, id2)` & `max(id1, id2)`) untuk mencegah *deadlock*.
     - Mengurangi saldo target asal, menambah saldo target tujuan, dan mencatat 2 mutasi ke tabel `transaksi` dalam satu kesatuan kerja atomik.

3. **Modul Riwayat Transaksi per Target (`transaksi/riwayat.php`)**:
   - Memfilter seluruh histori arus kas khusus untuk target tabungan tertentu (analog dengan fitur riwayat peminjaman per anggota di modul acuan).
   - Menampilkan kartu ringkasan target: Saldo saat ini, Target nominal, Progres pencapaian, Total disetor, dan Total ditarik.

4. **Peningkatan Concurrency Control pada Transaksi Biasa (`transaksi/proses_tambah.php`)**:
   - Pengecekan saldo dan pembaruan data setor/tarik kini berada **di dalam** `$pdo->beginTransaction()` dengan klausa `SELECT ... FOR UPDATE`.

5. **Fitur Pembatalan Transaksi / Reversal Saldo (`transaksi/hapus.php`)**:
   - Analog dengan fitur *Pengembalian Buku* pada modul perpustakaan: membatalkan transaksi mutasi dan mengembalikan/mereversal saldo target secara atomik dengan locking `FOR UPDATE`.

6. **Integrasi Autentikasi & Navigasi (`includes/header.php`)**:
   - Navbar menampilkan status login (`$_SESSION['nama']`) dan menu Logout.
   - Menu pembuatan target, transaksi, dan pindah saldo diproteksi dengan `includes/auth.php`.

7. **Integrasi Dashboard (`index.php`)**:
   - Menampilkan metrik dinamis: Total Saldo Terkumpul, Total Target Dana, Sisa Kebutuhan, Target Tercapai, dan Total Transaksi.
   - Menampilkan tabel ringkasan 5 transaksi mutasi terbaru.

---

## 2. Cara Menjalankan

### Persiapan Database PostgreSQL
Pastikan database `gramedia` (atau database proyek Anda) aktif di PostgreSQL / Laragon:
```bash
psql -U postgres -d gramedia -f sql/01_target_transaksi.sql
psql -U postgres -d gramedia -f sql/02_users.sql
```

### Menjalankan Server Web
**Opsi 1 — PHP Built-in Server**:
```bash
# Jalankan terminal di dalam folder Jobsheet-12
php -S localhost:8000
```
Buka browser pada: `http://localhost:8000`

**Opsi 2 — Laragon / Apache**:
Akses melalui virtual host atau path Laragon Anda, contoh:
`http://localhost/Pemrograman-Web/Jobsheet-12/`

---

## 3. Skenario Pengujian End-to-End (Bahan Presentasi ke Dosen)

Berikut alur pengujian lengkap untuk didemokan ke Dosen pengampu:

1. **Registrasi & Login**:
   - Buka menu Login &rarr; klik *Daftar di sini* &rarr; buat akun baru &rarr; login berhasil.
   - Perhatikan navbar kini menampilkan nama pengguna dan menu transaksi lengkap.
2. **Buat Target Tabungan**:
   - Buat Target 1: "Tabungan Laptop" (Target: Rp 10.000.000, Saldo awal: Rp 0).
   - Buat Target 2: "Dana Darurat" (Target: Rp 5.000.000, Saldo awal: Rp 0).
3. **Setor Dana (Nabung)**:
   - Masuk ke menu *Setor / Tarik* &rarr; pilih Target "Tabungan Laptop" &rarr; Setor Rp 500.000.
   - Cek dashboard: Total saldo bertambah Rp 500.000 dan transaksi muncul di 5 transaksi terbaru.
4. **Uji Fitur Pindah Saldo (Jobsheet 12)**:
   - Masuk ke menu *Transaksi* &rarr; *🔄 Pindah Saldo Antar Target*.
   - Pilih Target Asal: "Tabungan Laptop" (Saldo: Rp 500.000).
   - Pilih Target Tujuan: "Dana Darurat".
   - Masukkan nominal: Rp 200.000.
   - Klik *Proses Pindah Saldo*.
   - Periksa hasilnya: Saldo "Tabungan Laptop" menjadi Rp 300.000, dan saldo "Dana Darurat" menjadi Rp 200.000.
   - Di riwayat transaksi tercatat mutasi penarikan dari target asal dan mutasi setoran ke target tujuan.
5. **Cek Riwayat per Target**:
   - Masuk ke menu *Riwayat per Target* &rarr; pilih "Tabungan Laptop" &rarr; lihat seluruh mutasi dana khusus untuk target tersebut.
6. **Uji Pembatalan Transaksi (Reversal)**:
   - Buka menu *Semua Transaksi* &rarr; klik tombol *Batal* pada salah satu transaksi &rarr; konfirmasi &rarr; saldo target otomatis disesuaikan kembali secara aman.
7. **Logout**:
   - Klik menu pengguna di kanan atas &rarr; Logout &rarr; sesi berakhir aman.

---

## 4. Konsep Teori untuk Penjelasan ke Dosen

> **Pertanyaan Dosen:** *"Bagaimana cara kamu mencegah race condition saat pemindahan saldo atau penarikan?"*  
> **Jawaban:**  
> *"Saya menggunakan **Pessimistic Concurrency Control** dengan klausa `SELECT ... FOR UPDATE` di dalam blok transaksi PDO (`beginTransaction`). Saat transaksi berjalan, baris data target dikunci di level baris oleh database PostgreSQL, sehingga proses lain tidak bisa membaca atau menulis saldo lama hingga transaksi pertama selesai (`commit` atau `rollBack`). Selain itu, ID target diurutkan (`min/max`) sebelum dikunci untuk menghindari potensi deadlock."*
