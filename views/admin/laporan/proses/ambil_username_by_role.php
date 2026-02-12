<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

if (!isset($_POST['role']) || empty($_POST['role'])) {
    echo '<option value="" disabled selected>-- Pilih Username --</option>';
    exit();
}

$role = strtolower(trim($_POST['role']));
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
    default:
        echo '<option value="" disabled selected>-- Pilih Username --</option>';
        exit();
}

if ($table) {
    $query = "SELECT $id_column AS id, username FROM $table WHERE status_pengguna = 'aktif' ORDER BY username ASC";
    $result = $koneksi->query($query);

    echo '<option value="" disabled selected>-- Pilih Username --</option>';

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = htmlspecialchars($row['id']);
            $username = htmlspecialchars($row['username']);
            echo "<option value='$id'>$username</option>";
        }
    } else {
        echo '<option value="" disabled>Tidak ada pengguna aktif</option>';
    }
}
?>
