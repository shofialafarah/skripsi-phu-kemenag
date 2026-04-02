<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
$koneksi_path = __DIR__ . '/includes/koneksi.php';

if (file_exists($koneksi_path)) {
    include_once $koneksi_path;
} else {
    // Backup jika file koneksi tidak ditemukan agar CSS tidak pecah
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'phu-kemenag-banjar.infinityfreeapp.com';
    if (!defined('BASE_URL')) {
        define('BASE_URL', $protocol . '://' . $host . '/');
    }
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PHU KEMENAG BANJAR - 404 Not Found</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link rel="icon" href="<?= BASE_URL ?>assets/img/logo_kemenag.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Lora:wght@600;700&display=swap" rel="stylesheet"> 

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>landing-page/assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>landing-page/assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>landing-page/assets/css/bootstrap.min.css" rel="stylesheet">

    <link href="<?= BASE_URL ?>landing-page/assets/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl py-6 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <i class="bi bi-exclamation-triangle display-1 text-primary"></i>
                    <h1 class="display-1">404</h1>
                    <h1 class="mb-4">Halaman Tidak Ditemukan</h1>
                    <p class="mb-4">Maaf, halaman yang Anda cari tidak ada di website kami! Mungkin kembali ke beranda atau mencoba menggunakan menu yang tersedia?</p>
                    <a class="btn btn-primary rounded-pill py-3 px-5" href="<?= BASE_URL ?>index.php">Kembali Ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>landing-page/assets/lib/wow/wow.min.js"></script>
    <script src="<?= BASE_URL ?>landing-page/assets/lib/easing/easing.min.js"></script>
    <script src="<?= BASE_URL ?>landing-page/assets/lib/waypoints/waypoints.min.js"></script>
    <script src="<?= BASE_URL ?>landing-page/assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="<?= BASE_URL ?>landing-page/assets/js/main.js"></script>
</body>

</html>