<?php
include('koneksi.php');

// Ambil pengaturan logo dan nama aplikasi
$result = $koneksi->query("SELECT * FROM pengaturan WHERE key_name IN ('app_name', 'app_logo')");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Ambil pengaturan warna teks
$result = $koneksi->query("SELECT value FROM pengaturan WHERE key_name='theme_text_color'");
$theme_text_color = $result->fetch_assoc()['value'] ?? '#333333'; // Default abu-abu gelap
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Jamaah</title>
    <link rel="icon" href="logo_kemenag.png">
    <!-- <title>Kementerian Agama Kab.Banjar - SISFO PHU</title> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollToPlugin.min.js"></script>
    <link rel="stylesheet" href="header_jamaah.css">
    <style>
        /* Terapkan warna teks ke elemen tertentu */
        .logo-text {
            color: <?= htmlspecialchars($theme_text_color); ?>;
        }
        .nav-links a {
            color: <?= htmlspecialchars($theme_text_color); ?>;
        }
        .dropdown .dropbtn {
            color: <?= htmlspecialchars($theme_text_color); ?>;
        }
    </style>
</head>
<body>
    <!-- <div class="floating-cards"></div> -->
    
    <nav>
        <div class="logo">
            <img src="settings/<?= htmlspecialchars($settings['app_logo']); ?>" alt="Logo" width="50" height="50">
            <!-- <img src="logo_kemenag.png" width="50" height="50" alt="Logo" class="nav-logo"> -->
            <div class="logo-text">
                <?= htmlspecialchars($settings['app_name']); ?>
                <!-- Kementerian Agama<br>Kab.Banjar -->
            </div>
        </div>
        <div class="nav-links">
            <a href="jamaah.php">Kembali</a>
        </div>
    </nav>