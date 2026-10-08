-- Jobsheet 10: skema database tabungan (PostgreSQL)
-- Jalankan: psql -d gramedia -f sql/01_target_transaksi.sql

CREATE TABLE IF NOT EXISTS target (
    id SERIAL PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    target_nominal DECIMAL(15,2) NOT NULL,
    saldo_sekarang DECIMAL(15,2) NOT NULL DEFAULT 0,
    kategori VARCHAR(100),
    deadline DATE,
    link_barang VARCHAR(500)
);

CREATE TABLE IF NOT EXISTS transaksi (
    id SERIAL PRIMARY KEY,
    target_id INT NOT NULL,
    jenis_transaksi VARCHAR(20) NOT NULL, -- 'setor' atau 'tarik'
    jumlah DECIMAL(15,2) NOT NULL,
    catatan TEXT,
    tanggal_transaksi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (target_id) REFERENCES target(id)
);
