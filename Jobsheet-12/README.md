# TabunganKu — Jobsheet 12: Integrasi Sistem & Manajemen Transaksi Konkuren

Aplikasi web pengelolaan target tabungan pribadi dan pencatatan mutasi keuangan berbasis **PHP**, **PostgreSQL (PDO)**, dan **Bootstrap 5**.

Pada tahap **Jobsheet 12**, aplikasi telah melalui proses integrasi menyeluruh (*end-to-end*) antara antarmuka pengguna, sistem autentikasi, serta pengelolaan transaksi database yang aman dari *race condition* melalui mekanisme *pessimistic concurrency control*.

---

## 🎯 Gambaran Umum Proyek

**TabunganKu** membantu pengguna menetapkan tujuan keuangan (target tabungan), memantau persentase capaian secara *real-time*, mencatat setiap setoran maupun penarikan dana, serta melakukan pemindahan alokasi dana (*transfer saldo*) antar-target tabungan dengan aman.

---

## ✨ Fitur & Peningkatan pada Jobsheet 12

### 1. Modul Pindah Saldo Antar-Target (`pindah_saldo/`)
Memungkinkan pengguna mengalihkan sebagian atau seluruh dana dari satu target impian ke target impian lainnya secara langsung tanpa perlu melakukan proses tarik-setor manual.
* **Filter Sumber Dana:** Dropdown target asal otomatis menyaring hanya target yang memiliki saldo aktif (`> Rp 0`).
* **Validasi Relasi:** Mencegah pemindahan dana ke target yang sama dan memastikan nominal transfer tidak melampaui saldo yang tersedia.

### 2. Transaksi Database Atomik & Concurrency Control
Mengamankan seluruh mutasi finansial menggunakan standar **ACID (Atomicity, Consistency, Isolation, Durability)**:
* **Pessimistic Locking (`SELECT ... FOR UPDATE`):** Baris data target dikunci di tingkat database saat pengecekan saldo, mencegah terjadinya *race condition* atau saldo bernilai negatif ketika ada akses bersamaan.
* **Deadlock Prevention:** Pada transaksi yang melibatkan dua target (pindah saldo), penguncian baris diurutkan secara konsisten berdasarkan ID terkecil (`min/max`) sehingga tidak akan terjadi kebuntuan antar-proses.
* **All-or-Nothing:** Menggunakan `$pdo->beginTransaction()`, `$pdo->commit()`, dan `$pdo->rollBack()` untuk menjamin integritas data bila terjadi gangguan koneksi atau kegagalan sistem.

### 3. Filter Riwayat Mutasi per Target (`transaksi/riwayat.php`)
Halaman analitik khusus yang menampilkan:
* Ringkasan performa target terpilih (Saldo saat ini, Target dana, Progres bar capaian, Total dana masuk, Total dana keluar).
* Tabel histori transaksi terperinci khusus untuk target yang sedang ditinjau.

### 4. Pembatalan Transaksi & Reversal Saldo (`transaksi/hapus.php`)
Fitur koreksi transaksi yang mengembalikan (*reversal*) saldo target ke kondisi sebelum transaksi terjadi secara aman dan terisolasi.

### 5. Integrasi Autentikasi Pengguna & Navigasi Dinamis
* Navbar otomatis mendeteksi status login sesi pengguna (`$_SESSION['nama']`).
* Seluruh operasi mutasi saldo dan pembuatan target diproteksi oleh *guard clause* [`includes/auth.php`](includes/auth.php).

### 6. Dashboard Ringkasan Finansial (`index.php`)
Menampilkan metrik agregat langsung dari database:
* Total Saldo Terkumpul, Total Target Dana, dan Sisa Kebutuhan Dana.
* Indikator Target Selesai vs Target Dalam Proses.
* Tabel 5 mutasi transaksi paling mutakhir.

---

## 🏗️ Struktur Berkas & Direktori

