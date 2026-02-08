<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php");
    exit();
}

// Ambil data rekapitulasi berdasarkan kecamatan & jenis pelayanan
$query = "
    SELECT kecamatan, jenis_pelayanan, COUNT(*) as jumlah
    FROM (
        SELECT p.kecamatan, 'Pendaftaran' AS jenis_pelayanan FROM pendaftaran p
        UNION ALL
        SELECT pe.kecamatan, 'Pembatalan Ekonomi' FROM pembatalan_ekonomi pe
        UNION ALL
        SELECT pm.kecamatan, 'Pembatalan Meninggal' FROM pembatalan_meninggal pm
        UNION ALL
        SELECT ps.kecamatan_jamaah AS kecamatan, 'Pelimpahan Sakit' FROM pelimpahan_sakit ps
        UNION ALL
        SELECT pmg.kecamatan_jamaah AS kecamatan, 'Pelimpahan Meninggal' FROM pelimpahan_meninggal pmg
    ) AS gabungan
    GROUP BY kecamatan, jenis_pelayanan
    ORDER BY kecamatan, jenis_pelayanan
";

$result = $koneksi->query($query);
$data_rekap = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_rekap[] = $row;
    }
}

$tanggal = date('d');
$bulan_angka = date('m');
$tahun = date('Y');
$bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];
$format_tanggal = $tanggal . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekapitulasi Jamaah</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        /* ==== Styling Umum untuk Layar & Cetak (Dasar) ==== */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            /* Padding untuk konten utama */
            color: #333;
            font-size: 12px;
            /* Ukuran font standar untuk cetak */
        }

        .container {
            width: 100%;
            max-width: 800px;
            /* Sesuaikan dengan lebar halaman A4, umumnya sekitar 800px - 850px */
            margin: 0 auto;
            /* Tengah di halaman */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px 8px;
            /* Sesuaikan padding agar lebih padat di cetakan */
            text-align: left;
            vertical-align: top;
            /* Agar konten sel tidak terlalu tinggi */
        }

        th {
            background-color: #1b5e20;
            font-weight: bold;
            text-align: center;
            color: white;
            /* Sesuaikan agar rata tengah */
        }

        /* ==== Kop Surat ==== */
        .kop-surat {
            text-align: center;
            border-bottom: 4px double #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .kop-surat img {
            max-width: 80px;
            /* Ukuran logo */
            float: left;
            /* Logo di kiri */
            margin-right: 15px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 20px;
            /* Lebih kecil untuk cetak */
            color: #222;
        }

        .kop-surat p {
            margin: 2px 0;
            font-size: 12px;
            line-height: 1.4;
        }

        .email {
            color: #007bff;
        }

        /* Clearfix untuk kop surat agar float tidak merusak layout */
        .kop-surat::after {
            content: "";
            display: table;
            clear: both;
        }

        /* ==== Tanda Tangan Pengesahan ==== */
        .signature {
            text-align: right;
            margin-top: 50px;
        }

        /* ==== CSS Khusus untuk Pencetakan (Paling Penting!) ==== */
        @media print {
            body {
                margin: 0;
                padding: 0;
                /* Atur margin halaman untuk printer, biasanya 1cm atau 0.5in */
                /* Ini akan override body padding di atas */
                padding: 1cm;
            }

            .container {
                margin: 0;
                max-width: none;
                /* Izinkan mengambil lebar penuh halaman */
            }

            /* Sembunyikan elemen yang tidak perlu dicetak (jika ada) */
            .no-print {
                display: none !important;
            }

            /* Pastikan tabel tidak terpotong di tengah halaman */
            table {
                page-break-after: auto;
            }

            tr {
                page-break-inside: avoid;
                /* Hindari baris terpotong */
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
                /* Agar header tabel berulang di setiap halaman */
            }

            tfoot {
                display: table-footer-group;
                /* Agar footer tabel berulang di setiap halaman */
            }

            /* Kontrol agar kop surat dan tanda tangan tidak terpotong */
            .kop-surat,
            .ttd-area {
                page-break-inside: avoid;
            }

            /* Untuk kop surat agar selalu di bagian atas halaman pertama */
            .kop-surat {
                position: relative;
                top: 0;
                left: 0;
                width: 100%;
                box-sizing: border-box;
            }

            /* Kontrol agar tanda tangan tidak terpotong atau dipisahkan dari tabel */
            .ttd-area {
                margin-top: 30px;
                /* Atur jarak dari tabel */
                page-break-before: auto;
                /* Hindari page break yang tidak perlu sebelum ttd */
                page-break-after: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="kop-surat">
            <img align="left" src="logo_kemenag.png" alt="Logo Kemenag">
            <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
                KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
            <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
                Telp/Fax: (0511) 4721442 | Email: <u class="email">banjarkalsel@kemenag.go.id</u>
            </p>
        </div>

        <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN REKAPITULASI JAMAAH HAJI</h3>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kecamatan</th>
                    <th>Jenis Pelayanan</th>
                    <th>Jumlah Jamaah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data_rekap)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data_rekap as $row): ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($row['kecamatan']) ?></td>
                            <td style="text-align: center;"><?= htmlspecialchars($row['jenis_pelayanan']) ?></td>
                            <td style="text-align: center;"><?= $row['jumlah'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">Tidak ada data jamaah ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">Martapura, <?php echo $format_tanggal; ?></p>
                        <p style="margin: 0;">An. Kepala Kantor Kementerian Agama</p>
                        <p style="margin: 0;">Kabupaten Banjar,</p>
                        <p style="margin: 0;">Kepala Seksi Peny. Haji dan Umrah</p>
                        <div style="margin: 20px 0; text-align: center;">
                            <!-- <img src="ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 50px; height: auto;"> -->
                            <br><br><br>
                        </div>
                        <strong>
                            <p style="margin: 0;"><u>Erfan Maulana</u></p>
                            <p style="margin: 0;">NIP.198411042003121004</p>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
