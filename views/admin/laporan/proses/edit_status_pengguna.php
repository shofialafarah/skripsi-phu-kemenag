<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

$role = $_POST['role'];
$id = $_POST['id_pengguna'];
$status = $_POST['status_pengguna'];

$table = '';
$id_column = '';

switch ($role) {
    case 'jamaah':
        $table = 'jamaah';
        $id_column = 'id_jamaah';
        break;
    case 'staf':
        $table = 'staf';
        $id_column = 'id_staf';
        break;
    case 'kepala_seksi':
        $table = 'kepala_seksi';
        $id_column = 'id_kepala';
        break;
}

if ($table) {
    $stmt = $koneksi->prepare("UPDATE $table SET status_pengguna = ?, updated_at = NOW() WHERE $id_column = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    echo "sukses";
} else {
    echo "gagal";
}
?>
