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

if (isset($_GET['id_estimasi']) && is_numeric($_GET['id_estimasi'])) {
    $id_estimasi = intval($_GET['id_estimasi']);

    $query = "DELETE FROM estimasi WHERE id_estimasi = $id_estimasi";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $_SESSION['success_message'] = "Data berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus data.";
    }
} else {
    $_SESSION['error_message'] = "ID tidak ditemukan.";
}

header("Location: ../entry_estimasi.php");
exit();
?>
