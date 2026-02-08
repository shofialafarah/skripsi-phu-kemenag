<?php
// asumsi koneksi sudah di-include di halaman utama, kalau belum, bisa tetap include:
// include 'koneksi.php';

$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Ambil pengaturan dari database (jika belum diambil di halaman utama)
$result = $koneksi->query("SELECT * FROM pengaturan");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

$app_name = $settings['app_name'] ?? 'PHU KEMENAG';
$app_logo = $settings['app_logo'] ?? 'logo_kemenag.png';
$theme_text_color = $settings['theme_text_color'] ?? '#ffffff';

$penggunaPages = [
    'manajemen_jamaah.php',
    'tambah_jamaah.php',
    'edit_jamaah.php',
    'manajemen_staf.php',
    'tambah_staf.php',
    'edit_staf.php',
    'manajemen_kasi.php',
    'tambah_kasi.php',
    'edit_kasi.php'
];

$sistemPage = ['pengaturan.php', 'reset_password.php'];
$cetakPage = ['laporan_data_pengguna.php', 'laporan_aktivitas_pengguna.php'];
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
    .sidebar .header h1 { color: white !important; }
    .sidebar a:hover,
    .sidebar .dropdown-toggle:hover p,
    .sidebar .submenu a:hover { color: white !important; }
    .sidebar a.active,
    .sidebar a.active p,
    .sidebar a.active .material-symbols-outlined,
    .sidebar .submenu a.active { color: white !important; }
    .sidebar a.active { background-color: rgba(255, 255, 255, 0.1); }
    .sidebar .header .logo { width: 30px; height: 30px; object-fit: contain; }
</style>

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
                    <a href="dashboard_administrator.php" class="<?= ($currentPage == 'dashboard_administrator.php') ? 'active' : '' ?>">
                        <span class="material-symbols-outlined">dashboard</span>
                        <p>Dashboard</p>
                    </a>

                    <a class="dropdown-toggle <?= in_array($currentPage, $penggunaPages) ? 'active' : '' ?>" onclick="toggleDropdown('penggunaSubmenu', this)">
                        <span class="material-symbols-outlined">person</span>
                        <p>Pengguna</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $penggunaPages) ? 'open' : '' ?>" id="penggunaSubmenu">
                        <a href="manajemen_jamaah.php" class="<?= in_array($currentPage, ['manajemen_jamaah.php','tambah_jamaah.php','edit_jamaah.php']) ? 'active' : '' ?>">Jamaah</a>
                        <a href="manajemen_staf.php" class="<?= in_array($currentPage, ['manajemen_staf.php','tambah_staf.php','edit_staf.php']) ? 'active' : '' ?>">Staf PHU</a>
                        <a href="manajemen_kasi.php" class="<?= in_array($currentPage, ['manajemen_kasi.php','tambah_kasi.php','edit_kasi.php']) ? 'active' : '' ?>">Kepala Seksi</a>
                    </div>

                    <a class="dropdown-toggle <?= in_array($currentPage, $sistemPage) ? 'active' : '' ?>" onclick="toggleDropdown('sistemSubmenu', this)">
                        <span class="material-symbols-outlined">settings</span>
                        <p>Sistem</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $sistemPage) ? 'open' : '' ?>" id="sistemSubmenu">
                        <a href="pengaturan.php" class="<?= ($currentPage == 'pengaturan.php') ? 'active' : '' ?>">Pengaturan</a>
                        <a href="reset_password.php" class="<?= ($currentPage == 'reset_password.php') ? 'active' : '' ?>">Reset Password</a>
                    </div>

                    <a class="dropdown-toggle <?= in_array($currentPage, $cetakPage) ? 'active' : '' ?>" onclick="toggleDropdown('cetakSubmenu', this)">
                        <span class="material-symbols-outlined">print</span>
                        <p>Laporan</p>
                        <span class="material-symbols-outlined arrow">expand_more</span>
                    </a>
                    <div class="submenu <?= in_array($currentPage, $cetakPage) ? 'open' : '' ?>" id="cetakSubmenu">
                        <a href="laporan_data_pengguna.php" class="<?= ($currentPage == 'laporan_data_pengguna.php') ? 'active' : '' ?>">Data Pengguna</a>
                        <a href="laporan_aktivitas_pengguna.php" class="<?= ($currentPage == 'laporan_aktivitas_pengguna.php') ? 'active' : '' ?>">Riwayat Aksi</a>
                    </div>
                </nav>
                <button class="logout-btn" onclick="window.location.href='login.php'">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </div>
        </div>
    </aside>
</div>
