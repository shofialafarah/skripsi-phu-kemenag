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
include '../../partials/fungsi.php';

if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php");
    exit();
}

$id_kepala = $_SESSION['id_kepala'];

// Tangani aksi verifikasi dari modal
if (isset($_POST['verifikasi'])) {
    $idPembatalan = $_POST['id_pelimpahan'];
    $status = $_POST['validation_status'];
    $tanggalVerifikasi = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE pelimpahan 
                    SET status_verifikasi = '$status', 
                        tanggal_verifikasi = '$tanggalVerifikasi' 
                    WHERE id_pelimpahan = $idPembatalan";

    if (mysqli_query($koneksi, $updateQuery)) {
        updateAktivitasPengguna($id_kepala, 'kepala_seksi', 'Pelimpahan', 'Memverifikasi Data Pelimpahan Haji');
        echo "<script>alert('Verifikasi berhasil disimpan!'); window.location='verifikasi_pelimpahan.php';</script>";
    } else {
        echo "Gagal menyimpan verifikasi: " . mysqli_error($koneksi);
    }
}

// Ambil dan filter data
$filterKategori = $_GET['filter_kategori'] ?? 'semua';
$query = "";

if ($filterKategori === 'Sakit Permanen' || $filterKategori === 'semua') {
    $query .= "
    SELECT 
        'Sakit' AS jenis,
        p.id_pelimpahan,
        s.id_limpah_sakit AS id_cetak,
        s.nama_jamaah,
        s.nomor_porsi,
        s.alamat_jamaah,
        s.nama_ahliwaris,
        s.status_dengan_jamaah,
        s.no_telepon_ahliwaris,
        p.kategori,
        p.status_verifikasi,
        p.tanggal_pengajuan,
        p.tanggal_verifikasi,
         p.tanggal_validasi
    FROM pelimpahan_sakit s
    JOIN pelimpahan p ON s.id_pelimpahan = p.id_pelimpahan
    ";
    if ($filterKategori === 'Sakit Permanen') {
        $query .= "WHERE p.kategori = 'Sakit Permanen' ";
    }
}

if ($filterKategori === 'Meninggal Dunia' || $filterKategori === 'semua') {
    if (!empty($query)) {
        $query .= "UNION ALL ";
    }
    $query .= "
    SELECT 
        'Meninggal' AS jenis,
        p.id_pelimpahan,
        m.id_limpah_meninggal AS id_cetak,
        m.nama_jamaah,
        m.nomor_porsi,
        m.alamat_jamaah,
        m.nama_ahliwaris,
        m.status_dengan_jamaah,
        m.no_telepon_ahliwaris,
        p.kategori,
        p.status_verifikasi,
        p.tanggal_pengajuan,
        p.tanggal_verifikasi,
        p.tanggal_validasi
    FROM pelimpahan_meninggal m
    JOIN pelimpahan p ON m.id_pelimpahan = p.id_pelimpahan
    ";
    if ($filterKategori === 'Meninggal Dunia') {
        $query .= "WHERE p.kategori = 'Meninggal Dunia' ";
    }
}

