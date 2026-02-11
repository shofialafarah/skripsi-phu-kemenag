<?php
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
                        <a href="/phu-kemenag-banjar-copy/views/jamaah/dashboard_jamaah.php" class="<?= ($currentPage == '/phu-kemenag-banjar-copy/views/jamaah/dashboard_jamaah.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">dashboard</span>
                            <p>Dashboard</p>
                        </a>
                        <!-- Menu Pendaftaran Haji -->
                        <a href="/phu-kemenag-banjar-copy/views/jamaah/pendaftaran/pendaftaran_jamaah.php" class="<?= ($currentPage == '/phu-kemenag-banjar-copy/views/jamaah/pendaftaran/pendaftaran_jamaah.php' || $currentPage == '/phu-kemenag-banjar-copy/views/jamaah/pendaftaran/tambah_pendaftaran.php' || $currentPage == '/phu-kemenag-banjar-copy/views/jamaah/pendaftaran/edit_pendaftaran.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">assignment_add</span>
                            <p>Pendaftaran Haji</p>
                        </a>
                        <!-- Menu Cek Estimasi Keberangkatan Haji -->
                        <a href="/phu-kemenag-banjar-copy/views/jamaah/estimasi/estimasi_jamaah.php" class="<?= ($currentPage == '/phu-kemenag-banjar-copy/views/jamaah/estimasi/estimasi_jamaah.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">schedule</span>
                            <p>Estimasi Haji</p>
                        </a>
                        <!-- Menu Pembatalan Haji -->
                        <a href="/phu-kemenag-banjar-copy/views/jamaah/pembatalan/pembatalan.php" class="<?= ($currentPage == 'pembatalan_jamaah_ekonomi.php' || $currentPage == 'pembatalan_jamaah_meninggal.php' || $currentPage == 'tambah_pembatalan.php' || $currentPage == 'tambah_pembatalan_keperluan_ekonomi.php' || $currentPage == 'edit_pembatalan_keperluan_ekonomi.php' || $currentPage == 'tambah_pembatalan_meninggal_dunia.php' || $currentPage == 'edit_pembatalan_meninggal_dunia.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">cancel</span>
                            <p>Pembatalan Haji</p>
                        </a>
                        <!-- Menu Pelimpahan Haji -->
                        <a href="/phu-kemenag-banjar-copy/views/jamaah/pelimpahan/pelimpahan.php" class="<?= ($currentPage == 'pelimpahan_jamaah_sakit.php' || $currentPage == 'pelimpahan_jamaah_meninggal.php' || $currentPage == 'tambah_pelimpahan.php' || $currentPage == 'tambah_pelimpahan_sakit_permanen.php' || $currentPage == 'edit_pelimpahan_sakit_permanen.php' || $currentPage == 'tambah_pelimpahan_meninggal_dunia.php' || $currentPage == 'edit_pelimpahan_meninggal_dunia.php') ? 'active' : '' ?>">
                            <span class="material-symbols-outlined">swap_horiz</span>
                            <p>Pelimpahan Haji</p>
                        </a>
                    </nav>
                    <button id="logoutBtn" class="logout-btn">
                        <span class="material-symbols-outlined">logout</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
    <!-- Scripts -->
    <script src="sidebar.js"></script>
</body>

</html>