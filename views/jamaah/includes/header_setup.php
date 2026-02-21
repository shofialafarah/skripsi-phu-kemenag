<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

// Ambil ID jamaah dari session yang benar (biasanya id_jamaah, bukan id_admin)
if (!isset($_SESSION['id_jamaah'])) {
    // Jika tidak ada session, arahkan ke login atau beri nilai default
    header("Location: ../../auth/login.php"); 
    exit();
}

$id_session = $_SESSION['id_jamaah'];

// Ambil data jamaah
$sql_jamaah = "SELECT nama FROM jamaah WHERE id_jamaah = ?";
$stmt = $koneksi->prepare($sql_jamaah);
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$jamaah = $stmt->get_result()->fetch_assoc();

// =============================
// 1. NOTIF BEL (unggah ulang)
// =============================
$sql = "
    -- Pendaftaran
    SELECT 
        'Pendaftaran' AS jenis_pelayanan,
        p.dokumen_setor_awal_status,
        p.dokumen_ktp_status AS dokumen_ktp_ahliwaris_status,
        p.dokumen_kk_status,
        p.dokumen_lain_status AS dokumen_rekening_status,
        p.foto_wajah_status,
        p.status_verifikasi
    FROM pendaftaran p
    WHERE p.id_jamaah = ?

    UNION ALL

    -- Pembatalan
    SELECT 
        'Pembatalan' AS jenis_pelayanan,
        pe.dokumen_setor_awal_status,
        pe.dokumen_ktp_status AS dokumen_ktp_ahliwaris_status,
        pe.dokumen_kk_status,
        pe.dokumen_rekening_status,
        pe.foto_wajah_status,
        pb.status_verifikasi
    FROM pembatalan_ekonomi pe
    JOIN pembatalan pb ON pb.id_pembatalan = pe.id_pembatalan
    WHERE pb.id_jamaah = ?

    UNION ALL

    -- Pembatalan Meninggal
    SELECT 
        'Pembatalan' AS jenis_pelayanan,
        pm.dokumen_setor_awal_status,
        pm.dokumen_ktp_ahliwaris_status,
        pm.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        pm.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        pm.foto_wajah_status,
        pb.status_verifikasi
    FROM pembatalan_meninggal pm
    JOIN pembatalan pb ON pb.id_pembatalan = pm.id_pembatalan
    WHERE pb.id_jamaah = ?

    UNION ALL

    -- Pelimpahan Sakit
    SELECT 
        'Pelimpahan' AS jenis_pelayanan,
        ps.dokumen_setor_awal_status,
        ps.dokumen_ktp_ahliwaris_status,
        ps.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        ps.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        ps.foto_wajah_status,
        pl.status_verifikasi
    FROM pelimpahan_sakit ps
    JOIN pelimpahan pl ON pl.id_pelimpahan = ps.id_pelimpahan
    WHERE pl.id_jamaah = ?

    UNION ALL

    -- Pelimpahan Meninggal
    SELECT 
        'Pelimpahan' AS jenis_pelayanan,
        pm.dokumen_setor_awal_status,
        pm.dokumen_ktp_ahliwaris_status,
        pm.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        pm.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        pm.foto_wajah_status,
        pl.status_verifikasi
    FROM pelimpahan_meninggal pm
    JOIN pelimpahan pl ON pl.id_pelimpahan = pm.id_pelimpahan
    WHERE pl.id_jamaah = ?
";


$stmt_bell = $koneksi->prepare($sql);
$stmt_bell->bind_param("iiiii", $id_jamaah, $id_jamaah, $id_jamaah, $id_jamaah, $id_jamaah);
$stmt_bell->execute();
$result_bell = $stmt_bell->get_result();

$notif_bell = [];
function tambahNotif($pesan, $jenis_pelayanan, &$notif_bell) {
    $warnaBadge = '';
    switch ($jenis_pelayanan) {
        case 'Pendaftaran':
            $warnaBadge = 'bg-success'; // hijau
            break;
        case 'Pembatalan':
            $warnaBadge = 'bg-danger'; // merah
            break;
        case 'Pelimpahan':
            $warnaBadge = 'bg-warning text-dark'; // kuning
            break;
    }
    $notif_bell[] = "<span class=\"badge {$warnaBadge}\">{$jenis_pelayanan}</span> {$pesan}";
}

while ($data_bell = $result_bell->fetch_assoc()) {
    if (!empty($data_bell['dokumen_setor_awal_status']) && stripos($data_bell['dokumen_setor_awal_status'], 'unggah ulang') !== false) {
        tambahNotif("Bukti Setor Awal perlu diunggah ulang.", $data_bell['jenis_pelayanan'], $notif_bell);
    }
    if (!empty($data_bell['dokumen_ktp_ahliwaris_status']) && stripos($data_bell['dokumen_ktp_ahliwaris_status'], 'unggah ulang') !== false) {
        tambahNotif("KTP/KIA perlu diunggah ulang.", $data_bell['jenis_pelayanan'], $notif_bell);
    }
    if (!empty($data_bell['dokumen_kk_status']) && stripos($data_bell['dokumen_kk_status'], 'unggah ulang') !== false) {
        tambahNotif("Kartu Keluarga perlu diunggah ulang.", $data_bell['jenis_pelayanan'], $notif_bell);
    }
    if (!empty($data_bell['dokumen_rekening_status']) && stripos($data_bell['dokumen_rekening_status'], 'unggah ulang') !== false) {
        tambahNotif("Dokumen Lainnya perlu diunggah ulang.", $data_bell['jenis_pelayanan'], $notif_bell);
    }
    if (!empty($data_bell['foto_wajah_status']) && stripos($data_bell['foto_wajah_status'], 'unggah ulang') !== false) {
        tambahNotif("Foto Wajah perlu diunggah ulang.", $data_bell['jenis_pelayanan'], $notif_bell);
    }
}

