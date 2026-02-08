<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';
include 'header_jamaah.php';

// Validasi koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
$id_jamaah = $_SESSION['id_jamaah'];
// Inisialisasi variabel pencarian
$search_meninggal = '';
$search_ekonomi = '';

// Periksa apakah pencarian dikirimkan melalui GET
if (isset($_GET['search_meninggal'])) {
    $search_meninggal = $_GET['search_meninggal'];
}
if (isset($_GET['search_ekonomi'])) {
    $search_ekonomi = $_GET['search_ekonomi'];
}

// Query untuk mengambil data Pembatalan Meninggal Dunia
$query_meninggal = "SELECT * FROM pembatalan_meninggal WHERE alasan = 'Meninggal Dunia'";
$result_meninggal = mysqli_query($koneksi, $query_meninggal);

// Query untuk mengambil data Pembatalan Keperluan Ekonomi
$query_ekonomi = "SELECT * FROM pembatalan_ekonomi WHERE alasan = 'Keperluan Ekonomi'";
$result_ekonomi = mysqli_query($koneksi, $query_ekonomi);

// Jika ada pencarian pada masing-masing kategori
if ($search_meninggal != '') {
    $query_meninggal = "SELECT * FROM pembatalan_meninggal 
                        WHERE nama_jamaah LIKE '%$search_meninggal%'";
}
if ($search_ekonomi != '') {
    $query_ekonomi = "SELECT * FROM pembatalan_ekonomi 
                      WHERE nama_jamaah LIKE '%$search_ekonomi%'";
}

// Eksekusi query
$result_meninggal = mysqli_query($koneksi, $query_meninggal);
if (!$result_meninggal) {
    die("Query Error (Meninggal): " . mysqli_error($koneksi));
}

$result_ekonomi = mysqli_query($koneksi, $query_ekonomi);
if (!$result_ekonomi) {
    die("Query Error (Ekonomi): " . mysqli_error($koneksi));
}
?>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="pembatalan.css">
<div class="container">
    <h2>Pembatalan Haji</h2>

    <div class="row" style="display: flex; justify-content: space-between;">
    <!-- Form Pencarian untuk Meninggal Dunia -->
    <div class="col-md-6" style="flex: 0 0 48%;">
        <form method="GET" action="pembatalan_haji.php">
            <div class="cari mb-3">
                <input type="text" name="search_meninggal" class="form-control" placeholder="Cari Meninggal Dunia..." 
                       value="<?php echo isset($_GET['search_meninggal']) ? $_GET['search_meninggal'] : ''; ?>">
                <button class="btn btn-danger" type="submit">Cari</button>
            </div>
        </form>
    </div>

    <!-- Form Pencarian untuk Keperluan Ekonomi -->
    <div class="col-md-6" style="flex: 0 0 48%;">
        <form method="GET" action="pembatalan_haji.php">
            <div class="cari mb-3">
                <input type="text" name="search_ekonomi" class="form-control" placeholder="Cari Keperluan Ekonomi..." 
                       value="<?php echo isset($_GET['search_ekonomi']) ? $_GET['search_ekonomi'] : ''; ?>">
                <button class="btn btn-warning" type="submit">Cari</button>
            </div>
        </form>
    </div>
