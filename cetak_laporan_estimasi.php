<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php ada dan benar

if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php"); // Redirect ke login jika belum login
    exit();
}

// Ambil parameter filter dari URL jika ada
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$query = "SELECT 
    p.nama_jamaah,
    p.nomor_porsi,
    p.tanggal_lahir,
    e.tgl_pendaftaran,
    e.telah_menunggu,
    e.estimasi_berangkat,
    e.umur,
    e.sisa_menunggu,
    e.masa_menunggu,
    j.email
FROM 
    pendaftaran p
JOIN 
    estimasi e ON e.id_pendaftaran = p.id_pendaftaran
JOIN 
    jamaah j ON p.id_jamaah = j.id_jamaah
";

$where_clauses = [];
$params = [];
$types = '';

if ($start_date && $end_date) {
    $where_clauses[] = "e.estimasi_berangkat BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}
// Tambahkan ORDER BY di akhir query
$query .= " ORDER BY e.estimasi_berangkat DESC";

$stmt = null; // Inisialisasi $stmt untuk menghindari undefined variable warning
$result = null; // Inisialisasi $result

// Siapkan statement untuk menghindari SQL Injection
if (!empty($params)) {
    $stmt = $koneksi->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Handle prepare error
        echo "Error preparing statement: " . $koneksi->error;
        // Tetapkan $result ke false agar tidak melanjutkan dengan $result yang null
        $result = false;
    }
} else {
    // Jika tidak ada parameter (tidak ada filter tanggal), langsung jalankan query tanpa prepared statement
    // Pastikan query sudah aman dari SQL Injection di sini (karena tidak ada input user)
    $result = $koneksi->query($query);
    if ($result === false) {
        echo "Error executing query: " . $koneksi->error;
    }
}

$data_estimasi = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_estimasi[] = $row;
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

// --- AWAL KODE BARU UNTUK KETERANGAN FILTER TANGGAL ---
$keterangan_tanggal_filter = '';
// Fungsi untuk format tanggal menjadi "DD Bulan YYYY"
function formatTanggalIndo($tanggal_string)
{
    if (empty($tanggal_string)) {
        return '';
    }
    // Mengubah YYYY-MM-DD menjadi format yang bisa dipahami strtotime
    $timestamp = strtotime($tanggal_string);
    if ($timestamp === false) {
        return ''; // Handle invalid date string
    }
    $tanggal = date('d', $timestamp);
    $bulan_angka = date('m', $timestamp);
    $tahun = date('Y', $timestamp);

    $daftar_bulan = [
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

    return $tanggal . ' ' . $daftar_bulan[$bulan_angka] . ' ' . $tahun;
}


if (!empty($start_date) && !empty($end_date)) {
    $formatted_start_date = formatTanggalIndo($start_date);
    $formatted_end_date = formatTanggalIndo($end_date);

    if ($formatted_start_date === $formatted_end_date) {
        $keterangan_tanggal_filter = "Pada Tanggal: " . $formatted_start_date;
    } else {
        $keterangan_tanggal_filter = "Periode Tanggal: " . $formatted_start_date . " s/d " . $formatted_end_date;
    }
} elseif (!empty($start_date)) {
    $keterangan_tanggal_filter = "Mulai Tanggal: " . formatTanggalIndo($start_date);
} elseif (!empty($end_date)) {
    $keterangan_tanggal_filter = "Sampai Tanggal: " . formatTanggalIndo($end_date);
} else {
    $keterangan_tanggal_filter = "Seluruh Data";
}
// --- AKHIR KODE BARU UNTUK KETERANGAN FILTER TANGGAL ---
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Estimasi</title>
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

        <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN DATA ESTIMASI JAMAAH HAJI</h3>
        <p style="text-align: center; margin-top: 0; margin-bottom: 25px; font-size: 12px;">
            <?php echo $keterangan_tanggal_filter; ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Porsi</th>
                    <th>Nama Jamaah</th>
                    <th>Umur</th>
                    <th>Tanggal Pendaftaran</th>
                    <th>Telah Menunggu</th>
                    <th>Estimasi Berangkat</th>
                    <th>Sisa Menunggu</th>
                    <th>Masa Menunggu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (!empty($data_estimasi)) {
                    foreach ($data_estimasi as $row) {
                        echo "<tr>";
                        echo "<td style='text-align: center;'>" . $no++ . "</td>";
                        $nomor_porsi = !empty($row['nomor_porsi']) ? htmlspecialchars($row['nomor_porsi']) : '-';
                        echo "<td style='text-align: center;'>$nomor_porsi</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['umur']) . "</td>";
                        echo "<td style='text-align: center;'>" . date('d-m-Y', strtotime($row['tgl_pendaftaran'])) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['telah_menunggu']) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['estimasi_berangkat']) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['sisa_menunggu']) . "</td>";
                        echo "<td style='text-align: center;'>" . htmlspecialchars($row['masa_menunggu']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' style='text-align: center;'>Tidak ada data berkas pendaftaran jamaah haji yang ditemukan.</td></tr>";
                }
                ?>
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

        <script>
            // Otomatis memicu dialog cetak saat halaman dimuat
            window.onload = function() {
                window.print();
                // Opsional: Tutup tab setelah mencetak. Beberapa browser mungkin memblokir ini.
                // window.onafterprint = function() { window.close(); };
            };
        </script>
    </div>
</body>

</html>