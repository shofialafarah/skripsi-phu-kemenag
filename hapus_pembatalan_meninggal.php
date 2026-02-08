<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_batal_meninggal = $_GET['id'];
    
    // Query untuk menghapus data pembatalan
    $query = "DELETE FROM pembatalan_meninggal WHERE id_batal_meninggal = '$id_batal_meninggal'";

    if (mysqli_query($koneksi, $query)) {
        echo "Data berhasil dihapus.";
        header("Location: pembatalan_haji.php"); // Redirect kembali ke daftar pembatalan haji yang meninggal
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

include 'footer.php';
?>
