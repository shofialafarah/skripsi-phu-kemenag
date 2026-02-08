<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_batal_ekonomi = $_GET['id'];
    
    // Query untuk menghapus data pembatalan
    $query = "DELETE FROM pembatalan_ekonomi WHERE id_batal_ekonomi = '$id_batal_ekonomi'";

    if (mysqli_query($koneksi, $query)) {
        echo "Data berhasil dihapus.";
        header("Location: pembatalan_haji.php"); // Redirect kembali ke daftar pembatalan haji
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

include 'footer.php';
?>
