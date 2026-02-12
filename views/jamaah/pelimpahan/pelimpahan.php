<?php 
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../../includes/koneksi.php';

// Cek apakah jamaah sudah login
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit;
}

$id_jamaah = $_SESSION['id_jamaah'];

// Cek apakah jamaah ini sudah melakukan pelimpahan
$query = "SELECT kategori FROM pelimpahan WHERE id_jamaah = ? ORDER BY tanggal_pengajuan DESC LIMIT 1";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $_SESSION['id_jamaah']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Sudah pernah membatalkan
    $data = $result->fetch_assoc();
    $kategori = $data['kategori'];// misalnya kolomnya bernama 'kategori'

    if ($kategori == 'Meninggal Dunia') {
        header("Location: includes/pelimpahan_jamaah_meninggal.php");
    } elseif ($kategori == 'Sakit Permanen') {
        header("Location: includes/pelimpahan_jamaah_sakit.php");
    }
    exit;
} else {
    // Belum pernah membatalkan
    header("Location: tambah_pelimpahan.php");
    exit;
}
?>