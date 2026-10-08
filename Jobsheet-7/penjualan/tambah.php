<?php
$page_title = "Buat Transaksi";
include __DIR__ . '/../includes/header.php';
?>
        <section>
            <h2>Buat Transaksi</h2>
            <form id="form-tambah" style="max-width: 500px; margin: 0 auto;">
                <p>
                    <label for="id_transaksi">ID Transaksi</label><br>
                    <input type="text" id="id_transaksi" name="id_transaksi" readonly placeholder="Auto-generated">
                </p>
                <p>
                    <label for="tanggal">Tanggal</label><br>
                    <input type="date" id="tanggal" name="tanggal" required>
                </p>
                <p>
                    <label for="buku">Buku</label><br>
                    <select id="buku" name="buku" required>
                        <option value="">Pilih Buku</option>
                        <option value="Laskar Pelangi">Laskar Pelangi</option>
                        <option value="Bumi Manusia">Bumi Manusia</option>
                        <option value="Negeri 5 Menara">Negeri 5 Menara</option>
                    </select>
                </p>
                <p>
                    <label for="jumlah">Jumlah</label><br>
                    <input type="number" id="jumlah" name="jumlah" min="1" required>
                </p>
                <p>
                    <label for="total">Total Harga</label><br>
                    <input type="number" id="total" name="total" required>
                </p>
                <p>
                    <button type="submit">Simpan</button>
                </p>
            </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
