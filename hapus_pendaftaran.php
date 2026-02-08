<?php
include 'koneksi.php';

// Menggunakan prepared statement untuk menghindari SQL Injection
if (isset($_GET['id'])) {
    $id_pendaftaran = $_GET['id'];

    $query = "DELETE FROM pendaftaran WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    // Binding parameter
    mysqli_stmt_bind_param($stmt, "i", $id_pendaftaran);

    // Eksekusi query
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header('Location: pendaftaran_haji.php'); // Redirect setelah berhasil dihapus
        exit;
    } else {
        echo '<div class="alert alert-danger">Data gagal dihapus: ' . mysqli_error($koneksi) . '</div>';
    }

    mysqli_stmt_close($stmt);
} else {
    echo '<div class="alert alert-danger">ID Pendaftaran tidak ada.</div>';
}
