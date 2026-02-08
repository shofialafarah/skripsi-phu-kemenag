<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    echo "Akses ditolak. Hanya administrator yang dapat mengakses halaman ini.";
    exit();
}

$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$limit_date = date('Y-m-d H:i:s', strtotime('-7 days'));

// Jamaah
$koneksi->query("UPDATE jamaah SET status_pengguna = 'nonaktif' WHERE last_login_at < '$limit_date'");

// Staf
$koneksi->query("UPDATE staf SET status_pengguna = 'nonaktif' WHERE last_login_at < '$limit_date'");

// Kepala seksi
$koneksi->query("UPDATE kepala_seksi SET status_pengguna = 'nonaktif' WHERE last_login_at < '$limit_date'");

echo "Pengguna yang tidak aktif selama 7 hari telah di-nonaktifkan.";
?>
