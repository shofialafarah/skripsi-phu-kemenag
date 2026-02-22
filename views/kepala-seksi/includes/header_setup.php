<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../../includes/koneksi.php';

// Ambil ID jamaah dari session
if (!isset($_SESSION['id_kepala'])) {
    // Jika tidak ada session, arahkan ke login atau beri nilai default
    header("Location: ../../auth/login.php"); 
    exit();
}

// Cek session
if (!isset($_SESSION['id_kepala'])) {
    $id = 1; // Fallback sementara
} else {
    $id = $_SESSION['id_kepala'];
}
// Ambil data kasi (pastikan nama kolom di tabel 'kasi' adalah 'nama' — cek pakai DESCRIBE kasi di phpMykepala-seksi)
$kasiQuery = $koneksi->query("SELECT nama_kepala FROM kepala_seksi WHERE id_kepala = 1");
$kasi = $kasiQuery->fetch_assoc();
$base_url = "http://localhost/phu-kemenag-banjar-copy/";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Kepala Seksi</title>

    <link rel="icon" href="<?= $base_url ?>assets/img/logo_kemenag.png?v=1.1" type="image/png">
    <link rel="shortcut icon" href="<?= $base_url ?>assets/img/logo_kemenag.png?v=1.1" type="image/png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- css -->
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/global_style.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/header.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/sidebar.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/dashboard.css">
    <!-- css halaman tampil -->
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/entry.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/dashboard_kepala_seksi.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/verifikasi_kepala_seksi.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/laporan_kepala_seksi.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/estimasi.css">
</head>

<body>