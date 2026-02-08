<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php ada dan benar

if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    // header("Location: login.php"); // Redirect ke login jika belum login
    // exit();
}

// Ambil ID dari parameter URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID tidak valid!";
    exit;
}
$id = intval($_GET['id']); // aman

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
$stmt->bind_param("i", $id);
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
    $awal = new DateTime($tanggal_awal);
    $akhir = new DateTime($tanggal_akhir);
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
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body {
            font-family: Calibri, sans-serif;
            margin: 50px;
        }

        .container {
            padding: 30px;
        }

        .header {
            background-color: #388e3c;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #fff;
        }

        .content {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            color: rgb(3, 77, 0);
        }

        .column {
            width: 48%;
        }

        .field {
            margin-bottom: 10px;
        }

        .field span {
            display: inline-block;
            width: 160px;
        }

        .footer {
            margin-top: 50px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">INFORMASI ESTIMASI KEBERANGKATAN JEMAAH</div>
        <div class="content">
            <div class="column">
                <div class="field"><span>NAMA JAMAAH</span>: <?= htmlspecialchars($data['nama_jamaah']) ?></div>
                <div class="field"><span>NAMA AYAH</span>: <?= htmlspecialchars($data['nama_ayah']) ?></div>
                <div class="field"><span>JENIS KELAMIN</span>: <?= htmlspecialchars($data['jenis_kelamin']) ?></div>
                <div class="field"><span>TANGGAL LAHIR</span>: <?= !empty($data['tanggal_lahir']) ? date('d-m-Y', strtotime($data['tanggal_lahir'])) : '-' ?></div>
                <div class="field"><span>TGL PENDAFTARAN</span>: <?= !empty($data['tgl_pendaftaran']) ? date('d-m-Y', strtotime($data['tgl_pendaftaran'])) : '-' ?></div>
                <div class="field"><span>TELAH MENUNGGU</span>: <?= !empty($data['tgl_pendaftaran']) ? hitungSelisihWaktu($data['tgl_pendaftaran'], date('Y-m-d')) : '-' ?></div>
                <div class="field"><span>ESTIMASI BERANGKAT</span>: <?= !empty($data['estimasi_berangkat']) ? date('d-m-Y', strtotime($data['estimasi_berangkat'])) : '-' ?></div>
            </div>
            <div class="column">
                <div class="field"><span>STATUS HAJI</span>: <?= htmlspecialchars($data['status_pergi_haji']) ?></div>
                <div class="field"><span>UMUR</span>: <?= htmlspecialchars($data['umur']) ?> tahun</div>
                <div class="field"><span>SISA MENUNGGU</span>: <?= formatWaktuDariHari($data['sisa_menunggu'] ?? 0) ?></div>
                <div class="field"><span>MASA MENUNGGU</span>: <?= formatWaktuDariHari($data['masa_menunggu'] ?? 0) ?></div>
            </div>
        </div>
        <div class="footer">
            <p style="text-align: right;">Martapura, <?= tanggal_indo(date('Y-m-d')) ?></p>
            <br><br><br>
            <p style="text-align: right;">Staf PHU Kab Banjar</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>