<?php
session_start();
include '../../../includes/koneksi.php';
include '../../partials/fungsi.php';

if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];
// ✅ Catat aktivitas hanya jika data berhasil ditemukan
updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pembatalan', 'Membuka halaman pilihan jenis pembatalan haji');

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
                        <i class="fas fa-table me-1"></i> Pilih Pengajuan Pembatalan Haji
                    </div>
                    <div class="pPembatalan-body" style="color: #1b5e20;">

                        <div class="action-buttons">
                            <a href="includes/pembatalan-meninggal/tambah_pembatalan_meninggal_dunia.php" class="btn btn-danger btn-lg">
                                <i class="fas fa-plus"></i> Pembatalan Meninggal Dunia
                            </a>

                            <a href="includes/pembatalan-ekonomi/tambah_pembatalan_keperluan_ekonomi.php" class="btn btn-warning btn-lg">
                                <i class="fas fa-plus"></i> Pembatalan Keperluan Ekonomi
                            </a>
                        </div>
                    </div>
                    <?php include_once __DIR__ . '/../includes/footer_jamaah.php'; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/sidebar.js"></script>
    <script src="pendaftaran_jamaah.js"></script>
</body>

</html>