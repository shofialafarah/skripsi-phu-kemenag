<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Hapus staf berdasarkan ID
if (isset($_GET['id'])) {
    $id_staf = $_GET['id'];
    $sql = "DELETE FROM staf WHERE id_staf = '$id_staf'";

    if ($koneksi->query($sql)) {
        echo "<p>Data staf berhasil dihapus!</p>";
    } else {
        echo "<p>Error: " . $koneksi->error . "</p>";
    }
} else {
    echo "ID staf tidak ditemukan.";
    exit();
}
echo "<script> window.location='manajemen_staf.php';</script>";
?>
