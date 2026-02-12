<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

// Hapus staf berdasarkan ID
if (isset($_GET['id'])) {
    $id_staf = $_GET['id'];

    $stmt = $koneksi->prepare("DELETE FROM staf WHERE id_staf = ?");
    $stmt->bind_param('s', $id_staf);
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: manajemen_staf.php?deleted=1');
        exit();
    } else {
        $stmt->close();
        header('Location: manajemen_staf.php?deleted=0');
        exit();
    }
} else {
    header('Location: manajemen_staf.php?deleted=0');
    exit();
}
?>
