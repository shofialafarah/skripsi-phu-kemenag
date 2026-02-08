<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php");
    exit();
}

// Mendukung baik metode GET (dari link JavaScript) maupun POST (dari AJAX)
$id_pembatalan = isset($_GET['id_pembatalan']) ? $_GET['id_pembatalan'] : (isset($_POST['id_pembatalan']) ? $_POST['id_pembatalan'] : '');

if (!empty($id_pembatalan)) {
    $id_pembatalan = mysqli_real_escape_string($koneksi, $id_pembatalan);
    
    // Ambil informasi file sebelum menghapus
    $query = "SELECT upload_doc FROM pembatalan WHERE id_pembatalan = '$id_pembatalan'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $file_path = $row['upload_doc'];
        
        // Hapus file jika ada
        if (!empty($file_path) && file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Update database untuk menghapus referensi file
        $update_query = "UPDATE pembatalan SET upload_doc = NULL, keterangan = NULL WHERE id_pembatalan = '$id_pembatalan'";
        if (mysqli_query($koneksi, $update_query)) {
            $status = "success";
            $message = "Dokumen berhasil dihapus.";
            $_SESSION['success_message'] = $message;
        } else {
            $status = "error";
            $message = "Gagal menghapus referensi dokumen dari database: " . mysqli_error($koneksi);
            $_SESSION['error_message'] = $message;
        }
    } else {
        $status = "error";
        $message = "Dokumen tidak ditemukan.";
        $_SESSION['error_message'] = $message;
    }
} else {
    $status = "error";
    $message = "ID Pembatalan tidak valid.";
    $_SESSION['error_message'] = $message;
}

// Jika request AJAX, kembalikan respons JSON
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Jika bukan AJAX, redirect kembali ke halaman utama
header("Location: entry_pembatalan.php");
exit;
?>