```text
Jobsheet-12/
├── index.php                      # Dashboard utama & ringkasan statistik
├── README.md                      # Dokumentasi teknis proyek
├── assets/
│   ├── css/style.css              # Kustomisasi tema & palet warna
│   └── js/app.js                  # Interaktivitas front-end & validasi
├── auth/
│   ├── login.php                  # Halaman masuk sistem
│   ├── register.php               # Halaman pendaftaran pengguna
│   ├── proses_login.php           # Autentikasi & regenerasi session ID
│   ├── proses_register.php        # Hashing password (bcrypt) & simpan akun
│   └── logout.php                 # Terminasi sesi login
├── includes/
│   ├── auth.php                   # Pengecekan sesi login
│   ├── csrf.php                   # Pembuatan & validasi token CSRF
│   ├── footer.php                 # Penutup layout & pemanggilan pustaka JS
│   ├── header.php                 # Header aplikasi & navigasi responsif
│   ├── helpers.php                # Fungsi utilitas & sanitasi output
│   └── koneksi.php                # Konfigurasi koneksi PDO PostgreSQL
├── target/
│   ├── list.php                   # Tinjauan semua target & progres capaian
│   ├── tambah.php                 # Form pembuatan target baru
│   ├── proses_tambah.php          # Validasi & penyimpanan target
│   └── hapus.php                  # Penghapusan target bersaldo Rp 0
├── transaksi/
│   ├── list.php                   # Log seluruh transaksi dengan pencarian & paginasi
│   ├── riwayat.php                # Filter histori transaksi spesifik per target
│   ├── tambah.php                 # Formulir setor / tarik dana
│   ├── proses_tambah.php          # Proses setor/tarik dengan baris terkunci
│   └── hapus.php                  # Pembatalan transaksi & pemulihan saldo
├── pindah_saldo/
│   ├── tambah.php                 # Antarmuka pemindahan dana antar-target
│   └── proses_tambah.php          # Eksekusi pindah saldo atomik multi-baris
├── sql/
│   ├── 01_target_transaksi.sql    # DDL pembuatan tabel target & transaksi
│   ├── 02_users.sql               # DDL pembuatan tabel users autentikasi
│   └── 03_pindah_saldo.sql        # Skrip audit & panduan verifikasi transaksi
├── docs/wireframe.md              # Referensi rancangan antarmuka pengguna
└── Dokumentasi/                   # Catatan arsitektur & panduan modul
```

---

## 🚀 Panduan Menjalankan Aplikasi

### 1. Konfigurasi Database
Pastikan PostgreSQL berjalan (misalnya melalui Laragon atau instalasi PostgreSQL standalone), lalu inisialisasi skema basis data:
```bash
psql -U postgres -d gramedia -f sql/01_target_transaksi.sql
psql -U postgres -d gramedia -f sql/02_users.sql
```

### 2. Menjalankan Server Lokal
Anda dapat menggunakan server bawaan PHP dari dalam direktori `Jobsheet-12`:
```bash
php -S localhost:8000
```
Buka peramban (*browser*) dan arahkan ke alamat:
`http://localhost:8000`

---

## 🧪 Alur Simulasi & Demonstrasi Fitur

1. **Autentikasi Akun**: Masuk menggunakan akun terdaftar atau buat akun baru via menu *Registrasi Pengguna*.
2. **Inisialisasi Target Tabungan**: Buat dua target berbeda (contoh: "Laptop Kerja" dan "Dana Cadangan").
3. **Pencatatan Setoran**: Lakukan transaksi setor sejumlah Rp 1.000.000 pada target "Laptop Kerja". Amati pembaruan saldo di halaman utama.
4. **Eksekusi Pindah Saldo**: Masuk ke menu *Transaksi* &rarr; *Pindah Saldo*, alihkan dana sebesar Rp 300.000 dari "Laptop Kerja" ke "Dana Cadangan". Verifikasi penyesuaian saldo kedua target secara seketika.
5. **Pemeriksaan Mutasi per Target**: Buka menu *Riwayat per Target* untuk melihat rincian arus kas masuk dan keluar masing-masing pos tabungan.
6. **Koreksi Transaksi (Reversal)**: Batalkan transaksi pada daftar transaksi utama dan konfirmasi bahwa saldo target kembali ke nilai semula secara akurat.
