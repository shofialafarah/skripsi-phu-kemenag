<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
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
    ORDER BY p.tanggal_pengajuan DESC
";
$result = $koneksi->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $kategori = $row['kategori'];
        $id_pembatalan = $row['id_pembatalan'];

        if ($kategori === 'Meninggal Dunia') {
            $detail_query = "SELECT * FROM pembatalan_meninggal WHERE id_pembatalan = ?";
        } else {
            $detail_query = "SELECT * FROM pembatalan_ekonomi WHERE id_pembatalan = ?";
        }

        $stmt = $koneksi->prepare($detail_query);
        $stmt->bind_param("i", $id_pembatalan);
        $stmt->execute();
        $detail_result = $stmt->get_result();

        if ($detail_result->num_rows > 0) {
            $detail_data = $detail_result->fetch_assoc();
            $row = array_merge($row, $detail_data);
        }
        $stmt->close();

        if (empty($row['nama_pengaju']) && !empty($row['nama_ahliwaris'])) {
            $row['nama_pengaju'] = $row['nama_ahliwaris'];
        } elseif (empty($row['nama_pengaju'])) {
            $row['nama_pengaju'] = '-';
        }

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

$doc_fields = [
    'Meninggal Dunia' => [
        'dokumen_akta_kematian' => ['label' => 'Akta Kematian', 'status_field' => 'dokumen_akta_kematian_status'],
        'dokumen_setor_awal' => ['label' => 'Setor Awal', 'status_field' => 'dokumen_setor_awal_status'],
        'dokumen_spph' => ['label' => 'SPPH', 'status_field' => 'dokumen_spph_status'],
        'dokumen_ahli_waris' => ['label' => 'Ahli Waris', 'status_field' => 'dokumen_ahli_waris_status'],
        'dokumen_surat_kuasa' => ['label' => 'Surat Kuasa', 'status_field' => 'dokumen_surat_kuasa_status'],
        'dokumen_ktp_ahliwaris' => ['label' => 'KTP Ahli Waris', 'status_field' => 'dokumen_ktp_ahliwaris_status'],
        'dokumen_ktp_penerima_kuasa' => ['label' => 'KTP Penerima', 'status_field' => 'dokumen_ktp_penerima_kuasa_status'],
        'dokumen_kk_penerima_kuasa' => ['label' => 'KK Penerima', 'status_field' => 'dokumen_kk_penerima_kuasa_status'],
        'dokumen_akta_kelahiran' => ['label' => 'Akta Kelahiran', 'status_field' => 'dokumen_akta_kelahiran_status'],
        'dokumen_buku_nikah' => ['label' => 'Buku Nikah', 'status_field' => 'dokumen_buku_nikah_status'],
        'dokumen_rekening_kuasa' => ['label' => 'Rekening Kuasa', 'status_field' => 'dokumen_rekening_kuasa_status'],
        'foto_wajah' => ['label' => 'Foto Wajah', 'status_field' => 'foto_wajah_status']
    ],
    'Keperluan Ekonomi' => [
        'dokumen_setor_awal' => ['label' => 'Setor Awal', 'status_field' => 'dokumen_setor_awal_status'],
        'dokumen_spph' => ['label' => 'SPPH', 'status_field' => 'dokumen_spph_status'],
        'dokumen_ktp' => ['label' => 'KTP', 'status_field' => 'dokumen_ktp_status'],
        'dokumen_kk' => ['label' => 'KK', 'status_field' => 'dokumen_kk_status'],
        'dokumen_akta_kelahiran' => ['label' => 'Akta Kelahiran', 'status_field' => 'dokumen_akta_kelahiran_status'],
        'dokumen_rekening' => ['label' => 'Rekening', 'status_field' => 'dokumen_rekening_status'],
        'foto_wajah' => ['label' => 'Foto Wajah', 'status_field' => 'foto_wajah_status']
    ]
];

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

