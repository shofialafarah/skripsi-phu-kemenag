<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include '../../includes/koneksi.php';

// ---------- 1. BELUM diverifikasi (status_verifikasi = 'Pending') ----------
$queryJamaahBelumVerifikasi = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM pendaftaran  WHERE status_verifikasi = 'Pending'
        UNION ALL
        SELECT COUNT(*) AS total FROM pembatalan    WHERE status_verifikasi = 'Pending'
        UNION ALL
        SELECT COUNT(*) AS total FROM pelimpahan    WHERE status_verifikasi = 'Pending'
    ) AS belum_verifikasi
";
$resultJamaahBelumVerifikasi  = $koneksi->query($queryJamaahBelumVerifikasi);
$totalJamaahBelumVerifikasi   = $resultJamaahBelumVerifikasi->fetch_assoc()['total_gabungan'] ?? 0;

// ---------- 2. SUDAH diverifikasi (status_verifikasi = 'Disetujui') ----------
$queryJamaahSudahVerifikasi = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM pendaftaran  WHERE status_verifikasi = 'Disetujui'
        UNION ALL
        SELECT COUNT(*) AS total FROM pembatalan    WHERE status_verifikasi = 'Disetujui'
        UNION ALL
        SELECT COUNT(*) AS total FROM pelimpahan    WHERE status_verifikasi = 'Disetujui'
    ) AS sudah_verifikasi
";
$resultJamaahSudahVerifikasi  = $koneksi->query($queryJamaahSudahVerifikasi);
$totalJamaahSudahVerifikasi   = $resultJamaahSudahVerifikasi->fetch_assoc()['total_gabungan'] ?? 0;

// ---------- 3. (Opsional) DITOLAK ----------
$queryJamaahDitolak = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM pendaftaran  WHERE status_verifikasi = 'Ditolak'
        UNION ALL
        SELECT COUNT(*) AS total FROM pembatalan    WHERE status_verifikasi = 'Ditolak'
        UNION ALL
        SELECT COUNT(*) AS total FROM pelimpahan    WHERE status_verifikasi = 'Ditolak'
    ) AS ditolak
";
$resultJamaahDitolak = $koneksi->query($queryJamaahDitolak);
$totalJamaahDitolak  = $resultJamaahDitolak->fetch_assoc()['total_gabungan'] ?? 0;

// Data contoh untuk status dokumen (sesuaikan dengan struktur database Anda)
$statusDokumen = [];

