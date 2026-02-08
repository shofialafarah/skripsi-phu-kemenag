<?php
include 'koneksi.php';

// INI yang penting kamu tambahkan
$currentPage = basename($_SERVER['PHP_SELF']);

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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sidebar Jamaah</title>
    <link rel="stylesheet" href="kumpulan-css/global_style.css">
    <link rel="stylesheet" href="kumpulan-css/header.css">
    <link rel="stylesheet" href="kumpulan-css/sidebar.css">
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
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
                        <a href="dashboard_jamaah.php" class="<?= ($currentPage == 'dashboard_jamaah.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">dashboard</span>
                            <p>Dashboard</p>
                        </a>
                        <a href="pendaftaran_jamaah.php" class="<?= ($currentPage == 'pendaftaran_jamaah.php' || $currentPage == 'tambah_pendaftaran.php' || $currentPage == 'edit_pendaftaran.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">assignment_add</span>
                            <p>Pendaftaran Haji</p>
                        </a>
                        <a href="estimasi_jamaah.php" class="<?= ($currentPage == 'estimasi_jamaah.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">schedule</span>
                            <p>Estimasi Haji</p>
                        </a>
                        <a href="pembatalan.php" class="<?= ($currentPage == 'pembatalan_jamaah_ekonomi.php' || $currentPage == 'pembatalan_jamaah_meninggal.php' || $currentPage == 'tambah_pembatalan.php' || $currentPage == 'tambah_pembatalan_keperluan_ekonomi.php' || $currentPage == 'edit_pembatalan_keperluan_ekonomi.php' || $currentPage == 'tambah_pembatalan_meninggal_dunia.php' || $currentPage == 'edit_pembatalan_meninggal_dunia.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">cancel</span>
                            <p>Pembatalan Haji</p>
                        </a>
                        <a href="pelimpahan.php" class="<?= ($currentPage == 'pelimpahan_jamaah_sakit.php' || $currentPage == 'pelimpahan_jamaah_meninggal.php' || $currentPage == 'tambah_pelimpahan.php' || $currentPage == 'tambah_pelimpahan_sakit_permanen.php' || $currentPage == 'edit_pelimpahan_sakit_permanen.php' || $currentPage == 'tambah_pelimpahan_meninggal_dunia.php' || $currentPage == 'edit_pelimpahan_meninggal_dunia.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">swap_horiz</span>
                            <p>Pelimpahan Haji</p>
                        </a>
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