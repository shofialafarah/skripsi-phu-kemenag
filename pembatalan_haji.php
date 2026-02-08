<?php
include 'koneksi.php';

// Validasi koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
// Query gabungan
    $query = "
        SELECT pe.*, p.tanggal_pengajuan, p.kategori 
        FROM pembatalan_ekonomi pe
        JOIN pembatalan p ON pe.id_pembatalan = p.id_pembatalan
        WHERE p.id_jamaah = ?
        ORDER BY p.tanggal_pengajuan DESC 
    ";
// Inisialisasi variabel pencarian dan filter
$search_nama_jamaah = '';
$filter_kategori = '';

// Periksa apakah pencarian dikirimkan melalui GET
if (isset($_GET['search_nama_jamaah'])) {
    $search_nama_jamaah = $_GET['search_nama_jamaah'];
}
if (isset($_GET['filter_kategori'])) {
    $filter_kategori = $_GET['filter_kategori'];
}

// Query untuk mengambil data pembatalan Meninggal Dunia
$query_meninggal = "SELECT * FROM pembatalan_meninggal WHERE alasan = 'Meninggal Dunia'";
if ($search_nama_jamaah != '') {
    $query_meninggal .= " AND nama_jamaah LIKE '%$search_nama_jamaah%'";
}

// Query untuk mengambil data pembatalan Keperluan Ekonomi
$query_ekonomi = "SELECT * FROM pembatalan_ekonomi WHERE alasan = 'Keperluan Ekonomi'";
if ($search_nama_jamaah != '') {
    $query_ekonomi .= " AND nama_jamaah LIKE '%$search_nama_jamaah%'";
}

// Menyesuaikan query berdasarkan filter kategori
if ($filter_kategori == 'meninggal') {
    $query_ekonomi = ""; // Jangan tampilkan data ekonomi jika filter meninggal dipilih
} elseif ($filter_kategori == 'ekonomi') {
    $query_meninggal = ""; // Jangan tampilkan data meninggal jika filter ekonomi dipilih
}

// Menjalankan query untuk pembatalan meninggal dunia
if (!empty($query_meninggal)) {
    $result_meninggal = mysqli_query($koneksi, $query_meninggal);
    if (!$result_meninggal) {
        die("Query Error (Meninggal): " . mysqli_error($koneksi));
    }
} else {
    $result_meninggal = false; // Set to false instead of null
}

// Menjalankan query untuk pembatalan ekonomi
if (!empty($query_ekonomi)) {
    $result_ekonomi = mysqli_query($koneksi, $query_ekonomi);
    if (!$result_ekonomi) {
        die("Query Error (Ekonomi): " . mysqli_error($koneksi));
    }
} else {
    $result_ekonomi = false; // Set to false instead of null
}

// Query untuk mengambil data pembatalan Meninggal Dunia
$query_meninggal = "SELECT * FROM pembatalan_meninggal WHERE alasan = 'Meninggal Dunia'";
if ($search_nama_jamaah != '') {
    $query_meninggal .= " AND (nama_jamaah LIKE '%$search_nama_jamaah%' OR nomor_porsi LIKE '%$search_nama_jamaah%')";
}

