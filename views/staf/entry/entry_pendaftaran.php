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

// Ambil ID staf dari session
$id_staf = $_SESSION['id_staf'];

$query = "SELECT * FROM pendaftaran ORDER BY id_pendaftaran DESC";
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

// Proses upload dokumen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pendaftaran'])) {
    $id_pendaftaran = clean_input($_POST['id_pendaftaran']);

    // Periksa apakah ada file yang diupload dan tidak ada error
    if (isset($_FILES['upload_doc']) && $_FILES['upload_doc']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['upload_doc']['tmp_name'];
        $fileName = basename($_FILES['upload_doc']['name']);
        $fileSize = $_FILES['upload_doc']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if ($fileExt !== 'pdf') {
            $_SESSION['error_message'] = "Hanya file PDF yang diperbolehkan.";
            header("Location: entry_pendaftaran.php");
            exit;
        }

        // Validasi ukuran file (5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "Ukuran file maksimal 5MB.";
            header("Location: entry_pendaftaran.php");
            exit;
        }

        // --- MULAI LOGIKA PENYIMPANAN FOLDER ---

        // 1. Ambil data jamaah untuk membuat folder unik
        $query_jamaah = "SELECT nama_jamaah, nik FROM pendaftaran WHERE id_pendaftaran = '$id_pendaftaran'";
        $result_jamaah = mysqli_query($koneksi, $query_jamaah);
        
        if ($result_jamaah && mysqli_num_rows($result_jamaah) > 0) {
            $row_jam = mysqli_fetch_assoc($result_jamaah);
            
            // Bersihkan nama jamaah untuk folder
            $nama_jamaah_clean = str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $row_jam['nama_jamaah']));
            $folder_utama = $nama_jamaah_clean . "_" . $row_jam['nik'];

            // 2. Tentukan Path lengkap ke folder /Entry/
            // Keluar dari views/staf/ menuju assets/berkas/pendaftaran/Nama_NIK/Entry/
            $uploadDir = __DIR__ . "/../../../assets/berkas/pendaftaran/" . $folder_utama . "/Entry/";

            // 3. Buat folder otomatis (Recursive)
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // 4. Ambil data staf login untuk penamaan file
            $query_staf = "SELECT nama_staf, nip FROM staf WHERE id_staf = '$id_staf'";
            $res_staf = mysqli_query($koneksi, $query_staf);
            $row_staf = mysqli_fetch_assoc($res_staf);

            $nama_staf_clean = str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $row_staf['nama_staf']));
            $nip_staf = $row_staf['nip'];

            // 5. Nama File: Entry_NamaStaf_NIP.pdf
            $newFileName = "Entry_" . $nama_staf_clean . "_" . $nip_staf . ".pdf";
            $targetPath = $uploadDir . $newFileName;

            // 6. Pindahkan File
            if (move_uploaded_file($fileTmp, $targetPath)) {
                // Path yang disimpan ke DB (untuk dipanggil di link href)
                $dbPath = "assets/berkas/pendaftaran/" . $folder_utama . "/Entry/" . $newFileName;
                
                $sql = "UPDATE pendaftaran SET 
                        upload_doc = '$dbPath', 
                        tanggal_validasi = NOW() 
                        WHERE id_pendaftaran = $id_pendaftaran";

                if (mysqli_query($koneksi, $sql)) {
                    updateAktivitasPengguna($id_staf, 'staf', 'Pendaftaran', 'Mengupload data pendaftaran');
                    $_SESSION['success_message'] = "Dokumen berhasil diupload ke folder " . $folder_utama . "/Entry";
                } else {
                    $_SESSION['error_message'] = "Gagal simpan ke database.";
                }
            } else {
                $_SESSION['error_message'] = "Gagal memindahkan file ke folder tujuan.";
            }
        } else {
            $_SESSION['error_message'] = "Data jamaah tidak ditemukan di database.";
        }
    } else {
        $error = isset($_FILES['upload_doc']) ? $_FILES['upload_doc']['error'] : 'Tidak ada file';
        $_SESSION['error_message'] = "Error upload: " . $error;
    }

    header("Location: entry_pendaftaran.php");
    exit;
}
?>
<?php include '../includes/header_setup.php'; ?>
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
                    <i class="fas fa-table me-1"></i> Entry Dokumen Pendaftaran Jamaah Haji
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
                    <table id="tabelPendaftaran" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Jamaah</th>
                                <th class="text-center">NIK/NO KTP</th>
                                <th class="text-center">Dokumen</th>
                                <th class="text-center">Tanggal Pengajuan</th>
                                <th class="text-center">Tanggal Validasi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (!empty($data_pendaftaran)) {
                                $no = 1;
                                foreach ($data_pendaftaran as $row) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $no++ . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nik']) . "</td>";
                                    if (!empty($row['upload_doc'])) {
                                        echo "<td class='text-center'>
                                            <a href='" . htmlspecialchars($row['upload_doc']) . "' class='btn-lihat-dokumen' target='_blank'>
                                            <i class='fas fa-file-pdf'></i> Lihat</a></td>";
                                    } else {
                                        echo "<td class='text-center'>-</td>";
                                    }

                                    echo "<td>" . date('d-m-Y', strtotime($row['tanggal_pengajuan'])) . "</td>";
                                    echo "<td>" . (!empty($row['tanggal_validasi']) ? date('d-m-Y', strtotime($row['tanggal_validasi'])) : '-') . "</td>";

                                    // Kolom aksi dengan upload dan hapus dokumen
                                    echo "<td class='text-center'>";
                                    echo "<div class='btn-group' role='group'>";
                                    // Tombol Upload
                                    if (empty($row['tanggal_validasi']) || $row['tanggal_validasi'] == null) {
                                        // Jika tanggal_validasi kosong/null (tampil "-"), disable tombol
                                        echo "<button type='button' class='btn btn-sm btn-secondary me-1' disabled>";
                                        echo "<i class='fas fa-upload'></i>";
                                        echo "</button>";
                                    } else {
                                        // Jika sudah ada tanggal_validasi, tombol aktif
                                        echo "<button type='button' class='btn btn-sm btn-upload me-1 upload-doc-btn' data-bs-toggle='modal' data-bs-target='#uploadModal' data-id='" . $row['id_pendaftaran'] . "'>";
                                        echo "<i class='fas fa-upload'></i>";
                                        echo "</button>";
                                    }

                                    // Tombol Delete (hanya muncul jika ada dokumen)
                                    if (!empty($row['upload_doc'])) {
                                        echo "<button type='button' class='btn btn-sm btn-danger delete-doc-btn' data-id='" . $row['id_pendaftaran'] . "'>";
                                        echo "<i class='fas fa-trash'></i>";
                                        echo "</button>";
                                    }

                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>Tidak ada data berkas pendaftaran jamaah haji yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include_once __DIR__ . '/../includes/footer_staf.php'; ?>
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
            <form id="uploadForm" action="entry_pendaftaran.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_pendaftaran" id="id_pendaftaran">
                    <div class="mb-3">
                        <label for="upload_doc" class="form-label">Pilih Dokumen (PDF)</label>
                        <input type="file" class="form-control" id="upload_doc" name="upload_doc" accept=".pdf" required>
                        <div class="form-text">Ukuran maksimal file: 5MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-upload">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if (isset($_SESSION['success_message'])): ?>
    <div id="flash-success" style="display: none;"><?php echo $_SESSION['success_message']; ?></div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div id="flash-error" style="display: none;"><?php echo $_SESSION['error_message']; ?></div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/entry_pendaftaran.js"></script>
<script src="assets/js/filterTanggal_pendaftaran.js"></script>
</body>

</html>