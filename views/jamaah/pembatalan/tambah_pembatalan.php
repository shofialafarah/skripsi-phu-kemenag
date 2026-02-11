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
    <script src="../../assets/js/sidebar.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Pastikan file JS kustom dimuat setelah semua library -->
    <script src="pendaftaran_jamaah.js"></script>
</body>

</html>