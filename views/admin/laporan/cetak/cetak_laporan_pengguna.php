<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';

// Pastikan parameter ini diteruskan dari laporan_pendaftaran.php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$query = "
    SELECT 'Jamaah' AS role, id_jamaah AS id, nama AS nama_lengkap, username, email, nomor_telepon AS no_telepon, created_at, updated_at, status_pengguna
    FROM jamaah
    UNION ALL
    SELECT 'Staf' AS role, id_staf AS id, nama_staf AS nama_lengkap, username, email, no_telepon, created_at, updated_at, status_pengguna
    FROM staf
    UNION ALL
    SELECT 'Kepala Seksi' AS role, id_kepala AS id, nama_kepala AS nama_lengkap, username, email, no_telepon, created_at, updated_at, status_pengguna
    FROM kepala_seksi
    ORDER BY created_at DESC
";

$where_clauses = [];
$params = [];
$types = '';

if ($start_date && $end_date) {
    $where_clauses[] = "p.tanggal_pengajuan BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}

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
        $result = false;
    }
} else {
    $result = $koneksi->query($query);
}


$data_pengguna = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pengguna[] = $row;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pengguna</title>
    <link rel="icon" href="../assets/img/logo_kemenag.png">
    <style>
        .cetak-wrapper {
            width: 100%;
            font-family: Arial, sans-serif;
            /* Sesuaikan dengan lebar halaman A4, umumnya sekitar 800px - 850px */
            margin: 0 auto;
            color: #333;
            font-size: 12px;
        }

        .tabel-cetak {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .kepala-cetak,
        .badan-cetak {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: center;
            vertical-align: top;
        }

        .kepala-cetak {
            background-color: #1b5e20;
            font-weight: bold;
            color: white;
        }

        /* ==== Kop Surat ==== */
        .kop-surat {
            text-align: center;
            border-bottom: 4px double #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .kop-surat img {
            max-width: 100px;
            float: left;
            margin-right: 15px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 20px;
            color: #222;
        }

        .kop-surat p {
            margin: 2px 0;
            font-size: 12px;
            line-height: 1.4;
        }

        /* ==== untuk kop surat agar float tidak merusak layout */
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

        /* ==== CSS Khusus untuk Pencetakan ==== */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="cetak-wrapper">
        <div class="kop-surat">
            <img align="left" src="/phu-kemenag-banjar-copy/assets/logo_kemenag.png" alt="Logo Kemenag">
            <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
                KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
            <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
                Telp/Fax: (0511) 4721442 | Email: <a href="mailto:phukemenagbanjar@gmail.com">phukemenagbanjar@gmail.com</a>
            </p>
        </div>

        <h3 style="text-align: center; margin-bottom: 25px;">LAPORAN DATA PENGGUNA SISTEM</h3>

        <table class="tabel-cetak">
            <thead>
                <tr>
                    <th class="kepala-cetak">No</th>
                    <th class="kepala-cetak">Role</th>
                    <th class="kepala-cetak">Nama Lengkap</th>
                    <th class="kepala-cetak">Username</th>
                    <th class="kepala-cetak">Email</th>
                    <th class="kepala-cetak">Nomor Telepon</th>
                    <th class="kepala-cetak">Tanggal Akun Dibuat</th>
                    <th class="kepala-cetak">Tanggal Akun Diubah</th>
                    <th class="kepala-cetak">Status Pengguna</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (!empty($data_pengguna)) {
                    foreach ($data_pengguna as $row) {
                        // Hilangkan tag HTML dari badge, hanya ambil teksnya untuk dicetak
                        echo "<tr>";
                        echo "<td class='badan-cetak'>" . $no++ . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['username']) . "</td>";
                        $email = htmlspecialchars($row['email']);
                        if (!empty($email)) {
                            echo "<td class='badan-cetak email'><a href='mailto:$email'>$email</a></td>";
                        } else {
                            echo "<td class='badan-cetak email'>-</td>";
                        }
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['no_telepon']) . "</td>";

                        echo "<td class='badan-cetak'>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";
                        echo "<td class='badan-cetak'>" . date('d-m-Y', strtotime($row['updated_at'])) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['status_pengguna']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='badan-cetak'>Tidak ada data berkas pendaftaran jamaah haji yang ditemukan.</td></tr>";
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
                            <img src="../assets/img/ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 100px; height: auto;">
                            <!-- <br><br><br> -->
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