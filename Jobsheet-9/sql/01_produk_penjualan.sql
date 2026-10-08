-- Jobsheet 8: skema awal database simpus_mini (MySQL)
-- Jalankan setelah membuat database, misal:
--   CREATE DATABASE simpus_mini;
--   mysql -u root -p simpus_mini < sql/01_buku_anggota.sql


CREATE TABLE IF NOT EXISTS produk (
    id SERIAL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengarang VARCHAR(255) NOT NULL,
    tahun INT NOT NULL,
    isbn VARCHAR(50),
    stok INT NOT NULL DEFAULT 0,
    kategori VARCHAR(50),
    harga DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS penjualan (
    id SERIAL PRIMARY KEY,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    tanggal_penjualan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);
