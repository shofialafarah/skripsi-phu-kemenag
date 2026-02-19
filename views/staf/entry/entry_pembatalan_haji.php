<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../includes/koneksi.php';
include '../../partials/fungsi.php';

if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: ../auth/login.php");
    exit();
}
$id_staf = $_SESSION['id_staf'];

$data_pembatalan = [];
$jumlah_data = 0;

$query = "
    SELECT p.*, 
       CASE 
           WHEN p.kategori = 'Meninggal Dunia' THEN pm.nama_ahliwaris
           WHEN p.kategori = 'Keperluan Ekonomi' THEN ps.nama_jamaah
           ELSE NULL
       END as nama_pengaju
    FROM pembatalan p
    LEFT JOIN pembatalan_meninggal pm ON p.id_pembatalan = pm.id_pembatalan AND p.kategori = 'Meninggal Dunia'
    LEFT JOIN pembatalan_ekonomi ps ON p.id_pembatalan = ps.id_pembatalan AND p.kategori = 'Keperluan Ekonomi'
    ORDER BY p.tanggal_validasi DESC";
$result = $koneksi->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pembatalan[] = $row;
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
        <?php include_once __DIR__ . '/../includes/sidebar_staf.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../includes/header_staf.php'; ?>

            <main class="entry-wrapper">
                <div class="entry">
                    <div class="entry-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Entry Dokumen Pembatalan Jamaah Haji
                    </div>
                    <div class="entry-body">
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
                        </div>
                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">NO</th>
                                    <th>Nama ahliwaris</th>
                                    <th>KATEGORI</th>
                                    <th>TANGGAL PENGAJUAN</th>
                                    <th>TANGGAL VALIDASI</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (!empty($data_pembatalan)) {
                                    $no = 1;
                                    foreach ($data_pembatalan as $row) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_pengaju']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";

                                        

                                        echo "<td>" . date('d-m-Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
                                        echo "<td>" . (!empty($row['tanggal_validasi']) ? date('d-m-Y', strtotime($row['tanggal_validasi'])) : '-') . "</td>";

                                        // Kolom aksi dengan upload dan hapus dokumen
                                        echo "<td class='text-center'>";
                                        echo "<div class='btn-group' role='group'>";

                                        if ($row['kategori'] == 'Meninggal Dunia') {
                                            // Tombol Edit
                                            echo "<a class='btn btn-warning btn-sm' href='edit_pembatalan_meninggal.php?id=" . $row['id_pembatalan'] . "'><i class='fa-regular fa-pen-to-square'></i></a>";
                                            // Tombol Hapus
                                            echo "<a class='btn btn-danger btn-sm' href='hapus_pembatalan_meninggal.php?id=" . $row['id_pembatalan'] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\")'><i class='fa-solid fa-trash'></i></a>";

                                            // Tombol Cetak
                                            $isVerified = isset($row['tanggal_verifikasi_kasi']) && !empty($row['tanggal_verifikasi_kasi']);
                                            $cetakUrl = 'cetak_pembatalan_meninggal.php?id=' . $row['id_pembatalan'];
                                            $style = $isVerified ? '' : "style='pointer-events: none; color: gray;'";
                                            echo "<a class='btn btn-success btn-sm' href='$cetakUrl' $style><i class='fa-solid fa-print'></i></a>";
                                        } elseif ($row['kategori'] == 'Keperluan Ekonomi') {
                                            // Tombol Edit
                                            echo "<a class='btn btn-warning btn-sm' href='edit_pembatalan_ekonomi.php?id=" . $row['id_pembatalan'] . "'><i class='fa-regular fa-pen-to-square'></i></a>";
                                            // Tombol Hapus
                                            echo "<a class='btn btn-danger btn-sm' href='hapus_pembatalan_ekonomi.php?id=" . $row['id_pembatalan'] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus?\")'><i class='fa-solid fa-trash'></i></a>";

                                            // Tombol Cetak
                                            $isVerified = isset($row['tanggal_verifikasi_kasi']) && !empty($row['tanggal_verifikasi_kasi']);
                                            $cetakUrl = 'cetak_pembatalan_ekonomi.php?id=' . $row['id_pembatalan'];
                                            $style = $isVerified ? '' : "style='pointer-events: none; color: gray;'";
                                            echo "<a class='btn btn-success btn-sm' href='$cetakUrl' $style><i class='fa-solid fa-print'></i></a>";
                                        }

                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>Tidak ada data berkas pembatalan jamaah haji yang ditemukan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Upload Dokumen -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Dokumen Jamaah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadForm" action="entry_pembatalan.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_pembatalan" id="id_pembatalan">
                        <div class="mb-3">
                            <!-- Perbaikan: Menyesuaikan for dengan id -->
                            <label for="upload_doc" class="form-label">Pilih Dokumen (PDF)</label>
                            <input type="file" class="form-control" id="upload_doc" name="upload_doc" accept=".pdf" required>
                            <div class="form-text">Ukuran maksimal file: 5MB</div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
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

    <!-- Pastikan file JS kustom dimuat setelah semua library -->
    <script src="staf.js"></script>
    <script src="upload_pembatalan.js"></script>
</body>

</html>