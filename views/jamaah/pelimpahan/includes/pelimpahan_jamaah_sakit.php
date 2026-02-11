<?php
session_start();
include '../../../../includes/koneksi.php';
include '../../../partials/fungsi.php';

$limpah = null; // Inisialisasi variabel untuk menampung data pelimpahan
$status_verifikasi = null; // Inisialisasi status

if (isset($_SESSION['id_jamaah'])) {
    $id_jamaah = $_SESSION['id_jamaah'];

    // Query gabungan
    $query = "
        SELECT ps.*, p.tanggal_pengajuan, p.kategori, p.tanggal_validasi, p.status_verifikasi
        FROM pelimpahan_sakit ps
        JOIN pelimpahan p ON ps.id_pelimpahan = p.id_pelimpahan
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
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pelimpahan', 'Melihat data pelimpahan sakit permanen');
        $limpah = $result->fetch_assoc();
        $status_verifikasi = explode(' - ', $limpah['status_verifikasi'])[0]; // Ambil status utama
    }

    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}


// Cek dokumen KTP misalnya
$folder = 'uploads/pembatalan/pengajuan/ktp/';
$file_path = $folder . basename($limpah['dokumen_ktp'] ?? '');
$dokumen_ktp_ada = !empty($limpah['dokumen_ktp']) && file_exists($file_path);