</div>

    <br>

    <!-- Navigasi ke form masing-masing -->
    <div class="mb-3">
        <a href="tambah_pembatalan_meninggal.php" class="btn btn-danger">Pembatalan Meninggal Dunia</a>
        <a href="tambah_pembatalan_ekonomi.php" class="btn btn-warning">Pembatalan Keperluan Ekonomi</a>
    </div>

    <!-- Tabel Pembatalan Meninggal Dunia -->
    <h3>Pembatalan Meninggal Dunia</h3>
    <div class="table-responsive">
    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA JAMAAH</th>
                <th>TEMPAT LAHIR</th>
                <th>TANGGAL LAHIR</th>
                <th>ALAMAT</th>
                <th>KECAMATAN</th>
                <th>JENIS KELAMIN</th>
                <th>PEKERJAAN</th>
                <th>BPS</th>
                <th>Nomor Rekening</th>
                <th>BIN/BINTI</th>
                <th>NOMOR PORSI</th>
                <th>SPPH/VALIDASI</th>
                <th>ALASAN</th>
                <th>TANGGAL SURAT</th>
                <th>TANGGAL REGISTER</th>
                <th>NOMOR SURAT</th>
                <th>NOMINAL SETORAN</th>
                <th>Nomor Rekening (Ahli Waris)</th>
                <th>Bank (Bank Ahli Waris)</th>
                <th>Nama Ahli Waris</th>
                <th>Jenis Kelamin (Ahli Waris)</th>
                <th>Tanggal Lahir (Ahli Waris)</th>
                <th>Status dengan Ahli Waris</th>
                <th>Alamat (Ahli Waris)</th>
                <th>Kecamatan (Ahli Waris)</th>
                <th>NOMOR TELEPON</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result_meninggal)) : ?>
                <tr>
                    <td><?php echo $row['id_batal_meninggal']; ?></td>
                    <td><?php echo $row['nama_jamaah']; ?></td>
                    <td><?php echo $row['tempat_lahir']; ?></td>
                    <td><?php echo $row['tanggal_lahir']; ?></td>
                    <td><?php echo $row['alamat']; ?></td>
                    <td><?php echo $row['kecamatan']; ?></td>
                    <td><?php echo $row['jenis_kelamin']; ?></td>
                    <td><?php echo $row['pekerjaan']; ?></td>
                    <td><?php echo $row['bps']; ?></td>
                    <td><?php echo $row['nomor_rek']; ?></td>
                    <td><?php echo $row['bin_binti']; ?></td>
                    <td><?php echo $row['nomor_porsi']; ?></td>
                    <td><?php echo $row['spph_validasi']; ?></td>
                    <td><?php echo $row['alasan']; ?></td>
                    <td><?php echo $row['tanggal_surat']; ?></td>
                    <td><?php echo $row['tanggal_register']; ?></td>
                    <td><?php echo $row['nomor_surat']; ?></td>
                    <td><?php echo $row['nominal_setoran']; ?></td>
                    <td><?php echo $row['no_rekening_ahliwaris']; ?></td>
                    <td><?php echo $row['bank_ahliwaris']; ?></td>
                    <td><?php echo $row['nama_ahliwaris']; ?></td>
                    <td><?php echo $row['jenis_kelamin_ahliwaris']; ?></td>
                    <td><?php echo $row['tanggal_lahir_ahliwaris']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['alamat_ahliwaris']; ?></td>
                    <td><?php echo $row['kecamatan_ahliwaris']; ?></td>
                    <td><?php echo $row['no_telepon']; ?></td>
                    <td>
                        <!-- Tombol Edit -->
                        <a class="btn btn-warning btn-sm" href="edit_pembatalan_meninggal.php?id=<?php echo $row['id_batal_meninggal']; ?>">Edit</a> 
                        <!-- Tombol Hapus -->
                        <a class="btn btn-danger btn-sm" href="hapus_pembatalan_meninggal.php?id=<?php echo $row['id_batal_meninggal']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a> 
                        <!-- Tombol Cetak -->
                        <a class="btn btn-success btn-sm" href="cetak_pembatalan_meninggal.php?id=<?php echo $row['id_batal_meninggal']; ?>">Cetak</a>
                    </td>        
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    </div>
    
    <!-- Tabel Pembatalan Keperluan Ekonomi -->
    <h3>Pembatalan Keperluan Ekonomi</h3>
<div class="table-responsive">
<table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA JAMAAH</th>
                <th>TEMPAT LAHIR</th>
                <th>TANGGAL LAHIR</th>
                <th>ALAMAT</th>
                <th>KECAMATAN</th>
                <th>JENIS KELAMIN</th>
                <th>PEKERJAAN</th>
                <th>BPS</th>
                <th>Nomor Rekening</th>
                <th>BIN/BINTI</th>
                <th>NOMOR PORSI</th>
                <th>SPPH/VALIDASI</th>
                <th>ALASAN</th>
                <th>TANGGAL SURAT</th>
                <th>TANGGAL REGISTER</th>
                <th>NOMOR SURAT</th>
                <th>NOMINAL SETORAN</th>
                <th>NOMOR TELEPON</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result_ekonomi)) : ?>
                <tr>
                    <td><?php echo $row['id_batal_ekonomi']; ?></td>
                    <td><?php echo $row['nama_jamaah']; ?></td>
                    <td><?php echo $row['tempat_lahir']; ?></td>
                    <td><?php echo $row['tanggal_lahir']; ?></td>
                    <td><?php echo $row['alamat']; ?></td>
                    <td><?php echo $row['kecamatan']; ?></td>
                    <td><?php echo $row['jenis_kelamin']; ?></td>
                    <td><?php echo $row['pekerjaan']; ?></td>
                    <td><?php echo $row['bps']; ?></td>
                    <td><?php echo $row['nomor_rek']; ?></td>
                    <td><?php echo $row['bin_binti']; ?></td>
                    <td><?php echo $row['nomor_porsi']; ?></td>
                    <td><?php echo $row['spph_validasi']; ?></td>
                    <td><?php echo $row['alasan']; ?></td>
                    <td><?php echo $row['tanggal_surat']; ?></td>
                    <td><?php echo $row['tanggal_register']; ?></td>
                    <td><?php echo $row['nomor_surat']; ?></td>
                    <td><?php echo $row['nominal_setoran']; ?></td>
                    <td><?php echo $row['no_telepon']; ?></td>
                    <td>
                    <!-- Tombol Edit -->
                    <a class="btn btn-warning btn-sm" href="edit_pembatalan_ekonomi.php?id=<?php echo $row['id_batal_ekonomi']; ?>">Edit</a>
                    <!-- Tombol Hapus -->
                    <a class="btn btn-danger btn-sm" href="hapus_pembatalan_ekonomi.php?id=<?php echo $row['id_batal_ekonomi']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?')">Hapus</a> 
                    <!-- Tombol Cetak -->
                    <a class="btn btn-success btn-sm" href="cetak_pembatalan_ekonomi.php?id=<?php echo $row['id_batal_ekonomi']; ?>">Cetak</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
    
</div>

<?php
include 'footer.php';
?>
