<?php
session_start();
include 'koneksi.php';
include 'fungsi.php';

if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];
// ✅ Catat aktivitas hanya jika data berhasil ditemukan
updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pelimpahan', 'Membuka halaman pilihan jenis pelimpahan haji');

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Jamaah</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="global_style.css" />
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_jamaah.php'; ?>
        </div>

        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_jamaah.php'; ?>

            <main class="pPembatalan-wrapper">
                <div class="pPembatalan">
                    <div class="pPembatalan-header" style="background-color: #1b5e20; color: white;">
                        <i class="fas fa-table me-1"></i> Pilih Pengajuan Pelimpahan Haji
                    </div>
                    <div class="pPembatalan-body" style="color: #1b5e20;">

                        <div class="action-buttons">
                            <a href="tambah_pelimpahan_meninggal_dunia.php" class="btn btn-danger btn-lg">
                                <i class="fas fa-plus"></i> Pelimpahan Meninggal Dunia
                            </a>

                            <a href="tambah_pelimpahan_sakit_permanen.php" class="btn btn-warning btn-lg">
                                <i class="fas fa-plus"></i> Pelimpahan Sakit Permanen
                            </a>
                        </div>
                    </div>
                    <div class="footer" style="color: white; text-align: center;">
                        <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
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
</body>

</html>