<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Hapus data berdasarkan ID
$id_kepala = $_GET['id'];
$query = "DELETE FROM kepala_seksi WHERE id_kepala = $id_kepala";

if ($koneksi->query($query)) {
    echo "<script> window.location='manajemen_kasi.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data!');</script>";
}
?>
