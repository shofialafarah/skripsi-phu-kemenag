<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_jamaah = $_SESSION['id_jamaah'];

    // Ambil data dari form
    $nama_jamaah = $_POST['nama_jamaah'];
    $nik = $_POST['nomor_ktp'];
    $nama_ayah = $_POST['nama_ayah'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $pendidikan = $_POST['pendidikan'];
    $pekerjaan = $_POST['pekerjaan'];
    $kewarganegaraan = $_POST['kewarganegaraan'];
    $goldar = $_POST['goldar'];
    $telp_rumah = $_POST['telp_rumah'];
    $nomor_hp = $_POST['nomor_hp'];
    $status_perkawinan = $_POST['status_perkawinan'];
    $status_pergi_haji = $_POST['status_pergi_haji'];
    $alamat = $_POST['alamat'];
    $provinsi = $_POST['provinsi'];
    $kabupaten = $_POST['kabupaten'];
    $kecamatan = $_POST['kecamatan'];
    $desa = $_POST['kelurahan'];
    $kode_pos = $_POST['kode_pos'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $wajah = $_POST['wajah'];
    $tinggi_badan = $_POST['tinggi_badan'];
    $berat_badan = $_POST['berat_badan'];
    $rambut = $_POST['rambut'];

    // File upload (opsional jika form upload diaktifkan)
    $dokumen = $_FILES['dokumen']['name'] ?? null;
    $unique_name = null;
    if ($dokumen) {
        $unique_name = uniqid() . '.' . pathinfo($dokumen, PATHINFO_EXTENSION);
        $target_dir = "uploads/pendaftaran/pengajuan/";
        $target_file = $target_dir . $unique_name;
        move_uploaded_file($_FILES['dokumen']['tmp_name'], $target_file);
    }

    $tanggal_pengajuan = date('Y-m-d');

    $query = "INSERT INTO pendaftaran 
    (id_jamaah, nama_jamaah, nik, nama_ayah, tempat_lahir, tanggal_lahir, pendidikan, pekerjaan, kewarganegaraan, goldar, telp_rumah, no_telepon, status_perkawinan, status_pergi_haji, alamat, provinsi, kabupaten, kecamatan, desa, kode_pos, jenis_kelamin, wajah, tinggi_badan, berat_badan, rambut, dokumen, tanggal_pengajuan, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("isssssssssssssssssssssssssss", 
        $id_jamaah, $nama_jamaah, $nik, $nama_ayah, $tempat_lahir, $tanggal_lahir, $pendidikan, $pekerjaan, $kewarganegaraan, $goldar, $telp_rumah, $nomor_hp, 
        $status_perkawinan, $status_pergi_haji, $alamat, $provinsi, $kabupaten, $kecamatan, $desa, $kode_pos, 
        $jenis_kelamin, $wajah, $tinggi_badan, $berat_badan, $rambut, $unique_name, $tanggal_pengajuan
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Data pendaftaran berhasil disimpan.";
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan data. Error: " . $stmt->error;
    }

    header("Location: tambah_pendaftaran.php");
    exit();
}
?>
