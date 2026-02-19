<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../../includes/koneksi.php';
include '../../../../partials/fungsi.php';

if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: ../../../auth/login.php");
    exit();
}
$id_staf = $_SESSION['id_staf'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_batal_meninggal = $_POST['id_batal_meninggal'];
} else {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("⚠️ Parameter ID tidak valid.");
    }
    $id_batal_meninggal = $_GET['id'];
}

// Ambil data dari database untuk ditampilkan atau diproses
$query = "SELECT pm.*, p.kategori, p.tanggal_validasi
          FROM pembatalan_meninggal pm
          JOIN pembatalan p ON pm.id_pembatalan = p.id_pembatalan
          WHERE pm.id_batal_meninggal = '$id_batal_meninggal'";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) == 0) {
    die("⚠️ Data tidak ditemukan. Pastikan id_batal_meninggal = $id_batal_meninggal ada di tabel dan join cocok.");
}

$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama_jamaah = mysqli_real_escape_string($koneksi, $_POST['nama_jamaah']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kelurahan = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);
    $kode_pos = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $bps = mysqli_real_escape_string($koneksi, $_POST['bps']);
    $nomor_rek = mysqli_real_escape_string($koneksi, $_POST['nomor_rek']);
    $bin_binti = mysqli_real_escape_string($koneksi, $_POST['bin_binti']);
    $nomor_porsi = mysqli_real_escape_string($koneksi, $_POST['nomor_porsi']);
    $spph_validasi = mysqli_real_escape_string($koneksi, $_POST['spph_validasi']);
    $tanggal_surat = mysqli_real_escape_string($koneksi, $_POST['tanggal_surat']);
    $tanggal_register = mysqli_real_escape_string($koneksi, $_POST['tanggal_register']);
    $nomor_surat = mysqli_real_escape_string($koneksi, $_POST['nomor_surat']);
    $nominal_setoran = mysqli_real_escape_string($koneksi, $_POST['nominal_setoran']);

    // Data ahli waris
    $no_rekening_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['no_rekening_ahliwaris']);
    $bank_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['bank_ahliwaris']);
    $nama_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['nama_ahliwaris']);
    $jenis_kelamin_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin_ahliwaris']);
    $pekerjaan_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['pekerjaan_ahliwaris']);
    $tempat_lahir_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir_ahliwaris']);
    $tanggal_lahir_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir_ahliwaris']);
    $status_dengan_jamaah = mysqli_real_escape_string($koneksi, $_POST['status_dengan_jamaah']);
    $alamat_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['alamat_ahliwaris']);
    $kecamatan_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['kecamatan_ahliwaris']);
    $kelurahan_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['kelurahan_ahliwaris']);
    $kode_pos_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['kode_pos_ahliwaris']);
    $no_telepon_ahliwaris = mysqli_real_escape_string($koneksi, $_POST['no_telepon_ahliwaris']);

    // Update data
    $query = "UPDATE pembatalan_meninggal SET 
              nama_jamaah = '$nama_jamaah',
              tempat_lahir = '$tempat_lahir',
              tanggal_lahir = '$tanggal_lahir',
              alamat = '$alamat',
              kecamatan = '$kecamatan',
              kelurahan = '$kelurahan',
              kode_pos = '$kode_pos',
              jenis_kelamin = '$jenis_kelamin',
              pekerjaan = '$pekerjaan',
              bps = '$bps',
              nomor_rek = '$nomor_rek',
              bin_binti = '$bin_binti',
              nomor_porsi = '$nomor_porsi',
              spph_validasi = '$spph_validasi',
              tanggal_surat = '$tanggal_surat',
              tanggal_register = '$tanggal_register',
              nomor_surat = '$nomor_surat',
              nominal_setoran = '$nominal_setoran',
              no_rekening_ahliwaris = '$no_rekening_ahliwaris',
              bank_ahliwaris = '$bank_ahliwaris',
              nama_ahliwaris = '$nama_ahliwaris',
              jenis_kelamin_ahliwaris = '$jenis_kelamin_ahliwaris',
              pekerjaan_ahliwaris = '$pekerjaan_ahliwaris',
              tempat_lahir_ahliwaris = '$tempat_lahir_ahliwaris',
              tanggal_lahir_ahliwaris = '$tanggal_lahir_ahliwaris',
              status_dengan_jamaah = '$status_dengan_jamaah',
              alamat_ahliwaris = '$alamat_ahliwaris',
              kecamatan_ahliwaris = '$kecamatan_ahliwaris',
              kelurahan_ahliwaris = '$kelurahan_ahliwaris',
              kode_pos_ahliwaris = '$kode_pos_ahliwaris',
              no_telepon_ahliwaris = '$no_telepon_ahliwaris'
              WHERE id_batal_meninggal = '$id_batal_meninggal'";

    if (mysqli_query($koneksi, $query)) {
        updateAktivitasPengguna($id_staf, 'staf', 'Pembatalan', 'Menginput data pembatalan meninggal dunia');

        $_SESSION['success_message'] = "Data pembatalan meninggal <b>" . $nama_jamaah . "</b> </br> berhasil diperbarui!";
        header("Location: ../../entry_pembatalan.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data: " . mysqli_error($koneksi);
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../../includes/sidebar_staf.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../../../includes/header_staf.php'; ?>
        <link rel="stylesheet" href="../../assets/css/pembatalan.css">
        <main class="pembatalan-wrapper">
            <div class="pembatalan-container">
                <div class="pembatalan-header">
                    <h1>Pembatalan - Meninggal Dunia</h1>
                    <div class="button-group">
                        <button type="reset" form="form-data" class="btn btn-reset"><i class="fas fa-rotate-left"></i></button>
                        <button type="button" class="btn btn-back" onclick="window.location.href='../../entry_pembatalan.php'">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                    </div>
                </div>
                <form class="form-edit-pembatalan" action="edit_pembatalan_meninggal.php" method="POST" id="form-data">
                    <input type="hidden" name="id_batal_meninggal" value="<?php echo htmlspecialchars($data['id_batal_meninggal']); ?>">
                    <!-- ================================================================================================ -->
                    <div class="judul-form">DATA AHLI WARIS</div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Nama Lengkap:</label>
                            <input type="text" name="nama_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['nama_ahliwaris'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Jenis Kelamin:</label>
                            <select name="jenis_kelamin_ahliwaris" class="form-control" required>
                                <option disabled>--- Pilih Jenis Kelamin ---</option>
                                <option value="Laki-Laki" <?php if (($data['jenis_kelamin_ahliwaris'] ?? '') == 'Laki-Laki') echo 'selected'; ?>>Laki-Laki</option>
                                <option value="Perempuan" <?php if (($data['jenis_kelamin_ahliwaris'] ?? '') == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status dengan Jamaah:</label>
                            <select name="status_dengan_jamaah" class="form-control" required>
                                <option disabled>--- Pilih Status ---</option>
                                <option value="Suami" <?php if (($data['status_dengan_jamaah'] ?? '') == 'Suami') echo 'selected'; ?>>Suami</option>
                                <option value="Istri" <?php if (($data['status_dengan_jamaah'] ?? '') == 'Istri') echo 'selected'; ?>>Istri</option>
                                <option value="Orang Tua Kandung" <?php if (($data['status_dengan_jamaah'] ?? '') == 'Orang Tua Kandung') echo 'selected'; ?>>Orang Tua Kandung</option>
                                <option value="Anak Kandung" <?php if (($data['status_dengan_jamaah'] ?? '') == 'Anak Kandung') echo 'selected'; ?>>Anak Kandung</option>
                                <option value="Saudara Kandung" <?php if (($data['status_dengan_jamaah'] ?? '') == 'Saudara Kandung') echo 'selected'; ?>>Saudara Kandung</option>
                            </select>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Tempat Lahir:</label>
                            <input type="text" name="tempat_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir_ahliwaris'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir:</label>
                            <input type="date" name="tanggal_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir_ahliwaris'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Nama Bank:</label>
                            <select name="bank_ahliwaris" class="form-control" required>
                                <option disabled>-- Pilih Bank Syariah di Kalsel --</option>
                                <option value="451" <?php if (($data['bank_ahliwaris'] ?? '') == '451') echo 'selected'; ?>>Bank Syariah Indonesia (451)</option>
                                <option value="147" <?php if (($data['bank_ahliwaris'] ?? '') == '147') echo 'selected'; ?>>Bank Muamalat (147)</option>
                                <option value="506" <?php if (($data['bank_ahliwaris'] ?? '') == '506') echo 'selected'; ?>>Bank Mega Syariah (506)</option>
                                <option value="521" <?php if (($data['bank_ahliwaris'] ?? '') == '521') echo 'selected'; ?>>Bank Syariah Bukopin (521)</option>
                                <option value="011" <?php if (($data['bank_ahliwaris'] ?? '') == '011') echo 'selected'; ?>>Bank Danamon (011)</option>
                                <option value="122" <?php if (($data['bank_ahliwaris'] ?? '') == '122') echo 'selected'; ?>>BPD Kalsel Syariah (122)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nomor Rekening:</label>
                            <input type="text" name="no_rekening_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['no_rekening_ahliwaris'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Pekerjaan:</label>
                            <select name="pekerjaan_ahliwaris" class="form-control" required>
                                <option disabled>--- Pilih Pekerjaan ---</option>
                                <option value="Pelajar/Mahasiswa" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Pelajar/Mahasiswa') echo 'selected'; ?>>Pelajar/Mahasiswa</option>
                                <option value="Pegawai Negeri" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Pegawai Negeri') echo 'selected'; ?>>Pegawai Negeri</option>
                                <option value="Pegawai Swasta" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Pegawai Swasta') echo 'selected'; ?>>Pegawai Swasta</option>
                                <option value="Ibu Rumah Tangga" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Ibu Rumah Tangga') echo 'selected'; ?>>Ibu Rumah Tangga</option>
                                <option value="Pensiunan" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Pensiunan') echo 'selected'; ?>>Pensiunan</option>
                                <option value="Polri" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Polri') echo 'selected'; ?>>Polri</option>
                                <option value="Pedagang" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Pedagang') echo 'selected'; ?>>Pedagang</option>
                                <option value="Tani" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Tani') echo 'selected'; ?>>Tani</option>
                                <option value="Pegawai BUMN" <?php if (($data['pekerjaan_ahliwaris'] ?? '') == 'Pegawai BUMN') echo 'selected'; ?>>Pegawai BUMN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon:</label>
                            <input type="text" name="no_telepon_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['no_telepon_ahliwaris'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Alamat:</label>
                            <textarea name="alamat_ahliwaris" class="form-control" required><?php echo htmlspecialchars($data['alamat_ahliwaris'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <select name="kecamatan_ahliwaris" id="kecamatan_ahliwaris" class="form-control" required>
                                <option disabled>--- Pilih Kecamatan ---</option>
                                <option value="Aluh-Aluh" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Aluh-Aluh') echo 'selected'; ?>>Aluh-Aluh</option>
                                <option value="Aranio" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Aranio') echo 'selected'; ?>>Aranio</option>
                                <option value="Astambul" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Astambul') echo 'selected'; ?>>Astambul</option>
                                <option value="Beruntung Baru" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Beruntung Baru') echo 'selected'; ?>>Beruntung Baru</option>
                                <option value="Cintapuri Darussalam" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Cintapuri Darussalam') echo 'selected'; ?>>Cintapuri Darussalam</option>
                                <option value="Gambut" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Gambut') echo 'selected'; ?>>Gambut</option>
                                <option value="Karang Intan" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Karang Intan') echo 'selected'; ?>>Karang Intan</option>
                                <option value="Kertak Hanyar" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Kertak Hanyar') echo 'selected'; ?>>Kertak Hanyar</option>
                                <option value="Mataraman" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Mataraman') echo 'selected'; ?>>Mataraman</option>
                                <option value="Martapura" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Martapura') echo 'selected'; ?>>Martapura</option>
                                <option value="Martapura Barat" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Martapura Barat') echo 'selected'; ?>>Martapura Barat</option>
                                <option value="Martapura Timur" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Martapura Timur') echo 'selected'; ?>>Martapura Timur</option>
                                <option value="Paramasan" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Paramasan') echo 'selected'; ?>>Paramasan</option>
                                <option value="Pengaron" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Pengaron') echo 'selected'; ?>>Pengaron</option>
                                <option value="Sambung Makmur" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Sambung Baru') echo 'selected'; ?>>Sambung Makmur</option>
                                <option value="Simpang Empat" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Simpang Empat') echo 'selected'; ?>>Simpang Empat</option>
                                <option value="Sungai Pinang" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Sungai Pinang') echo 'selected'; ?>>Sungai Pinang</option>
                                <option value="Sungai Tabuk" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Sungai Tabuk') echo 'selected'; ?>>Sungai Tabuk</option>
                                <option value="Tatah Makmur" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Tatah Makmur') echo 'selected'; ?>>Tatah Makmur</option>
                                <option value="Telaga Bauntung" <?php if (($data['kecamatan_ahliwaris'] ?? '') == 'Telaga Bauntung') echo 'selected'; ?>>Telaga Bauntung</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kelurahan:</label>
                            <select name="kelurahan_ahliwaris" id="kelurahan_ahliwaris" class="form-control" required>
                                <option disabled>--- Pilih Kelurahan ---</option>
                                <option value="<?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?>" selected><?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kode Pos:</label>
                            <input type="text" name="kode_pos_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['kode_pos_ahliwaris']); ?>" required>
                        </div>
                    </div>

                    <div class="judul-form">DATA JAMAAH</div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Nama Lengkap:</label>
                            <input type="text" name="nama_jamaah" class="form-control" value="<?php echo htmlspecialchars($data['nama_jamaah'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>BIN/BINTI:</label>
                            <input type="text" name="bin_binti" class="form-control" value="<?php echo htmlspecialchars($data['bin_binti'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Porsi:</label>
                            <input type="text" name="nomor_porsi" class="form-control" value="<?php echo htmlspecialchars($data['nomor_porsi'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Tempat Lahir:</label>
                            <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Lahir:</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="baris-form">
                        <div class="form-group">
                            <label>Jenis Kelamin:</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option disabled>--- Pilih Jenis Kelamin ---</option>
                                <option value="Laki-Laki" <?php if (($data['jenis_kelamin'] ?? '') == 'Laki-Laki') echo 'selected'; ?>>Laki-Laki</option>
                                <option value="Perempuan" <?php if (($data['jenis_kelamin'] ?? '') == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pekerjaan:</label>
                            <select name="pekerjaan" class="form-control" required>
                                <option disabled>--- Pilih Pekerjaan ---</option>
                                <option value="Pelajar/Mahasiswa" <?php if (($data['pekerjaan'] ?? '') == 'Pelajar/Mahasiswa') echo 'selected'; ?>>Pelajar/Mahasiswa</option>
                                <option value="Pegawai Negeri" <?php if (($data['pekerjaan'] ?? '') == 'Pegawai Negeri') echo 'selected'; ?>>Pegawai Negeri</option>
                                <option value="Pegawai Swasta" <?php if (($data['pekerjaan'] ?? '') == 'Pegawai Swasta') echo 'selected'; ?>>Pegawai Swasta</option>
                                <option value="Ibu Rumah Tangga" <?php if (($data['pekerjaan'] ?? '') == 'Ibu Rumah Tangga') echo 'selected'; ?>>Ibu Rumah Tangga</option>
                                <option value="Pensiunan" <?php if (($data['pekerjaan'] ?? '') == 'Pensiunan') echo 'selected'; ?>>Pensiunan</option>
                                <option value="Polri" <?php if (($data['pekerjaan'] ?? '') == 'Polri') echo 'selected'; ?>>Polri</option>
                                <option value="Pedagang" <?php if (($data['pekerjaan'] ?? '') == 'Pedagang') echo 'selected'; ?>>Pedagang</option>
                                <option value="Tani" <?php if (($data['pekerjaan'] ?? '') == 'Tani') echo 'selected'; ?>>Tani</option>
                                <option value="Pegawai BUMN" <?php if (($data['pekerjaan'] ?? '') == 'Pegawai BUMN') echo 'selected'; ?>>Pegawai BUMN</option>
                            </select>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Nama Bank Penerima Setoran (BPS):</label>
                            <select name="bps" class="form-control" required>
                                <option disabled>-- Pilih Bank Syariah di Kalsel --</option>
                                <option value="451" <?php if (($data['bps'] ?? '') == '451') echo 'selected'; ?>>Bank Syariah Indonesia (451)</option>
                                <option value="147" <?php if (($data['bps'] ?? '') == '147') echo 'selected'; ?>>Bank Muamalat (147)</option>
                                <option value="506" <?php if (($data['bps'] ?? '') == '506') echo 'selected'; ?>>Bank Mega Syariah (506)</option>
                                <option value="521" <?php if (($data['bps'] ?? '') == '521') echo 'selected'; ?>>Bank Syariah Bukopin (521)</option>
                                <option value="011" <?php if (($data['bps'] ?? '') == '011') echo 'selected'; ?>>Bank Danamon (011)</option>
                                <option value="122" <?php if (($data['bps'] ?? '') == '122') echo 'selected'; ?>>BPD Kalsel Syariah (122)</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label>Nomor Rekening:</label>
                            <input type="text" name="nomor_rek" class="form-control" value="<?php echo htmlspecialchars($data['nomor_rek']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nominal Setoran:</label>
                            <input type="text" name="nominal_setoran" class="form-control" value="<?php echo htmlspecialchars($data['nominal_setoran']); ?>" required>
                        </div>
                    </div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>SPPH/Validasi:</label>
                            <input type="text" name="spph_validasi" class="form-control" value="<?php echo htmlspecialchars($data['spph_validasi']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori:</label>
                            <input type="text" name="kategori" class="form-control" value="<?php echo htmlspecialchars($data['kategori']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Register:</label>
                            <input type="date" name="tanggal_register" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_register']); ?>" required>
                        </div>
                    </div>

                    <div class="baris-form">
                        <div class="form-group">
                            <label>Alamat:</label>
                            <textarea name="alamat" class="form-control" required><?php echo htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <select name="kecamatan" id="kecamatan" class="form-control" required>
                                <option disabled>--- Pilih Kecamatan ---</option>
                                <option value="Aluh-Aluh" <?php if (($data['kecamatan'] ?? '') == 'Aluh-Aluh') echo 'selected'; ?>>Aluh-Aluh</option>
                                <option value="Aranio" <?php if (($data['kecamatan'] ?? '') == 'Aranio') echo 'selected'; ?>>Aranio</option>
                                <option value="Astambul" <?php if (($data['kecamatan'] ?? '') == 'Astambul') echo 'selected'; ?>>Astambul</option>
                                <option value="Beruntung Baru" <?php if (($data['kecamatan'] ?? '') == 'Beruntung Baru') echo 'selected'; ?>>Beruntung Baru</option>
                                <option value="Cintapuri Darussalam" <?php if (($data['kecamatan'] ?? '') == 'Cintapuri Darussalam') echo 'selected'; ?>>Cintapuri Darussalam</option>
                                <option value="Gambut" <?php if (($data['kecamatan'] ?? '') == 'Gambut') echo 'selected'; ?>>Gambut</option>
                                <option value="Karang Intan" <?php if (($data['kecamatan'] ?? '') == 'Karang Intan') echo 'selected'; ?>>Karang Intan</option>
                                <option value="Kertak Hanyar" <?php if (($data['kecamatan'] ?? '') == 'Kertak Hanyar') echo 'selected'; ?>>Kertak Hanyar</option>
                                <option value="Mataraman" <?php if (($data['kecamatan'] ?? '') == 'Mataraman') echo 'selected'; ?>>Mataraman</option>
                                <option value="Martapura" <?php if (($data['kecamatan'] ?? '') == 'Martapura') echo 'selected'; ?>>Martapura</option>
                                <option value="Martapura Barat" <?php if (($data['kecamatan'] ?? '') == 'Martapura Barat') echo 'selected'; ?>>Martapura Barat</option>
                                <option value="Martapura Timur" <?php if (($data['kecamatan'] ?? '') == 'Martapura Timur') echo 'selected'; ?>>Martapura Timur</option>
                                <option value="Paramasan" <?php if (($data['kecamatan'] ?? '') == 'Paramasan') echo 'selected'; ?>>Paramasan</option>
                                <option value="Pengaron" <?php if (($data['kecamatan'] ?? '') == 'Pengaron') echo 'selected'; ?>>Pengaron</option>
                                <option value="Sambung Makmur" <?php if (($data['kecamatan'] ?? '') == 'Sambung Makmur') echo 'selected'; ?>>Sambung Makmur</option>
                                <option value="Simpang Empat" <?php if (($data['kecamatan'] ?? '') == 'Simpang Empat') echo 'selected'; ?>>Simpang Empat</option>
                                <option value="Sungai Pinang" <?php if (($data['kecamatan'] ?? '') == 'Sungai Pinang') echo 'selected'; ?>>Sungai Pinang</option>
                                <option value="Sungai Tabuk" <?php if (($data['kecamatan'] ?? '') == 'Sungai Tabuk') echo 'selected'; ?>>Sungai Tabuk</option>
                                <option value="Tatah Makmur" <?php if (($data['kecamatan'] ?? '') == 'Tatah Makmur') echo 'selected'; ?>>Tatah Makmur</option>
                                <option value="Telaga Bauntung" <?php if (($data['kecamatan'] ?? '') == 'Telaga Bauntung') echo 'selected'; ?>>Telaga Bauntung</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Kelurahan:</label>
                            <select name="kelurahan" id="kelurahan" class="form-control" required>
                                <option disabled>--- Pilih Kelurahan ---</option>
                                <option value="<?php echo htmlspecialchars($data['kelurahan']); ?>" selected><?php echo htmlspecialchars($data['kelurahan']); ?></option>
                            </select>
                        </div>
                        <script>
                            // Data kelurahan berdasarkan kecamatan
                            const kelurahanData = {
                                "Aluh-Aluh": ["Aluh-Aluh Besar", "Aluh-Aluh Kecil", "Aluh-Aluh Kecil Muara", "Bakambat", "Balimau", "Bunipah", "Handil Baru", "Handil Bujur", "Kuin Besar", "Kuin Kecil", "Labat Muara", "Pemurus", "Podok", "Pulantan", "Simpang Warga", "Simpang Warga Dalam", "Sungai Musang", "Tanipah", "Terapu"],
                                "Aranio": ["Apuai", "Aranio", "Artain", "Belangian", "Benua Riam", "Kalaan", "Paau", "Rantau Balai", "Rantau Bujur", "Tiwingan Baru", "Tiwingan Lama"],
                                "Astambul": ["Astambul Kota", "Astambul Seberang", "Banua Anyar DS", "Banua Anyar ST", "Danau Salak", "Jati", "Kalampaian Tengah", "Kalampaian Ilir", "Kalampaian Ulu", "Kaliukan", "Limamar", "Lok Gabang", "Munggu Raya", "Pasar Jati", "Pematang Hambawang", "Pingaran Ilir", "Pingaran Ulu", "Sungai Alat", "Sungai Tuan Ulu", "Sungai Tuan Ilir", "Tambak Danau", "Tambangan"],
                                "Beruntung Baru": ["Babirik", "Handil Purai", "Haur Kuning", "Jambu Burung", "Jambu Raya", "Kampung Baru", "Lawahan", "Muara Halayung", "Pindahan Baru", "Rumpiang", "Selat Makmur", "Tambak Padi"],
                                "Cintapuri Darussalam": ["Alalak Padang", "Benua Anyar", "Cintapuri", "Garis Hanyar", "Karya Makmur", "Keramat Mina", "Makmur Karya", "Simpang Lima", "Sindang Jaya", "Sumber Sari", "Surian Hanyar"],
                                "Gambut": ["Gambut", "Gambut Barat", "Banyu Hirang", "Guntung Papuyu", "Guntung Ujung", "Kayu Bawang", "Keladan Baru", "Makmur", "Malintang", "Malintang Baru", "Sungai Kupang", "Tambak Sirang Baru", "Tambak Sirang Darat", "Tambak Sirang Laut"],
                                "Karang Intan": ["Abirau", "Awang Bangkal Barat", "Awang Bangkal Timur", "Balau", "Bi'ih", "Jingah Habang Ilir", "Jingah Habang Ulu", "Karang Intan", "Kiram", "Lihung", "Lok Tangga", "Mali-Mali", "Mandi Angin Barat", "Mandi Angin Timur", "Mandi Kapau Barat", "Mandi Kapau Timur", "Padang Panjang", "Pandak Daun", "Pasar Lama", "Penyambaran", "Pulau Nyiur", "Sungai Alang", "Sungai Arfat", "Sungai Asam", "Sungai Besar", "Sungai Landas"],
                                "Kertak Hanyar": ["Kertak Hanyar I", "Manarap Lama", "Mandar Sari", "Banua Hanyar", "Kertak Hanyar II", "Manarap Baru", "Manarap Tengah", "Mekar Raya", "Pasar Kemis", "Simpang Empat", "Sungai Lakum", "Tatah Belayung Baru", "Tatah Pemangkih Laut"],
                                "Mataraman": ["Baru", "Bawahan Pasar", "Bawahan Seberang", "Bawahan Selan", "Gunung Ulin", "Lok Tamu", "Mangkalawat", "Mataraman", "Pasiraman", "Pematang Danau", "Simpang Tiga", "Sungai Jati", "Surian", "Takuti", "Tanah Abang"],
                                "Martapura": ["Jawa", "Keraton", "Murung Keraton", "Pasayangan", "Sekumpul", "Sungai Paring", "Tanjung Rema Darat", "Bincau", "Bincau Muara", "Cindai Alus", "Indra Sari", "Jawa Laut", "Labuan Tabu", "Murung Kenanga", "Pasayangan Barat", "Pasayangan Selatan", "Pasayangan Utara", "Sungai Sipai", "Tambak Baru", "Tambak Baru Ilir", "Tambak Baru Ulu", "Tanjung Rema", "Tunggul Irang", "Tunggul Irang Ilir", "Tunggul Irang Ulu", "Tungkaran"],
                                "Martapura Barat": ["Antasan Sutun", "Keliling Benteng Tengah", "Keliling Benteng Ulu", "Penggalaman", "Sungai Batang", "Sungai Batang Ilir", "Sungai Rangas", "Sungai Rangas Hambuku", "Sungai Rangas Tengah", "Sungai Rangas Ulu", "Tangkas", "Teluk Selong", "Teluk Selong Ulu"],
                                "Martapura Timur": ["Akar Bagantung", "Akar Baru", "Antasan Senor", "Antasan Senor Ilir", "Dalam Pagar", "Dalam Pagar Ulu", "Keramat", "Keramat Baru", "Mekar", "Melayu Ilir", "Melayu Tengah", "Melayu Ulu", "Pekauman", "Pekauman Dalam", "Pekauman Ulu", "Pematang Baru", "Sungai Kitano", "Tambak Anyar", "Tambak Anyar Ilir", "Tambak Anyar Ulu"],
                                "Paramasan": ["Angkipih", "Paramasan Atas", "Paramasan Bawah", "Remo"],
                                "Pengaron": ["Alimukim", "Antaraku", "Ati'im", "Benteng", "Kertak Empat", "Lobang Baru", "Lok Tunggul", "Lumpangi", "Mangkauk", "Maniapun", "Panyiuran", "Pengaron"],
                                "Sambung Makmur": ["Baliangin", "Batang Banyu", "Batu Tanam", "Gunung Batu", "Madurejo", "Pasar Baru", "Sungai Lurus"],
                                "Simpang Empat": ["Batu Balian", "Berkat Mulia", "Cabi", "Lawiran", "Lok Cantung", "Paku", "Paring Tali", "Pasar Lama", "Simpang Empat", "Sungai Langsat", "Sungai Raya", "Sungai Tabuk", "Sungkai", "Sungkai Baru", "Tanah Intan"],
                                "Sungai Pinang": ["Belimbing Baru", "Belimbing Lama", "Hakim Makmur", "Kahelaan", "Kupang Rejo", "Pakutik", "Rantau Bakula", "Rantau Nangka", "Sumber Baru", "Sumber Harapan", "Sungai Pinang"],
                                "Sungai Tabuk": ["Sungai Lulut", "Abumbun Jaya", "Gudang Hirang", "Gudang Tengah", "Keliling Benteng Hilir", "Lok Baintan", "Lok Baintan Dalam", "Lok Buntar", "Paku Alam", "Pejambuan", "Pemakuan", "Pematang Panjang", "Pembantanan", "Sungai Bakung", "Sungai Bangkal", "Sungai Pinang Baru", "Sungai Pinang Lama", "Sungai Tabuk Keramat", "Sungai Tabuk Kota", "Sungai Tandipah", "Tajau Landung"],
                                "Tatah Makmur": ["Jaruju Laut", "Layap Baru", "Mekar Sari", "Pandan Sari", "Pemangkih Baru", "Taibah Raya", "Tampang Awang", "Tatah Bangkal", "Tatah Bangkal Tengah", "Tatah Jaruju", "Tatah Layap", "Tatah Pemangkih Darat", "Tatah Pemangkih Tengah"],
                                "Telaga Bauntung": ["Lok Tanah", "Rampah", "Rantau Bujur", "Telaga Baru"]
                            };

                            // Mendapatkan elemen select
                            const kecamatanSelect = document.getElementById('kecamatan');
                            const kelurahanSelect = document.getElementById('kelurahan');

                            // Fungsi untuk memuat kelurahan berdasarkan kecamatan yang dipilih
                            function loadKelurahan(selectedKecamatan) {
                                // Mengosongkan dropdown kelurahan
                                kelurahanSelect.innerHTML = '<option value="" disabled selected>-- Pilih Kelurahan --</option>';

                                // Jika ada kecamatan yang dipilih
                                if (selectedKecamatan) {
                                    // Mengambil data kelurahan untuk kecamatan yang dipilih
                                    const kelurahanList = kelurahanData[selectedKecamatan];

                                    // Menambahkan opsi kelurahan ke dropdown
                                    kelurahanList.forEach(kelurahan => {
                                        const option = document.createElement('option');
                                        option.value = kelurahan;
                                        option.textContent = kelurahan;
                                        kelurahanSelect.appendChild(option);
                                    });
                                }
                            }

                            // Event listener untuk perubahan pada select kecamatan
                            kecamatanSelect.addEventListener('change', function() {
                                const selectedKecamatan = this.value;
                                loadKelurahan(selectedKecamatan);
                            });

                            // Mengatur nilai default jika ada data sebelumnya (misalnya dari database)
                            const selectedKecamatan = "<?php echo htmlspecialchars($data['kecamatan']); ?>";
                            if (selectedKecamatan) {
                                kecamatanSelect.value = selectedKecamatan;
                                loadKelurahan(selectedKecamatan);
                            }

                            // Mengatur nilai default kelurahan berdasarkan data yang ada
                            const selectedKelurahan = "<?php echo htmlspecialchars($data['kelurahan']); ?>";
                            if (selectedKelurahan) {
                                kelurahanSelect.value = selectedKelurahan;
                            }
                        </script>
                        <div class="form-group">
                            <label>Kode Pos:</label>
                            <input type="text" name="kode_pos" class="form-control" value="<?php echo htmlspecialchars($data['kode_pos']); ?>" required>
                        </div>
                    </div>

                    <div class="judul-form">KETERANGAN SURAT</div>
                    <div class="baris-form">
                        <div class="form-group">
                            <label>Tanggal Surat:</label>
                            <input type="date" name="tanggal_surat" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_surat']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Surat:</label>
                            <input type="text" name="nomor_surat" class="form-control" value="<?php echo htmlspecialchars($data['nomor_surat']); ?>" required>
                        </div>
                    </div>
                    <!-- tombol edit data -->
                    <div>
                        <button type="submit" name="update" class="edit-data"><i class="fas fa-edit"></i> Edit Data</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<script src="../../../assets/js/sidebar.js"></script>
<script src="assets/js/tambah_data_ahliwaris.js"></script>

</body>

</html>