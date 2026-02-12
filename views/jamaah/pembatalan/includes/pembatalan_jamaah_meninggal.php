<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include '../../../../includes/koneksi.php';
include '../../../partials/fungsi.php';

$batal = null; // Inisialisasi variabel untuk menampung data pembatalan meninggal
$status_verifikasi = null; // Inisialisasi status

if (isset($_SESSION['id_jamaah'])) {
    $id_jamaah = $_SESSION['id_jamaah'];

    // Query gabungan
    $query = "
        SELECT pe.*, p.tanggal_pengajuan, p.kategori, p.tanggal_validasi, p.status_verifikasi
        FROM pembatalan_meninggal pe
        JOIN pembatalan p ON pe.id_pembatalan = p.id_pembatalan
        WHERE p.id_jamaah = ?
        ORDER BY p.tanggal_pengajuan DESC 
        LIMIT 1
    ";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_jamaah);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ Catat aktivitas hanya jika data berhasil ditemukan
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pembatalan', 'Melihat data pembatalan meninggal dunia');
        $batal = $result->fetch_assoc();
        $status_verifikasi = explode(' - ', $batal['status_verifikasi'])[0]; // Ambil status utama
    }

    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}

// Cek dokumen KTP misalnya
$folder = '../assets/berkas/pengajuan/ktp/';
$file_path = $folder . basename($batal['dokumen_ktp'] ?? '');
$dokumen_ktp_ada = !empty($batal['dokumen_ktp']) && file_exists($file_path);

