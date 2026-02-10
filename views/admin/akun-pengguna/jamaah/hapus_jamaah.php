<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Hapus jamaah berdasarkan ID
if (isset($_GET['id'])) {
    $id_jamaah = $_GET['id'];
    $sql = "DELETE FROM jamaah WHERE id_jamaah = '$id_jamaah'";

    if ($koneksi->query($sql)) {
        header('Location: manajemen_jamaah.php?deleted=1');
        exit();
    } else {
        header('Location: manajemen_jamaah.php?deleted=0');
        exit();
    }
} else {
    header('Location: manajemen_jamaah.php?deleted=0');
    exit();
}
?>