?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once '../../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once '../../includes/header_jamaah.php'; ?>

            <main class="pPelimpahan-wrapper">
                <div class="pPendaftaran">
                    <div class="pPelimpahan-header" style="background-color: #1b5e20; color: white;">
                        <div class="header-section">
                            <div class="header-content">
                                <h1 class="header-title">
                                    <i class="fas fa-kaaba"></i>
                                    Data Pelimpahan Haji Sakit Permanen
                                </h1>
                            </div>
                        </div>
                    </div>
                    <div class="pPelimpahan-body" style="color: #1b5e20;">
                        <?php if ($limpah): ?>
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
                                                <span class="info-value">: <?= isset($limpah['nama_ahliwaris']) ? htmlspecialchars($limpah['nama_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Nama Ayah</span>
                                                <span class="info-value">: <?= isset($limpah['nama_ayah_ahliwaris']) ? htmlspecialchars($limpah['nama_ayah_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Tempat Lahir</span>
                                                <span class="info-value">: <?= isset($limpah['tempat_lahir_ahliwaris']) ? htmlspecialchars($limpah['tempat_lahir_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Tanggal Lahir</span>
                                                <span class="info-value">: <?= isset($limpah['tanggal_lahir_ahliwaris']) ? htmlspecialchars($limpah['tanggal_lahir_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Jenis Kelamin</span>
                                                <span class="info-value">: <?= isset($limpah['jenis_kelamin_ahliwaris']) ? htmlspecialchars($limpah['jenis_kelamin_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Pekerjaan</span>
                                                <span class="info-value">: <?= isset($limpah['pekerjaan_ahliwaris']) ? htmlspecialchars($limpah['pekerjaan_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">No. Telepon</span>
                                                <span class="info-value">: <?= isset($limpah['no_telepon_ahliwaris']) ? htmlspecialchars($limpah['no_telepon_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Nama Bank</span>
                                                <span class="info-value">: <?= isset($limpah['bank_ahliwaris']) ? htmlspecialchars($limpah['bank_ahliwaris']) : '-' ?></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">No. Rekening</span>
                                                <span class="info-value">: <?= isset($limpah['no_rekening_ahliwaris']) ? htmlspecialchars($limpah['no_rekening_ahliwaris']) : '-' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat & Status Pelimpahan -->
                                <div class="info-card">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-kaaba"></i>
                                        </div>
                                        <div class="card-title">Alamat & Status Pelimpahan</div>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Alamat</span>
                                        <span class="info-value">: <?= isset($limpah['alamat_ahliwaris']) ? htmlspecialchars($limpah['alamat_ahliwaris']) : '-' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Kecamatan</span>
                                        <span class="info-value">: <?= isset($limpah['kecamatan_ahliwaris']) ? htmlspecialchars($limpah['kecamatan_ahliwaris']) : '-' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Kelurahan/Desa</span>
                                        <span class="info-value">: <?= isset($limpah['kelurahan_ahliwaris']) ? htmlspecialchars($limpah['kelurahan_ahliwaris']) : '-' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Kode Pos</span>
                                        <span class="info-value">: <?= isset($limpah['kode_pos_ahliwaris']) ? htmlspecialchars($limpah['kode_pos_ahliwaris']) : '-' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Status dgn Jamaah</span>
                                        <span class="info-value">: <?= isset($limpah['status_dengan_jamaah']) ? htmlspecialchars($limpah['status_dengan_jamaah']) : '-' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Kategori</span>
                                        <span class="info-value">
                                            <span class="status-badge status-paid">
                                                <?= isset($limpah['kategori']) ? htmlspecialchars($limpah['kategori']) : 'Belum Memilih' ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Uang Pengembalian</span>
                                        <span class="info-value">: <?= isset($limpah['total_setoran']) ? 'Rp ' . number_format($limpah['total_setoran'], 0, ',', '.') : 'Rp 0' ?></span>
                                    </div>
                                    <?php $status_dokumen = (!isset($limpah['tanggal_validasi']) || $limpah['tanggal_validasi'] == null || $limpah['tanggal_validasi'] == '-')
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
                                        <?= isset($limpah['tanggal_pengajuan']) ? date('d-m-Y', strtotime($limpah['tanggal_pengajuan'])) : '-' ?>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Tanggal Validasi</span>
                                        <span class="info-value">: <?= isset($limpah['tanggal_validasi']) ? date('d-m-Y', strtotime($limpah['tanggal_validasi'])) : '-' ?>
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
                                            <?= badgeStatus($limpah['dokumen_surat_sakit_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_surat_sakit_status']);
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
                                            <?= badgeStatus($limpah['dokumen_setor_awal_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_setor_awal_status']);
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
                                            <?= badgeStatus($limpah['dokumen_spph_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_spph_status']);
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
                                            <?= badgeStatus($limpah['dokumen_ahli_waris_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_ahli_waris_status']);
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
                                            <?= badgeStatus($limpah['dokumen_surat_kuasa_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_surat_kuasa_status']);
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
                                            <?= badgeStatus($limpah['dokumen_ktp_ahliwaris_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_ktp_ahliwaris_status']);
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
                                            <?= badgeStatus($limpah['dokumen_ktp_penerima_kuasa_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_ktp_penerima_kuasa_status']);
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
                                            <?= badgeStatus($limpah['dokumen_kk_penerima_kuasa_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_kk_penerima_kuasa_status']);
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
                                            <?= badgeStatus($limpah['dokumen_akta_kelahiran_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_akta_kelahiran_status']);
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
                                            <?= badgeStatus($limpah['dokumen_buku_nikah_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_buku_nikah_status']);
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
                                            <?= badgeStatus($limpah['dokumen_rekening_kuasa_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['dokumen_rekening_kuasa_status']);
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
                                            <?= badgeStatus($limpah['foto_wajah_status']) ?>
                                            <?php
                                            $catatan = getCatatanPenolakan($limpah['foto_wajah_status']);
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
                                <a href="<?= $limpah['status_verifikasi'] == 'Disetujui' ? 'cetak_pelimpahan_sakit.php?id=' . $limpah['id_limpah_sakit'] : '#' ?>"
                                    class="btn btn-primary <?= $limpah['status_verifikasi'] != 'Disetujui' ? 'disabled' : '' ?>"
                                    <?= $limpah['status_verifikasi'] != 'Disetujui' ? 'onclick="return false;" title=\'Data belum disetujui\'' : 'target="_blank"' ?>>
                                    <i class="fas fa-print"></i> Cetak Data
                                </a>

                                <a href="edit_pelimpahan_sakit_permanen.php?id_pelimpahan=<?= $limpah['id_pelimpahan'] ?>" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i> Edit Data
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h4>Data Pelimpahan Tidak Ditemukan</h4>
                                <p>Silakan daftar terlebih dahulu.</p>
                                <a href="tambah_pelimpahan.php" class="btn btn-primary">Daftar Sekarang</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="footer" style="color: white; text-align: center;">
                        <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                    </div>
                </div>

                <!-- Floating Help Button -->
                <div class="floating-help" onclick="alert('Hubungi Call Center Haji: 14045 atau WhatsApp: 0812-3456-7890')">
                    <i class="fas fa-question"></i>
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