function isAllDocumentsVerified($row, $doc_fields)
{
    $kategori = $row['kategori'];
    $documents = $doc_fields[$kategori] ?? [];

    foreach ($documents as $field => $info) {
        $file = $row[$field] ?? '';
        $status_field = $info['status_field'] ?? null;
        if (!$status_field) continue;

        if (!empty($file)) {
            $status_raw = $row[$status_field] ?? null;
            $doc_status = getDocumentStatus($status_raw);
            if ($doc_status !== 'Terverifikasi') {
                return false;
            }
        }
    }
    return true;
}

function updateValidationDateIfAllVerified($id_pembatalan, $koneksi, $doc_fields)
{
    // Ambil data pembatalan
    $query = "SELECT kategori FROM pembatalan WHERE id_pembatalan = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_pembatalan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $kategori = $row['kategori'];

        // Ambil data dokumen
        $table = ($kategori === 'Meninggal Dunia') ? 'pembatalan_meninggal' : 'pembatalan_ekonomi';
        $doc_query = "SELECT * FROM $table WHERE id_pembatalan = ?";
        $doc_stmt = $koneksi->prepare($doc_query);
        $doc_stmt->bind_param("i", $id_pembatalan);
        $doc_stmt->execute();
        $doc_result = $doc_stmt->get_result();

        if ($doc_result->num_rows > 0) {
            $doc_data = $doc_result->fetch_assoc();
            $combined_data = array_merge($row, $doc_data);

            if (isAllDocumentsVerified($combined_data, $doc_fields)) {
                // Cek apakah tanggal validasi sudah ada
                $check_query = "SELECT tanggal_validasi FROM pembatalan WHERE id_pembatalan = ?";
                $check_stmt = $koneksi->prepare($check_query);
                $check_stmt->bind_param("i", $id_pembatalan);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $check_data = $check_result->fetch_assoc();

                if (empty($check_data['tanggal_validasi'])) {
                    $tanggal_validasi = date("Y-m-d H:i:s");
                    $update_query = "UPDATE pembatalan SET tanggal_validasi = ? WHERE id_pembatalan = ?";
                    $update_stmt = $koneksi->prepare($update_query);
                    $update_stmt->bind_param("si", $tanggal_validasi, $id_pembatalan);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
                $check_stmt->close();
            } else {
                $reset_query = "UPDATE pembatalan SET tanggal_validasi = NULL WHERE id_pembatalan = ?";
                $reset_stmt = $koneksi->prepare($reset_query);
                $reset_stmt->bind_param("i", $id_pembatalan);
                $reset_stmt->execute();
                $reset_stmt->close();
            }
        }
        $doc_stmt->close();
    }
    $stmt->close();
}

