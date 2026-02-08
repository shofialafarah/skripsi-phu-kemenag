<?php
include 'header.php';
?>

<h2>Tambah Pembatalan Haji</h2>
<hr>

<!-- Tombol Pilihan untuk Meninggal Dunia atau Keperluan Ekonomi -->
<div class="btn-group" role="group" aria-label="Basic example">
    <button type="button" class="btn btn-primary" id="btnMeninggalDunia">Meninggal Dunia</button>
    <button type="button" class="btn btn-secondary" id="btnKeperluanEkonomi">Keperluan Ekonomi</button>
</div>

<!-- Form Pembatalan Meninggal Dunia -->
<div id="formMeninggalDunia" style="display: none; margin-top: 20px;">
    <form method="post" action="proses_tambah_pembatalan.php">
        <h3>Form Pembatalan - Meninggal Dunia</h3>
        <label>Nama / Bin:</label>
        <input type="text" name="nama_bin" class="form-control" required>

        <label>Tempat Lahir:</label>
        <input type="text" name="tempat_lahir" class="form-control" required>

        <label>Tanggal Lahir:</label>
        <input type="date" name="tanggal_lahir" class="form-control" required>

        <label>Alamat:</label>
        <textarea name="alamat" class="form-control" required></textarea>

        <label>Kecamatan:</label>
        <input type="text" name="kecamatan" class="form-control" required>

        <label>Jenis Kelamin:</label>
        <select name="jenis_kelamin" class="form-control" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>

        <label>Pekerjaan:</label>
        <input type="text" name="pekerjaan" class="form-control" required>

        <label>BPS:</label>
        <input type="text" name="bps" class="form-control" required>

        <label>No. Rekening:</label>
        <input type="text" name="no_rekening" class="form-control" required>

        <label>BIN/BINTI:</label>
        <input type="text" name="bin_binti" class="form-control" required>

        <label>Nomor Porsi:</label>
        <input type="text" name="nomor_porsi" class="form-control" required>

        <label>SPPH / Validasi:</label>
        <input type="text" name="spph_validasi" class="form-control" required>

        <label>Tanggal Surat:</label>
        <input type="date" name="tanggal_surat" class="form-control" required>

        <label>Tanggal Register:</label>
        <input type="date" name="tanggal_register" class="form-control" required>

        <label>Nomor Surat:</label>
        <input type="text" name="nomor_surat" class="form-control" required>

        <label>Nominal Setoran:</label>
        <input type="number" name="nominal_setoran" class="form-control" required>

        <!-- Inputan Ahli Waris -->
        <h4>Data Ahli Waris</h4>
        <label>Nama Ahli Waris:</label>
        <input type="text" name="nama_ahli_waris" class="form-control" required>

        <label>No. Rekening Ahli Waris:</label>
        <input type="text" name="no_rekening_ahli_waris" class="form-control" required>

        <label>Bank Ahli Waris:</label>
        <input type="text" name="bank_ahli_waris" class="form-control" required>

        <label>Jenis Kelamin Ahli Waris:</label>
        <select name="jenis_kelamin_ahli_waris" class="form-control">
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>

        <label>Pekerjaan Ahli Waris:</label>
        <input type="text" name="pekerjaan_ahli_waris" class="form-control" required>

        <label>Tanggal Lahir Ahli Waris:</label>
        <input type="date" name="tanggal_lahir_ahli_waris" class="form-control" required>

        <label>Status dengan Jamaah:</label>
        <input type="text" name="status_dengan_jamaah" class="form-control" required>

        <label>Alamat Ahli Waris:</label>
        <textarea name="alamat_ahli_waris" class="form-control" required></textarea>

        <label>Kecamatan Ahli Waris:</label>
        <input type="text" name="kecamatan_ahli_waris" class="form-control" required>

        <label>No. Telpon Ahli Waris:</label>
        <input type="text" name="no_telpon_ahli_waris" class="form-control" required>

        <br>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<!-- Form Pembatalan Keperluan Ekonomi -->
<div id="formKeperluanEkonomi" style="display: none; margin-top: 20px;">
    <form method="post" action="proses_tambah_pembatalan.php">
        <h3>Form Pembatalan - Keperluan Ekonomi</h3>
        <label>Nama / Bin:</label>
        <input type="text" name="nama_bin" class="form-control" required>

        <label>Tempat Lahir:</label>
        <input type="text" name="tempat_lahir" class="form-control" required>

        <label>Tanggal Lahir:</label>
        <input type="date" name="tanggal_lahir" class="form-control" required>

        <label>Alamat:</label>
        <textarea name="alamat" class="form-control" required></textarea>

        <label>Kecamatan:</label>
        <input type="text" name="kecamatan" class="form-control" required>

        <label>Jenis Kelamin:</label>
        <select name="jenis_kelamin" class="form-control" required>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
        </select>

        <label>Pekerjaan:</label>
        <input type="text" name="pekerjaan" class="form-control" required>

        <label>BPS:</label>
        <input type="text" name="bps" class="form-control" required>

        <label>No. Rekening:</label>
        <input type="text" name="no_rekening" class="form-control" required>

        <label>BIN/BINTI:</label>
        <input type="text" name="bin_binti" class="form-control" required>

        <label>Nomor Porsi:</label>
        <input type="text" name="nomor_porsi" class="form-control" required>

        <label>SPPH / Validasi:</label>
        <input type="text" name="spph_validasi" class="form-control" required>

        <label>Tanggal Surat:</label>
        <input type="date" name="tanggal_surat" class="form-control" required>

        <label>Tanggal Register:</label>
        <input type="date" name="tanggal_register" class="form-control" required>

        <label>Nomor Surat:</label>
        <input type="text" name="nomor_surat" class="form-control" required>

        <label>Nominal Setoran:</label>
        <input type="number" name="nominal_setoran" class="form-control" required>

        <br>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
    // Mengatur tampilan form berdasarkan pilihan tombol
    document.getElementById("btnMeninggalDunia").onclick = function() {
        document.getElementById("formMeninggalDunia").style.display = "block";
        document.getElementById("formKeperluanEkonomi").style.display = "none";
    };

    document.getElementById("btnKeperluanEkonomi").onclick = function() {
        document.getElementById("formKeperluanEkonomi").style.display = "block";
        document.getElementById("formMeninggalDunia").style.display = "none";
    };
</script>

<?php
include 'footer.php';
?>
