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

// Ambil ID staf dari session
$id_kepala = $_SESSION['id_kepala'];

// Function untuk membersihkan input
function clean_input($data)
{
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

// Tangani aksi verifikasi dari modal
if (isset($_POST['verifikasi'])) {
    $idPembatalan = $_POST['id_pendaftaran'];
    $status = $_POST['validation_status'];
    $tanggalVerifikasi = date('Y-m-d H:i:s');

    $updateQuery = "UPDATE pendaftaran 
                    SET status_verifikasi = '$status', 
                        tanggal_verifikasi = '$tanggalVerifikasi' 
                    WHERE id_pendaftaran = $idPembatalan";

    if (mysqli_query($koneksi, $updateQuery)) {
        $_SESSION['success_message'] = "Verifikasi berhasil disimpan!";
        header("Location: verifikasi_pendaftaran.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan verifikasi: " . mysqli_error($koneksi);
    }
}

// Proses validasi dokumen oleh kepala seksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_document'])) {
    $id_pendaftaran = clean_input($_POST['id_pendaftaran']);
    $status_verifikasi = clean_input($_POST['validation_status']);

    $query = "UPDATE pendaftaran 
              SET status_verifikasi = '$status_verifikasi', 
                  tanggal_verifikasi = NOW()
              WHERE id_pendaftaran = $id_pendaftaran";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success_message'] = "Status verifikasi berhasil disimpan.";
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan status verifikasi: " . mysqli_error($koneksi);
    }

    header("Location: verifikasi_pendaftaran.php");
    exit;
}

// Proses verifikasi langsung
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifikasi_langsung'])) {
    $id_pendaftaran = (int)$_POST['id_pendaftaran'];
    $status = 'Terverifikasi';

    $query = "UPDATE pendaftaran 
              SET status_verifikasi = '$status', 
                  tanggal_verifikasi = NOW()
              WHERE id_pendaftaran = $id_pendaftaran";

    if (mysqli_query($koneksi, $query)) {
        updateAktivitasPengguna($id_kepala, 'kepala_seksi', 'Pendaftaran', 'Memverifikasi Data Pendaftaran Haji');
        $_SESSION['success_message'] = "Dokumen berhasil diverifikasi.";
    } else {
        $_SESSION['error_message'] = "Gagal memverifikasi dokumen: " . mysqli_error($koneksi);
    }

    header("Location: verifikasi_pendaftaran.php");
    exit;
}

// PERBAIKAN UTAMA: Query untuk mengambil data
$query = "SELECT * FROM pendaftaran ORDER BY id_pendaftaran DESC";
$result = mysqli_query($koneksi, $query);

// Debug: Tampilkan error jika ada
if (!$result) {
    die("Error dalam query: " . mysqli_error($koneksi));
}

// Debug: Hitung jumlah data
$total_data = mysqli_num_rows($result);
?>
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
                <div class="verifikasi-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Verifikasi Pendaftaran Jamaah Haji
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
                    </div>

                    <!-- Tabel Data Pendaftaran -->
                    <?php if ($result && $total_data > 0): ?>
                        <div class="table-responsive">
                            <table id="tabelVerifikasi" class="table table-striped table-bordered table-hover">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Jamaah</th>
                                        <th>Nomor Porsi</th>
                                        <th>Alamat</th>
                                        <th>Nomor Telepon</th>
                                        <th class="text-center">Dokumen</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Tanggal Verifikasi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM pendaftaran ORDER BY id_pendaftaran DESC";
                                    $result = mysqli_query($koneksi, $query);
                                    $no = 1;

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $isVerified = !empty($row['tanggal_verifikasi']);
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $no . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_jamaah'] ?? '') . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nomor_porsi'] ?? '') . "</td>";

                                        // Format alamat
                                        $alamat_parts = array_filter([
                                            $row['alamat'] ?? '',
                                            $row['kecamatan'] ?? '',
                                            $row['kelurahan'] ?? '',
                                            $row['kode_pos'] ?? ''
                                        ]);
                                        echo "<td>" . htmlspecialchars(implode(', ', $alamat_parts)) . "</td>";

                                        echo "<td>" . htmlspecialchars($row['no_telepon'] ?? '') . "</td>";

                                        // Dokumen
                                        if (!empty($row['upload_doc'])) {
                                            echo "<td><a href='" . htmlspecialchars($row['upload_doc']) . "' target='_blank' class='btn btn-sm btn-outline-primary'>
        <i class='fas fa-file-pdf'></i> Lihat
    </a></td>";
                                        } else {
                                            echo "<td class='text-center'>-</td>";
                                        }


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
                                            echo "<button class='btn btn-secondary btn-sm' disabled><i class='fas fa-edit'></i> Belum Validasi</button>";
                                        } else {
                                            echo "<button type='button' class='btn btn-warning btn-sm ms-1' 
        onclick='openVerificationModal(" . $row['id_pendaftaran'] . ")'>
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
                            <i class="fas fa-info-circle"></i> Tidak ada data pendaftaran yang ditemukan.
                            <br><small>Periksa koneksi database atau pastikan tabel 'pendaftaran' memiliki data.</small>
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
                <h5 class="modal-title" id="verificationModalLabel">Verifikasi Pendaftaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="verifikasi" value="1">
                <input type="hidden" name="id_pendaftaran" id="modal_id_pendaftaran">

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

<script src="assets/js/filterTanggal_pendaftaran.js"></script>
<script>
    function openVerificationModal(id) {
        document.getElementById('modal_id_pendaftaran').value = id;
        const modal = new bootstrap.Modal(document.getElementById('verificationModal'));
        modal.show();
    }
</script>

</body>

</html>