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
    header("Location: ../../../auth/login.php");
    exit();
}
$id_jamaah = $_SESSION['id_jamaah'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =========================
    // AMBIL DATA FORM
    // =========================
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

    $tanggal_pengajuan = date('Y-m-d');
// =========================
    // BUAT FOLDER OTOMATIS
    // =========================

    // Amankan nama folder
    $nama_folder = preg_replace('/[^A-Za-z0-9\-]/', '_', $nama_jamaah);
    $folder_jamaah = $nama_folder . '-' . $nik;

    $base_dir = "../../../../uploads/pendaftaran/pengajuan-jamaah/";
    $target_dir = $base_dir . $folder_jamaah . "/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // =========================
    // FUNCTION UPLOAD FILE
    // =========================
    function uploadDokumen($fileFieldName, $target_dir, $prefix, $nama_jamaah)
    {
        if (!isset($_FILES[$fileFieldName]) || $_FILES[$fileFieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file_ext = strtolower(pathinfo($_FILES[$fileFieldName]['name'], PATHINFO_EXTENSION));

        // Validasi MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$fileFieldName]['tmp_name']);
        finfo_close($finfo);

        if ($fileFieldName === 'foto_wajah') {
            if (!in_array($mime, ['image/jpeg', 'image/png'])) {
                die("Foto wajah harus JPG atau PNG.");
            }
        } else {
            if ($mime !== 'application/pdf') {
                die("File $fileFieldName harus berupa PDF.");
            }
        }

        // Amankan nama jamaah untuk nama file
        $safe_nama = preg_replace('/[^A-Za-z0-9\-]/', '_', $nama_jamaah);

        $new_file_name = $prefix . '_' . $safe_nama . '.' . $file_ext;
        $target_path = $target_dir . $new_file_name;

        if (!move_uploaded_file($_FILES[$fileFieldName]['tmp_name'], $target_path)) {
            die("Gagal upload file $fileFieldName.");
        }

        return $target_path;
    }

    // =========================
    // UPLOAD FILE
    // =========================
   
    $dokumen_setor_awal = uploadDokumen('dokumen_setor_awal', $target_dir, 'SetorAwal', $nama_jamaah);
    $dokumen_ktp        = uploadDokumen('dokumen_ktp', $target_dir, 'KTP', $nama_jamaah);
    $dokumen_kk         = uploadDokumen('dokumen_kk', $target_dir, 'KK', $nama_jamaah);
    $dokumen_lain       = uploadDokumen('dokumen_lain', $target_dir, 'DokumenLain', $nama_jamaah);
    $foto_wajah         = uploadDokumen('foto_wajah', $target_dir, 'FotoWajah', $nama_jamaah);

    // =========================
    // INSERT DATABASE
    // =========================

    $query = "INSERT INTO pendaftaran (
        id_jamaah, nama_jamaah, nik, nama_ayah, tempat_lahir, tanggal_lahir,
        pendidikan, pekerjaan, kewarganegaraan, goldar, telp_rumah, no_telepon,
        status_perkawinan, status_pergi_haji,
        ktp_alamat, ktp_kecamatan, ktp_kelurahan, ktp_kode_pos,
        alamat, kecamatan, kelurahan, kode_pos, jenis_kelamin, wajah,
        tinggi_badan, berat_badan, rambut, alis, hidung,
        dokumen_setor_awal, dokumen_ktp, dokumen_kk, dokumen_lain, foto_wajah,
        tanggal_pengajuan
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $koneksi->prepare($query);

    if (!$stmt) {
        die("Prepare failed: " . $koneksi->error);
    }

    $types = "issssssssssssssssssssssssiissssssss"; 

    $stmt->bind_param(
        $types,
        $id_jamaah,
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
        $ktp_alamat,            
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
        $tanggal_pengajuan
    );

    if ($stmt->execute()) {
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pendaftaran', 'Menambahkan data pendaftaran baru');

        $_SESSION['success_message'] = "Pengajuan pendaftaran berhasil dikirim.";
        header("Location: ../pendaftaran_jamaah.php");
        exit();
    } else {
        echo "Execute failed: " . $stmt->error;
    }
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
                        Tambah Pendaftaran Haji
                    </div>
                    <div class="pendaftaran-body" style="color: #1b5e20;">
                        <div class="section-title">Masukkan Data Pribadi</div>
                        <hr>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_jamaah" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nomor KTP</label>
                                    <input type="text" name="nik" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Ayah Kandung</label>
                                    <input type="text" name="nama_ayah" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pendidikan Terakhir</label>
                                    <select name="pendidikan" id="pendidikan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Pendidikan Terakhir --</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SM">SM</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pekerjaan</label>
                                    <select name="pekerjaan" id="pekerjaan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                        <option value="Mahasiswa">Mahasiswa</option>
                                        <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                        <option value="Pensiunan">Pensiunan</option>
                                        <option value="Polri">Polri</option>
                                        <option value="Pedagang">Pedagang</option>
                                        <option value="Tani">Tani</option>
                                        <option value="Pegawai BUMN">Pegawai BUMN</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kewarganegaraan</label>
                                    <select name="kewarganegaraan" id="kewarganegaraan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Kewarganegaraan --</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Asing">Asing</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Golongan Darah</label>
                                    <select name="goldar" id="goldar" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Golongan Darah --</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Telp. Rumah</label>
                                    <input type="number" name="telp_rumah" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP</label>
                                    <input type="number" name="no_telepon" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select name="status_perkawinan" id="status_perkawinan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Status Perkawinan --</option>
                                        <option value="Belum Menikah">Belum Menikah</option>
                                        <option value="Menikah">Menikah</option>
                                        <option value="Duda">Duda</option>
                                        <option value="Janda">Janda</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Pernah Pergi Haji</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_pergi_haji" id="sudah_haji" value="Sudah Haji" required>
                                        <label class="form-check-label" for="sudah_haji">Sudah Haji</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_pergi_haji" id="belum_haji" value="Belum Haji" required>
                                        <label class="form-check-label" for="belum_haji">Belum Haji</label>
                                    </div>
                                </div>
                            </div>
                            <!-- ================================================================================================= -->
                            <div class="section-title">Masukkan Data Tempat Tinggal Sekarang</div>
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
                                    <input type="text" name="ktp_alamat" id="ktp_alamat" class="form-control" required>

                                    <label class="form-label">Kecamatan KTP</label>
                                    <select name="ktp_kecamatan" id="ktp_kecamatan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Kecamatan --</option>
                                        <option value="Aluh-Aluh">Aluh-Aluh</option>
                                        <option value="Aranio">Aranio</option>
                                        <option value="Astambul">Astambul</option>
                                        <option value="Beruntung Baru">Beruntung Baru</option>
                                        <option value="Cintapuri Darussalam">Cintapuri Darussalam</option>
                                        <option value="Gambut">Gambut</option>
                                        <option value="Karang Intan">Karang Intan</option>
                                        <option value="Kertak Hanyar">Kertak Hanyar</option>
                                        <option value="Mataraman">Mataraman</option>
                                        <option value="Martapura">Martapura</option>
                                        <option value="Martapura Barat">Martapura Barat</option>
                                        <option value="Martapura Timur">Martapura Timur</option>
                                        <option value="Paramasan">Paramasan</option>
                                        <option value="Pengaron">Pengaron</option>
                                        <option value="Sambung Makmur">Sambung Makmur</option>
                                        <option value="Simpang Empat">Simpang Empat</option>
                                        <option value="Sungai Pinang">Sungai Pinang</option>
                                        <option value="Sungai Tabuk">Sungai Tabuk</option>
                                        <option value="Tatah Makmur">Tatah Makmur</option>
                                        <option value="Telaga Bauntung">Telaga Bauntung</option>
                                    </select>

                                    <label class="form-label">Kelurahan/Desa KTP</label>
                                    <select name="ktp_kelurahan" id="ktp_kelurahan" class="select-daftar" required>
                                        <option value="" selected>-- Pilih Kelurahan --</option>
                                    </select>

                                    <label class="form-label">Kode Pos KTP</label>
                                    <input type="number" name="ktp_kode_pos" id="ktp_kode_pos" class="form-control" required>
                                </div>

                                <!-- Kolom kanan: Alamat Domisili Sekarang -->
                                <div class="col-md-6">
                                    <label class="form-label">Alamat Tinggal Sekarang</label>
                                    <input type="text" name="alamat" id="alamat" class="form-control" required>

                                    <label class="form-label">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Kecamatan --</option>
                                        <option value="Aluh-Aluh">Aluh-Aluh</option>
                                        <option value="Aranio">Aranio</option>
                                        <option value="Astambul">Astambul</option>
                                        <option value="Beruntung Baru">Beruntung Baru</option>
                                        <option value="Cintapuri Darussalam">Cintapuri Darussalam</option>
                                        <option value="Gambut">Gambut</option>
                                        <option value="Karang Intan">Karang Intan</option>
                                        <option value="Kertak Hanyar">Kertak Hanyar</option>
                                        <option value="Mataraman">Mataraman</option>
                                        <option value="Martapura">Martapura</option>
                                        <option value="Martapura Barat">Martapura Barat</option>
                                        <option value="Martapura Timur">Martapura Timur</option>
                                        <option value="Paramasan">Paramasan</option>
                                        <option value="Pengaron">Pengaron</option>
                                        <option value="Sambung Makmur">Sambung Makmur</option>
                                        <option value="Simpang Empat">Simpang Empat</option>
                                        <option value="Sungai Pinang">Sungai Pinang</option>
                                        <option value="Sungai Tabuk">Sungai Tabuk</option>
                                        <option value="Tatah Makmur">Tatah Makmur</option>
                                        <option value="Telaga Bauntung">Telaga Bauntung</option>
                                    </select>

                                    <label class="form-label">Kelurahan/Desa</label>
                                    <select name="kelurahan" id="kelurahan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Kelurahan --</option>
                                    </select>

                                    <label class="form-label">Kode Pos</label>
                                    <input type="number" name="kode_pos" id="kode_pos" class="form-control" required>
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

                            <!-- ================================================================================================ -->
                            <div class="section-title">Masukkan Data Pribadi Lainnya</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-Laki">Laki-Laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Bentuk Wajah</label>
                                    <select name="wajah" id="wajah" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Jenis Wajah --</option>
                                        <option value="Oval">Oval</option>
                                        <option value="Lonjong">Lonjong</option>
                                        <option value="Kotak">Kotak</option>
                                        <option value="Bulat">Bulat</option>
                                        <option value="Persegi">Persegi</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Tinggi Badan</label>
                                    <input type="number" name="tinggi_badan" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label>Berat Badan</label>
                                    <input type="number" name="berat_badan" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label>Rambut</label>
                                    <select name="rambut" id="rambut" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Jenis Rambut --</option>
                                        <option value="Panjang">Panjang</option>
                                        <option value="Jilbab">Jilbab</option>
                                        <option value="Pendek Lurus">Pendek Lurus</option>
                                        <option value="Pendek Ikal">Pendek Ikal</option>
                                        <option value="Cepak">Cepak</option>
                                        <option value="Keriting">Keriting</option>
                                        <option value="Botak">Botak</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Alis</label>
                                    <select name="alis" id="alis" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Jenis Alis --</option>
                                        <option value="Tebal">Tebal</option>
                                        <option value="Tipis">Tipis</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Hidung</label>
                                    <select name="hidung" id="hidung" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Jenis Hidung --</option>
                                        <option value="Mancung">Mancung</option>
                                        <option value="Besar">Besar</option>
                                        <option value="Bengkok">Bengkok</option>
                                        <option value="Kecil">Kecil</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Pesek">Pesek</option>
                                    </select>
                                </div>
                            </div>
                            <!-- ================================================================================================= -->
                            <div class="section-title">Upload Berkas Pendaftaran</div>
                            <hr>
                            <div class="row g-3">
                                <!-- Buku Setoran Awal Haji -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Buku Setoran Awal Haji (PDF)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="dokumen_setor_awal" class="form-control" accept="application/pdf" required>
                                </div>
                                <!-- KTP atau KIA -->
                                <div class="col-md-6">
                                    <label class="form-label">KTP atau KIA (PDF)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="dokumen_ktp" class="form-control" accept="application/pdf">
                                </div>

                                <!-- Kartu Keluarga -->
                                <div class="col-md-6">
                                    <label class="form-label">Kartu Keluarga (PDF)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="dokumen_kk" class="form-control" accept="application/pdf">
                                </div>

                                <!-- Akta Lahir / Ijazah / Buku Nikah -->
                                <div class="col-md-6">
                                    <label class="form-label">Akta Lahir / Ijazah / Buku Nikah (PDF)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="dokumen_lain" class="form-control" accept="application/pdf">
                                </div>
                                <!-- Foto Wajah -->
                                <div class="col-md-12">
                                    <label class="form-label">Foto Wajah 80% (JPG/PNG)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="foto_wajah" class="form-control" accept="image/jpeg, image/png">
                                </div>
                            </div>

                            <hr>
                            <div class="action-buttons">
                                <button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i> TAMBAH DATA PENDAFTARAN</button>
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