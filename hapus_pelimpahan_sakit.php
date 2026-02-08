<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_limpah_sakit = $_GET['id'];
    
    // Query untuk menghapus data pembatalan
    $query = "DELETE FROM pelimpahan_sakit WHERE id_limpah_sakit = '$id_limpah_sakit'";

    if (mysqli_query($koneksi, $query)) {
        echo "Data berhasil dihapus.";
        header("Location: pelimpahan_haji.php"); // Redirect kembali ke daftar pembatalan haji yang meninggal
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

?>
