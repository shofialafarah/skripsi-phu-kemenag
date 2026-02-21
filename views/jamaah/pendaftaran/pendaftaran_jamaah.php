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

$pendaftar = null; // Inisialisasi variabel untuk menampung data pendaftaran
$status_verifikasi = null; // Inisialisasi status

if (isset($_SESSION['id_jamaah'])) {
    $id_jamaah = $_SESSION['id_jamaah'];

    // Query untuk mengambil data pendaftaran berdasarkan id_jamaah
    $query = "SELECT * FROM pendaftaran WHERE id_jamaah = ? LIMIT 1";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_jamaah);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil data jika ada
    if ($result->num_rows > 0) {
        $pendaftar = $result->fetch_assoc();
        $status_verifikasi = explode(' - ', $pendaftar['status_verifikasi'])[0];
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pendaftaran', 'Melihat data pendaftaran');
    }

    $stmt->close();
} else {
    // Jika session tidak ada, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Set default target date untuk countdown
$target_date = date('Y-m-d H:i:s', strtotime('+30 days'));

//Jika Staf mengupload dokumen, maka tombol cetak berfungsi
$file_path = isset($pendaftar['upload_doc']) ? $pendaftar['upload_doc'] : null;
?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../includes/header_jamaah.php'; ?>
        <main class="pendaftaran-wrapper">
            <div class="pendaftaran">
                <div class="pendaftaran-header" style="background-color: #1b5e20; color: white; margin-bottom:30px;">
                    <div class="header-section">
                        <div class="header-content">
                            <h1 class="header-title">
                                <i class="fas fa-kaaba"></i>
                                Data Pendaftaran Haji Reguler
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="pendaftaran-body" style="color: #1b5e20;">
                    <?php if ($pendaftar): ?>
                        <!-- Info Grid -->
                        <div class="info-grid">
                            <div class="info-card data-pribadi-group">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="card-title">Data Pribadi</div>
                                </div>

                                <div class="data-pribadi-flex">
                                    <!-- Kiri -->
                                    <div class="data-pribadi-left">
                                        <div class="info-item">
                                            <span class="info-label">
                                                <?= !empty($pendaftar['nomor_porsi']) ? 'Nomor Porsi' : 'ID Pendaftar' ?>
                                            </span>
                                            <span class="status-badge status-completed">
                                                <?= !empty($pendaftar['nomor_porsi']) ? htmlspecialchars($pendaftar['nomor_porsi']) : (isset($pendaftar['id_pendaftaran']) ? htmlspecialchars($pendaftar['id_pendaftaran']) : '-') ?>
                                            </span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Nama Lengkap</span>
                                            <span class="info-value">: <?= isset($pendaftar['nama_jamaah']) ? htmlspecialchars($pendaftar['nama_jamaah']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Tempat, Tanggal Lahir</span>
                                            <span class="info-value">:
                                                <?=
                                                (isset($pendaftar['tempat_lahir']) ? htmlspecialchars($pendaftar['tempat_lahir']) : '-')
                                                    . ', ' .
                                                    (isset($pendaftar['tanggal_lahir']) ? date('d/m/Y', strtotime($pendaftar['tanggal_lahir'])) : '-')
                                                ?>
                                            </span>
                                        </div>

                                        <div class="info-item">
                                            <span class="info-label">NIK/ No. KTP</span>
                                            <span class="info-value">: <?= isset($pendaftar['nik']) ? htmlspecialchars($pendaftar['nik']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Nama Ayah</span>
                                            <span class="info-value">: <?= isset($pendaftar['nama_ayah']) ? htmlspecialchars($pendaftar['nama_ayah']) : '-' ?></span>
                                        </div>
                                    </div>

                                    <!-- Kanan -->
                                    <div class="data-pribadi-right">
                                        <div class="info-item">
                                            <span class="info-label">Pendidikan</span>
                                            <span class="info-value">: <?= isset($pendaftar['pendidikan']) ? htmlspecialchars($pendaftar['pendidikan']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Pekerjaan</span>
                                            <span class="info-value">: <?= isset($pendaftar['pekerjaan']) ? htmlspecialchars($pendaftar['pekerjaan']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Kewarganegaraan</span>
                                            <span class="info-value">: <?= isset($pendaftar['kewarganegaraan']) ? htmlspecialchars($pendaftar['kewarganegaraan']) : '-' ?></span>
                                        </div>


                                    </div>
                                </div>
                            </div>


                            <!-- Data Lainnya -->
                            <div class="info-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="card-title">Data Lainnya</div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Jenis Kelamin</span>
                                    <span class="info-value">: <?= isset($pendaftar['jenis_kelamin']) ? htmlspecialchars($pendaftar['jenis_kelamin']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Golongan Darah</span>
                                    <span class="info-value">: <?= isset($pendaftar['goldar']) ? htmlspecialchars($pendaftar['goldar']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Bentuk Wajah</span>
                                    <span class="info-value">: <?= isset($pendaftar['wajah']) ? htmlspecialchars($pendaftar['wajah']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tinggi Badan</span>
                                    <span class="info-value">: <?= isset($pendaftar['tinggi_badan']) ? htmlspecialchars($pendaftar['tinggi_badan']) : '-' ?> cm</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Berat Badan</span>
                                    <span class="info-value">: <?= isset($pendaftar['berat_badan']) ? htmlspecialchars($pendaftar['berat_badan']) : '-' ?> kg</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Rambut</span>
                                    <span class="info-value">: <?= isset($pendaftar['rambut']) ? htmlspecialchars($pendaftar['rambut']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Alis</span>
                                    <span class="info-value">: <?= isset($pendaftar['alis']) ? htmlspecialchars($pendaftar['alis']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Hidung</span>
                                    <span class="info-value">: <?= isset($pendaftar['hidung']) ? htmlspecialchars($pendaftar['hidung']) : '-' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="info-grid">
                            <!-- Kontak & Alamat -->
                            <div class="info-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-address-card"></i>
                                    </div>
                                    <div class="card-title">Kontak & Alamat</div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Telp. Rumah</span>
                                    <span class="info-value">: <?= isset($pendaftar['telp_rumah']) ? htmlspecialchars($pendaftar['telp_rumah']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Handphone</span>
                                    <span class="info-value">: <?= isset($pendaftar['no_telepon']) ? htmlspecialchars($pendaftar['no_telepon']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Alamat Tinggal</span>
                                    <span class="info-value">: <?= isset($pendaftar['alamat']) ? htmlspecialchars($pendaftar['alamat']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kecamatan</span>
                                    <span class="info-value">: <?= isset($pendaftar['kecamatan']) ? htmlspecialchars($pendaftar['kecamatan']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kelurahan</span>
                                    <span class="info-value">: <?= isset($pendaftar['kelurahan']) ? htmlspecialchars($pendaftar['kelurahan']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kode Pos</span>
                                    <span class="info-value">: <?= isset($pendaftar['kode_pos']) ? htmlspecialchars($pendaftar['kode_pos']) : '-' ?></span>
                                </div>
                            </div>

                            <!-- Status & Pembayaran -->
                            <div class="info-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="card-title">Status & Pembayaran</div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Perkawinan</span>
                                    <span class="info-value">: <?= isset($pendaftar['status_perkawinan']) ? htmlspecialchars($pendaftar['status_perkawinan']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Pernah Pergi Haji</span>
                                    <span class="info-value">: <?= isset($pendaftar['status_pergi_haji']) ? htmlspecialchars($pendaftar['status_pergi_haji']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tanggal Pengajuan</span>
                                    <span class="info-value">
                                        <span class="status-badge status-paid">
                                            <i class="fas fa-check-circle"></i>
                                            <?= isset($pendaftar['tanggal_pengajuan']) ? date('d-m-Y', strtotime($pendaftar['tanggal_pengajuan'])) : 'Belum Lunas' ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Setoran Awal</span>
                                    <span class="info-value">: <?= isset($pendaftar['setoran_awal']) ? 'Rp ' . number_format($pendaftar['setoran_awal'], 0, ',', '.') : 'Rp 0' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Total Setoran</span>
                                    <span class="info-value">: <?= isset($pendaftar['total_setoran']) ? 'Rp ' . number_format($pendaftar['total_setoran'], 0, ',', '.') : 'Rp 0' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Sisa</span>
                                    <span class="info-value">: <?= isset($pendaftar['sisa_pembayaran']) ? 'Rp ' . number_format($pendaftar['sisa_pembayaran'], 0, ',', '.') : 'Rp 0' ?></span>
                                </div>
                                <?php
                                // Tentukan status dari database
                                $db_status = isset($pendaftar['status']) ? $pendaftar['status'] : null;

                                $tampilan_status = '';
                                $class_badge = '';

                                if ($db_status === 'valid') {
                                    $tampilan_status = 'Lengkap';
                                    $class_badge = 'status-completed'; // Kelas untuk status "Lengkap"
                                } elseif ($db_status === 'pending') {
                                    $tampilan_status = 'Pending';
                                    $class_badge = 'status-pending'; // Kelas untuk status "Pending"
                                } else {
                                    // Ini mencakup status 'Tidak Valid' atau null (belum ada status)
                                    $tampilan_status = 'Belum Lengkap';
                                    $class_badge = 'status-incomplete'; // Kelas untuk status "Belum Lengkap"
                                }
                                ?>

                                <div class="info-item">
                                    <span class="info-label">Status Dokumen</span>
                                    <span class="info-value">
                                        <span class="status-badge <?= $class_badge ?>">
                                            <i class="fas fa-folder"></i>
                                            <?= htmlspecialchars($tampilan_status) ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tanggal Validasi</span>
                                    <span class="info-value">: <?= isset($pendaftar['tanggal_validasi']) ? date('d-m-Y', strtotime($pendaftar['tanggal_validasi'])) : '-' ?></span>
                                </div>
                            </div>
                        </div>



                </div>
                <!-- Documents Section -->
                <div class="progress-section">
                    <?php
                        // Fungsi untuk menentukan status dokumen - VERSI SEDERHANA
                        function getDocumentStatus($status_value)
                        {
                            $status = strtolower(trim((string)$status_value));

                            if (strpos($status, 'terverifikasi') !== false) {
                                return 'Terverifikasi';
                            } elseif (strpos($status, 'unggah ulang') !== false) {
                                return 'Unggah Ulang';
                            } else {
                                return 'Pending';
                            }
                        }

                        // Fungsi untuk menampilkan badge Bootstrap - DIPERBAIKI
                        function badgeStatus($status_value)
                        {
                            $status = getDocumentStatus($status_value);

                            switch ($status) {
                                case 'Terverifikasi':
                                    return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Terverifikasi</span>';
                                case 'Unggah Ulang':
                                    return '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Perlu Diunggah Ulang</span>';
                                default: // Pending 
                                    return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>';
                            }
                        }
                        // Fungsi untuk mendapatkan catatan penolakan (jika ada)
                        function getCatatanPenolakan($status_value)
                        {
                            $status = trim((string)$status_value);

                            if (strpos($status, 'Unggah Ulang - ') !== false) {
                                $parts = explode(' - ', $status, 2);
                                return isset($parts[1]) ? $parts[1] : '';
                            }

                            return '';
                        }

                        // Fungsi untuk menentukan apakah dokumen bisa diedit/upload ulang
                        function canReupload($status_value)
                        {
                            $status = getDocumentStatus($status_value);
                            return ($status == 'Unggah Ulang' || $status == 'Pending');
                        }
                    ?>
                    <div class="progress-title">
                        <i class="fas fa-file-alt"></i>
                        Status Dokumen Persyaratan
                    </div>
                    <div class="documents-grid">
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas fa-receipt"></i> <!-- Bukti Setor Awal -->
                            </div>
                            <div class="document-name">Bukti Setor Awal</div>
                            <div class="document-status">
                                <?= badgeStatus($pendaftar['dokumen_setor_awal_status']) ?>
                                <?php
                                $catatan = getCatatanPenolakan($pendaftar['dokumen_setor_awal_status']);
                                if (!empty($catatan)):
                                ?>
                                    <div class="catatan-penolakan mt-1">
                                        <small class="text-danger">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($catatan) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="document-name">KTP/KIA</div>
                            <div class="document-status">
                                <?= badgeStatus($pendaftar['dokumen_ktp_status']) ?>
                                <?php
                                $catatan = getCatatanPenolakan($pendaftar['dokumen_ktp_status']);
                                if (!empty($catatan)):
                                ?>
                                    <div class="catatan-penolakan mt-1">
                                        <small class="text-danger">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($catatan) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas fa-users"></i> <!-- Kartu Keluarga -->
                            </div>
                            <div class="document-name">Kartu Keluarga</div>
                            <div class="document-status">
                                <?= badgeStatus($pendaftar['dokumen_kk_status']) ?>
                                <?php
                                $catatan = getCatatanPenolakan($pendaftar['dokumen_kk_status']);
                                if (!empty($catatan)):
                                ?>
                                    <div class="catatan-penolakan mt-1">
                                        <small class="text-danger">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($catatan) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas fa-folder-open"></i> <!-- Dokumen Lainnya -->
                            </div>
                            <div class="document-name">Dokumen Lainnya</div>
                            <div class="document-status">
                                <?= badgeStatus($pendaftar['dokumen_lain_status']) ?>
                                <?php
                                $catatan = getCatatanPenolakan($pendaftar['dokumen_lain_status']);
                                if (!empty($catatan)):
                                ?>
                                    <div class="catatan-penolakan mt-1">
                                        <small class="text-danger">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($catatan) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas fa-camera"></i> <!-- Foto Wajah -->
                            </div>
                            <div class="document-name">Foto Wajah</div>
                            <div class="document-status">
                                <?= badgeStatus($pendaftar['foto_wajah_status']) ?>
                                <?php
                                $catatan = getCatatanPenolakan($pendaftar['foto_wajah_status']);
                                if (!empty($catatan)):
                                ?>
                                    <div class="catatan-penolakan mt-1">
                                        <small class="text-danger">
                                            <i class="fas fa-info-circle"></i> <?= htmlspecialchars($catatan) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="<?= $status_verifikasi == 'Disetujui' ? $file_path : '#' ?>"
                        class="btn btn-primary <?= $status_verifikasi != 'Disetujui' ? 'disabled' : '' ?>"
                        <?= $status_verifikasi != 'Disetujui'
                            ? 'onclick="return false;" title="Data belum disetujui"'
                            : 'target="_blank"' ?>>
                        <i class="fas fa-print"></i>
                        Cetak Data
                    </a>

                    <a href="includes/edit_pendaftaran.php?id_pendaftaran=<?= $pendaftar['id_pendaftaran'] ?>" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Edit Data
                    </a>
                </div>

                <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h4>Data Pendaftaran Tidak Ditemukan</h4>
                    <p>Silakan daftar terlebih dahulu.</p>
                    <a href="includes/tambah_pendaftaran.php" class="btn btn-primary">Daftar Sekarang</a>
                </div>
                <?php endif; ?>
                <?php include_once __DIR__ . '/../includes/footer_jamaah.php'; ?>
            </div>
        </main>
    </div>

    <script src="../assets/js/sidebar.js"></script>
    <?php include_once __DIR__ . '/../includes/link_script.php'; ?>
    <script src="assets/js/jamaah.js"></script>

    </body>

    </html>