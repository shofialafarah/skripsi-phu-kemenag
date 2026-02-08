<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Hapus staf berdasarkan ID
if (isset($_GET['id'])) {
    $id_jamaah = $_GET['id'];
    $sql = "DELETE FROM jamaah WHERE id_jamaah = '$id_jamaah'";

    if ($koneksi->query($sql)) {
        echo "<p>Data staf berhasil dihapus!</p>";
    } else {
        echo "<p>Error: " . $koneksi->error . "</p>";
    }
} else {
    echo "ID jamaah tidak ditemukan.";
    exit();
}
echo "<script> window.location='manajemen_jamaah.php';</script>";
?>
