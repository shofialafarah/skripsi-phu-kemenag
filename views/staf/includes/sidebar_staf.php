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

$default_system_logo = '/phu-kemenag-banjar-copy/assets/img/sistem.png';

if (empty($app_logo)) {
    $app_logo_src = $default_system_logo;
} elseif (filter_var($app_logo, FILTER_VALIDATE_URL)) {
    $app_logo_src = $app_logo;
} elseif (strpos($app_logo, '/') === 0) {
    $app_logo_src = $app_logo;
} else {
    $candidate = '/phu-kemenag-banjar-copy/assets/img/' . $app_logo;
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $candidate)) {
        $app_logo_src = $candidate;
    } else {
        $app_logo_src = $default_system_logo;
    }
}

$monitoringPages = ['monitoring_pendaftaran.php', 'monitoring_pembatalan.php', 'monitoring_pelimpahan.php'];
$entryPages = ['entry_pendaftaran.php', 'entry_pembatalan.php', 'edit_pembatalan_meninggal.php', 'cetak_pembatalan_meninggal.php', 'edit_pembatalan_ekonomi.php', 'cetak_pembatalan_ekonomi.php', 'edit_pelimpahan.php', 'cetak_pelimpahan.php', 'entry_estimasi.php', 'entry_pelimpahan.php', 'edit_pelimpahan_meninggal.php', 'cetak_pelimpahan_meninggal.php', 'edit_pelimpahan_sakit.php', 'cetak_pelimpahan_sakit.php'];
?>

<style>
    :root {
        --sidebar-text-color: <?= htmlspecialchars($theme_text_color); ?>;
    }

    .sidebar .menu a p,
    .sidebar .menu a .material-symbols-outlined,
    .sidebar .dropdown-toggle p,
    .sidebar .dropdown-toggle .material-symbols-outlined,
    .sidebar .submenu a {
        color: var(--sidebar-text-color) !important;
    }

    .sidebar .header h1 {
        color: white !important;
    }

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

    .sidebar a.active,
    .sidebar a.active p,
    .sidebar a.active .material-symbols-outlined,
    .sidebar .submenu a.active {
        color: white !important;
    }

    .sidebar a.active {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
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
                    <a href="/phu-kemenag-banjar-copy/views/staf/dashboard_staf.php" class="<?= ($currentPage == 'dashboard_staf.php') ? 'active' : '' ?>">
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
                        <a href="/phu-kemenag-banjar-copy/views/staf/monitoring/monitoring_pendaftaran.php" class="<?= ($currentPage == 'monitoring_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                        <a href="/phu-kemenag-banjar-copy/views/staf/monitoring/monitoring_pembatalan.php" class="<?= ($currentPage == 'monitoring_pembatalan.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                        <a href="/phu-kemenag-banjar-copy/views/staf/monitoring/monitoring_pelimpahan.php" class="<?= ($currentPage == 'monitoring_pelimpahan.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
                    </div>
                    <!-- Entry -->
                    <a class="dropdown-toggle <?= in_array($currentPage, $entryPages) ? 'active' : '' ?>" onclick="toggleDropdown('entrySubmenu', this)">
                        <span class="material-symbols-outlined">analytics</span>
                        <p>Entry</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $entryPages) ? 'open' : '' ?>" id="entrySubmenu">
                        <a href="/phu-kemenag-banjar-copy/views/staf/entry/entry_pendaftaran.php" class="<?= ($currentPage == 'entry_pendaftaran.php') ? 'active' : '' ?>">Pendaftaran Haji</a>
                        <a href="/phu-kemenag-banjar-copy/views/staf/entry/entry_estimasi.php" class="<?= ($currentPage == 'entry_estimasi.php') ? 'active' : '' ?>">Estimasi Haji</a>
                        <a href="/phu-kemenag-banjar-copy/views/staf/entry/entry_pembatalan.php" class="<?= ($currentPage == 'entry_pembatalan.php' || $currentPage == 'edit_pembatalan_meninggal.php' || $currentPage == 'cetak_pembatalan_meninggal.php' || $currentPage == 'edit_pembatalan_ekonomi.php' || $currentPage == 'cetak_pembatalan_ekonomi.php') ? 'active' : '' ?>">Pembatalan Haji</a>
                        <a href="/phu-kemenag-banjar-copy/views/staf/entry/entry_pelimpahan.php" class="<?= ($currentPage == 'entry_pelimpahan.php' || $currentPage == 'edit_pelimpahan_meninggal.php' || $currentPage == 'cetak_pelimpahan.php' || $currentPage == 'edit_pelimpahan_sakit.php') ? 'active' : '' ?>">Pelimpahan Haji</a>
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
                <a href="<?= BASE_URL ?>views/auth/logout.php" id="tombol-logout" class="logout-btn">
                    <span class="material-symbols-outlined">logout</span>
                </a>
            </div>
        </div>
    </aside>
</div>


<!-- Scripts -->
<script src="../assets/js/sidebar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('tombol-logout').addEventListener('click', function(e) {
        e.preventDefault(); // Mencegah link pindah halaman langsung
        const href = this.getAttribute('href');

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda akan keluar dari aplikasi ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4caf50',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user klik Ya, arahkan ke logout.php
                window.location.href = href;
            }
        })
    });
</script>
</body>

</html>