$query = "SELECT * FROM ( $query ) AS gabungan ORDER BY tanggal_validasi DESC";
$result = mysqli_query($koneksi, $query);

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
        <main class="verifikasi-wrapper">
            <div class="verifikasi">
                <div class="verifikasi-header" style="background-color: #1b5e20; color: white;">
                    <i class="fas fa-table me-1"></i> Verifikasi Pelimpahan Jamaah Haji
                </div>
                <div class="verifikasi-body">
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
                        <!-- Filter Kategori -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form method="GET" class="filter-form d-flex align-items-center gap-2">
                                    <label for="filter_kategori" class="form-label mb-0">Kategori:</label>
                                    <select id="filter_kategori" name="filter_kategori" class="form-select" style="width: auto;">
                                        <option value="semua" <?= $filterKategori == 'semua' ? 'selected' : '' ?>>Semua Kategori</option>
                                        <option value="Sakit Permanen" <?= $filterKategori == 'Sakit Permanen' ? 'selected' : '' ?>>Sakit</option>
                                        <option value="Meninggal Dunia" <?= $filterKategori == 'Meninggal Dunia' ? 'selected' : '' ?>>Meninggal</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data Pelimpahan -->
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>

                        <div class="table-responsive">
                            <table id="tabelVerifikasi" class="table table-striped table-bordered table-hover">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Nama Jamaah</th>
                                        <th>Nomor Porsi</th>
                                        <th>Alamat</th>
                                        <th>Nama Ahli Waris</th>
                                        <th>Status dengan Jamaah</th>
                                        <th>No Telepon</th>
                                        <th>Dokumen</th>
                                        <th>Status</th>
                                        <th>Tgl Pengajuan</th>
                                        <th>Tgl Verifikasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM ( $query ) AS gabungan ORDER BY tanggal_validasi DESC";
                                    $result = mysqli_query($koneksi, $query);
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $isVerified = !empty($row['tanggal_verifikasi']);
                                        echo "<tr>";
                                        echo "<td>" . $no . "</td>";
                                        //Kategori
                                        $kategori = htmlspecialchars($row['kategori']);
                                        $badgeClass = ($kategori === 'Meninggal Dunia') ? 'badge bg-danger text-white' : (($kategori === 'Sakit Permanen') ? 'badge bg-warning text-dark' : '');
                                        echo "<td><span class='$badgeClass'>$kategori</span></td>";

                                        //Nama Jamaah
                                        echo "<td>" . htmlspecialchars($row['nama_ahliwaris']) . "</td>";

                                        //Nomor Porsi
                                        echo "<td>" . htmlspecialchars($row['nomor_porsi']) . "</td>";

                                        //Alamat
                                        echo "<td>" . htmlspecialchars($row['alamat_jamaah']) . "</td>";

                                        //Nama Ahliwaris
                                        echo "<td>" . htmlspecialchars($row['nama_ahliwaris']) . "</td>";

                                        //Status Ahliwaris dengan Jamaah
                                        echo "<td>" . htmlspecialchars($row['status_dengan_jamaah']) . "</td>";

                                        //Nomor Telepon
                                        echo "<td>" . htmlspecialchars(
                                            $row['kategori'] === 'Meninggal Dunia' && array_key_exists('no_telepon_ahliwaris', $row)
                                                ? $row['no_telepon_ahliwaris']
                                                : ($row['no_telepon_ahliwaris'] ?? '-')
                                        ) . "</td>";

                                        //Dokumen
                                        $linkCetak = ($row['kategori'] === 'Meninggal Dunia')
                                            ? "cetak_pelimpahan_meninggal.php?id=" . $row['id_cetak']
                                            : "cetak_pelimpahan_sakit.php?id=" . $row['id_cetak'];

                                        echo "<td><a href='" . $linkCetak . "' target='_blank' class='btn btn-sm btn-outline-primary'>
                                                <i class='fas fa-file-pdf'></i> Lihat
                                            </a></td>";

                                        //Status Verifikasi
                                        $status = htmlspecialchars($row['status_verifikasi']);

                                        $badgeClass = match ($status) {
                                            'Disetujui' => 'badge bg-success',
                                            'Ditolak' => 'badge bg-danger',
                                            'Pending' => 'badge bg-warning text-dark',
                                            default => 'badge bg-secondary',
                                        };

                                        echo "<td><span class='$badgeClass'>$status</span></td>";

                                        // Format tanggal_pengajuan
                                        $tanggalPengajuan = date('d-m-Y', strtotime($row['tanggal_pengajuan']));
                                        echo "<td>" . $tanggalPengajuan . "</td>";

                                        // Format tanggal_verifikasi
                                        $tanggalVerifikasi = $row['tanggal_verifikasi'];

                                        if (!empty($tanggalVerifikasi) && $tanggalVerifikasi != '0000-00-00 00:00:00') {
                                            $formattedTanggal = date('d-m-Y', strtotime($tanggalVerifikasi));
                                            echo "<td><span class='badge bg-success'>" . $formattedTanggal . "</span></td>";
                                        } else {
                                            echo "<td class='text-center'>-</td>";
                                        }

                                        // Tombol AKSI
                                        $tanggalValidasiKosong = empty($row['tanggal_validasi']) || $row['tanggal_validasi'] == '0000-00-00' || $row['tanggal_validasi'] == null;

                                        echo "<td>";
                                        if ($tanggalValidasiKosong) {
                                            echo "<button class='btn btn-secondary btn-sm' disabled><i class='fas fa-edit'></i> Belum Divalidasi</button>";
                                        } else {
                                            echo "<button type='button' class='btn btn-warning btn-sm ms-1' 
                                                    onclick='openVerificationModal(" . $row['id_pelimpahan'] . ")'>
                                                    <i class='fas fa-edit'></i> Opsi
                                                </button>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";

                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Tidak ada data pelimpahan yang ditemukan.
                        </div>
                    <?php endif; ?>
                </div>
                <?php include_once __DIR__ . '/../includes/footer_kasi.php'; ?>
            </div>
        </main>
    </div>
</div>

<!-- Modal Verifikasi -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Verifikasi Pelimpahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="verifikasi" value="1">
                <input type="hidden" name="id_pelimpahan" id="modal_id_pelimpahan">

                <div class="mb-3">
                    <label for="validation_status" class="form-label">Status Verifikasi</label>
                    <select class="form-select" name="validation_status" id="validation_status" required>
                        <option value="">Pilih Status</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
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

<script src="assets/js/filterTanggal_pelimpahan.js"></script>
<script>
    function openVerificationModal(id) {
        document.getElementById('modal_id_pelimpahan').value = id;
        const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
        modal.show();
    }
</script>
</body>

</html>