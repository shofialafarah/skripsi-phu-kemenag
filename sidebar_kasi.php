<?php
include 'koneksi.php';

// INI yang penting kamu tambahkan
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Ambil pengaturan dari database
$result = $koneksi->query("SELECT * FROM pengaturan");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Default nilai jika belum diatur
$app_name = $settings['app_name'] ?? 'PHU KEMENAG';
$app_logo = $settings['app_logo'] ?? 'logo_kemenag.png';
$theme_text_color = $settings['theme_text_color'] ?? '#ffffff';

// Buat array submenu agar lebih gampang
$verifikasiPages = ['verifikasi_pendaftaran.php', 'verifikasi_pembatalan.php', 'verifikasi_pelimpahan.php'];
$cetakPages = ['laporan_pendaftaran.php', 'laporan_pembatalan.php', 'laporan_pelimpahan.php', 'laporan_estimasi.php', 'laporan_rekapitulasi.php'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sidebar Kasi</title>
    <link rel="stylesheet" href="kumpulan-css/global_style.css">
    <link rel="stylesheet" href="kumpulan-css/header.css">
    <link rel="stylesheet" href="kumpulan-css/sidebar.css">
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
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
</head>

<body>

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
                        <a href="dashboard_kepala_seksi.php" class="<?= ($currentPage == 'dashboard_kepala_seksi.php') ? 'active' : '' ?>">
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
                            <a href="verifikasi_pendaftaran.php" class="<?= ($currentPage == 'verifikasi_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                            <a href="verifikasi_pembatalan.php" class="<?= ($currentPage == 'verifikasi_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                            <a href="verifikasi_pelimpahan.php" class="<?= ($currentPage == 'verifikasi_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                        </div>
                        <!-- Laporan -->
                        <a class="dropdown-toggle <?= in_array($currentPage, $cetakPages) ? 'active' : '' ?>" onclick="toggleDropdown('entrySubmenu', this)">
                            <span class="material-symbols-outlined">print</span>
                            <p>Laporan</p>
                            <span class="material-symbols-outlined arrow">expand_more</span>
                        </a>
                        <div class="submenu <?= in_array($currentPage, $cetakPages) ? 'open' : '' ?>" id="entrySubmenu">
                            <a href="laporan_pendaftaran.php" class="<?= ($currentPage == 'laporan_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                            <a href="laporan_estimasi.php" class="<?= ($currentPage == 'laporan_estimasi.php') ? 'active' : '' ?>">Estimasi Haji</a>
                            <a href="laporan_pembatalan.php" class="<?= ($currentPage == 'laporan_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                            <a href="laporan_pelimpahan.php" class="<?= ($currentPage == 'laporan_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                            <a href="laporan_rekapitulasi.php" class="<?= ($currentPage == 'laporan_rekapitulasi.php') ? 'active' : '' ?>">Rekapitulasi</a>
                        </div>
                    </nav>
                    <button class="logout-btn" onclick="window.location.href='login.php'">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>


    <!-- Scripts -->
    <script src="sidebar_staf.js"></script>
</body>

</html>