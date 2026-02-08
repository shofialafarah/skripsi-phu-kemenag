<?php
include 'koneksi.php';
include 'fungsi.php';

session_start();
// Cek apakah user sudah login
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php"); // Redirect ke login jika belum login atau session tidak ada
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

        // Validasi ukuran file
        if ($fileSize > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "Ukuran file maksimal 5MB.";
            header("Location: entry_pendaftaran.php");
            exit;
        }
        
        $uploadDir = 'uploads/pendaftaran/entry_pendaftaran/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 1. Ambil nama_jamaah dari database
        $query_nama = "SELECT nama_jamaah FROM pendaftaran WHERE id_pendaftaran = '$id_pendaftaran'";
        $result_nama = mysqli_query($koneksi, $query_nama);
        
        if ($result_nama && mysqli_num_rows($result_nama) > 0) {
            $row_nama = mysqli_fetch_assoc($result_nama);
            $nama_jamaah = $row_nama['nama_jamaah'];
            
            // Bersihkan nama jamaah agar aman untuk nama file (ganti spasi dengan underscore)
            $nama_jamaah_clean = str_replace(' ', '_', $nama_jamaah);
            $nama_jamaah_clean = preg_replace('/[^a-zA-Z0-9_]/', '', $nama_jamaah_clean);
            
            // 2. Buat nama file baru dengan format yang diminta
            $newFileName = "Entry_" . $nama_jamaah_clean . "_" . uniqid() . ".pdf"; 
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $targetPath)) {
                // 3. Perbaikan query update
                // Catatan: Variabel $keterangan tidak digunakan di form, jadi saya hapus dari query.
                $sql = "UPDATE pendaftaran SET upload_doc = '$targetPath', tanggal_validasi = NOW() WHERE id_pendaftaran = $id_pendaftaran";
                
                if (mysqli_query($koneksi, $sql)) {
                    updateAktivitasPengguna($id_staf, 'staf', 'Pendaftaran', 'Mengupload data pendaftaran');
                    $_SESSION['success_message'] = "Dokumen berhasil diupload.";
                } else {
                    $_SESSION['error_message'] = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
                    error_log("Database Error: " . mysqli_error($koneksi));
                }
            } else {
                $_SESSION['error_message'] = "Gagal memindahkan file.";
                error_log("Failed to move uploaded file from " . $fileTmp . " to " . $targetPath);
            }
        } else {
            $_SESSION['error_message'] = "Data jamaah tidak ditemukan.";
        }
    } else {
        $error = isset($_FILES['upload_doc']) ? $_FILES['upload_doc']['error'] : 'Tidak ada file yang diupload';
        $_SESSION['error_message'] = "File tidak terunggah dengan benar. Error: " . $error;
        error_log("File upload error: " . $error);
    }
    
    header("Location: entry_pendaftaran.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Staf</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        /* Buat tombol sejajar dan beri jarak antar tombol */
        .dt-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .dt-button {
            font-size: 0.85rem !important;
            padding: 6px 12px !important;
        }
    </style>

</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_staf.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_staf.php'; ?>

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
                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Jamaah</th>
                                    <th>NIK/NO KTP</th>
                                    <th class="text-center">Dokumen</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tanggal Validasi</th>
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
                                            <a href='" . htmlspecialchars($row['upload_doc']) . "' class='btn-dokumen' target='_blank'>
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
                                            echo "<button type='button' class='btn btn-sm btn-primary me-1 upload-doc-btn' data-bs-toggle='modal' data-bs-target='#uploadModal' data-id='" . $row['id_pendaftaran'] . "'>";
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

                                        // echo "<td class='text-center'>";
                                        // echo "<div class='btn-group' role='group'>";
                                        // echo "<button type='button' class='btn btn-sm btn-primary me-1 upload-doc-btn btn-entry' data-bs-toggle='modal' data-bs-target='#uploadModal' data-id='" . $row['id_pendaftaran'] . "'><i class='fas fa-upload'></i></button>";
                                        // if (!empty($row['upload_doc'])) {
                                        //     echo "<button type='button' class='btn btn-sm btn-danger delete-doc-btn btn-entry' data-id='" . $row['id_pendaftaran'] . "'><i class='fas fa-trash'></i></button>";
                                        // }
                                        // echo "</div></td>";
                                        // echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='text-center'>Tidak ada data berkas pendaftaran jamaah haji yang ditemukan.</td></tr>";
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
                            <!-- Perbaikan: Menyesuaikan for dengan id -->
                            <label for="upload_doc" class="form-label">Pilih Dokumen (PDF)</label>
                            <input type="file" class="form-control" id="upload_doc" name="upload_doc" accept=".pdf" required>
                            <div class="form-text">Ukuran maksimal file: 5MB</div>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Pastikan file JS kustom dimuat setelah semua library -->
    <script src="tanggal_entry_pendaftaran.js"></script>
    <script src="upload_pendaftaran.js"></script>
</body>

</html>