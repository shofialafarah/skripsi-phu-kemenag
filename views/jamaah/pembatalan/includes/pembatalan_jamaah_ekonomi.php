<?php
session_start();
include '../../../../includes/koneksi.php';
include '../../../partials/fungsi.php';

$batal = null; // Inisialisasi variabel untuk menampung data pembatalan meninggal

if (isset($_SESSION['id_jamaah'])) {
    $id_jamaah = $_SESSION['id_jamaah'];

    // Query gabungan
    $query = "
        SELECT pe.*, p.tanggal_pengajuan, p.kategori, p.status_verifikasi, p.tanggal_validasi
        FROM pembatalan_ekonomi pe
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
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pembatalan', 'Melihat data pembatalan keperluan ekonomi');
        $batal = $result->fetch_assoc();
        $status_verifikasi = explode(' - ', $batal['status_verifikasi'])[0]; // Ambil status utama
    }

    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}

// Cek dokumen KTP misalnya
$folder = 'uploads/pembatalan/pengajuan/ktp/';
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
                                Data Pembatalan Haji Keperluan Ekonomi
                            </h1>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <div class="pPembatalan-body" style="color: #1b5e20;">
                    <?php if ($batal): ?>
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
                                            <span class="info-label">Nama Jamaah</span>
                                            <span class="info-value">: <?= isset($batal['nama_jamaah']) ? htmlspecialchars($batal['nama_jamaah']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Tempat Lahir</span>
                                            <span class="info-value">: <?= isset($batal['tempat_lahir']) ? htmlspecialchars($batal['tempat_lahir']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Tanggal Lahir</span>
                                            <span class="info-value">: <?= isset($batal['tanggal_lahir']) ? date('d-m-Y', strtotime($batal['tanggal_lahir'])) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Jenis Kelamin</span>
                                            <span class="info-value">: <?= isset($batal['jenis_kelamin']) ? htmlspecialchars($batal['jenis_kelamin']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Pekerjaan</span>
                                            <span class="info-value">: <?= isset($batal['pekerjaan']) ? htmlspecialchars($batal['pekerjaan']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">No. Telepon</span>
                                            <span class="info-value">: <?= isset($batal['no_telepon']) ? htmlspecialchars($batal['no_telepon']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Nama Bank</span>
                                            <span class="info-value">: <?= isset($batal['bps']) ? htmlspecialchars($batal['bps']) : '-' ?></span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">No. Rekening</span>
                                            <span class="info-value">: <?= isset($batal['nomor_rek']) ? htmlspecialchars($batal['nomor_rek']) : '-' ?></span>
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
                                    <span class="info-value">: <?= isset($batal['alamat']) ? htmlspecialchars($batal['alamat']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kecamatan</span>
                                    <span class="info-value">: <?= isset($batal['kecamatan']) ? htmlspecialchars($batal['kecamatan']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kelurahan/Desa</span>
                                    <span class="info-value">: <?= isset($batal['kelurahan']) ? htmlspecialchars($batal['kelurahan']) : '-' ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Kelurahan/Desa</span>
                                    <span class="info-value">: <?= isset($batal['kode_pos']) ? htmlspecialchars($batal['kode_pos']) : '-' ?></span>
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
                                        : <?= isset($batal['tanggal_pengajuan']) ? date('d-m-Y H:i', strtotime($batal['tanggal_pengajuan'])) : '-' ?>
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
                                        <i class="fas fa-id-card"></i> <!-- KTP -->
                                    </div>
                                    <div class="document-name">KTP</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_ktp_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_ktp_status']);
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
                                        <?= badgeStatus($batal['dokumen_kk_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_kk_status']);
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
                                        <i class="fas fa-university"></i> <!-- Buku Rekening -->
                                    </div>
                                    <div class="document-name">Buku Rekening</div>
                                    <div class="document-status">
                                        <?= badgeStatus($batal['dokumen_rekening_status']) ?>
                                        <?php
                                        $catatan = getCatatanPenolakan($batal['dokumen_rekening_status']);
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
                            <a href="<?= $batal['status_verifikasi'] == 'Disetujui' ? 'cetak_pembatalan_ekonomi.php?id=' . $batal['id_batal_ekonomi'] : '#' ?>"
                                class="btn btn-primary <?= $batal['status_verifikasi'] != 'Disetujui' ? 'disabled' : '' ?>"
                                <?= $batal['status_verifikasi'] != 'Disetujui' ? 'onclick="return false;" title=\'Data belum disetujui\'' : 'target="_blank"' ?>>
                                <i class="fas fa-print"></i> Cetak Data
                            </a>

                            <a href="pembatalan-ekonomi/edit_pembatalan_keperluan_ekonomi.php?id_pembatalan=<?= $batal['id_pembatalan'] ?>" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit Data
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4>Data Pembatalan Tidak Ditemukan</h4>
                            <p>Silakan daftar terlebih dahulu.</p>
                            <a href="tambah_pembatalan.php" class="btn btn-primary">Daftar Sekarang</a>
                        </div>
                    <?php endif; ?>

                    <!-- Floating Help Button -->
                    <div class="floating-help" onclick="alert('Hubungi Call Center Haji: 14045 atau WhatsApp: 0812-3456-7890')">
                        <i class="fas fa-question"></i>
                    </div>
                </div>
                <?php include_once __DIR__ . '/../../includes/footer_jamaah.php'; ?>
            </div>
        </main>
    </div>
</div>
<script src="../../assets/js/sidebar.js"></script>
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

<script src="../../assets/js/jamaah.js"></script>

</body>

</html>