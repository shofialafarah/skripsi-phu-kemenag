<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../includes/koneksi.php';

$id_pendaftaran = isset($_GET['id_pendaftaran']) ? $_GET['id_pendaftaran'] : (isset($_POST['id_pendaftaran']) ? $_POST['id_pendaftaran'] : '');

if (!empty($id_pendaftaran)) {
    $id_pendaftaran = mysqli_real_escape_string($koneksi, $id_pendaftaran);
    
    // Ambil informasi file sebelum menghapus
    $query = "SELECT upload_doc FROM pendaftaran WHERE id_pendaftaran = '$id_pendaftaran'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $file_path = $row['upload_doc'];
        
        // Hapus file jika ada
        $full_path = __DIR__ . "/../../../../" . $file_path;

    if (!empty($file_path) && file_exists($full_path)) {
        unlink($full_path); // Ini yang akan menghapus file di komputer
    }
        
        // Update database untuk menghapus referensi file
        $update_query = "UPDATE pendaftaran SET upload_doc = NULL, keterangan = NULL WHERE id_pendaftaran = '$id_pendaftaran'";
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
    $message = "ID Pendaftaran tidak valid.";
    $_SESSION['error_message'] = $message;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

header("Location: ../entry_pendaftaran.php");
exit;
?>