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
           pm.id_batal_meninggal, 
           ps.id_batal_ekonomi,
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
                    <table id="tabelPembatalan" class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">Nama Pengaju</th>
                                <th class="text-center">KATEGORI</th>
                                <th class="text-center">TANGGAL PENGAJUAN</th>
                                <th class="text-center">TANGGAL VALIDASI</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (!empty($data_pembatalan)) {
                                foreach ($data_pembatalan as $row) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_pengaju']) . "</td>";
                                    $kategori = htmlspecialchars($row['kategori']);
                                    $badgeClass = '';

                                    if ($kategori === 'Meninggal Dunia') {
                                        $badgeClass = 'badge bg-danger text-white';
                                    } elseif ($kategori === 'Keperluan Ekonomi') {
                                        $badgeClass = 'badge bg-warning text-dark';
                                    }

                                    echo "<td class='text-center'><span class='$badgeClass'>$kategori</span></td>";

                                    echo "<td class='text-center'>" . date('d-m-Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
                                    echo "<td class='text-center'>" . (!empty($row['tanggal_validasi']) ? date('d-m-Y', strtotime($row['tanggal_validasi'])) : '-') . "</td>";

                                    // Tombol AKSI:
                                    // Jika tanggal_validasi kosong/null ("-"), maka disable tombol edit dan hapus
                                    // Jika tanggal_validasi ada isinya, maka aktifkan tombol edit dan hapus
                                    $isValidated = !empty($row['tanggal_validasi']);
                                    $disabledAttr = !$isValidated ? 'style="pointer-events: none; opacity: 0.5;"' : '';

                                    // Tombol cetak tetap seperti sebelumnya (aktif jika sudah diverifikasi Kasi)
                                    $isVerified = !empty($row['tanggal_verifikasi_kasi']);
                                    $cetakDisabledAttr = ($row['status_verifikasi'] === 'Disetujui') ? '' : 'style="pointer-events: none; color: gray;"';


                                    echo "<td class='text-center'>";
                                    echo "<div class='btn-group' role='group'>";

                                    if ($row['kategori'] == 'Meninggal Dunia') {
                                        $id_detail = $row['id_batal_meninggal'];
                                        echo "<a class='btn btn-warning btn-sm' href='includes/pembatalan/edit_pembatalan_meninggal.php?id=$id_detail' title='Edit Data Pembatalan Meninggal Dunia' $disabledAttr><i class='fa-regular fa-pen-to-square'></i></a>";
                                        echo "<a class='btn btn-success btn-sm' href='includes/pembatalan/cetak_pembatalan_meninggal.php?id=$id_detail' title='Cetak Data Pembatalan Meninggal Dunia' $cetakDisabledAttr><i class='fa-solid fa-print'></i></a>";
                                    } elseif ($row['kategori'] == 'Keperluan Ekonomi') {
                                        $id_detail = $row['id_batal_ekonomi'];
                                        echo "<a class='btn btn-warning btn-sm' href='includes/pembatalan/edit_pembatalan_ekonomi.php?id=$id_detail' title='Edit Data Pembatalan Keperluan Ekonomi' $disabledAttr><i class='fa-regular fa-pen-to-square'></i></a>";
                                        echo "<a class='btn btn-success btn-sm' href='includes/pembatalan/cetak_pembatalan_ekonomi.php?id=$id_detail' title='Cetak Data Pembatalan Keperluan Ekonomi' $cetakDisabledAttr><i class='fa-solid fa-print'></i></a>";
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
            <div class="footer" style="color: white; text-align: center;">
                <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
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

<script src="assets/js/filterTanggal_pembatalan.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: '<?php echo $_SESSION['success_message']; ?>',
            confirmButtonColor: '#388e3c'
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Waduh!',
            text: '<?php echo $_SESSION['error_message']; ?>',
            confirmButtonColor: '#d33'
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
</body>

</html>