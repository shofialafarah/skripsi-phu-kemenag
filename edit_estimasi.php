<?php
include 'koneksi.php';
session_start();

function clean_input($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(stripslashes(trim($data))));
}

// Ambil data dari form
$id_estimasi = clean_input($_POST['id_estimasi']);
$nomor_porsi = clean_input($_POST['nomor_porsi']);
$nama_jamaah = clean_input($_POST['nama_jamaah']);
$nama_ayah = clean_input($_POST['nama_ayah']);
$jenis_kelamin = clean_input($_POST['jenis_kelamin']);
$tanggal_lahir = clean_input($_POST['tanggal_lahir']);
$tgl_pendaftaran = clean_input($_POST['tgl_pendaftaran']);
$status_haji = clean_input($_POST['status_haji']);

// Validasi nomor porsi (tidak boleh sama dengan jamaah lain)
$cek = mysqli_query($koneksi, "SELECT id_estimasi FROM estimasi WHERE nomor_porsi = '$nomor_porsi' AND id_estimasi != '$id_estimasi'");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['error_message'] = 'Nomor Porsi sudah digunakan oleh jamaah lain!';
    header("Location: entry_estimasi.php");
    exit();
}

// Hitung umur
$lahir = new DateTime($tanggal_lahir);
$now = new DateTime();
$umur = $lahir->diff($now)->y;

function formatInterval($interval) {
    $result = [];

    if ($interval->y > 0) {
        $result[] = $interval->y . ' tahun';
    }
    if ($interval->m > 0) {
        $result[] = $interval->m . ' bulan';
    }
    if ($interval->d > 0) {
        $result[] = $interval->d . ' hari';
    }

    return implode(' ', $result);
}


$masa_menunggu_tahun = 25;
$daftar = new DateTime($tgl_pendaftaran);

// Telah Menunggu
$interval_telahan_menunggu = $daftar->diff($now);
$telah_menunggu = formatInterval($interval_telahan_menunggu);

// Estimasi Berangkat (sebelumnya sudah benar)
$estimasi_berangkat = (clone $daftar)->modify("+$masa_menunggu_tahun years")->format('Y-m-d');

// Sisa Menunggu
$estimasi_date = new DateTime($estimasi_berangkat);
$interval_sisa_menunggu = $now->diff($estimasi_date);
$sisa_menunggu = formatInterval($interval_sisa_menunggu);

// Masa Menunggu (pakai dummy date)
$masa_date = (clone $daftar)->modify("+$masa_menunggu_tahun years");
$interval_masa_menunggu = $daftar->diff($masa_date);
$masa_menunggu = formatInterval($interval_masa_menunggu);


// Update data di database
$query = "UPDATE estimasi SET 
    nomor_porsi = '$nomor_porsi',
    nama_jamaah = '$nama_jamaah',
    nama_ayah = '$nama_ayah',
    jenis_kelamin = '$jenis_kelamin',
    tanggal_lahir = '$tanggal_lahir', 
    umur = '$umur',
    tgl_pendaftaran = '$tgl_pendaftaran',  
    status_haji = '$status_haji', 
    telah_menunggu = '$telah_menunggu',
    estimasi_berangkat = '$estimasi_berangkat',
    sisa_menunggu = '$sisa_menunggu',
    masa_menunggu = '$masa_menunggu'
    WHERE id_estimasi = '$id_estimasi'";

if (mysqli_query($koneksi, $query)) {
    $_SESSION['success_message'] = 'Data berhasil diperbarui!';
} else {
    // Menangani error SQL dengan mysqli_error()
    $_SESSION['error_message'] = 'Gagal memperbarui data! Error: ' . mysqli_error($koneksi);
}

header("Location: entry_estimasi.php");
exit();
?>