?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once '../../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once '../../includes/header_jamaah.php'; ?>

        <main class="pPembatalan-wrapper">
            <div class="pPendaftaran">
                <div class="pPembatalan-header" style="background-color: #1b5e20; color: white;">
                    <div class="header-section">
                        <div class="header-content">
                            <h1 class="header-title">
                                <i class="fas fa-kaaba"></i>
                                Data Pembatalan Haji Meninggal Dunia
                            </h1>
                        </div>
                    </div>
                </div>
                <div class="pPembatalan-body" style="color: #1b5e20;">
                    <?php if ($batal): ?>
                        <!-- Info Grid -->
                        <div class="info-grid">
                            <div class="info-card data-pribadi-group">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="card-title">Data Ahli Waris</div>
                                </div>

                                <div class="data-pribadi-flex">
                                    <!-- Kiri -->
                                    <div class="data-pribadi-left">
                                        <div class="info-item">
                                            <span class="info-label">Nama Ahli Waris</span>
                                            <span class="info-value">: <?= isset($batal['nama_ahliwaris']) ? htmlspecialchars($batal['nama_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Tempat Lahir</span>
                                            <span class="info-value">: <?= isset($batal['tempat_lahir_ahliwaris']) ? htmlspecialchars($batal['tempat_lahir_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Tanggal Lahir</span>
                                            <span class="info-value">: <?= isset($batal['tanggal_lahir_ahliwaris']) ? htmlspecialchars($batal['tanggal_lahir_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Jenis Kelamin</span>
                                            <span class="info-value">: <?= isset($batal['jenis_kelamin_ahliwaris']) ? htmlspecialchars($batal['jenis_kelamin_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Pekerjaan</span>
                                            <span class="info-value">: <?= isset($batal['pekerjaan_ahliwaris']) ? htmlspecialchars($batal['pekerjaan_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">No. Telepon</span>
                                            <span class="info-value">: <?= isset($batal['no_telepon_ahliwaris']) ? htmlspecialchars($batal['no_telepon_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Nama Bank</span>
                                            <span class="info-value">: <?= isset($batal['bank_ahliwaris']) ? htmlspecialchars($batal['bank_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">No. Rekening</span>
                                            <span class="info-value">: <?= isset($batal['no_rekening_ahliwaris']) ? htmlspecialchars($batal['no_rekening_ahliwaris']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Status dgn Jamaah</span>
                                            <span class="info-value">: <?= isset($batal['status_dengan_jamaah']) ? htmlspecialchars($batal['status_dengan_jamaah']) : '-' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat & Status Pembatalan -->
                            <div class="info-card">
                                <div class="card-header">
                                    <div class="card-icon">
                                        <i class="fas fa-kaaba"></i>
                                    </div>
                                    <div class="card-title">Alamat & Status Pembatalan</div>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Alamat</span>
                                    <span class="info-value">: <?= isset($batal['alamat_ahliwaris']) ? htmlspecialchars($batal['alamat_ahliwaris']) : '-' ?></span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Kecamatan</span>
                                    <span class="info-value">: <?= isset($batal['kecamatan_ahliwaris']) ? htmlspecialchars($batal['kecamatan_ahliwaris']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kelurahan/Desa</span>
                                    <span class="info-value">: <?= isset($batal['kelurahan_ahliwaris']) ? htmlspecialchars($batal['kelurahan_ahliwaris']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kode Pos</span>
                                    <span class="info-value">: <?= isset($batal['kode_pos_ahliwaris']) ? htmlspecialchars($batal['kode_pos_ahliwaris']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kategori</span>
                                    <span class="info-value">
                                        <span class="status-badge status-paid">
                                            <?= isset($batal['kategori']) ? htmlspecialchars($batal['kategori']) : 'Belum Memilih' ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Uang Pengembalian</span>
                                    <span class="info-value">: <?= isset($batal['total_setoran']) ? 'Rp ' . number_format($batal['total_setoran'], 0, ',', '.') : 'Rp 0' ?></span>
                                </div>

                                <?php $status_dokumen = (!isset($batal['tanggal_validasi']) || $batal['tanggal_validasi'] == null || $batal['tanggal_validasi'] == '-')
                                    ? 'Belum Valid'
                                    : 'Valid';
                                ?>
                                <div class="info-item">
                                    <span class="info-label">Dokumen</span>
                                    <span class="info-value">
                                        <span class="status-badge <?= $status_dokumen === 'Valid' ? 'status-completed' : 'status-pending' ?>">
                                            <i class="fa-solid fa-folder"></i>
                                            <?= htmlspecialchars($status_dokumen) ?>
                                        </span>
                                    </span>
                                </div>

                                <div class="info-item">
                                    <span class="info-label">Tanggal Pengajuan</span>
                                    <span class="info-value">
                                        : <?= isset($batal['tanggal_pengajuan']) ? date('d-m-Y', strtotime($batal['tanggal_pengajuan'])) : '-' ?>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Tanggal Validasi</span>
                                    <span class="info-value">: <?= isset($batal['tanggal_validasi']) ? date('d-m-Y', strtotime($batal['tanggal_validasi'])) : '-' ?>
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
                                    return 'Menunggu Verifikasi';
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
                                        return '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Unggah Ulang</span>';
                                    default: // Menunggu Verifikasi 
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
                                return ($status == 'Unggah Ulang' || $status == 'Menunggu Verifikasi');
                            }
                            ?>
                            <div class="progress-title">
                                <i class="fas fa-file-alt"></i>
                                Status Dokumen Persyaratan
                            </div>
                            <div class="documents-grid">
                                <div class="document-item">
                                    <div class="document-icon">
                                        <i class="fas fa-file-medical"></i> <!-- Akta Kematian -->
                                    </div>
                                    <div class="document-name">Akta Kematian</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_akta_kematian_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_akta_kematian_status']);
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
                                        <i class="fas fa-receipt"></i> <!-- Bukti Setor Awal -->
                                    </div>
                                    <div class="document-name">Bukti Setor Awal</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_setor_awal_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_setor_awal_status']);
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
                                        <i class="fas fa-file-signature"></i> <!-- SPPH -->
                                    </div>
                                    <div class="document-name">SPPH</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_spph_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_spph_status']);
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
                                        <i class="fas fa-user-friends"></i> <!-- Surat Keterangan Ahli Waris -->
                                    </div>
                                    <div class="document-name">Surat Keterangan Ahli Waris</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_ahli_waris_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_ahli_waris_status']);
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
                                        <i class="fas fa-handshake"></i> <!-- Surat Kuasa -->
                                    </div>
                                    <div class="document-name">Surat Kuasa</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_surat_kuasa_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_surat_kuasa_status']);
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
                                        <i class="fas fa-id-card"></i> <!-- KTP Ahli Waris -->
                                    </div>
                                    <div class="document-name">KTP Ahli Waris</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_ktp_ahliwaris_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_ktp_ahliwaris_status']);
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
                                        <i class="fas fa-id-card-alt"></i> <!-- KTP Penerima Kuasa -->
                                    </div>
                                    <div class="document-name">KTP Penerima Kuasa</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_ktp_penerima_kuasa_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_ktp_penerima_kuasa_status']);
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
                                        <i class="fas fa-users"></i> <!-- KK Penerima Kuasa -->
                                    </div>
                                    <div class="document-name">KK Penerima Kuasa</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_kk_penerima_kuasa_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_kk_penerima_kuasa_status']);
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
                                        <i class="fas fa-baby"></i> <!-- Akta Kelahiran -->
                                    </div>
                                    <div class="document-name">Akta Kelahiran</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_akta_kelahiran_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_akta_kelahiran_status']);
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
                                        <i class="fas fa-book-open"></i> <!-- Buku Nikah -->
                                    </div>
                                    <div class="document-name">Buku Nikah</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_buku_nikah_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_buku_nikah_status']);
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
                                        <i class="fas fa-university"></i> <!-- Buku Rekening Penerima Kuasa -->
                                    </div>
                                    <div class="document-name">Buku Rekening Penerima Kuasa</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_rekening_kuasa_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_rekening_kuasa_status']);
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
                                    <div class="document-name">Foto Wajah (80%)</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['foto_wajah_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['foto_wajah_status']);
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
                            <a href="<?= $batal['status_verifikasi'] == 'Disetujui' ? 'cetak_pembatalan_meninggal.php?id=' . $batal['id_batal_meninggal'] : '#' ?>"
                                class="btn btn-primary <?= $batal['status_verifikasi'] != 'Disetujui' ? 'disabled' : '' ?>"
                                <?= $batal['status_verifikasi'] != 'Disetujui' ? 'onclick="return false;" title=\'Data belum disetujui\'' : 'target="_blank"' ?>>
                                <i class="fas fa-print"></i> Cetak Data
                            </a>


                            <a href="pembatalan-meninggal/edit_pembatalan_meninggal_dunia.php?id_pembatalan=<?= $batal['id_pembatalan'] ?>" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>Data Pembatalan Tidak Ditemukan</h4>
                            <p>Silakan daftar terlebih dahulu.</p>
                            <a href="../tambah_pembatalan.php" class="btn btn-primary">Daftar Sekarang</a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php include_once __DIR__ . '/../../includes/footer_jamaah.php'; ?>
            </div>
        </main>
    </div>
</div>
<script src="../../assets/js/sidebar.js"></script>
<script src="../../assets/js/jamaah.js"></script>

</body>

</html>