// PERBAIKAN: Handler untuk validasi dokumen individual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate_document'])) {
    error_log(">>> POST: " . print_r($_POST, true));

    $id_pembatalan = clean_input($_POST['id_pembatalan']);
    $document_field = clean_input($_POST['document_field']);
    $validation_status = clean_input($_POST['validation_status']);
    $catatan = isset($_POST['catatan']) ? clean_input($_POST['catatan']) : '';

    // Ambil kategori untuk menentukan tabel yang benar
    $query_kategori = "SELECT kategori FROM pembatalan WHERE id_pembatalan = ?";
    $stmt_kategori = $koneksi->prepare($query_kategori);
    $stmt_kategori->bind_param("i", $id_pembatalan);
    $stmt_kategori->execute();
    $result_kategori = $stmt_kategori->get_result();

    if ($result_kategori->num_rows > 0) {
        $kategori_data = $result_kategori->fetch_assoc();
        $kategori = $kategori_data['kategori'];

        // Pastikan field dokumen valid untuk kategori ini
        if (isset($doc_fields[$kategori][$document_field])) {
            $status_field = $doc_fields[$kategori][$document_field]['status_field'];

            // Tentukan nilai status yang akan disimpan
            $status_value = '';
            if ($validation_status == 'Terverifikasi') {
                $status_value = 'Terverifikasi';
            } elseif ($validation_status == 'Unggah Ulang') {
                $status_value = 'Unggah Ulang';
                if (!empty($catatan)) {
                    $status_value = 'Unggah Ulang - ' . $catatan;
                }
            }

            // Update status dokumen
            $table = ($kategori === 'Meninggal Dunia') ? 'pembatalan_meninggal' : 'pembatalan_ekonomi';
            $query = "UPDATE $table SET $status_field = ? WHERE id_pembatalan = ?";

            $stmt = $koneksi->prepare($query);
            if ($stmt) {
                $stmt->bind_param("si", $status_value, $id_pembatalan);

                if ($stmt->execute()) {
                    // ✅ Catat aktivitas hanya jika data berhasil ditemukan
                    updateAktivitasPengguna($id_staf, 'staf', 'Pembatalan', 'Memperbarui status pembatalan');
                    // Update tanggal validasi jika semua dokumen terverifikasi
                    updateValidationDateIfAllVerified($id_pembatalan, $koneksi, $doc_fields);

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
        } else {
            echo "<script>alert('Error: Field dokumen tidak valid untuk kategori ini'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Error: Data pembatalan tidak ditemukan'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }
    $stmt_kategori->close();
}

// Initialize modal list
$modal_list = [];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Staf</title>
    <link rel="icon" href="logo_kemenag.png">
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

            <main class="mPembatalan-wrapper">
                <div class="mPembatalan">
                    <div class="mPembatalan-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Berkas Menunggu Verifikasi Pembatalan Jamaah Haji
                    </div>
                    <div class="mPembatalan-body">
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

                        <!-- Legend untuk warna kategori -->
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-square text-danger"></i> Meninggal Dunia &nbsp;&nbsp;
                                <i class="fas fa-square text-warning"></i> Keperluan Ekonomi
                            </small>
                        </div>

                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Pengaju</th>
                                    <th>Kategori</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tanggal Validasi</th>
                                    <th class="text-center">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (!empty($data_pembatalan)) {
                                    foreach ($data_pembatalan as $row) {
                                        // Tentukan class untuk warna baris
                                        $row_class = '';
                                        if ($row['kategori'] === 'Meninggal Dunia') {
                                            $row_class = 'kategori-meninggal';
                                        } elseif ($row['kategori'] === 'Keperluan Ekonomi') {
                                            $row_class = 'kategori-sakit';
                                        }

                                        echo "<tr class='$row_class'>";
                                        echo "<td class='text-center'>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_pengaju']) . "</td>";

                                        // Kategori dengan badge berwarna
                                        $badge_class = ($row['kategori'] === 'Meninggal Dunia') ? 'badge-kategori-meninggal' : 'badge-kategori-sakit';
                                        echo "<td><span class='badge $badge_class'>" . htmlspecialchars($row['kategori']) . "</span></td>";

                                        echo "<td>" . date('d-m-Y', strtotime($row['tanggal_pengajuan'])) . "</td>";

                                        // Tanggal validasi
                                        $all_verified = isAllDocumentsVerified($row, $doc_fields);
                                        if ($all_verified && !empty($row['tanggal_validasi'])) {
                                            $tanggal_validasi = date('d-m-Y H:i', strtotime($row['tanggal_validasi']));
                                            echo "<td><span class='badge bg-success'><i class='fas fa-check-circle'></i> " . $tanggal_validasi . "</span></td>";
                                        } else {
                                            echo "<td><span class='text-muted'>-</span></td>";
                                        }

                                        // Kolom dokumen
                                        echo "<td>";

                                        $kategori = $row['kategori'];
                                        $documents = $doc_fields[$kategori] ?? [];

                                        foreach ($documents as $field => $info) {
                                            $file = trim($row[$field] ?? '');
                                            $label = $info['label'] ?? 'Tidak diketahui';
                                            $status_field = $info['status_field'] ?? '';

                                            $field_safe = htmlspecialchars($field);
                                            $modal_id = "modalCatatan_" . $row['id_pembatalan'] . "_" . $field_safe;

                                            // Dapatkan status validasi dari database
                                            $status_raw = $row[$status_field] ?? null;
                                            $doc_status = getDocumentStatus($status_raw);

                                            // Ekstrak catatan jika ada
                                            $catatan_text = '';
                                            if ($doc_status == 'Unggah Ulang' && !empty($status_raw)) {
                                                if (strpos($status_raw, ' - ') !== false) {
                                                    $parts = explode(' - ', $status_raw, 2);
                                                    $catatan_text = $parts[1];
                                                }
                                            }

                                            echo "<div class='mb-2 document-item' data-status='$doc_status'>";
                                            echo "<div class='w-100'>";

                                            if (!empty($file)) {
                                                echo "<div class='d-flex align-items-center gap-2 flex-wrap'>";
                                                // Link untuk melihat dokumen
                                                echo "<a href='{$file}' class='btn btn-sm btn-primary' target='_blank' style='min-width: 120px;'>";
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
                                                    echo "<form method='post' class='d-inline' onsubmit='return confirm(\"Ubah status dokumen $label menjadi TERVERIFIKASI?\");'>";
                                                    echo "<input type='hidden' name='validate_document' value='1'>";
                                                    echo "<input type='hidden' name='id_pembatalan' value='" . $row['id_pembatalan'] . "'>";
                                                    echo "<input type='hidden' name='document_field' value='$field'>";
                                                    echo "<input type='hidden' name='validation_status' value='Terverifikasi'>";
                                                    echo "<button type='submit' class='btn btn-sm btn-outline-success' title='Terverifikasi'>";
                                                    echo "<i class='fas fa-check'></i></button>";
                                                    echo "</form>";
                                                } else { // Menunggu Verifikasi
                                                    echo "<span class='badge bg-warning text-dark'><i class='fas fa-clock'></i> Menunggu Verifikasi</span>";
                                                    echo "<form method='post' class='d-inline' onsubmit='return confirm(\"Tandai dokumen $label sebagai TERVERIFIKASI?\");'>";
                                                    echo "<input type='hidden' name='validate_document' value='1'>";
                                                    echo "<input type='hidden' name='id_pembatalan' value='" . $row['id_pembatalan'] . "'>";
                                                    echo "<input type='hidden' name='document_field' value='$field'>";
                                                    echo "<input type='hidden' name='validation_status' value='Terverifikasi'>";
                                                    echo "<button type='submit' class='btn btn-sm btn-success' title='Terverifikasi'>";
                                                    echo "<i class='fas fa-check'></i></button>";
                                                    echo "</form>";
                                                    echo "<button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#$modal_id' title='Tolak Dokumen'>";
                                                    echo "<i class='fas fa-times'></i></button>";
                                                }
                                                echo "</div>";

                                                // Kumpulkan data modal
                                                $modal_list[] = [
                                                    'id' => $modal_id,
                                                    'label' => $label,
                                                    'field' => $field,
                                                    'id_pembatalan' => $row['id_pembatalan'],
                                                    'current_status' => $doc_status,
                                                    'current_note' => $catatan_text
                                                ];
                                            } else {
                                                echo "<div class='d-flex align-items-center gap-2'>";
                                                echo "<button class='btn btn-sm btn-secondary' disabled style='min-width: 120px;'><i class='fas fa-file-slash'></i> $label</button>";
                                                echo "<span class='text-muted ms-2'>Tidak ada file</span>";
                                                echo "</div>";
                                            }
                                            echo "</div>"; // Tutup div w-100
                                            echo "</div>"; // Tutup div document-item
                                        }
                                        echo "</td>";

                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data berkas pembatalan jamaah haji yang ditemukan.</td></tr>";
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
                        <input type='hidden' name='id_pembatalan' value='{$modal['id_pembatalan']}'>
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

    <!-- Pastikan file JS kustom dimuat setelah semua library -->
    <script src="tanggal_monitoring_pembatalan.js"></script>
</body>

</html>