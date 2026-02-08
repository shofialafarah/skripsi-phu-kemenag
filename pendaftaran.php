<?php 
session_start();
include 'koneksi.php';

// Cek apakah jamaah sudah login
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit;
}

$id_jamaah = $_SESSION['id_jamaah'];

// Cek apakah jamaah ini sudah mengisi pendaftaran
$query = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE id_jamaah = '$id_jamaah'");

if (mysqli_num_rows($query) > 0) {
    // Sudah pernah daftar → arahkan ke halaman lihat data
    header("Location: pendaftaran_jamaah.php");
    exit;
} else {
    // Belum daftar → arahkan ke halaman tambah data
    header("Location: tambah_pendaftaran.php");
    exit;
}
?>
