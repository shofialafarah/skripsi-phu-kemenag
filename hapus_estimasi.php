<?php
session_start();
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = "DELETE FROM estimasi WHERE id_estimasi = $id";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['success_message'] = "Data berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data.";
    }
} else {
    $_SESSION['error_message'] = "ID tidak ditemukan.";
}

header("Location: entry_estimasi.php"); // Ganti dengan nama file utama kamu
exit();
?>
