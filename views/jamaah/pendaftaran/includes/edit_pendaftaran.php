<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../includes/koneksi.php';

include '../../../partials/fungsi.php';

if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];

if (!isset($_GET['id_pendaftaran']) || empty($_GET['id_pendaftaran'])) {
    echo "ID pendaftaran tidak ditemukan.";
    exit();
}

$id_pendaftaran = intval($_GET['id_pendaftaran']);

// Ambil data lama untuk digunakan saat edit
$query = "SELECT * FROM pendaftaran WHERE id_pendaftaran = ? AND id_jamaah = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("ii", $id_pendaftaran, $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Data tidak ditemukan.";
    header("Location: pendaftaran_jamaah.php");
    exit();
}

$data = $result->fetch_assoc();

function uploadDokumen($fileFieldName, $target_dir, $old_file_path = null)
{
    if (!isset($_FILES[$fileFieldName]) || $_FILES[$fileFieldName]['error'] !== UPLOAD_ERR_OK) {
        return $old_file_path;
    }

    $file_name = $_FILES[$fileFieldName]['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $unique_file_name = uniqid() . '.' . $file_ext;
    $target_path = $target_dir . $unique_file_name;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES[$fileFieldName]['tmp_name']);
    finfo_close($finfo);

    if ($fileFieldName === 'foto_wajah') {
        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            die("foto_wajah harus berupa JPEG atau PNG.");
        }
    } else {
        if ($mime !== 'application/pdf') {
            die("File $fileFieldName harus berupa PDF.");
        }
    }

    if (move_uploaded_file($_FILES[$fileFieldName]['tmp_name'], $target_path)) {
        if (!empty($old_file_path) && file_exists($old_file_path)) {
            unlink($old_file_path);
        }
        return $target_path;
    }

    return $old_file_path;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_jamaah = $_POST['nama_jamaah'] ?? '';
    $nik = $_POST['nik'] ?? '';
    $nama_ayah = $_POST['nama_ayah'] ?? '';
    $tempat_lahir = $_POST['tempat_lahir'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $pendidikan = $_POST['pendidikan'] ?? '';
    $pekerjaan = $_POST['pekerjaan'] ?? '';
    $kewarganegaraan = $_POST['kewarganegaraan'] ?? '';
    $goldar = $_POST['goldar'] ?? '';
    $telp_rumah = $_POST['telp_rumah'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';
    $status_perkawinan = strtolower($_POST['status_perkawinan'] ?? '');
    $status_pergi_haji = strtolower($_POST['status_pergi_haji'] ?? '');
    $ktp_alamat = $_POST['ktp_alamat'] ?? '';
    $ktp_kecamatan = $_POST['ktp_kecamatan'] ?? '';
    $ktp_kelurahan = $_POST['ktp_kelurahan'] ?? '';
    $ktp_kode_pos = $_POST['ktp_kode_pos'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $kecamatan = $_POST['kecamatan'] ?? '';
    $kelurahan = $_POST['kelurahan'] ?? '';
    $kode_pos = $_POST['kode_pos'] ?? '';
    $jenis_kelamin = strtolower($_POST['jenis_kelamin'] ?? '');
    $wajah = $_POST['wajah'] ?? '';
    $tinggi_badan = $_POST['tinggi_badan'] ?? '';
    $berat_badan = $_POST['berat_badan'] ?? '';
    $rambut = $_POST['rambut'] ?? '';
    $alis = $_POST['alis'] ?? '';
    $hidung = $_POST['hidung'] ?? '';

    $target_dir = "../assets/berkas/pengajuan/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Perbarui dokumen jika ada file baru yang diupload
    $dokumen_setor_awal = uploadDokumen('dokumen_setor_awal', $target_dir, $data['dokumen_setor_awal']);
    $dokumen_ktp        = uploadDokumen('dokumen_ktp', $target_dir, $data['dokumen_ktp']);
    $dokumen_kk         = uploadDokumen('dokumen_kk', $target_dir, $data['dokumen_kk']);
    $dokumen_lain       = uploadDokumen('dokumen_lain', $target_dir, $data['dokumen_lain']);
    $foto_wajah         = uploadDokumen('foto_wajah', $target_dir, $data['foto_wajah']);

    // Update query
    $query_update = "UPDATE pendaftaran SET 
        nama_jamaah = ?, nik = ?, nama_ayah = ?, tempat_lahir = ?, tanggal_lahir = ?, 
        pendidikan = ?, pekerjaan = ?, kewarganegaraan = ?, goldar = ?, telp_rumah = ?, 
        no_telepon = ?, status_perkawinan = ?, status_pergi_haji = ?, 
        ktp_alamat = ?, ktp_kecamatan = ?, ktp_kelurahan = ?, ktp_kode_pos = ?,
        alamat = ?, kecamatan = ?, kelurahan = ?, kode_pos = ?, 
        jenis_kelamin = ?, wajah = ?, tinggi_badan = ?, berat_badan = ?, 
        rambut = ?, alis = ?, hidung = ?, 
        dokumen_setor_awal = ?, dokumen_ktp = ?, dokumen_kk = ?, dokumen_lain = ?, foto_wajah = ?
        WHERE id_pendaftaran = ? AND id_jamaah = ?";

    $stmt_update = $koneksi->prepare($query_update);

    if (!$stmt_update) {
        die("Query update gagal: " . $koneksi->error);
    }

    // Hitung jumlah variabel yang di-bind
    $bindVars = [
        $nama_jamaah,
        $nik,
        $nama_ayah,
        $tempat_lahir,
        $tanggal_lahir,
        $pendidikan,
        $pekerjaan,
        $kewarganegaraan,
        $goldar,
        $telp_rumah,
        $no_telepon,
        $status_perkawinan,
        $status_pergi_haji,
        $ktp_alamat,           // Pastikan ini ada dan benar
        $ktp_kecamatan,
        $ktp_kelurahan,
        $ktp_kode_pos,
        $alamat,
        $kecamatan,
        $kelurahan,
        $kode_pos,
        $jenis_kelamin,
        $wajah,
        $tinggi_badan,
        $berat_badan,
        $rambut,
        $alis,
        $hidung,
        $dokumen_setor_awal,
        $dokumen_ktp,
        $dokumen_kk,
        $dokumen_lain,
        $foto_wajah,
        $id_pendaftaran,
        $id_jamaah
    ];

    echo "Total bind vars: " . count($bindVars) . "<br>";

    $stmt_update->bind_param(
        "sssssssssssssssssssssssssssssssssii",

        $nama_jamaah,
        $nik,
        $nama_ayah,
        $tempat_lahir,
        $tanggal_lahir,
        $pendidikan,
        $pekerjaan,
        $kewarganegaraan,
        $goldar,
        $telp_rumah,
        $no_telepon,
        $status_perkawinan,
        $status_pergi_haji,
        $ktp_alamat,           // asumsi ini ktp_alamat
        $ktp_kecamatan,         // ktp_kecamatan
        $ktp_kelurahan,        // ktp_kelurahan
        $ktp_kode_pos,
        $alamat,
        $kecamatan,
        $kelurahan,
        $kode_pos,
        $jenis_kelamin,
        $wajah,
        $tinggi_badan,
        $berat_badan,
        $rambut,
        $alis,
        $hidung,
        $dokumen_setor_awal,
        $dokumen_ktp,
        $dokumen_kk,
        $dokumen_lain,
        $foto_wajah,
        $id_pendaftaran,
        $id_jamaah
    );

    if ($stmt_update->execute()) {
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pendaftaran', 'Mengedit data pendaftaran');

        $_SESSION['success_message'] = "Data berhasil diperbarui.";
        header("Location: pendaftaran_jamaah.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data: " . $stmt_update->error;
    }

    $stmt_update->close();
}
?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../../includes/header_jamaah.php'; ?>
            <main class="pendaftaran-wrapper">
                <div class="pendaftaran">
                    <div class="pendaftaran-header" style="background-color: #1b5e20; color: white;">
                        Edit Pendaftaran Haji
                    </div>
                    <div class="pendaftaran-body" style="color: #1b5e20;">

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger">
                                <?php
                                echo $_SESSION['error_message'];
                                unset($_SESSION['error_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="section-title">Edit Data Pribadi</div>
                        <hr>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_jamaah" class="form-control" value="<?php echo htmlspecialchars($data['nama_jamaah']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nomor KTP</label>
                                    <input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($data['nik']); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Ayah Kandung</label>
                                    <input type="text" name="nama_ayah" class="form-control" value="<?php echo htmlspecialchars($data['nama_ayah']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo $data['tanggal_lahir']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pendidikan</label>
                                    <select name="pendidikan" id="pendidikan" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Pendidikan --</option>
                                        <option value="SD" <?php echo ($data['pendidikan'] == 'SD') ? 'selected' : ''; ?>>SD</option>
                                        <option value="SMP" <?php echo ($data['pendidikan'] == 'SMP') ? 'selected' : ''; ?>>SMP</option>
                                        <option value="SMA" <?php echo ($data['pendidikan'] == 'SMA') ? 'selected' : ''; ?>>SMA</option>
                                        <option value="SM" <?php echo ($data['pendidikan'] == 'SM') ? 'selected' : ''; ?>>SM</option>
                                        <option value="S1" <?php echo ($data['pendidikan'] == 'S1') ? 'selected' : ''; ?>>S1</option>
                                        <option value="S2" <?php echo ($data['pendidikan'] == 'S2') ? 'selected' : ''; ?>>S2</option>
                                        <option value="S3" <?php echo ($data['pendidikan'] == 'S3') ? 'selected' : ''; ?>>S3</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pekerjaan</label>
                                    <select name="pekerjaan" id="pekerjaan" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Pekerjaan --</option>
                                        <option value="Mahasiswa" <?php echo ($data['pekerjaan'] == 'Mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                                        <option value="Pegawai Negeri" <?php echo ($data['pekerjaan'] == 'Pegawai Negeri') ? 'selected' : ''; ?>>Pegawai Negeri</option>
                                        <option value="Pegawai Swasta" <?php echo ($data['pekerjaan'] == 'Pegawai Swasta') ? 'selected' : ''; ?>>Pegawai Swasta</option>
                                        <option value="Ibu Rumah Tangga" <?php echo ($data['pekerjaan'] == 'Ibu Rumah Tangga') ? 'selected' : ''; ?>>Ibu Rumah Tangga</option>
                                        <option value="Pensiunan" <?php echo ($data['pekerjaan'] == 'Pensiunan') ? 'selected' : ''; ?>>Pensiunan</option>
                                        <option value="Polri" <?php echo ($data['pekerjaan'] == 'Polri') ? 'selected' : ''; ?>>Polri</option>
                                        <option value="Pedagang" <?php echo ($data['pekerjaan'] == 'Pedagang') ? 'selected' : ''; ?>>Pedagang</option>
                                        <option value="Tani" <?php echo ($data['pekerjaan'] == 'Tani') ? 'selected' : ''; ?>>Tani</option>
                                        <option value="Pegawai BUMN" <?php echo ($data['pekerjaan'] == 'Pegawai BUMN') ? 'selected' : ''; ?>>Pegawai BUMN</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kewarganegaraan</label>
                                    <select name="kewarganegaraan" id="kewarganegaraan" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Kewarganegaraan --</option>
                                        <option value="Indonesia" <?php echo ($data['kewarganegaraan'] == 'Indonesia') ? 'selected' : ''; ?>>Indonesia</option>
                                        <option value="Asing" <?php echo ($data['kewarganegaraan'] == 'Asing') ? 'selected' : ''; ?>>Asing</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Golongan Darah</label>
                                    <select name="goldar" id="goldar" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Golongan Darah --</option>
                                        <option value="A" <?php echo ($data['goldar'] == 'A') ? 'selected' : ''; ?>>A</option>
                                        <option value="B" <?php echo ($data['goldar'] == 'B') ? 'selected' : ''; ?>>B</option>
                                        <option value="AB" <?php echo ($data['goldar'] == 'AB') ? 'selected' : ''; ?>>AB</option>
                                        <option value="O" <?php echo ($data['goldar'] == 'O') ? 'selected' : ''; ?>>O</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telp. Rumah</label>
                                    <input type="number" name="telp_rumah" class="form-control" value="<?php echo $data['telp_rumah']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP</label>
                                    <input type="number" name="no_telepon" class="form-control" value="<?php echo $data['no_telepon']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select name="status_perkawinan" id="status_perkawinan" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Status Perkawinan --</option>
                                        <option value="Belum Menikah" <?php echo (strtolower($data['status_perkawinan']) == 'belum menikah') ? 'selected' : ''; ?>>Belum Menikah</option>
                                        <option value="Menikah" <?php echo (strtolower($data['status_perkawinan']) == 'menikah') ? 'selected' : ''; ?>>Menikah</option>
                                        <option value="Duda" <?php echo (strtolower($data['status_perkawinan']) == 'duda') ? 'selected' : ''; ?>>Duda</option>
                                        <option value="Janda" <?php echo (strtolower($data['status_perkawinan']) == 'janda') ? 'selected' : ''; ?>>Janda</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Pernah Pergi Haji</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_pergi_haji" id="sudah_haji" value="Sudah Haji"
                                            <?php echo (strtolower($data['status_pergi_haji']) == 'sudah haji') ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="sudah_haji">Sudah Haji</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_pergi_haji" id="belum_haji" value="Belum Haji"
                                            <?php echo (strtolower($data['status_pergi_haji']) == 'belum haji') ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="belum_haji">Belum Haji</label>
                                    </div>
                                </div>
                            </div>
                            <!-- ================================================================================================= -->
                            <div class="section-title">Edit Data Tempat Tinggal</div>
                            <hr>
                            <div class="row g-3">
                                <!-- Kolom kiri: Alamat KTP --><!-- Checkbox di atas -->
                                <div class="form-check mb-3 offset-md-6">
                                    <input class="form-check-input" type="checkbox" id="sameAsKTP" onchange="copyFromKTP()">
                                    <label class="form-check-label" for="sameAsKTP">
                                        Sama dengan alamat KTP
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alamat KTP</label>
                                    <input type="text" name="ktp_alamat" id="ktp_alamat" class="form-control" value="<?php echo htmlspecialchars($data['ktp_alamat'] ?? ''); ?>" required>

                                    <label class="form-label">Kecamatan KTP</label>
                                    <select name="ktp_kecamatan" id="ktp_kecamatan" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Kecamatan --</option>
                                        <option value="Aluh-Aluh" <?php echo ($data['ktp_kecamatan'] == 'Aluh-Aluh') ? 'selected' : ''; ?>>Aluh-Aluh</option>
                                        <option value="Aranio" <?php echo ($data['ktp_kecamatan'] == 'Aranio') ? 'selected' : ''; ?>>Aranio</option>
                                        <option value="Astambul" <?php echo ($data['ktp_kecamatan'] == 'Astambul') ? 'selected' : ''; ?>>Astambul</option>
                                        <option value="Beruntung Baru" <?php echo ($data['ktp_kecamatan'] == 'Beruntung Baru') ? 'selected' : ''; ?>>Beruntung Baru</option>
                                        <option value="Cintapuri Darussalam" <?php echo ($data['ktp_kecamatan'] == 'Cintapuri Darussalam') ? 'selected' : ''; ?>>Cintapuri Darussalam</option>
                                        <option value="Gambut" <?php echo ($data['ktp_kecamatan'] == 'Gambut') ? 'selected' : ''; ?>>Gambut</option>
                                        <option value="Karang Intan" <?php echo ($data['ktp_kecamatan'] == 'Karang Intan') ? 'selected' : ''; ?>>Karang Intan</option>
                                        <option value="Kertak Hanyar" <?php echo ($data['ktp_kecamatan'] == 'Kertak Hanyar') ? 'selected' : ''; ?>>Kertak Hanyar</option>
                                        <option value="Mataraman" <?php echo ($data['ktp_kecamatan'] == 'Mataraman') ? 'selected' : ''; ?>>Mataraman</option>
                                        <option value="Martapura" <?php echo ($data['ktp_kecamatan'] == 'Martapura') ? 'selected' : ''; ?>>Martapura</option>
                                        <option value="Martapura Barat" <?php echo ($data['ktp_kecamatan'] == 'Martapura Barat') ? 'selected' : ''; ?>>Martapura Barat</option>
                                        <option value="Martapura Timur" <?php echo ($data['ktp_kecamatan'] == 'Martapura Timur') ? 'selected' : ''; ?>>Martapura Timur</option>
                                        <option value="Paramasan" <?php echo ($data['ktp_kecamatan'] == 'Paramasan') ? 'selected' : ''; ?>>Paramasan</option>
                                        <option value="Pengaron" <?php echo ($data['ktp_kecamatan'] == 'Pengaron') ? 'selected' : ''; ?>>Pengaron</option>
                                        <option value="Sambung Makmur" <?php echo ($data['ktp_kecamatan'] == 'Sambung Makmur') ? 'selected' : ''; ?>>Sambung Makmur</option>
                                        <option value="Simpang Empat" <?php echo ($data['ktp_kecamatan'] == 'Simpang Empat') ? 'selected' : ''; ?>>Simpang Empat</option>
                                        <option value="Sungai Pinang" <?php echo ($data['ktp_kecamatan'] == 'Sungai Pinang') ? 'selected' : ''; ?>>Sungai Pinang</option>
                                        <option value="Sungai Tabuk" <?php echo ($data['ktp_kecamatan'] == 'Sungai Tabuk') ? 'selected' : ''; ?>>Sungai Tabuk</option>
                                        <option value="Tatah Makmur" <?php echo ($data['ktp_kecamatan'] == 'Tatah Makmur') ? 'selected' : ''; ?>>Tatah Makmur</option>
                                        <option value="Telaga Bauntung" <?php echo ($data['ktp_kecamatan'] == 'Telaga Bauntung') ? 'selected' : ''; ?>>Telaga Bauntung</option>
                                    </select>

                                    <label class="form-label">Kelurahan/Desa KTP</label>
                                    <select name="ktp_kelurahan" id="ktp_kelurahan" class="select-daftar" required>
                                        <option value="<?php echo htmlspecialchars($data['ktp_kelurahan']); ?>" selected><?php echo htmlspecialchars($data['ktp_kelurahan']); ?></option>
                                    </select>

                                    <label class="form-label">Kode Pos KTP</label>
                                    <input type="number" name="ktp_kode_pos" id="ktp_kode_pos" class="form-control" value="<?php echo htmlspecialchars($data['ktp_kode_pos'] ?? ''); ?>" required>
                                </div>

                                <!-- Kolom kanan: Alamat Domisili Sekarang -->
                                <div class="col-md-6">
                                    <label class="form-label">Alamat Tinggal Sekarang</label>
                                    <input type="text" name="alamat" id="alamat" class="form-control" value="<?php echo htmlspecialchars($data['alamat']); ?>">

                                    <label class="form-label">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Kecamatan --</option>
                                        <option value="Aluh-Aluh" <?php echo ($data['kecamatan'] == 'Aluh-Aluh') ? 'selected' : ''; ?>>Aluh-Aluh</option>
                                        <option value="Aranio" <?php echo ($data['kecamatan'] == 'Aranio') ? 'selected' : ''; ?>>Aranio</option>
                                        <option value="Astambul" <?php echo ($data['kecamatan'] == 'Astambul') ? 'selected' : ''; ?>>Astambul</option>
                                        <option value="Beruntung Baru" <?php echo ($data['kecamatan'] == 'Beruntung Baru') ? 'selected' : ''; ?>>Beruntung Baru</option>
                                        <option value="Cintapuri Darussalam" <?php echo ($data['kecamatan'] == 'Cintapuri Darussalam') ? 'selected' : ''; ?>>Cintapuri Darussalam</option>
                                        <option value="Gambut" <?php echo ($data['kecamatan'] == 'Gambut') ? 'selected' : ''; ?>>Gambut</option>
                                        <option value="Karang Intan" <?php echo ($data['kecamatan'] == 'Karang Intan') ? 'selected' : ''; ?>>Karang Intan</option>
                                        <option value="Kertak Hanyar" <?php echo ($data['kecamatan'] == 'Kertak Hanyar') ? 'selected' : ''; ?>>Kertak Hanyar</option>
                                        <option value="Mataraman" <?php echo ($data['kecamatan'] == 'Mataraman') ? 'selected' : ''; ?>>Mataraman</option>
                                        <option value="Martapura" <?php echo ($data['kecamatan'] == 'Martapura') ? 'selected' : ''; ?>>Martapura</option>
                                        <option value="Martapura Barat" <?php echo ($data['kecamatan'] == 'Martapura Barat') ? 'selected' : ''; ?>>Martapura Barat</option>
                                        <option value="Martapura Timur" <?php echo ($data['kecamatan'] == 'Martapura Timur') ? 'selected' : ''; ?>>Martapura Timur</option>
                                        <option value="Paramasan" <?php echo ($data['kecamatan'] == 'Paramasan') ? 'selected' : ''; ?>>Paramasan</option>
                                        <option value="Pengaron" <?php echo ($data['kecamatan'] == 'Pengaron') ? 'selected' : ''; ?>>Pengaron</option>
                                        <option value="Sambung Makmur" <?php echo ($data['kecamatan'] == 'Sambung Makmur') ? 'selected' : ''; ?>>Sambung Makmur</option>
                                        <option value="Simpang Empat" <?php echo ($data['kecamatan'] == 'Simpang Empat') ? 'selected' : ''; ?>>Simpang Empat</option>
                                        <option value="Sungai Pinang" <?php echo ($data['kecamatan'] == 'Sungai Pinang') ? 'selected' : ''; ?>>Sungai Pinang</option>
                                        <option value="Sungai Tabuk" <?php echo ($data['kecamatan'] == 'Sungai Tabuk') ? 'selected' : ''; ?>>Sungai Tabuk</option>
                                        <option value="Tatah Makmur" <?php echo ($data['kecamatan'] == 'Tatah Makmur') ? 'selected' : ''; ?>>Tatah Makmur</option>
                                        <option value="Telaga Bauntung" <?php echo ($data['kecamatan'] == 'Telaga Bauntung') ? 'selected' : ''; ?>>Telaga Bauntung</option>
                                    </select>

                                    <label class="form-label">Kelurahan/Desa</label>
                                    <select name="kelurahan" id="kelurahan" class="select-daftar" required>
                                        <option value="<?php echo htmlspecialchars($data['kelurahan']); ?>" selected><?php echo htmlspecialchars($data['kelurahan']); ?></option>
                                    </select>

                                    <label class="form-label">Kode Pos</label>
                                    <input type="number" name="kode_pos" id="kode_pos" class="form-control" value="<?php echo $data['kode_pos']; ?>">
                                </div>
                            </div>

                            <script>
                                function copyFromKTP() {
                                    const checked = document.getElementById('sameAsKTP').checked;

                                    const alamat = document.getElementById('alamat');
                                    const kecamatan = document.getElementById('kecamatan');
                                    const kelurahan = document.getElementById('kelurahan');
                                    const kode_pos = document.getElementById('kode_pos');

                                    if (checked) {
                                        alamat.value = document.getElementById('ktp_alamat').value;
                                        kecamatan.value = document.getElementById('ktp_kecamatan').value;
                                        kode_pos.value = document.getElementById('ktp_kode_pos').value;

                                        // Ambil value dan text kelurahan dari KTP
                                        const ktp_kel = document.getElementById('ktp_kelurahan');
                                        const selectedOption = ktp_kel.options[ktp_kel.selectedIndex];

                                        // Set ulang kelurahan domisili agar ada option-nya
                                        kelurahan.innerHTML = `<option value="${selectedOption.value}" selected>${selectedOption.text}</option>`;
                                    } else {
                                        alamat.value = '';
                                        kecamatan.value = '';
                                        kode_pos.value = '';
                                        kelurahan.innerHTML = '<option value="" disabled selected>-- Pilih Kelurahan --</option>';
                                    }
                                }
                            </script>


                            <div class="section-title">Edit Data Pribadi Lainnya</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-Laki" <?php echo (strtolower($data['jenis_kelamin']) == 'Laki-Laki') ? 'selected' : ''; ?>>Laki-Laki</option>
                                        <option value="Perempuan" <?php echo (strtolower($data['jenis_kelamin']) == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bentuk Wajah</label>
                                    <select name="wajah" id="wajah" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Wajah --</option>
                                        <option value="Oval" <?php echo ($data['wajah'] == 'Oval') ? 'selected' : ''; ?>>Oval</option>
                                        <option value="Lonjong" <?php echo ($data['wajah'] == 'Lonjong') ? 'selected' : ''; ?>>Lonjong</option>
                                        <option value="Kotak" <?php echo ($data['wajah'] == 'Kotak') ? 'selected' : ''; ?>>Kotak</option>
                                        <option value="Bulat" <?php echo ($data['wajah'] == 'Bulat') ? 'selected' : ''; ?>>Bulat</option>
                                        <option value="Persegi" <?php echo ($data['wajah'] == 'Persegi') ? 'selected' : ''; ?>>Persegi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">TinggiBadan</label>
                                    <input type="number" name="tinggi_badan" class="form-control" value="<?php echo $data['tinggi_badan']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Berat Badan</label>
                                    <input type="number" name="berat_badan" class="form-control" value="<?php echo $data['berat_badan']; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Rambut</label>
                                    <select name="rambut" id="rambut" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Rambut --</option>
                                        <option value="Panjang" <?php echo (strtolower($data['rambut']) == 'Panjang') ? 'selected' : ''; ?>>Panjang</option>
                                        <option value="Jilbab" <?php echo (strtolower($data['rambut']) == 'Jilbab') ? 'selected' : ''; ?>>Jilbab</option>
                                        <option value="Pendek Lurus" <?php echo (strtolower($data['rambut']) == 'Pendek Lurus') ? 'selected' : ''; ?>>Pendek Lurus</option>
                                        <option value="Pendek Ikal" <?php echo (strtolower($data['rambut']) == 'Pendek Ikal') ? 'selected' : ''; ?>>Pendek Ikal</option>
                                        <option value="Cepak" <?php echo (strtolower($data['rambut']) == 'Cepak') ? 'selected' : ''; ?>>Cepak</option>
                                        <option value="Keriting" <?php echo (strtolower($data['rambut']) == 'Keriting') ? 'selected' : ''; ?>>Keriting</option>
                                        <option value="Botak" <?php echo (strtolower($data['rambut']) == 'Botak') ? 'selected' : ''; ?>>Botak</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Alis</label>
                                    <select name="alis" id="alis" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Alis --</option>
                                        <option value="Tebal" <?php echo (strtolower($data['alis']) == 'Tebal') ? 'selected' : ''; ?>>Tebal</option>
                                        <option value="Tipis" <?php echo (strtolower($data['alis']) == 'Tipis') ? 'selected' : ''; ?>>Tipis</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hidung</label>
                                    <select name="hidung" id="hidung" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Hidung --</option>
                                        <option value="Mancung" <?php echo (strtolower($data['hidung']) == 'Mancung') ? 'selected' : ''; ?>>Mancung</option>
                                        <option value="Besar" <?php echo (strtolower($data['hidung']) == 'Besar') ? 'selected' : ''; ?>>Besar</option>
                                        <option value="Bengkok" <?php echo (strtolower($data['hidung']) == 'Bengkok') ? 'selected' : ''; ?>>Bengkok</option>
                                        <option value="Kecil" <?php echo (strtolower($data['hidung']) == 'Kecil') ? 'selected' : ''; ?>>Kecil</option>
                                        <option value="Sedang" <?php echo (strtolower($data['hidung']) == 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                                        <option value="Pesek" <?php echo (strtolower($data['hidung']) == 'Pesek') ? 'selected' : ''; ?>>Pesek</option>
                                    </select>
                                </div>
                            </div>
                            <div class="section-title">Upload Berkas Pendaftaran</div>
                            <hr>
                            <div class="row g-3">
                                <!-- Buku Setoran Awal Haji -->
                                <div class="col-md-6">
                                    <label class="form-label">Buku Setoran Awal Haji (PDF)</label> <br>
                                    <?php if (!empty($data['dokumen_setor_awal'])): ?>
                                        <small>File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_setor_awal']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file</small><br>
                                    <?php endif; ?>
                                    <input type="file" name="dokumen_setor_awal" class="form-control" accept="application/pdf">
                                </div>
                                <!-- KTP atau KIA -->
                                <div class="col-md-6">
                                    <label class="form-label">KTP atau KIA (PDF)</label> <br>
                                    <?php if (!empty($data['dokumen_ktp'])): ?>
                                        <small>File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_ktp']); ?>" target="_blank"><?php echo basename($data['dokumen_ktp']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file</small><br>
                                    <?php endif; ?>
                                    <input type="file" name="dokumen_ktp" class="form-control" accept="application/pdf">
                                </div>

                                <!-- Kartu Keluarga -->
                                <div class="col-md-6">
                                    <label class="form-label">Kartu Keluarga (PDF)</label> <br>
                                    <?php if (!empty($data['dokumen_kk'])): ?>
                                        <small>File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_kk']); ?>" target="_blank"><?php echo basename($data['dokumen_kk']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file</small><br>
                                    <?php endif; ?>
                                    <input type="file" name="dokumen_kk" class="form-control" accept="application/pdf">
                                </div>

                                <!-- Akta Lahir / Ijazah / Buku Nikah -->
                                <div class="col-md-6">
                                    <label class="form-label">Akta Lahir / Ijazah / Buku Nikah (PDF)</label> <br>
                                    <?php if (!empty($data['dokumen_lain'])): ?>
                                        <small>File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_lain']); ?>" target="_blank"><?php echo basename($data['dokumen_lain']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file</small><br>
                                    <?php endif; ?>
                                    <input type="file" name="dokumen_lain" class="form-control" accept="application/pdf">
                                </div>
                                <!-- Foto Wajah -->
                                <div class="col-md-12">
                                    <label class="form-label">Foto Wajah 80% (JPG/PNG)</label> <br>
                                    <?php if (!empty($data['foto_wajah'])): ?>
                                        <small>File saat ini: <a href="<?php echo htmlspecialchars($data['foto_wajah']); ?>" target="_blank"><?php echo basename($data['foto_wajah']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file</small><br>
                                    <?php endif; ?>
                                    <input type="file" name="foto_wajah" class="form-control" accept="image/jpeg, image/png">
                                </div>
                            </div>

                            <hr>
                            <div class="action-buttons">
                                <button type="submit" class="btn btn-success">EDIT DATA PENDAFTARAN</button>
                                <a href="../pendaftaran_jamaah.php" class="btn btn-secondary">KEMBALI</a>
                            </div>
                        </form>
                    </div>
                    <?php include_once __DIR__ . '/../../includes/footer_jamaah.php'; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="../../assets/js/sidebar.js"></script>
    <script src="/../../includes/link_script.php"></script>
    <script src="../assets/js/tambah_data.js"></script>
</body>

</html>