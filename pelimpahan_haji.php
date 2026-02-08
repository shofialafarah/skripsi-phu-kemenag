<?php
include 'koneksi.php';

// Fungsi untuk format tanggal Indonesia
function formatTanggalIndonesia($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00') return '-';
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
}

// Inisialisasi variabel pencarian dan filter
$search_nama_jamaah = isset($_GET['search_nama_jamaah']) ? $_GET['search_nama_jamaah'] : '';
$filter_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';

// Escape input untuk keamanan
$search_nama_jamaah = mysqli_real_escape_string($koneksi, $search_nama_jamaah);

// Query untuk data meninggal
$query_meninggal = "SELECT 
    pm.id_limpah_meninggal,
    pm.nama_jamaah,
    pm.alasan,
    pm.nama_ahliwaris,
    pm.nama_ayah_ahliwaris,
    pm.tempat_lahir_ahliwaris,
    pm.tanggal_lahir_ahliwaris,
    pm.alamat_ahliwaris,
    pm.kecamatan_ahliwaris,
    pm.kelurahan_ahliwaris,
    pm.kode_pos_ahliwaris,
    pm.jenis_kelamin_ahliwaris,
    pm.pekerjaan_ahliwaris,
    pm.no_telepon_ahliwaris,
    pm.bank_ahliwaris,
    pm.no_rekening_ahliwaris,
    pm.nama_kuasa_waris,
    pm.status_dengan_jamaah,
    pm.bin_binti,
    pm.nomor_porsi,
    pm.tempat_lahir_jamaah,
    pm.tanggal_lahir_jamaah,
    pm.alamat_jamaah,
    pm.kecamatan_jamaah,
    pm.kelurahan_jamaah,
    pm.kode_pos_jamaah,
    pm.bps,
    pm.nomor_rekening,
    pm.nominal_setoran,
    pm.spph_validasi,
    pm.tanggal_register,
    pm.tanggal_wafat,
    pm.tanggal_sptjm,
    pm.tanggal_rekomendasi,
    pm.tanggal_masuk_surat,
    pm.nomor_surat,
    pm.tanggal_verifikasi_kasi,
    p.kategori
FROM pelimpahan_meninggal pm
LEFT JOIN pelimpahan p ON pm.id_pelimpahan = p.id_pelimpahan
WHERE pm.alasan = 'Meninggal Dunia'";

// Query untuk data sakit
$query_sakit = "SELECT 
    ps.id_limpah_sakit,
    ps.nama_jamaah,
    ps.alasan,
    ps.nama_ahliwaris,
    ps.nama_ayah_ahliwaris,
    ps.tempat_lahir_ahliwaris,
    ps.tanggal_lahir_ahliwaris,
    ps.alamat_ahliwaris,
    ps.kecamatan_ahliwaris,
    ps.kelurahan_ahliwaris,
    ps.kode_pos_ahliwaris,
    ps.jenis_kelamin_ahliwaris,
    ps.pekerjaan_ahliwaris,
    ps.no_telepon_ahliwaris,
    ps.bank_ahliwaris,
    ps.no_rekening_ahliwaris,
    ps.nama_kuasa_waris,
    ps.status_dengan_jamaah,
    ps.bin_binti,
    ps.nomor_porsi,
    ps.tempat_lahir_jamaah,
    ps.tanggal_lahir_jamaah,
    ps.alamat_jamaah,
    ps.kecamatan_jamaah,
    ps.kelurahan_jamaah,
    ps.kode_pos_jamaah,
    ps.bps,
    ps.nomor_rekening,
    ps.nominal_setoran,
    ps.spph_validasi,
    ps.tanggal_register,
    ps.tanggal_sakit,
    ps.tanggal_sptjm,
    ps.tanggal_rekomendasi,
    ps.tanggal_masuk_surat,
    ps.nomor_surat,
    ps.tanggal_verifikasi_kasi,
    p.kategori
FROM pelimpahan_sakit ps
LEFT JOIN pelimpahan p ON ps.id_pelimpahan = p.id_pelimpahan
WHERE ps.alasan = 'Sakit Permanen'";

// Tambahkan pencarian nama jika ada
if ($search_nama_jamaah != '') {
    $query_meninggal .= " AND ps.nama_jamaah LIKE '%$search_nama_jamaah%'";
    $query_sakit .= " AND ps.nama_jamaah LIKE '%$search_nama_jamaah%'";
}

// Eksekusi query berdasarkan filter
$result_meninggal = null;
$result_sakit = null;

if ($filter_kategori == '' || $filter_kategori == 'meninggal') {
    $result_meninggal = mysqli_query($koneksi, $query_meninggal);
    if (!$result_meninggal) {
        die("Query Error (Meninggal): " . mysqli_error($koneksi));
    }
}

