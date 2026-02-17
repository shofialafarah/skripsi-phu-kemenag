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
$verifikasiPages = ['verifikasi_pendaftaran.php', 'verifikasi_pembatalan.php', 'verifikasi_pelimpahan.php'];
$cetakPages = ['laporan_pendaftaran.php', 'laporan_pembatalan.php', 'laporan_pelimpahan.php', 'laporan_estimasi.php', 'laporan_rekapitulasi.php'];
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
                    <img src="<?= file_exists('settings/' . $app_logo) ? 'settings/' . htmlspecialchars($app_logo) : htmlspecialchars($app_logo); ?>" class="logo" />
                    <h1><?= htmlspecialchars($app_name); ?></h1>
                </div>
                <div class="search">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" placeholder="Search" />
                </div>

                <div class="content-wrapper">
                    <nav class="menu">
                        <a href="../dashboard_kepala_seksi.php" class="<?= ($currentPage == 'dashboard_kepala_seksi.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">dashboard</span>
                            <p>Dashboard</p>
                        </a>
                        <!-- Verifikasi -->
                        <a class="dropdown-toggle <?= in_array($currentPage, $verifikasiPages) ? 'active' : '' ?>" onclick="toggleDropdown('monitoringSubmenu', this)">
                            <span class="material-symbols-outlined">checklist</span>
                            <p>Verifikasi</p>
                            <span class="material-symbols-outlined arrow">expand_more</span>
                        </a>
                        <div class="submenu <?= in_array($currentPage, $verifikasiPages) ? 'open' : '' ?>" id="monitoringSubmenu">
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/verifikasi/verifikasi_pendaftaran.php" class="<?= ($currentPage == 'verifikasi_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/verifikasi/verifikasi_pembatalan.php" class="<?= ($currentPage == 'verifikasi_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/verifikasi/verifikasi_pelimpahan.php" class="<?= ($currentPage == 'verifikasi_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                        </div>
                        <!-- Laporan -->
                        <a class="dropdown-toggle <?= in_array($currentPage, $cetakPages) ? 'active' : '' ?>" onclick="toggleDropdown('entrySubmenu', this)">
                            <span class="material-symbols-outlined">print</span>
                            <p>Laporan</p>
                            <span class="material-symbols-outlined arrow">expand_more</span>
                        </a>
                        <div class="submenu <?= in_array($currentPage, $cetakPages) ? 'open' : '' ?>" id="entrySubmenu">
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/laporan/laporan_pendaftaran.php" class="<?= ($currentPage == 'laporan_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/laporan/laporan_estimasi.php" class="<?= ($currentPage == 'laporan_estimasi.php') ? 'active' : '' ?>">Estimasi Haji</a>
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/laporan/laporan_pembatalan.php" class="<?= ($currentPage == 'laporan_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/laporan/laporan_pelimpahan.php" class="<?= ($currentPage == 'laporan_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                            <a href="/phu-kemenag-banjar-copy/views/kepala-seksi/laporan/laporan_rekapitulasi.php" class="<?= ($currentPage == 'laporan_rekapitulasi.php') ? 'active' : '' ?>">Rekapitulasi</a>
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
    <script src="sidebar.js"></script>
</body>

</html>