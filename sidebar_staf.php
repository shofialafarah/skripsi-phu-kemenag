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
$monitoringPages = ['monitoring_pendaftaran.php', 'monitoring_pembatalan.php', 'monitoring_pelimpahan.php'];
$entryPages = ['entry_pendaftaran.php', 'entry_pembatalan.php', 'entry_pelimpahan.php', 'entry_estimasi.php'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sidebar Staf</title>
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
                        <a href="dashboard_staf.php" class="<?= ($currentPage == 'dashboard_staf.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">dashboard</span>
                            <p>Dashboard</p>
                        </a>
                        <!-- Monitoring -->
                        <a class="dropdown-toggle <?= in_array($currentPage, $monitoringPages) ? 'active' : '' ?>" onclick="toggleDropdown('monitoringSubmenu', this)">
                            <span class="material-symbols-outlined">monitoring</span>
                            <p>Monitoring</p>
                            <span class="material-symbols-outlined arrow">expand_more</span>
                        </a>
                        <div class="submenu <?= in_array($currentPage, $monitoringPages) ? 'open' : '' ?>" id="monitoringSubmenu">
                            <a href="monitoring_pendaftaran.php" class="<?= ($currentPage == 'monitoring_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                            <a href="monitoring_pembatalan.php" class="<?= ($currentPage == 'monitoring_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                            <a href="monitoring_pelimpahan.php" class="<?= ($currentPage == 'monitoring_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                        </div>
                        <!-- Entry -->
                        <a class="dropdown-toggle <?= in_array($currentPage, $entryPages) ? 'active' : '' ?>" onclick="toggleDropdown('entrySubmenu', this)">
                            <span class="material-symbols-outlined">analytics</span>
                            <p>Entry</p>
                            <span class="material-symbols-outlined arrow">expand_more</span>
                        </a>
                        <div class="submenu <?= in_array($currentPage, $entryPages) ? 'open' : '' ?>" id="entrySubmenu">
                            <a href="entry_pendaftaran.php" class="<?= ($currentPage == 'entry_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                            <a href="entry_estimasi.php" class="<?= ($currentPage == 'entry_estimasi.php') ? 'active' : '' ?>">Estimasi Haji</a>
                            <a href="entry_pembatalan.php" class="<?= ($currentPage == 'entry_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                            <a href="entry_pelimpahan.php" class="<?= ($currentPage == 'entry_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                        </div>
                        <!-- <a href="pembatalan_haji.php" class="<?= ($currentPage == 'pembatalan_haji.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">cancel</span>
                            <p>Pembatalan Haji</p>
                        </a>
                        <a href="pelimpahan_haji.php">
                            <span class="material-symbols-outlined" class="<?= ($currentPage == 'pelimpahan_haji.php') ? 'active' : '' ?>">swap_horiz</span>
                            <p>Pelimpahan Haji</p>
                        </a> -->
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