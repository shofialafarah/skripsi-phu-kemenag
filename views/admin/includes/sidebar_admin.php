<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Ambil pengaturan dari database
$result = $koneksi->query("SELECT * FROM pengaturan");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Default nilai jika belum diatur
$app_name = $settings['app_name'] ?? 'PHU KEMENAG';
$app_logo = $settings['app_logo'] ?? '';
$theme_text_color = $settings['theme_text_color'] ?? '#ffffff';

// Tentukan src logo untuk sidebar (sama dengan logika di pengaturan)
$default_system_logo = '/phu-kemenag-banjar-copy/assets/sistem.png';
if (empty($app_logo)) {
    $app_logo_src = $default_system_logo;
} elseif (filter_var($app_logo, FILTER_VALIDATE_URL)) {
    $app_logo_src = $app_logo;
} elseif (strpos($app_logo, '/') === 0) {
    $app_logo_src = $app_logo;
} else {
    $candidate = '/phu-kemenag-banjar-copy/assets/' . $app_logo;
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $candidate)) {
        $app_logo_src = $candidate;
    } else {
        $app_logo_src = $default_system_logo;
    }
}

// Buat array submenu agar lebih gampang
$penggunaPages = [
    'manajemen_jamaah.php',
    'tambah_jamaah.php',
    'edit_jamaah.php',
    'manajemen_staf.php',
    'tambah_staf.php',
    'edit_staf.php',
    'profil_staf.php',
    'manajemen_kasi.php',
    'edit_kasi.php',
    'profil_kasi.php'
];

$sistemPage = ['pengaturan.php', 'reset_password.php'];
$cetakPage = ['laporan_data_pengguna.php', 'laporan_riwayat_aksi.php'];
?>

<!-- Terapkan pengaturan warna teks -->
<style>
    :root {
        --sidebar-text-color: <?= htmlspecialchars($theme_text_color); ?>;
    }

    /* Warna default dari database */

    .sidebar .menu a p,
    .sidebar .menu a .material-symbols-outlined,
    .sidebar .dropdown-toggle p,
    .sidebar .dropdown-toggle .material-symbols-outlined,
    .sidebar .submenu a {
        color: var(--sidebar-text-color) !important;
    }

    .sidebar .header h1 {
        color: white !important;
        /* atau warna tetap lainnya */
    }

    /* Hover putih */
    .sidebar a:hover,
    .sidebar a:hover p,
    .sidebar a:hover .material-symbols-outlined,
    .sidebar .dropdown-toggle:hover p,
    .sidebar .dropdown-toggle:hover .material-symbols-outlined,
    .sidebar .submenu a:hover {
        color: white !important;
    }

    .sidebar .header .logo {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }

    /* Saat item aktif (halaman dibuka), warnanya tetap putih */
    .sidebar a.active,
    .sidebar a.active p,
    .sidebar a.active .material-symbols-outlined,
    .sidebar .submenu a.active {
        color: white !important;
    }

    /* Opsional: tambahkan background gelap transparan agar terlihat terpilih */
    .sidebar a.active {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>

<!-- SIDEBAR langsung di sini (karena baru satu) -->
<div class="sidebar-wrapper">
    <aside class="sidebar">
        <button class="toggle" type="button" onclick="toggleOpen()">
            <span class="material-symbols-outlined">chevron_right</span>
        </button>
        <div class="inner">
            <div class="header">
                <img src="<?= htmlspecialchars($app_logo_src); ?>" class="logo" />
                <h1><?= htmlspecialchars($app_name); ?></h1>
            </div>
            <div class="search">
                <span class="material-symbols-outlined">search</span>
                <input type="text" placeholder="Search" />
            </div>

            <div class="content-wrapper">
                <nav class="menu">
                    <a href="../../dashboard_administrator.php" class="<?= ($currentPage == 'dashboard_administrator.php') ? 'active' : '' ?>">
                        <span class="material-symbols-outlined">dashboard</span>
                        <p>Dashboard</p>
                    </a>
                    <!-- Manajemen Pengguna -->
                    <a class="dropdown-toggle <?= in_array($currentPage, $penggunaPages) ? 'active' : '' ?>" onclick="toggleDropdown('penggunaSubmenu', this)">
                        <span class="material-symbols-outlined">person</span>
                        <p>Pengguna</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $penggunaPages) ? 'open' : '' ?>" id="penggunaSubmenu">
                        <a href="/phu-kemenag-banjar-copy/views/admin/akun-pengguna/jamaah/manajemen_jamaah.php" class="<?= ($currentPage == 'akun-pengguna/jamaah/manajemen_jamaah.php' || $currentPage == 'tambah_jamaah.php' || $currentPage == 'edit_jamaah.php') ? 'active' : '' ?>">Jamaah</a>
                        <a href="/phu-kemenag-banjar-copy/views/admin/akun-pengguna/staf/manajemen_staf.php" class="<?= ($currentPage == 'akun-pengguna/staf/manajemen_staf.php' || $currentPage == 'tambah_staf.php' || $currentPage == 'edit_staf.php' || $currentPage == 'profil_staf.php') ? 'active' : '' ?>">Staf PHU</a>
                        <a href="/phu-kemenag-banjar-copy/views/admin/akun-pengguna/kasi/manajemen_kasi.php" class="<?= ($currentPage == 'akun-pengguna/kasi/manajemen_kasi.php' || $currentPage == 'edit_kasi.php' || $currentPage == 'profil_kasi.php') ? 'active' : '' ?>">Kepala Seksi</a>
                    </div>
                    <!-- Manajemen Sistem -->
                    <a class="dropdown-toggle <?= in_array($currentPage, $sistemPage) ? 'active' : '' ?>" onclick="toggleDropdown('sistemSubmenu', this)">
                        <span class="material-symbols-outlined">settings</span>
                        <p>Sistem</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $sistemPage) ? 'open' : '' ?>" id="sistemSubmenu">
                        <a href="/phu-kemenag-banjar-copy/views/admin/kelola-sistem/pengaturan.php" class="<?= ($currentPage == 'kelola-sistem/pengaturan.php') ? 'active' : '' ?>">Pengaturan</a>
                        <a href="/phu-kemenag-banjar-copy/views/admin/kelola-sistem/reset_password.php" class="<?= ($currentPage == 'kelola-sistem/reset_password.php') ? 'active' : '' ?>">Reset Password</a>
                    </div>
                    <!-- Laporan Data Sistem -->
                    <a class="dropdown-toggle <?= in_array($currentPage, $cetakPage) ? 'active' : '' ?>" onclick="toggleDropdown('cetakSubmenu', this)">
                        <span class="material-symbols-outlined">print</span>
                        <p>Laporan</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $cetakPage) ? 'open' : '' ?>" id="cetakSubmenu">
                        <a href="/phu-kemenag-banjar-copy/views/admin/laporan/laporan_data_pengguna.php" class="<?= ($currentPage == 'laporan/laporan_data_pengguna.php') ? 'active' : '' ?>">Data Pengguna</a>
                        <a href="/phu-kemenag-banjar-copy/views/admin/laporan/laporan_riwayat_aksi.php" class="<?= ($currentPage == 'laporan_riwayat_aksi.php') ? 'active' : '' ?>">Riwayat Aksi</a>
                    </div>
                </nav>
                <a href="<?= BASE_URL ?>views/auth/logout.php" class="logout-btn">
                    <span class="material-symbols-outlined">logout</span>
                </a>
            </div>
        </div>
    </aside>
</div>

<!-- Scripts -->
<script src="assets/js/sidebar_staf.js"></script>
</body>

</html>