-- Jobsheet 12: Integrasi Transaksi Antar-Target (Pindah Saldo) & Concurrency Control
-- File: sql/03_pindah_saldo.sql
-- Pada Jobsheet 12 ini, fitur Pindah Saldo memanfaatkan transaksi atomik (BEGIN, COMMIT, ROLLBACK)
-- dan pessimistic locking (SELECT ... FOR UPDATE) untuk memindahkan saldo antar baris tabel target
-- secara konsisten dan mencatat mutasi ke tabel transaksi.

-- Pastikan tabel target dan transaksi sudah dibuat dari 01_target_transaksi.sql
-- Verifikasi skema tabel yang digunakan:

SELECT 'Memeriksa tabel target...' AS status;
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'target';

SELECT 'Memeriksa tabel transaksi...' AS status;
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'transaksi';

-- Contoh simulasi transaksi pindah saldo di PostgreSQL (CLI / psql):
-- BEGIN;
--   -- 1. Kunci kedua baris target untuk mencegah race condition
--   SELECT saldo_sekarang FROM target WHERE id IN (1, 2) FOR UPDATE;
--   -- 2. Kurangi saldo target asal
--   UPDATE target SET saldo_sekarang = saldo_sekarang - 50000 WHERE id = 1;
--   -- 3. Tambah saldo target tujuan
--   UPDATE target SET saldo_sekarang = saldo_sekarang + 50000 WHERE id = 2;
--   -- 4. Catat riwayat penarikan dan setoran pemindahan
--   INSERT INTO transaksi (target_id, jenis_transaksi, jumlah, catatan) 
--   VALUES (1, 'tarik', 50000, 'Pindah saldo ke Target #2');
--   INSERT INTO transaksi (target_id, jenis_transaksi, jumlah, catatan) 
--   VALUES (2, 'setor', 50000, 'Pindah saldo dari Target #1');
-- COMMIT;
