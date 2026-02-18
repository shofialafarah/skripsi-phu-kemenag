<?php session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php"); // Redirect ke login jika belum login atau session tidak ada
    exit();
}

// Ambil ID staf dari session
$id_staf = $_SESSION['id_staf'];

// Initialize variables
$data_pelimpahan = [];
$jumlah_data = 0;

// PERBAIKAN: Query yang lebih baik untuk menggabungkan data
$query = "
    SELECT p.*, 
           pm.id_limpah_meninggal, 
           ps.id_limpah_sakit,
           CASE 
               WHEN p.kategori = 'Meninggal Dunia' THEN pm.nama_ahliwaris
               WHEN p.kategori = 'Sakit Permanen' THEN ps.nama_ahliwaris
               ELSE NULL
           END as nama_pengaju
    FROM pelimpahan p
    LEFT JOIN pelimpahan_meninggal pm ON p.id_pelimpahan = pm.id_pelimpahan AND p.kategori = 'Meninggal Dunia'
    LEFT JOIN pelimpahan_sakit ps ON p.id_pelimpahan = ps.id_pelimpahan AND p.kategori = 'Sakit Permanen'
    ORDER BY p.tanggal_validasi DESC";

$result = $koneksi->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pelimpahan[] = $row;
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pelimpahan'])) {
    $id_pelimpahan = isset($_POST['id_pelimpahan']) ? intval($_POST['id_pelimpahan']) : 0;

    if ($id_pelimpahan <= 0) {
        $_SESSION['error_message'] = "ID Pelimpahan tidak valid.";
        header("Location: entry_pelimpahan.php");
        exit;
    }

    if (isset($_FILES['upload_doc']) && $_FILES['upload_doc']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['upload_doc']['tmp_name'];
        $fileName = basename($_FILES['upload_doc']['name']);
        $fileSize = $_FILES['upload_doc']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExt !== 'pdf') {
            $_SESSION['error_message'] = "Hanya file PDF yang diperbolehkan.";
            header("Location: entry_pelimpahan.php");
            exit;
        }

        if ($fileSize > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "Ukuran file maksimal 5MB.";
            header("Location: entry_pelimpahan.php");
            exit;
        }

        $uploadDir = 'uploads/pelimpahan/entry_pelimpahan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Buat folder jika belum ada
        }

        $newFileName = uniqid() . "_" . $fileName;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Simpan hanya upload_doc dan tanggal_validasi
            $sql = "UPDATE pelimpahan SET upload_doc = '$targetPath', tanggal_validasi = NOW() WHERE id_pelimpahan = $id_pelimpahan";
            echo "SQL: $sql <br>";  // Debug

            error_log("SQL Query: " . $sql);

            if (mysqli_query($koneksi, $sql)) {
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
        $error = isset($_FILES['upload_doc']) ? $_FILES['upload_doc']['error'] : 'No file uploaded';
        $_SESSION['error_message'] = "File tidak terunggah dengan benar. Error: " . $error;
        error_log("File upload error: " . $error);
    }

    header("Location: entry_pelimpahan.php");
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
                        <i class="fas fa-table me-1"></i> Entry Dokumen Pelimpahan Jamaah Haji
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
                                    <th class="text-center">NAMA AHLIWARIS</th>
                                    <th class="text-center">KATEGORI</th>
                                    <th class="text-center">TANGGAL PENGAJUAN</th>
                                    <th class="text-center">TANGGAL VALIDASI</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (!empty($data_pelimpahan)) {
                                    foreach ($data_pelimpahan as $row) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $no++ . "</td>";

                                        // Nama Pengaju Pelimpahan
                                        echo "<td>" . htmlspecialchars($row['nama_pengaju']) . "</td>";

                                        // Kategori Pelimpahan
                                        $kategori = htmlspecialchars($row['kategori']);
                                        $badgeClass = '';
                                        if ($kategori === 'Meninggal Dunia') {
                                            $badgeClass = 'badge bg-danger text-white';
                                        } elseif ($kategori === 'Sakit Permanen') {
                                            $badgeClass = 'badge bg-warning text-dark';
                                        }
                                        echo "<td class='text-center'><span class='$badgeClass'>$kategori</span></td>";

                                        // Tanggal Pengajuan
                                        echo "<td class='text-center'>" . date('d-m-Y', strtotime($row['tanggal_pengajuan'])) . "</td>";

                                        // Tanggal Validasi
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
                                            $id_detail = $row['id_limpah_meninggal'];
                                            echo "<a class='btn btn-warning btn-sm' href='edit_pelimpahan_meninggal.php?id=$id_detail' $disabledAttr><i class='fa-regular fa-pen-to-square'></i></a>";
                                            echo "<a class='btn btn-success btn-sm' href='cetak_pelimpahan_meninggal.php?id=$id_detail' $cetakDisabledAttr><i class='fa-solid fa-print'></i></a>";
                                        } elseif ($row['kategori'] == 'Sakit Permanen') {
                                            $id_detail = $row['id_limpah_sakit'];
                                            echo "<a class='btn btn-warning btn-sm' href='edit_pelimpahan_sakit.php?id=$id_detail' $disabledAttr><i class='fa-regular fa-pen-to-square'></i></a>";
                                            echo "<a class='btn btn-success btn-sm' href='cetak_pelimpahan_sakit.php?id=$id_detail' $cetakDisabledAttr><i class='fa-solid fa-print'></i></a>";
                                        }

                                        echo "</div>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9' class='text-center'>Tidak ada data berkas pelimpahan jamaah haji yang ditemukan.</td></tr>";
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
                <form id="uploadForm" action="entry_pelimpahan.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_pelimpahan" id="id_pelimpahan">
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

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="hapus_ePelimpahan.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus dokumen ini?
                        <input type="hidden" name="id_pelimpahan" id="delete-id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
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
    <script src="tanggal_entry_pembatalan.js"></script>
</body>

</html>