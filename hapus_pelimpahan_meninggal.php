<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_limpah_meninggal = $_GET['id'];
    
    // Query untuk menghapus data pembatalan
    $query = "DELETE FROM pelimpahan_meninggal WHERE id_limpah_meninggal = '$id_limpah_meninggal'";

    if (mysqli_query($koneksi, $query)) {
        echo "Data berhasil dihapus.";
        header("Location: pelimpahan_haji.php"); // Redirect kembali ke daftar pelimpahan haji yang meninggal
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

?>
