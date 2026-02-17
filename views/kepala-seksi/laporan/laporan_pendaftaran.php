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

$query = "SELECT p.*, j.email 
          FROM pendaftaran p 
          JOIN jamaah j ON p.id_jamaah = j.id_jamaah
          ORDER BY p.tanggal_verifikasi DESC;";
$result = $koneksi->query($query);
$data_pendaftaran = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pendaftaran[] = $row;
    }
}

function clean_input($data)
{
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

?>
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
                    <i class="fas fa-table me-1"></i> Laporan Pendaftaran Jamaah Haji
                </div>
                <div class="laporan-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div id="tanggal-filter" class="d-flex gap-2 flex-wrap align-items-end">
                            <div>
                                <label for="filter-start" class="form-label">Tanggal Mulai</label>
                                <input type="date" id="filter-start" class="form-control form-control-sm border border-secondary">
                            </div>
                            <div>
                                <label for="filter-end" class="form-label">Tanggal Akhir</label>
                                <input type="date" id="filter-end" class="form-control form-control-sm border border-secondary">
                            </div>
                            <div class="d-flex gap-2 align-items-end">
                                <button id="filter-btn" class="btn btn-sm btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <button id="reset-btn" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ms-auto"> <button id="print-report-btn" class="btn btn-info btn-sm">
                                <i class="fas fa-print me-1"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="tabelPendaftaran" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No. Porsi</th>
                                    <th class="text-center">Nama Jamaah</th>
                                    <th class="text-center">Bin/Binti</th>
                                    <th class="text-center">No. Telepon</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Alamat</th>
                                    <th class="text-center">Tanggal Verifikasi</th>
                                    <th class="text-center">Status Pendaftaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (!empty($data_pendaftaran)) {
                                    foreach ($data_pendaftaran as $row) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $no++ . "</td>";
                                        $nomor_porsi = htmlspecialchars($row['nomor_porsi']);
                                        if (!empty($nomor_porsi)) {
                                            echo "<td class='text-center'><span class='badge bg-primary'>$nomor_porsi</span></td>";
                                        } else {
                                            echo "<td class='text-center'><span class='badge bg-danger'>❌ Belum ada</span></td>";
                                        }

                                        echo "<td class='text-center'>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                                        echo "<td class='text-center'>" . htmlspecialchars($row['nama_ayah']) . "</td>";
                                        $no_wa = preg_replace('/[^0-9]/', '', $row['no_telepon']);
                                        $link_wa = "https://wa.me/" . $no_wa;
                                        echo "<td class='text-center'>
                                                <a href='$link_wa' target='_blank' class='text-decoration-none'>
                                                    <span class='badge bg-success'><i class='fas fa-phone-alt me-1'></i>" . htmlspecialchars($row['no_telepon']) . "</span>
                                                </a>
                                            </td>";


                                        echo "<td class='text-center'>
                                                <a href='mailto:" . htmlspecialchars($row['email']) . "' class='text-primary text-decoration-underline'>
                                                    " . htmlspecialchars($row['email']) . "
                                                </a>
                                            </td>";

                                        echo "<td class='text-center'>" . htmlspecialchars($row['alamat']) . "</td>";
                                        $tanggalVerifikasi = $row['tanggal_verifikasi'];

                                        echo "<td style='text-align: center;'>";
                                        if (!empty($tanggalVerifikasi)) {
                                            echo date('d-m-Y', strtotime($tanggalVerifikasi));
                                        } else {
                                            echo "-";
                                        }
                                        echo "</td>";
                                        // Status Verifikasi dengan badge warna
                                        $status_raw = strtolower($row['status_verifikasi']);
                                        switch ($status_raw) {
                                            case 'pending':
                                                $status_tampil = "<span class='badge bg-warning text-dark'>Pending</span>";
                                                break;
                                            case 'disetujui':
                                                $status_tampil = "<span class='badge bg-success'>Disetujui</span>";
                                                break;
                                            case 'ditolak':
                                                $status_tampil = "<span class='badge bg-danger'>Ditolak</span>";
                                                break;
                                            default:
                                                $status_tampil = "<span class='badge bg-secondary'>" . ucfirst($status_raw) . "</span>";
                                                break;
                                        }

                                        echo "<td class='text-center'>" . $status_tampil . "</td>";

                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data berkas pendaftaran jamaah haji yang ditemukan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/filterTanggal_pendaftaran.js"></script>
<script src="assets/js/cetakPendaftaran.js"></script>
</body>

</html>