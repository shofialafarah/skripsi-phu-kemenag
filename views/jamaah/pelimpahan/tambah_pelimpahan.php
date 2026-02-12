<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include '../../../includes/koneksi.php';
include '../../partials/fungsi.php';

if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];
updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pelimpahan', 'Membuka halaman pilihan jenis pelimpahan haji');

?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once '../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once '../includes/header_jamaah.php'; ?>

            <main class="pPembatalan-wrapper">
                <div class="pPembatalan">
                    <div class="pPembatalan-header" style="background-color: #1b5e20; color: white;">
                        <i class="fas fa-table me-1"></i> Pilih Pengajuan Pelimpahan Haji
                    </div>
                    <div class="pPembatalan-body" style="color: #1b5e20;">

                        <div class="action-buttons">
                            <a href="includes/pelimpahan-meninggal/tambah_pelimpahan_meninggal_dunia.php" class="btn btn-danger btn-lg">
                                <i class="fas fa-plus"></i> Pelimpahan Meninggal Dunia
                            </a>

                            <a href="includes/pelimpahan-sakit/tambah_pelimpahan_sakit_permanen.php" class="btn btn-warning btn-lg">
                                <i class="fas fa-plus"></i> Pelimpahan Sakit Permanen
                            </a>
                        </div>
                    </div>
                    <?php include_once __DIR__ . '/../includes/footer_jamaah.php'; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/sidebar.js"></script>
</body>

</html>