if ($filter_kategori == '' || $filter_kategori == 'sakit') {
    $result_sakit = mysqli_query($koneksi, $query_sakit);
    if (!$result_sakit) {
        die("Query Error (Sakit): " . mysqli_error($koneksi));
    }
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
            <div class="pelimpahan-container">
                <h2>Pelimpahan Haji</h2>
                
                <!-- Form Filter Kategori -->
                <form method="GET" action="pelimpahan_haji.php" class="mb-3">
                    <select name="filter_kategori" class="form-control" style="width: 150px; display: inline-block;">
                        <option value="">Pilih Kategori</option>
                        <option value="meninggal" <?php echo (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == 'meninggal') ? 'selected' : ''; ?>>Meninggal Dunia</option>
                        <option value="sakit" <?php echo (isset($_GET['filter_kategori']) && $_GET['filter_kategori'] == 'sakit') ? 'selected' : ''; ?>>Sakit Permanen</option>
                    </select>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i></button>
                </form>

                <!-- Form Pencarian -->
                <form method="GET" action="pelimpahan_haji.php" class="mb-3">
                    <input type="hidden" name="filter_kategori" value="<?php echo $filter_kategori; ?>">
                    <input type="text" name="search_nama_jamaah" class="form-control" style="width: 200px; display: inline-block;" 
                           placeholder="Cari nama jamaah..." value="<?php echo htmlspecialchars($search_nama_jamaah); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </form>

                <!-- Tabel pelimpahan Meninggal Dunia -->
                <?php if (($filter_kategori == 'meninggal' || $filter_kategori == '') && $result_meninggal): ?>
                    <h3>Pelimpahan Meninggal Dunia</h3>
                    <a href="tambah_pelimpahan_meninggal.php" class="btn btn-primary">Tambah Data</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <!-- DATA AHLIWARIS -->
                                    <th>NAMA AHLI WARIS</th>
                                    <th>NAMA AYAH</th>
                                    <th>TEMPAT, TANGGAL LAHIR</th>
                                    <th>ALAMAT</th>
                                    <th>KECAMATAN</th>
                                    <th>KELURAHAN</th>
                                    <th>KODE POS</th>
                                    <th>JENIS KELAMIN</th>
                                    <th>PEKERJAAN</th>
                                    <th>NOMOR TELEPON</th>
                                    <th>NAMA BANK</th>
                                    <th>NOMOR REKENING</th>
                                    <th>NAMA KUASA WARIS</th>
                                    <th>STATUS DENGAN JAMAAH</th>
                                    <!-- DATA JAMAAH -->
                                    <th>NAMA JAMAAH</th>
                                    <th>BIN/BINTI</th>
                                    <th>NOMOR PORSI</th>
                                    <th>TEMPAT & TANGGAL LAHIR JAMAAH</th>
                                    <th>ALAMAT JAMAAH</th>
                                    <th>KECAMATAN JAMAAH</th>
                                    <th>KELURAHAN JAMAAH</th>
                                    <th>KODE POS JAMAAH</th>
                                    <th>BPS</th>
                                    <th>NOMOR REKENING</th>
                                    <th>NOMINAL SETORAN</th>
                                    <th>SPPH/VALIDASI</th>
                                    <th>ALASAN</th>
                                    <th>TANGGAL REGISTER</th>
                                    <th>TANGGAL WAFAT</th>
                                    <!-- SURAT -->
                                    <th>TANGGAL SPTJM</th>
                                    <th>TANGGAL REKOMENDASI</th>
                                    <th>TANGGAL SURAT</th>
                                    <th>NOMOR SURAT</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no_meninggal = 1;
                                while ($row = mysqli_fetch_assoc($result_meninggal)) :
                                ?>
                                <tr>
                                    <td><?= $no_meninggal++; ?></td>
                                    <td><?= $row['nama_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['nama_ayah_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= ($row['tempat_lahir_ahliwaris'] ?? '-') . ', ' . formatTanggalIndonesia($row['tanggal_lahir_ahliwaris'] ?? date('Y-m-d')); ?></td>
                                    <td><?= $row['alamat_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['kecamatan_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['kelurahan_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['kode_pos_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['jenis_kelamin_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['pekerjaan_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['no_telepon_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['bank_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['no_rekening_ahliwaris'] ?? '-'; ?></td>
                                    <td><?= $row['nama_kuasa_waris'] ?? '-'; ?></td>
                                    <td><?= $row['status_dengan_jamaah'] ?? '-'; ?></td>
                                    <td><?= $row['nama_jamaah'] ?? '-'; ?></td>
                                    <td><?= $row['bin_binti'] ?? '-'; ?></td>
                                    <td><b><span style="background-color:rgb(229, 255, 224); color: green; padding: 4px 8px; border-radius: 8px;">
                                        <?= $row['nomor_porsi'] ?? '-'; ?>
                                    </span></b></td>
                                    <td><?= ($row['tempat_lahir_jamaah'] ?? '-') . ', ' . formatTanggalIndonesia($row['tanggal_lahir_jamaah'] ?? date('Y-m-d')); ?></td>
                                    <td><?= $row['alamat_jamaah'] ?? '-'; ?></td>
                                    <td><?= $row['kecamatan_jamaah'] ?? '-'; ?></td>
                                    <td><?= $row['kelurahan_jamaah'] ?? '-'; ?></td>
                                    <td><?= $row['kode_pos_jamaah'] ?? '-'; ?></td>
                                    <td><?= $row['bps'] ?? '-'; ?></td>
                                    <td><?= $row['nomor_rekening'] ?? '-'; ?></td>
                                    <td><?= $row['nominal_setoran'] ?? '-'; ?></td>
                                    <td><b><span style="background-color:rgb(248, 224, 255); color: purple; padding: 4px 8px; border-radius: 8px;">
                                        <?= $row['spph_validasi'] ?? '-'; ?>
                                    </span></b></td>
                                    <td><?= $row['alasan'] ?? '-'; ?></td>
                                    <td><?= formatTanggalIndonesia($row['tanggal_register'] ?? date('Y-m-d')); ?></td>
                                    <td><b><span style="background-color:rgb(255, 224, 224); color: red; padding: 4px 8px; border-radius: 8px;">
                                        <?= formatTanggalIndonesia($row['tanggal_wafat'] ?? date('Y-m-d')); ?>
                                    </span></b></td>
                                    <td><?= formatTanggalIndonesia($row['tanggal_sptjm'] ?? date('Y-m-d')); ?></td>
                                    <td><?= formatTanggalIndonesia($row['tanggal_rekomendasi'] ?? date('Y-m-d')); ?></td>
                                    <td><?= formatTanggalIndonesia($row['tanggal_masuk_surat'] ?? date('Y-m-d')); ?></td>
                                    <td><?= $row['nomor_surat'] ?? '-'; ?></td>
                                    <td>
                                        <a class="btn btn-warning btn-sm" href="edit_pelimpahan_meninggal.php?id=<?= $row['id_limpah_meninggal']; ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <a class="btn btn-danger btn-sm" href="hapus_pelimpahan_meninggal.php?id=<?= $row['id_limpah_meninggal']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?')"><i class="fa-solid fa-trash"></i></a>
                                        <?php
                                        $isVerified = !empty($row['tanggal_verifikasi_kasi']);
                                        $cetakUrl = 'cetak_pelimpahan_meninggal.php?id=' . $row['id_limpah_meninggal'];
                                        $style = $isVerified ? '' : "style='pointer-events: none; color: gray;'";
                                        ?>
                                        <a class="btn btn-success btn-sm" href="<?= $cetakUrl; ?>" <?= $style; ?>><i class="fa-solid fa-print"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Tabel pelimpahan Sakit Permanen -->
                <?php if (($filter_kategori == 'sakit' || $filter_kategori == '') && $result_sakit): ?>
                    <h3>Pelimpahan Sakit Permanen</h3>
                    <a href="tambah_pelimpahan_sakit.php" class="btn btn-primary">Tambah Data</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA AHLI WARIS</th>
                                    <th>NAMA AYAH</th>
                                    <th>TEMPAT & TANGGAL LAHIR</th>
                                    <th>ALAMAT</th>
                                    <th>KECAMATAN</th>
                                    <th>KELURAHAN</th>
                                    <th>KODE POS</th>
                                    <th>JENIS KELAMIN</th>
                                    <th>PEKERJAAN</th>
                                    <th>NOMOR TELEPON</th>
                                    <th>Nama Bank</th>
                                    <th>Nomor Rekening</th>
                                    <th>Nama Kuasa Waris</th>
                                    <th>STATUS DENGAN JAMAAH</th>
                                    <th>NAMA JAMAAH</th>
                                    <th>BIN/ Binti</th>
                                    <th>NOMOR PORSI</th>
                                    <th>TEMPAT & Tanggal LAHIR JAMAAH</th>
                                    <th>ALAMAT JAMAAH</th>
                                    <th>KECAMATAN JAMAAH</th>
                                    <th>KELURAHAN JAMAAH</th>
                                    <th>KODE POS JAMAAH</th>
                                    <th>BPS</th>
                                    <th>NOMOR REKENING</th>
                                    <th>NOMINAL SETORAN</th>
                                    <th>SPPH/VALIDASI</th>
                                    <th>Alasan</th>
                                    <th>TANGGAL REGISTER</th>
                                    <th>Tahun Sakit</th>
                                    <th>TANGGAL SPTJM</th>
                                    <th>TANGGAL REKOMENDASI</th>
                                    <th>TANGGAL MASUK SURAT</th>
                                    <th>NOMOR SURAT</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no_sakit = 1;
                                while ($row = mysqli_fetch_assoc($result_sakit)) : ?>
                                    <tr>
                                        <td><?php echo $no_sakit++; ?></td>
                                        <td><?php echo $row['nama_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['nama_ayah_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo ($row['tempat_lahir_ahliwaris'] ?? '-') . ', ' . formatTanggalIndonesia($row['tanggal_lahir_ahliwaris'] ?? date('Y-m-d')); ?></td>
                                        <td><?php echo $row['alamat_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['kecamatan_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['kelurahan_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['kode_pos_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['jenis_kelamin_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['pekerjaan_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['no_telepon_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['bank_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['no_rekening_ahliwaris'] ?? '-'; ?></td>
                                        <td><?php echo $row['nama_kuasa_waris'] ?? '-'; ?></td>
                                        <td><?php echo $row['status_dengan_jamaah'] ?? '-'; ?></td>
                                        <td><?php echo $row['nama_jamaah'] ?? '-'; ?></td>
                                        <td><?php echo $row['bin_binti'] ?? '-'; ?></td>
                                        <td><b>
                                            <span style="background-color:rgb(229, 255, 224); color: green; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                <?php echo $row['nomor_porsi'] ?? '-'; ?>
                                            </span>
                                        </b></td>
                                        <td><?php echo ($row['tempat_lahir_jamaah'] ?? '-') . ', ' . formatTanggalIndonesia($row['tanggal_lahir_jamaah'] ?? date('Y-m-d')); ?></td>
                                        <td><?php echo $row['alamat_jamaah'] ?? '-'; ?></td>
                                        <td><?php echo $row['kecamatan_jamaah'] ?? '-'; ?></td>
                                        <td><?php echo $row['kelurahan_jamaah'] ?? '-'; ?></td>
                                        <td><?php echo $row['kode_pos_jamaah'] ?? '-'; ?></td>
                                        <td><?php echo $row['bps'] ?? '-'; ?></td>
                                        <td><?php echo $row['nomor_rekening'] ?? '-'; ?></td>
                                        <td><?php echo $row['nominal_setoran'] ?? '-'; ?></td>
                                        <td><b>
                                            <span style="background-color:rgb(248, 224, 255); color: purple; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                <?php echo $row['spph_validasi'] ?? '-'; ?>
                                            </span>
                                        </b></td>
                                        <td><?php echo $row['alasan'] ?? '-'; ?></td>
                                        <td><?php echo formatTanggalIndonesia($row['tanggal_register'] ?? date('Y-m-d')); ?></td>
                                        <td><b>
                                            <span style="background-color:rgb(255, 224, 224); color: red; padding: 4px 8px; border-radius: 8px; font-weight: 500; display: inline-block; font-size: 0.9em;">
                                                <?php echo $row['tanggal_sakit'] ?? '-'; ?>
                                            </span>
                                        </b></td>
                                        <td><?php echo formatTanggalIndonesia($row['tanggal_sptjm'] ?? date('Y-m-d')); ?></td>
                                        <td><?php echo formatTanggalIndonesia($row['tanggal_rekomendasi'] ?? date('Y-m-d')); ?></td>
                                        <td><?php echo formatTanggalIndonesia($row['tanggal_masuk_surat'] ?? date('Y-m-d')); ?></td>
                                        <td><?php echo $row['nomor_surat'] ?? '-'; ?></td>
                                        <td>
                                            <a class="btn btn-warning btn-sm" href="edit_pelimpahan_sakit.php?id=<?php echo $row['id_limpah_sakit']; ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <a class="btn btn-danger btn-sm" href="hapus_pelimpahan_sakit.php?id=<?php echo $row['id_limpah_sakit']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus?')"><i class="fa-solid fa-trash"></i></a>
                                            <?php
                                            $isVerified = isset($row['tanggal_verifikasi_kasi']) && !empty($row['tanggal_verifikasi_kasi']);
                                            $cetakUrl = 'cetak_pelimpahan_sakit.php?id=' . $row['id_limpah_sakit'];
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
        <!-- <?php include 'footer.php'; ?> -->
    </div>

</body>
</html>