<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pelimpahan'])) {
    $id = intval($_POST['id_pelimpahan']);

    // Ambil path file dulu
    $query = "SELECT upload_doc FROM pelimpahan WHERE id_pelimpahan = $id";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row && file_exists($row['upload_doc'])) {
        unlink($row['upload_doc']); // Hapus file dari server
    }

    $update = "UPDATE pelimpahan SET upload_doc = NULL WHERE id_pelimpahan = $id";
    if (mysqli_query($koneksi, $update)) {
        $_SESSION['success_message'] = "Dokumen berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus dokumen.";
    }
}

header("Location: entry_pelimpahan.php");
exit;