// Gabungkan semua nama dari tabel-tabel terkait
$sql = "
    SELECT 
        p.nama_jamaah, 
        p.nomor_porsi, 
        'Pendaftaran Haji' AS jenis_pelayanan,
        NULL AS dokumen_akta_kematian_status,
        p.dokumen_setor_awal_status,
        NULL AS dokumen_spph_status,
        NULL AS dokumen_ahli_waris_status,
        NULL AS dokumen_surat_kuasa_status,
        p.dokumen_ktp_status AS dokumen_ktp_ahliwaris_status,
        NULL AS dokumen_ktp_penerima_kuasa_status,
        p.dokumen_kk_status,
        NULL AS dokumen_akta_kelahiran_status,
        NULL AS dokumen_buku_nikah_status,
        p.dokumen_lain AS dokumen_rekening_status,
        p.foto_wajah_status,
        p.status_verifikasi
    FROM pendaftaran p

    UNION ALL

    SELECT 
        pe.nama_jamaah, 
        pe.nomor_porsi, 
        'Pembatalan Haji' AS jenis_pelayanan,
        NULL AS dokumen_akta_kematian_status,
        pe.dokumen_setor_awal_status,
        pe.dokumen_spph_status,
        NULL AS dokumen_ahli_waris_status,
        NULL AS dokumen_surat_kuasa_status,
        pe.dokumen_ktp_status AS dokumen_ktp_ahliwaris_status,
        NULL AS dokumen_ktp_penerima_kuasa_status,
        pe.dokumen_kk_status,
        pe.dokumen_akta_kelahiran_status,
        NULL AS dokumen_buku_nikah_status,
        pe.dokumen_rekening_status,
        pe.foto_wajah_status,
        pb.status_verifikasi
    FROM pembatalan_ekonomi pe
    JOIN pembatalan pb ON pb.id_pembatalan = pe.id_pembatalan

    UNION ALL

    SELECT 
        pm.nama_jamaah, 
        pm.nomor_porsi, 
        'Pembatalan Haji' AS jenis_pelayanan,
        pm.dokumen_akta_kematian_status,
        pm.dokumen_setor_awal_status,
        pm.dokumen_spph_status,
        pm.dokumen_ahli_waris_status,
        pm.dokumen_surat_kuasa_status,
        pm.dokumen_ktp_ahliwaris_status,
        pm.dokumen_ktp_penerima_kuasa_status,
        pm.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        pm.dokumen_akta_kelahiran_status,
        pm.dokumen_buku_nikah_status,
        pm.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        pm.foto_wajah_status,
        pb.status_verifikasi
    FROM pembatalan_meninggal pm
    JOIN pembatalan pb ON pb.id_pembatalan = pm.id_pembatalan

    UNION ALL

    SELECT 
        ps.nama_jamaah, 
        ps.nomor_porsi, 
        'Pelimpahan Haji' AS jenis_pelayanan,
        NULL AS dokumen_akta_kematian_status,
        ps.dokumen_setor_awal_status,
        ps.dokumen_spph_status,
        ps.dokumen_ahli_waris_status,
        ps.dokumen_surat_kuasa_status,
        ps.dokumen_ktp_ahliwaris_status,
        ps.dokumen_ktp_penerima_kuasa_status,
        ps.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        ps.dokumen_akta_kelahiran_status,
        ps.dokumen_buku_nikah_status,
        ps.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        ps.foto_wajah_status,
        pl.status_verifikasi
    FROM pelimpahan_sakit ps
    JOIN pelimpahan pl ON pl.id_pelimpahan = ps.id_pelimpahan

    UNION ALL

    SELECT 
        pm.nama_jamaah, 
        pm.nomor_porsi, 
        'Pelimpahan Haji' AS jenis_pelayanan,
        pm.dokumen_akta_kematian_status,
        pm.dokumen_setor_awal_status,
        pm.dokumen_spph_status,
        pm.dokumen_ahli_waris_status,
        pm.dokumen_surat_kuasa_status,
        pm.dokumen_ktp_ahliwaris_status,
        pm.dokumen_ktp_penerima_kuasa_status,
        pm.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        pm.dokumen_akta_kelahiran_status,
        pm.dokumen_buku_nikah_status,
        pm.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        pm.foto_wajah_status,
        pl.status_verifikasi
    FROM pelimpahan_meninggal pm
    JOIN pelimpahan pl ON pl.id_pelimpahan = pm.id_pelimpahan
";


$result = $koneksi->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Daftar semua kemungkinan kolom dokumen
        $dokumenKeys = [
            'dokumen_akta_kematian_status',
            'dokumen_setor_awal_status',
            'dokumen_spph_status',
            'dokumen_ahli_waris_status',
            'dokumen_surat_kuasa_status',
            'dokumen_ktp_ahliwaris_status',
            'dokumen_ktp_penerima_kuasa_status',
            'dokumen_kk_penerima_kuasa_status',
            'dokumen_kk_status',
            'dokumen_ktp_status',
            'dokumen_akta_kelahiran_status',
            'dokumen_buku_nikah_status',
            'dokumen_rekening_status',
            'dokumen_rekening_kuasa_status',
            'dokumen_surat_sakit_status',
            'foto_wajah_status'
        ];

        // Ambil hanya kolom yang tersedia dari hasil $row
        $dokumenStatus = [];
        foreach ($dokumenKeys as $key) {
            if (isset($row[$key])) {
                $dokumenStatus[$key] = strtolower($row[$key]);
            }
        }

        $statusDokumen[] = [
            'nama'    => $row['nama_jamaah'],
            'nomor_porsi'        => $row['nomor_porsi'],
            'jenis_pelayanan' => $row['jenis_pelayanan'],
            'dokumen_status'    => $dokumenStatus,
            'verifikasi_status' => strtolower($row['status_verifikasi'] ?? 'pending') // fallback pending
        ];
    }
}


function getStatusClass($status)
{
    switch (strtolower($status)) {
        case 'disetujui':
            return 'status-complete';
        case 'pending':
            return 'status-pending';
        case 'ditolak':
            return 'status-missing';
        default:
            return 'status-pending';
    }
}


