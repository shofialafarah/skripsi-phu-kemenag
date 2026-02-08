<?php
include 'koneksi.php';
include 'header.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query untuk menghapus data pembatalan
    $query = "DELETE FROM pembatalan_haji WHERE id = '$id'";

    if (mysqli_query($koneksi, $query)) {
        echo "Data berhasil dihapus.";
        header("Location: pembatalan_haji.php"); // Redirect kembali ke daftar pembatalan haji
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

include 'footer.php';
?>
