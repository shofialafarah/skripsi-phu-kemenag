<?php
session_start();
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

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
<?php include '../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../includes/sidebar_kasi.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../includes/header_kasi.php'; ?>
        <link rel="stylesheet" href="assets/css/laporan.css">
        <main class="laporan-wrapper">
            <div class="laporan">
                <div class="laporan-header">
                    <i class="fas fa-table me-1"></i> Laporan Rekapitulasi Jamaah Haji
                </div>
                <div class="laporan-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <!-- Tombol Cetak -->
                        <div class="ms-auto">
                            <button id="cetak-laporan-btn" class="btn btn-info btn-sm">
                                <i class="fas fa-print me-1"></i> Cetak Laporan
                            </button>
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table id="tabelRekapitulasi" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class='text-center'>No</th>
                                    <th class='text-center'>Kecamatan</th>
                                    <th class='text-center'>Jenis Pelayanan</th>
                                    <th class='text-center'>Jumlah Jamaah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data_rekap as $row) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>{$no}</td>";
                                    echo "<td class='text-center'>{$row['kecamatan']}</td>";
                                    echo "<td class='text-center'>{$row['jenis_pelayanan']}</td>";
                                    echo "<td class='text-center'>{$row['jumlah']} Jamaah</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php include_once __DIR__ . '/../includes/footer_kasi.php'; ?>
                </div>
        </main>
    </div>
</div>
<script src="../assets/js/sidebar.js"></script>
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

<script src="assets/js/cetakRekapitulasi.js"></script>
</body>

</html>