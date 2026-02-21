<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../../includes/koneksi.php';

if (!isset($_GET['id_estimasi']) || !is_numeric($_GET['id_estimasi'])) {
    echo "ID tidak valid!";
    exit;
}
$id_estimasi = intval($_GET['id_estimasi']);

// Ambil data staf yang sedang login dari session
$id_staf = $_SESSION['id_staf']; // Pastikan nama session ID sesuai dengan saat login
$query_staf = "SELECT nama_staf, nip FROM staf WHERE id_staf = ?";
$stmt_staf = $koneksi->prepare($query_staf);
$stmt_staf->bind_param("i", $id_staf);
$stmt_staf->execute();
$staf = $stmt_staf->get_result()->fetch_assoc();

// Jika data staf tidak ditemukan di tabel staf, ambil dari session sebagai cadangan
$nama_tampil = $staf['nama_staf'] ?? $_SESSION['nama_lengkap'] ?? 'Nama Staf';
$nip_tampil = $staf['nip'] ?? $_SESSION['nip'] ?? '-';

// Ambil data dari JOIN
$query = "
    SELECT p.nama_jamaah, p.nama_ayah, p.jenis_kelamin, p.tanggal_lahir, p.status_pergi_haji,
           e.tgl_pendaftaran, e.telah_menunggu, e.estimasi_berangkat,
           e.umur, e.sisa_menunggu, e.masa_menunggu
    FROM pendaftaran p
    JOIN estimasi e ON p.id_pendaftaran = e.id_pendaftaran
    WHERE p.id_pendaftaran = ?
";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_estimasi);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}

function tanggal_indo($tanggal)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $pecah = explode('-', $tanggal);
    return (int)$pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Fungsi untuk format waktu dari hari (sama seperti di entry_estimasi.php)
function formatWaktuDariHari($total_hari)
{
    $total_hari = (int) $total_hari;

    if ($total_hari == 0) {
        return 'Hari ini';
    }

    $tahun = floor($total_hari / 365);
    $sisa_hari = $total_hari % 365;
    $bulan = floor($sisa_hari / 30);
    $hari = $sisa_hari % 30;

    $hasil = [];

    if ($tahun > 0) {
        $hasil[] = $tahun . ' tahun';
    }
    if ($bulan > 0) {
        $hasil[] = $bulan . ' bulan';
    }
    if ($hari > 0) {
        $hasil[] = $hari . ' hari';
    }

    return empty($hasil) ? '0 hari' : implode(', ', $hasil);
}

// Fungsi untuk menghitung selisih waktu dalam format yang lebih detail (sama seperti di entry_estimasi.php)
function hitungSelisihWaktu($tanggal_awal, $tanggal_akhir)
{
    $awal = new \DateTime($tanggal_awal);
    $akhir = new \DateTime($tanggal_akhir);
    $selisih = $awal->diff($akhir);

    $hasil = [];

    if ($selisih->y > 0) {
        $hasil[] = $selisih->y . ' tahun';
    }
    if ($selisih->m > 0) {
        $hasil[] = $selisih->m . ' bulan';
    }
    if ($selisih->d > 0) {
        $hasil[] = $selisih->d . ' hari';
    }

    // Jika tidak ada selisih yang signifikan, tampilkan hari
    if (empty($hasil)) {
        $total_hari = $awal->diff($akhir)->days;
        if ($total_hari == 0) {
            return 'Hari ini';
        } else {
            return $total_hari . ' hari';
        }
    }

    return implode(', ', $hasil);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Estimasi Keberangkatan Jemaah</title>
    <link rel="icon" href="../../../assets/img/logo_kemenag.png" type="image/png">
    <link rel="stylesheet" href="../../../assets/css/cetak_estimasi.css">
</head>

<body>
    <div class="cetak-estimasi-wrapper">
        <div class="cetak-estimasi-header">INFORMASI ESTIMASI KEBERANGKATAN JEMAAH</div>
        <div class="cetak-estimasi-content">
            <div class="cetak-estimasi-column">
                <div class="cetak-estimasi-field"><span>NAMA JAMAAH</span>: <?= htmlspecialchars($data['nama_jamaah']) ?></div>
                <div class="cetak-estimasi-field"><span>NAMA AYAH</span>: <?= htmlspecialchars($data['nama_ayah']) ?></div>
                <div class="cetak-estimasi-field"><span>JENIS KELAMIN</span>: <?= htmlspecialchars($data['jenis_kelamin']) ?></div>
                <div class="cetak-estimasi-field"><span>TANGGAL LAHIR</span>: <?= !empty($data['tanggal_lahir']) ? date('d-m-Y', strtotime($data['tanggal_lahir'])) : '-' ?></div>
                <div class="cetak-estimasi-field"><span>TGL PENDAFTARAN</span>: <?= !empty($data['tgl_pendaftaran']) ? date('d-m-Y', strtotime($data['tgl_pendaftaran'])) : '-' ?></div>
                <div class="cetak-estimasi-field"><span>TELAH MENUNGGU</span>: <?= !empty($data['tgl_pendaftaran']) ? hitungSelisihWaktu($data['tgl_pendaftaran'], date('Y-m-d')) : '-' ?></div>
                <div class="cetak-estimasi-field"><span>ESTIMASI BERANGKAT</span>: <?= !empty($data['estimasi_berangkat']) ? date('d-m-Y', strtotime($data['estimasi_berangkat'])) : '-' ?></div>
            </div>
            <div class="cetak-estimasi-column">
                <div class="cetak-estimasi-field"><span>STATUS HAJI</span>: <?= htmlspecialchars($data['status_pergi_haji']) ?></div>
                <div class="cetak-estimasi-field"><span>UMUR</span>: <?= htmlspecialchars($data['umur']) ?> tahun</div>
                <div class="cetak-estimasi-field"><span>SISA MENUNGGU</span>: <?= formatWaktuDariHari($data['sisa_menunggu'] ?? 0) ?></div>
                <div class="cetak-estimasi-field"><span>MASA MENUNGGU</span>: <?= formatWaktuDariHari($data['masa_menunggu'] ?? 0) ?></div>
            </div>
        </div>
        <div class="footer-ttd">
            <div class="cetak-estimasi-ttd">
                <p>Martapura, <?= tanggal_indo(date('Y-m-d')) ?></p>
                <p>Staf PHU Kab Banjar,</p>
                <div style="height: 80px;"></div>
                <p><u><?= htmlspecialchars($nama_tampil); ?></u></p>
                <p>NIP. <?= htmlspecialchars($nip_tampil); ?></p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>