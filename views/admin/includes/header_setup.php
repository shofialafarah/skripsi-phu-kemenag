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

// Cek session
if (!isset($_SESSION['id_admin'])) {
    $id = 1; // Fallback sementara
} else {
    $id = $_SESSION['id_admin'];
}

// Ambil data admin
$staffQuery = $koneksi->query("SELECT nama_admin FROM administrator WHERE id_admin = $id");
$adminstrator = $staffQuery->fetch_assoc();

$base_url = "http://localhost/phu-kemenag-banjar-copy/";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Administrator</title>

    <link rel="icon" href="<?= $base_url ?>assets/img/logo_kemenag.png?v=1.1" type="image/png">
    <link rel="shortcut icon" href="<?= $base_url ?>assets/img/logo_kemenag.png?v=1.1" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/admin/assets/css/global_style.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/admin/assets/css/header.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/admin/assets/css/sidebar.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/admin/assets/css/dashboard.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/admin/assets/css/dashboard_administrator.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/admin/assets/css/pendaftaran_jamaah.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css">
</head>
<body>