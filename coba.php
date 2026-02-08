<?php 
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
</style>
<nav class="sidebar">
    <div class="sidebar-header">
        <img src="settings/<?= htmlspecialchars($settings['app_logo']); ?>" width="50px" height="auto" align="left">
        <h2><?= htmlspecialchars($settings['app_name']); ?></h2>
    </div>
    <ul class="menu">
        <li><a href="?page=dashboard" class="<?= isset($_GET['page']) && $_GET['page'] === 'dashboard' ? 'active' : ''; ?>">Dashboard Utama</a></li>
        <li><a href="?page=manage_jamaah" class="<?= isset($_GET['page']) && $_GET['page'] === 'manage_jamaah' ? 'active' : ''; ?>">Manajemen Jamaah</a></li>
        <li><a href="?page=manage_staff" class="<?= isset($_GET['page']) && $_GET['page'] === 'manage_staff' ? 'active' : ''; ?>">Manajemen Staf</a></li>
        <li><a href="?page=manage_kasi" class="<?= isset($_GET['page']) && $_GET['page'] === 'manage_kasi' ? 'active' : ''; ?>">Manajemen Kepala Seksi</a></li>
        <li><a href="?page=settings" class="<?= isset($_GET['page']) && $_GET['page'] === 'settings' ? 'active' : ''; ?>">Pengaturan Sistem</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>