function getStatusIcon($status)
{
    switch (strtolower($status)) {
        case 'disetujui':
            return '✓';
        case 'pending':
            return '⏳';
        case 'ditolak':
            return '✗';
        default:
            return '⏳';
    }
}

function calculateProgress($dokumen)
{
    $dokumenStatuses = $dokumen['dokumen_status'];
    $complete = 0;
    $total = 0;

    foreach ($dokumenStatuses as $status) {
        if ($status === null) {
            continue; // lewati dokumen yang tidak relevan (NULL)
        }
        $total++;
        if (strtolower($status) === 'terverifikasi') {
            $complete++;
        }
    }

    // Hindari pembagian nol
    if ($total === 0) return 0;

    return ($complete / $total) * 100;
}

function getBadgeClass($jenis)
{
    switch (strtolower($jenis)) {
        case 'pendaftaran haji':
            return 'badge bg-success'; // hijau
        case 'pembatalan haji':
            return 'badge bg-danger';  // merah
        case 'pelimpahan haji':
            return 'badge bg-warning text-dark'; // kuning
        default:
            return 'badge bg-secondary';
    }
}

?>
<style>
    .dokumen-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .dokumen-tag {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        border: 1px solid #bbdefb;
    }

    .dokumen-tag:hover {
        background-color: #bbdefb;
    }
</style>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include 'includes/sidebar_kasi.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include 'includes/header_kasi.php'; ?>

        <!-- Ganti bagian <main class="dashboard-wrapper"> dengan kode ini -->
        <main class="dashboard-wrapper">
            <div class="kasi-grid">
                <!-- Kalender -->
                <div class="card kalender">
                    <div class="header-kalender text-center">
                        <span>May 2025</span>
                    </div>
                    <div class="grid-kalender">
                        <div class="hari">Sun</div>
                        <div class="hari">Mon</div>
                        <div class="hari">Tue</div>
                        <div class="hari">Wed</div>
                        <div class="hari">Thu</div>
                        <div class="hari">Fri</div>
                        <div class="hari">Sat</div>

                        <!-- contoh isi tanggal -->
                        <div class="tanggal">1</div>
                        <div class="tanggal">2</div>
                        <div class="tanggal today-fix">3</div>
                        <div class="tanggal">4</div>
                        <!-- lanjutkan sesuai kebutuhan -->
                    </div>
                </div>
                <!-- Stats Cards - Jamaah Ditolak & Jamaah Sudah Diverifikasi -->
                <div class="card stats-cards">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Jamaah Ditolak</div>
                            <span class="material-symbols-outlined stat-icon">close</span>
                        </div>
                        <div class="stat-value"><?= number_format($totalJamaahDitolak) ?></div>
                        <div class="stat-description">
                            Jamaah tidak diverifikasi
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Jamaah Sudah Diverifikasi</div>
                            <span class="material-symbols-outlined stat-icon">verified</span>
                        </div>
                        <div class="stat-value"><?= number_format($totalJamaahSudahVerifikasi) ?></div>
                        <div class="stat-description">
                            Jamaah terverifikasi
                        </div>
                    </div>
                </div>

                <!-- Cuaca -->
                <div class="card cuaca">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                        <div>
                            <div class="temperature">24°C</div>
                            <div class="date-info" style="font-size: 1rem; color: gray;">Martapura, Kalimantan Selatan</div>
                        </div>
                        <span class="material-symbols-outlined" style="font-size: 48px; color: #f39c12;">wb_sunny</span>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Jamaah Belum Diverifikasi</div>
                            <span class="material-symbols-outlined stat-icon">pending</span>
                        </div>
                        <div class="stat-value"><?= number_format($totalJamaahBelumVerifikasi) ?></div>
                        <div class="stat-description">
                            Jamaah menunggu verifikasi
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="assets/js/sidebar.js"></script>
<script src="assets/js/kalender_dashboard.js"></script>
<script src="assets/js/cuaca_dashboard.js"></script>
<script>
    function togglePopup() {
        const popup = document.getElementById("popupForm");
        popup.style.display = (popup.style.display === "block") ? "none" : "block";
    }
</script>

</body>

</html>