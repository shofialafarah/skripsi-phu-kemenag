<?php session_start();
include 'koneksi.php';
include 'fungsi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php");
    exit();
}

// Ambil ID staf dari session
$id_staf = $_SESSION['id_staf'];

// Initialize variables
$data_pendaftaran = [];
$jumlah_data = 0;

// Query dengan error handling yang lebih baik
$query = "SELECT * FROM pendaftaran ORDER BY tanggal_pengajuan DESC";
$result = $koneksi->query($query);

if ($result) {
    $jumlah_data = $result->num_rows;
    if ($jumlah_data > 0) {
        while ($row = $result->fetch_assoc()) {
            $data_pendaftaran[] = $row;
        }
    }
} else {
    echo "<!-- ERROR: " . $koneksi->error . " -->";
    $jumlah_data = 0;
}

echo "<!-- DEBUG: Jumlah data: " . $jumlah_data . " -->";

function clean_input($data)
{
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

// Mapping kolom dokumen dengan kolom status
$doc_fields_with_status = [
    'dokumen_setor_awal' => ['label' => 'Setor Awal', 'status_field' => 'dokumen_setor_awal_status'],
    'dokumen_ktp' => ['label' => 'KTP', 'status_field' => 'dokumen_ktp_status'],
    'dokumen_kk' => ['label' => 'KK', 'status_field' => 'dokumen_kk_status'],
    'dokumen_lain' => ['label' => 'Lainnya', 'status_field' => 'dokumen_lain_status'],
    'foto_wajah' => ['label' => 'Foto Wajah', 'status_field' => 'foto_wajah_status']
];

// Fungsi untuk menentukan status dokumen
function getDocumentStatus($status_value)
{
    $status = strtolower(trim((string)$status_value));

    if (strpos($status, 'terverifikasi') !== false) {
        return 'Terverifikasi';
    } elseif (strpos($status, 'unggah ulang') !== false) {
        return 'Unggah Ulang';
    } else {
        return 'Menunggu Verifikasi';
    }
}

// Tambahkan fungsi untuk mengecek apakah semua dokumen sudah terverifikasi
function isAllDocumentsVerified($row, $doc_fields_with_status)
{
    foreach ($doc_fields_with_status as $field => $info) {
        $file = $row[$field] ?? '';
        $status_field = $info['status_field'];

        // Jika ada file yang diupload
        if (!empty($file)) {
            $status_raw = isset($row[$status_field]) ? $row[$status_field] : null;
            $doc_status = getDocumentStatus($status_raw);

            // Jika ada dokumen yang belum terverifikasi
            if ($doc_status !== 'Terverifikasi') {
                return false;
            }
        }
    }
    return true;
}

// Fungsi untuk update tanggal validasi jika semua dokumen terverifikasi
function updateValidationDateIfAllVerified($id_pendaftaran, $koneksi, $doc_fields_with_status)
{
    // Ambil data pendaftaran terbaru
    $query = "SELECT * FROM pendaftaran WHERE id_pendaftaran = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_pendaftaran);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Cek apakah semua dokumen sudah terverifikasi
        if (isAllDocumentsVerified($row, $doc_fields_with_status)) {
            $status = 'Valid';
            $tanggal_validasi = date("Y-m-d H:i:s");

            // Update status dan tanggal validasi
            $update_query = "UPDATE pendaftaran SET tanggal_validasi = ?, status = ? WHERE id_pendaftaran = ?";
            $update_stmt = $koneksi->prepare($update_query);
            $update_stmt->bind_param("ssi", $tanggal_validasi, $status, $id_pendaftaran);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Jika tidak semua dokumen terverifikasi, reset tanggal validasi dan status
            $status = 'Belum Valid';
            $reset_query = "UPDATE pendaftaran SET tanggal_validasi = NULL, status = ? WHERE id_pendaftaran = ?";
            $reset_stmt = $koneksi->prepare($reset_query);
            $reset_stmt->bind_param("si", $status, $id_pendaftaran);
            $reset_stmt->execute();
            $reset_stmt->close();
        }
    }
    $stmt->close();
}