$jumlah_notif_bell = count($notif_bell);

// =============================
// 2. NOTIF MAIL (berkas disetujui)
// =============================
$sql_mail_pendaftaran = "
    SELECT 'Pendaftaran' AS jenis_pelayanan, upload_doc AS link
    FROM pendaftaran
    WHERE id_jamaah = ?
        AND status_verifikasi = 'Disetujui'
        AND upload_doc IS NOT NULL
";

$sql_mail_pembatalan_ekonomi = "
    SELECT 'Pembatalan' AS jenis_pelayanan, CONCAT('cetak_pembatalan_ekonomi.php?id=', id_pembatalan) AS link
    FROM pembatalan
    WHERE id_jamaah = ?
        AND status_verifikasi = 'Disetujui'
        AND kategori = 'Ekonomi'
";

$sql_mail_pembatalan_meninggal = "
    SELECT 'Pembatalan' AS jenis_pelayanan, CONCAT('cetak_pembatalan_meninggal.php?id=', id_pembatalan) AS link
    FROM pembatalan
    WHERE id_jamaah = ?
        AND status_verifikasi = 'Disetujui'
        AND kategori = 'Meninggal'
";

$sql_mail_pelimpahan_sakit = "
    SELECT 'Pelimpahan' AS jenis_pelayanan, CONCAT('cetak_pelimpahan_sakit.php?id=', id_pelimpahan) AS link
    FROM pelimpahan
    WHERE id_jamaah = ?
        AND status_verifikasi = 'Disetujui'
        AND kategori = 'Sakit'
";

$sql_mail_pelimpahan_meninggal = "
    SELECT 'Pelimpahan' AS jenis_pelayanan, CONCAT('cetak_pelimpahan_meninggal.php?id=', id_pelimpahan) AS link
    FROM pelimpahan
    WHERE id_jamaah = ?
        AND status_verifikasi = 'Disetujui'
        AND kategori = 'Meninggal'
";

$sql_mail = "$sql_mail_pendaftaran
            UNION ALL
            $sql_mail_pembatalan_ekonomi
            UNION ALL
            $sql_mail_pembatalan_meninggal
            UNION ALL
            $sql_mail_pelimpahan_sakit
            UNION ALL
            $sql_mail_pelimpahan_meninggal";

$stmt_mail = $koneksi->prepare($sql_mail);
$stmt_mail->bind_param("iiiii", $id_jamaah, $id_jamaah, $id_jamaah, $id_jamaah, $id_jamaah);
$stmt_mail->execute();
$result_mail = $stmt_mail->get_result();

$notif_mail = [];
while ($row = $result_mail->fetch_assoc()) {
    $jenis_pelayanan = $row['jenis_pelayanan'];
    $warnaBadge = '';
    switch ($jenis_pelayanan) {
        case 'Pendaftaran':
            $warnaBadge = 'bg-success'; // hijau
            break;
        case 'Pembatalan':
            $warnaBadge = 'bg-danger'; // merah
            break;
        case 'Pelimpahan':
            $warnaBadge = 'bg-warning text-dark'; // kuning
            break;
        default:
            $warnaBadge = 'bg-secondary';
            break;
    }
    
    $notif_mail[] = [
        'pesan' => "<span class=\"badge {$warnaBadge}\">{$jenis_pelayanan}</span> Berkas sudah tersedia untuk diunduh.",
        'link'  => $row['link']
    ];
}
$jumlah_notif_mail = count($notif_mail);


$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);
$base_url = "http://localhost/phu-kemenag-banjar-copy/";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Jamaah</title>

    <link rel="icon" href="<?= $base_url ?>assets/img/logo_kemenag.png?v=1.1" type="image/png">
    <link rel="shortcut icon" href="<?= $base_url ?>assets/img/logo_kemenag.png?v=1.1" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />

    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/global_style.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/header.css" />
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/sidebar.css" />
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/entry.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/dashboard_jamaah.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/pendaftaran_jamaah.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/pembatalan_jamaah.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/pelimpahan_jamaah.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/jamaah/assets/css/estimasi.css">
    <style>
        .dropdown-menu.scrollable-dropdown {
            max-height: 300px;
            /* batas tinggi dropdown */
            overflow-y: auto;
            /* bikin scroll kalau isi banyak */
        }

        /* Hover badge notifikasi (lonceng) */
        .header-actions .dropdown:hover .badge-notif {
            color: #fff;
            /* warna teks */
        }
    </style>
    
</head>
<body>