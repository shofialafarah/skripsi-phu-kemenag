<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login sebagai kepala seksi
if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php");
    exit();
}

// Ambil ID kepala seksi
$id_kepala = $_SESSION['id_kepala'];

// Ambil data rekapitulasi berdasarkan kecamatan & jenis pelayanan
$query = "
    SELECT kecamatan, jenis_pelayanan, COUNT(*) as jumlah
    FROM (
        SELECT p.kecamatan, 'Pendaftaran' AS jenis_pelayanan FROM pendaftaran p
        UNION ALL
        SELECT pe.kecamatan, 'Pembatalan Ekonomi' FROM pembatalan_ekonomi pe
        UNION ALL
        SELECT pm.kecamatan, 'Pembatalan Meninggal' FROM pembatalan_meninggal pm
        UNION ALL
        SELECT ps.kecamatan_jamaah AS kecamatan, 'Pelimpahan Sakit' FROM pelimpahan_sakit ps
        UNION ALL
        SELECT pmg.kecamatan_jamaah AS kecamatan, 'Pelimpahan Meninggal' FROM pelimpahan_meninggal pmg
    ) AS gabungan
    GROUP BY kecamatan, jenis_pelayanan
    ORDER BY kecamatan, jenis_pelayanan
";

$result = $koneksi->query($query);
$data_rekap = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_rekap[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Kepala Seksi</title>
    <link rel="icon" href="logo_kemenag.png">
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_kasi.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_kasi.php'; ?>

            <main class="pPendaftaran-wrapper">
                <div class="pPendaftaran">
                    <div class="pPendaftaran-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Laporan Rekapitulasi Jamaah Haji
                    </div>
                    <div class="pPendaftaran-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <!-- Tombol Cetak -->
                            <div class="ms-auto">
                                <button id="cetak-laporan-btn" class="btn btn-info btn-sm">
                                    <i class="fas fa-print me-1"></i> Cetak Laporan
                                </button>
                            </div>

                        </div>
                        <!-- Tabel untuk tampilan normal -->
                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kecamatan</th>
                                    <th>Jenis Pelayanan</th>
                                    <th>Jumlah Jamaah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data_rekap as $row) {
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>{$row['kecamatan']}</td>";
                                    echo "<td>{$row['jenis_pelayanan']}</td>";
                                    echo "<td>{$row['jumlah']}</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>

    <!-- PDF Generation Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script src="tanggal_cetak_rekapitulasi.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cetakLaporanBtn = document.getElementById('cetak-laporan-btn');

            if (cetakLaporanBtn) {
                cetakLaporanBtn.addEventListener('click', function() {
                    // Langsung buka halaman cetak
                    window.open('cetak_laporan_rekapitulasi.php', '_blank');
                });
            }
        });
    </script>
</body>

</html>