// Handler untuk validasi dokumen individual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_document'])) {
    $id_pendaftaran = clean_input($_POST['id_pendaftaran']);
    $document_field = clean_input($_POST['document_field']);
    $validation_status = clean_input($_POST['validation_status']);
    $catatan = isset($_POST['catatan']) ? clean_input($_POST['catatan']) : '';

    // Dapatkan nama kolom status yang benar
    $status_field = '';
    foreach ($doc_fields_with_status as $field => $info) {
        if ($field == $document_field) {
            $status_field = $info['status_field'];
            break;
        }
    }

    if (!empty($status_field)) {
        // Tentukan nilai yang akan disimpan
        $status_value = '';
        if ($validation_status == 'Terverifikasi') {
            $status_value = 'Terverifikasi';
        } elseif ($validation_status == 'Unggah Ulang') {
            // Gabungkan status dengan catatan jika ada
            $status_value = 'Unggah Ulang';
            if (!empty($catatan)) {
                $status_value = 'Unggah Ulang - ' . $catatan;
            }
        }

        // Update status dokumen dengan prepared statement
        $query = "UPDATE pendaftaran SET $status_field = ? WHERE id_pendaftaran = ?";
        $stmt = $koneksi->prepare($query);

        if ($stmt) {
            $stmt->bind_param("si", $status_value, $id_pendaftaran);

            if ($stmt->execute()) {
                // ✅ Catat aktivitas hanya jika data berhasil ditemukan
                updateAktivitasPengguna($id_staf, 'staf', 'Pendaftaran', 'Memperbarui status pendaftaran');
                // Setelah berhasil update status dokumen, cek dan update tanggal validasi
                updateValidationDateIfAllVerified($id_pendaftaran, $koneksi, $doc_fields_with_status);

                $message = ($validation_status == 'Terverifikasi') ? 'Dokumen berhasil diverifikasi!' : 'Dokumen ditolak!';
                echo "<script>alert('$message'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
                exit;
            } else {
                echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
                exit;
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing statement: " . $koneksi->error . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        }
    }
}

// Handler untuk setuju/tolak keseluruhan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pendaftaran'], $_POST['action']) && !isset($_POST['validate_document'])) {
    $id_pendaftaran = clean_input($_POST['id_pendaftaran']);
    $action = clean_input($_POST['action']);
    $tanggal_validasi = date("Y-m-d H:i:s");

    $status = ($action === 'setuju') ? 'Disetujui' : 'tidak Disetujui';

    // Gunakan prepared statement untuk keamanan
    $query = "UPDATE pendaftaran SET status = ?, tanggal_validasi = ? WHERE id_pendaftaran = ?";
    $stmt = $koneksi->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ssi", $status, $tanggal_validasi, $id_pendaftaran);

        if ($stmt->execute()) {
            // ✅ Catat aktivitas hanya jika data berhasil ditemukan
            updateAktivitasPengguna($id_staf, 'staf', 'Pendaftaran', 'Memperbarui status pendaftaran');
            $_SESSION['success_message'] = "Status pendaftaran berhasil diperbarui";
            echo "<script>alert('Status pendaftaran berhasil diperbarui!'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui status: " . $stmt->error;
            echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement: " . $koneksi->error . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Staf</title>
    <link rel="icon" href="logo_kemenag.png">

    <!-- css global -->
    <link rel="stylesheet" href="kumpulan-css/global_style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <style>
        /* Warna pembeda kategori */
        .kategori-meninggal {
            background-color: rgba(220, 53, 69, 0.1) !important;
            /* Merah muda */
            /* border-left: 4px solid #dc3545 !important; */
        }

        .kategori-sakit {
            background-color: rgba(255, 193, 7, 0.1) !important;
            /* Kuning muda */
            /* border-left: 4px solid #ffc107 !important; */
        }

        .badge-kategori-meninggal {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .badge-kategori-sakit {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        /* Styling untuk dokumen */
        .document-item {
            padding: 8px;
            margin: 2px 0;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .document-item[data-status="Terverifikasi"] {
            background-color: rgba(25, 135, 84, 0.1);
            border-color: #198754;
        }

        .document-item[data-status="Unggah Ulang"] {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: #dc3545;
        }

        .document-item[data-status="Menunggu Verifikasi"] {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: #ffc107;
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

            <main class="mPendaftaran-wrapper">
                <div class="mPendaftaran">
                    <div class="mPendaftaran-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Berkas Menunggu Verifikasi Pendaftaran Jamaah Haji
                    </div>
                    <div class="mPendaftaran-body">
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

                        <div class="table-responsive">
                            <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Jamaah</th>
                                        <th>NIK/KTP</th>
                                        <th>Nomor Telepon</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Tanggal Validasi</th>
                                        <th class="text-center">Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $modal_list = []; // Initialize modal list

                                    if (!empty($data_pendaftaran)) {
                                        $no = 1;
                                        foreach ($data_pendaftaran as $row) {
                                            echo "<tr>";
                                            echo "<td class='text-center'>" . $no++ . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_jamaah'] ?? '') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nik'] ?? '') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['no_telepon'] ?? '') . "</td>";

                                            // Tanggal pengajuan
                                            $tanggal_pengajuan = isset($row['tanggal_pengajuan']) ? date('d-m-Y', strtotime($row['tanggal_pengajuan'])) : '-';
                                            echo "<td>" . $tanggal_pengajuan . "</td>";

                                            // Tanggal validasi - hanya tampil jika semua dokumen terverifikasi
                                            $all_verified = isAllDocumentsVerified($row, $doc_fields_with_status);
                                            if ($all_verified && !empty($row['tanggal_validasi'])) {
                                                $tanggal_validasi = date('d-m-Y', strtotime($row['tanggal_validasi']));
                                                echo "<td><span class='badge bg-success'><i class='fas fa-check-circle'></i> " . $tanggal_validasi . "</span></td>";
                                            } else {
                                                echo "<td><span class='text-muted'>-</span></td>";
                                            }

                                            // Kolom lihat dokumen jamaah
                                            echo "<td>";
                                            foreach ($doc_fields_with_status as $field => $info) {
                                                $file = $row[$field] ?? '';
                                                $label = $info['label'];
                                                $status_field = $info['status_field'];
                                                $field_safe = htmlspecialchars($field);
                                                $modal_id = "modalCatatan_" . $row['id_pendaftaran'] . "_" . $field_safe;

                                                // Dapatkan status validasi dari database
                                                $status_raw = isset($row[$status_field]) ? $row[$status_field] : null;
                                                $doc_status = getDocumentStatus($status_raw);

                                                // Ekstrak catatan jika ada (untuk status Unggah Ulang)
                                                $catatan_text = '';
                                                if ($doc_status == 'Unggah Ulang' && !empty($status_raw)) {
                                                    if (strpos($status_raw, ' - ') !== false) {
                                                        $parts = explode(' - ', $status_raw, 2);
                                                        $catatan_text = $parts[1];
                                                    }
                                                }

                                                echo "<div class='mb-2 d-flex align-items-center gap-2 document-item' data-status='$doc_status'>";

                                                if (!empty($file)) {
                                                    // Link untuk melihat dokumen
                                                    echo "<a href='{$file}' class='btn btn-sm btn-primary' target='_blank' style='min-width: 100px;'>";
                                                    echo "<i class='fas fa-file'></i> $label</a>";

                                                    // Status dan tombol aksi
                                                    if ($doc_status == 'Terverifikasi') {
                                                        echo "<span class='badge bg-success'><i class='fas fa-check-circle'></i> Terverifikasi</span>";
                                                        echo "<button class='btn btn-sm btn-outline-warning btn-change-status' data-bs-toggle='modal' data-bs-target='#$modal_id' title='Ubah ke Tidak Valid'>";
                                                        echo "<i class='fas fa-edit'></i></button>";
                                                    } elseif ($doc_status == 'Unggah Ulang') {
                                                        echo "<span class='badge bg-danger'><i class='fas fa-times-circle'></i> Unggah Ulang</span>";
                                                        if (!empty($catatan_text)) {
                                                            echo "<small class='text-muted ms-1' title='$catatan_text'><i class='fas fa-info-circle'></i></small>";
                                                        }
                                                        echo "<form method='post' class='d-inline'>";
                                                        echo "<input type='hidden' name='validate_document' value='1'>";
                                                        echo "<input type='hidden' name='id_pendaftaran' value='" . $row['id_pendaftaran'] . "'>";
                                                        echo "<input type='hidden' name='document_field' value='$field'>";
                                                        echo "<input type='hidden' name='validation_status' value='Terverifikasi'>";
                                                        echo "<button type='submit' class='btn btn-sm btn-outline-success' title='Terverifikasi' onclick=\"return confirm('Ubah status dokumen $label menjadi TERVERIFIKASI?');\">";
                                                        echo "<i class='fas fa-check'></i></button>";
                                                        echo "</form>";
                                                    } else { // Menunggu Verifikasi
                                                        echo "<span class='badge bg-warning text-dark'><i class='fas fa-clock'></i> Menunggu Verifikasi</span>";
                                                        echo "<form method='post' class='d-inline'>";
                                                        echo "<input type='hidden' name='validate_document' value='1'>";
                                                        echo "<input type='hidden' name='id_pendaftaran' value='" . $row['id_pendaftaran'] . "'>";
                                                        echo "<input type='hidden' name='document_field' value='$field'>";
                                                        echo "<input type='hidden' name='validation_status' value='Terverifikasi'>";
                                                        echo "<button type='submit' class='btn btn-sm btn-success' title='Terverifikasi' onclick=\"return confirm('Tandai dokumen $label sebagai TERVERIFIKASI?');\">";
                                                        echo "<i class='fas fa-check'></i></button>";
                                                        echo "</form>";
                                                        echo "<button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#$modal_id' title='Tolak Dokumen'>";
                                                        echo "<i class='fas fa-times'></i></button>";
                                                    }

                                                    // Kumpulkan data modal
                                                    $modal_list[] = [
                                                        'id' => $modal_id,
                                                        'label' => $label,
                                                        'field' => $field,
                                                        'id_pendaftaran' => $row['id_pendaftaran'],
                                                        'current_status' => $doc_status,
                                                        'current_note' => $catatan_text
                                                    ];
                                                } else {
                                                    echo "<button class='btn btn-sm btn-secondary' disabled style='min-width: 100px;'><i class='fas fa-file-slash'></i> $label</button>";
                                                    echo "<span class='text-muted ms-2'>Tidak ada file</span>";
                                                }

                                                echo "</div>";
                                            }
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
                </div>
                <div class="footer" style="color: white; text-align: center;">
                    <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                </div>
            </main>
        </div>
    </div>
    <?php
    // Render semua modal
    if (!empty($modal_list)) {
        foreach ($modal_list as $modal) {
            $is_changing_valid = ($modal['current_status'] == 'Terverifikasi');
            $modal_title = $is_changing_valid ? 'Ubah Status Dokumen: ' . $modal['label'] : 'Tolak Dokumen: ' . $modal['label'];
            $button_text = $is_changing_valid ? 'Ubah ke Unggah Ulang' : 'Tolak Dokumen';
            $placeholder_text = $is_changing_valid ? 'Berikan alasan mengapa dokumen ini perlu diunggah ulang...' : 'Berikan alasan mengapa dokumen ini ditolak...';

            echo "
    <div class='modal fade' id='{$modal['id']}' tabindex='-1' aria-labelledby='{$modal['id']}Label' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='{$modal['id']}Label'>$modal_title</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <form method='post'>
                    <div class='modal-body'>
                        <div class='alert alert-warning'>
                            <i class='fas fa-exclamation-triangle'></i> 
                            Status akan diubah ke: <strong>Unggah Ulang - [catatan Anda]</strong>
                        </div>
                        <div class='mb-3'>
                            <label for='catatan_{$modal['id']}' class='form-label'>Catatan: <span class='text-danger'>*</span></label>
                            <textarea class='form-control' id='catatan_{$modal['id']}' name='catatan' rows='3' 
                                placeholder='$placeholder_text' required>{$modal['current_note']}</textarea>
                        </div>
                        <input type='hidden' name='validate_document' value='1'>
                        <input type='hidden' name='id_pendaftaran' value='{$modal['id_pendaftaran']}'>
                        <input type='hidden' name='document_field' value='{$modal['field']}'>
                        <input type='hidden' name='validation_status' value='Unggah Ulang'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                        <button type='submit' class='btn btn-danger'>$button_text</button>
                    </div>
                </form>
            </div>
        </div>
    </div>";
        }
    }
    ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Document ready');
            console.log('Table rows count:', $('#tabelStaf tbody tr').length);

            // Handler untuk form validasi dokumen
            $('form[method="post"]').on('submit', function(e) {
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

                setTimeout(function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }, 3000);
            });

            // Tooltip untuk menampilkan catatan lengkap
            $('[title]').tooltip();

            console.log('Document validation system loaded');
        });
    </script>
    <script src="tanggal_monitoring_pendaftaran.js"></script>
</body>

</html>