// Query untuk mengambil data pembatalan Keperluan Ekonomi
$query_ekonomi = "SELECT * FROM pembatalan_ekonomi WHERE alasan = 'Keperluan Ekonomi'";
if ($search_nama_jamaah != '') {
    $query_ekonomi .= " AND (nama_jamaah LIKE '%$search_nama_jamaah%' OR nomor_porsi LIKE '%$search_nama_jamaah%')";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Staf</title>
    <link rel="icon" href="logo_kemenag.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_staf.php'; ?>
        </div>

        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header.php'; ?>
            <div class="pembatalan-container">
                <h2>Pembatalan Haji</h2>
                <!-- Form Filter Kategori -->
                <form method="GET" action="pembatalan_haji.php" class="mb-3">
                    <select name="filter_kategori" class="form-control" style="width: 150px; display: inline-block;">
                        <option value="">Pilih Kategori</option>
                        <option value="meninggal" <?php echo (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == 'meninggal') ? 'selected' : ''; ?>>Meninggal Dunia</option>
                        <option value="ekonomi" <?php echo (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == 'ekonomi') ? 'selected' : ''; ?>>Keperluan Ekonomi</option>
                    </select>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i></button>
                </form>

                <?php if ($filter_kategori == '' || $filter_kategori == 'meninggal') : ?>
                    <!-- Tabel Pembatalan Meninggal Dunia -->
                    <h3>Pembatalan Meninggal Dunia</h3>
                    <div class="row" style="display: flex; justify-content: space-between;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="tambah_pembatalan_meninggal.php" class="btn btn-primary">Tambah Data</a>
                            <!-- Form Pencarian berdasarkan Nama Jamaah -->
                            <form method="GET" action="pembatalan_haji.php" class="d-flex" style="gap: 8px;">
                                <input type="text" name="search_nama_jamaah" class="form-control"
                                    placeholder="Cari Nama atau Nomor Porsi..."
                                    value="<?php echo isset($_GET['search_nama_jamaah']) ? $_GET['search_nama_jamaah'] : ''; ?>">
                                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped tabelPembatalan">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA JAMAAH</th>
                                    <th>BIN/BINTI</th>
                                    <th>NOMOR PORSI</th>
                                    <th>TEMPAT, TANGGAL LAHIR</th>
                                    <th>ALAMAT</th>
                                    <th>KECAMATAN</th>
                                    <th>KELURAHAN</th>
                                    <th>JENIS KELAMIN</th>
                                    <th>PEKERJAAN</th>
                                    <th>BPS</th>
                                    <th>Nomor Rekening</th>
                                    <th>SPPH/VALIDASI</th>
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
                                    <th>Kelurahan (Ahli Waris)</th>
                                    <th>NOMOR TELEPON</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no_meninggal = 1; // Variabel penghitung
                                if ($result_meninggal) {
                                    while ($row = mysqli_fetch_assoc($result_meninggal)) : ?>
                                        <tr>
                                            <td><?php echo $no_meninggal++; ?></td>
                                            <td><?php echo $row['nama_jamaah']; ?></td>
                                            <td><?php echo $row['bin_binti']; ?></td>
                                            <td> <b>
                                                    <span style="background-color:rgb(229, 255, 224); color: green; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                        <?php echo $row['nomor_porsi']; ?>
                                                    </span>
                                                </b>
                                            </td>

                                            <td><?php echo $row['tempat_lahir'] . ', ' . date('d M Y', strtotime($row['tanggal_lahir'])); ?></td>
                                            <td><?php echo $row['alamat']; ?></td>
                                            <td><?php echo $row['kecamatan']; ?></td>
                                            <td><?php echo $row['kelurahan']; ?></td>
                                            <td><?php echo $row['jenis_kelamin']; ?></td>
                                            <td><?php echo $row['pekerjaan']; ?></td>
                                            <td><?php echo $row['bps']; ?></td>
                                            <td><?php echo $row['nomor_rek']; ?></td>
                                            <td> <b>
                                                    <span style="background-color:rgb(248, 224, 255); color: purple; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                        <?php echo $row['spph_validasi']; ?>
                                                    </span>
                                                </b>
                                            </td>
                                            <td><?php echo $row['tanggal_surat']; ?></td>
                                            <td><?php echo $row['tanggal_register']; ?></td>
                                            <td><?php echo $row['nomor_surat']; ?></td>
                                            <td><?php echo $row['nominal_setoran']; ?></td>
                                            <td><?php echo $row['no_rekening_ahliwaris']; ?></td>
                                            <td><?php echo $row['bank_ahliwaris']; ?></td>
                                            <td><?php echo $row['nama_ahliwaris']; ?></td>
                                            <td><?php echo $row['jenis_kelamin_ahliwaris']; ?></td>
                                            <td><?php echo $row['tanggal_lahir_ahliwaris']; ?></td>
                                            <td><?php echo $row['status_dengan_jamaah']; ?></td>
                                            <td><?php echo $row['alamat_ahliwaris']; ?></td>
                                            <td><?php echo $row['kecamatan_ahliwaris']; ?></td>
                                            <td><?php echo $row['kelurahan_ahliwaris']; ?></td>
                                            <td><?php echo $row['no_telepon_ahliwaris']; ?></td>
                                            <td>
                                                <!-- Tombol Edit -->
                                                <a class="btn btn-warning btn-sm" href="edit_pembatalan_meninggal.php?id=<?php echo $row['id_batal_meninggal']; ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                                <!-- Tombol Hapus -->
                                                <a class="btn btn-danger btn-sm" href="hapus_pembatalan_meninggal.php?id=<?php echo $row['id_batal_meninggal']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?')"><i class="fa-solid fa-trash"></i></a>
                                                <!-- Tombol Cetak -->
                                                <?php
                                                $isVerified = isset($row['tanggal_verifikasi_kasi']) && !empty($row['tanggal_verifikasi_kasi']);
                                                $cetakUrl = 'cetak_pembatalan_meninggal.php?id=' . $row['id_batal_meninggal'];
                                                $style = $isVerified ? '' : "style='pointer-events: none; color: gray;'";
                                                ?>
                                                <a class="btn btn-success btn-sm" href="<?php echo $cetakUrl; ?>" <?php echo $style; ?>><i class="fa-solid fa-print"></i></a>

                                            </td>
                                        </tr>
                                <?php endwhile;
                                } else {
                                    echo "<tr><td colspan='25'>Data tidak ditemukan</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>



                <?php if ($filter_kategori == '' || $filter_kategori == 'ekonomi') : ?>
                    <!-- Tabel Pembatalan Keperluan Ekonomi -->
                    <h3>Pembatalan Keperluan Ekonomi</h3>

                    <div class="row" style="display: flex; justify-content: space-between;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="tambah_pembatalan_ekonomi.php" class="btn btn-primary">Tambah Data</a>
                            <!-- Form Pencarian berdasarkan Nama Jamaah -->
                            <form method="GET" action="pembatalan_haji.php">
                                <div class="cari mb-3">
                                    <!-- Input Pencarian Nama Jamaah -->
                                    <input type="text" name="search_nama_jamaah" class="form-control" placeholder="Cari Nama Jamaah..."
                                        value="<?php echo isset($_GET['search_nama_jamaah']) ? $_GET['search_nama_jamaah'] : ''; ?>">
                                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped tabelPembatalan">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA JAMAAH</th>
                                    <th>BIN/BINTI</th>
                                    <th>NOMOR PORSI</th>
                                    <th>TEMPAT, TANGGAL LAHIR</th>
                                    <th>ALAMAT</th>
                                    <th>KECAMATAN</th>
                                    <th>KELURAHAN</th>
                                    <th>JENIS KELAMIN</th>
                                    <th>PEKERJAAN</th>
                                    <th>NOMOR TELEPON</th>
                                    <th>BPS</th>
                                    <th>Nomor Rekening</th>
                                    <th>SPPH/VALIDASI</th>
                                    <th>TANGGAL SURAT</th>
                                    <th>TANGGAL REGISTER</th>
                                    <th>NOMOR SURAT</th>
                                    <th>NOMINAL SETORAN</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no_ekonomi = 1; // Variabel penghitung
                                while ($row = mysqli_fetch_assoc($result_ekonomi)) : ?>
                                    <tr>
                                        <td><?php echo $no_ekonomi++; ?></td>
                                        <td><?php echo $row['nama_jamaah']; ?></td>
                                        <td><?php echo $row['bin_binti']; ?></td>
                                        <td> <b>
                                                <span style="background-color:rgb(229, 255, 224); color: green; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                    <?php echo $row['nomor_porsi']; ?>
                                                </span>
                                            </b>
                                        </td>
                                        <td><?php echo $row['tempat_lahir'] . ', ' . date('d M Y', strtotime($row['tanggal_lahir'])); ?></td>
                                        <td><?php echo $row['alamat']; ?></td>
                                        <td><?php echo $row['kecamatan']; ?></td>
                                        <td><?php echo $row['kelurahan']; ?></td>
                                        <td><?php echo $row['jenis_kelamin']; ?></td>
                                        <td><?php echo $row['pekerjaan']; ?></td>
                                        <td><?php echo $row['no_telepon']; ?></td>
                                        <td><?php echo $row['bps']; ?></td>
                                        <td><?php echo $row['nomor_rek']; ?></td>
                                        <td> <b>
                                                <span style="background-color:rgb(248, 224, 255); color: purple; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                    <?php echo $row['spph_validasi']; ?>
                                                </span>
                                            </b>
                                        </td>
                                        <td><?php echo $row['tanggal_surat']; ?></td>
                                        <td><?php echo $row['tanggal_register']; ?></td>
                                        <td><?php echo $row['nomor_surat']; ?></td>
                                        <td><?php echo $row['nominal_setoran']; ?></td>

                                        <td>
                                            <!-- Tombol Edit -->
                                            <a class="btn btn-warning btn-sm" href="edit_pembatalan_ekonomi.php?id=<?php echo $row['id_batal_ekonomi']; ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <!-- Tombol Hapus -->
                                            <a class="btn btn-danger btn-sm" href="hapus_pembatalan_ekonomi.php?id=<?php echo $row['id_batal_ekonomi']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?')"><i class="fa-solid fa-trash"></i></a>
                                            <!-- Tombol Cetak -->
                                            <?php
                                            $isVerified = isset($row['tanggal_verifikasi_kasi']) && !empty($row['tanggal_verifikasi_kasi']);
                                            $cetakUrl = 'cetak_pembatalan_ekonomi.php?id=' . $row['id_batal_ekonomi'];
                                            $style = $isVerified ? '' : "style='pointer-events: none; color: gray;'";
                                            ?>
                                            <a class="btn btn-success btn-sm" href="<?php echo $cetakUrl; ?>" <?php echo $style; ?>><i class="fa-solid fa-print"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- <?php include 'footer.php'; ?> -->
</body>

</html>