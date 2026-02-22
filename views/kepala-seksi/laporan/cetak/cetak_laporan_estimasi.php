<?php
session_start();
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

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
    <link rel="icon" href="../../../../assets/img/logo_kemenag.png">
    <link rel="stylesheet" href="../assets/css/cetak.css">
</head>

<body>
    <div class="cetak-wrapper">
        <div class="kop-surat">
            <img align="left" src="../../../../assets/img/logo_kemenag.png" alt="Logo Kemenag">
            <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
                KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
            <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
                Telp/Fax: (0511) 4721442 | Email: <a href="mailto:phukemenagbanjar@gmail.com">phukemenagbanjar@gmail.com</a>
            </p>
        </div>

        <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN DATA ESTIMASI JAMAAH HAJI</h3>
        <p style="text-align: center; margin-top: 0; margin-bottom: 25px; font-size: 12px;">
            <?php echo $keterangan_tanggal_filter; ?>
        </p>

        <table class="tabel-cetak">
            <thead>
                <tr>
                    <th class="kepala-cetak">No.</th>
                    <th class="kepala-cetak">Nomor Porsi</th>
                    <th class="kepala-cetak">Nama Jamaah</th>
                    <th class="kepala-cetak">Umur</th>
                    <th class="kepala-cetak">Tanggal Pendaftaran</th>
                    <th class="kepala-cetak">Telah Menunggu</th>
                    <th class="kepala-cetak">Estimasi Berangkat</th>
                    <th class="kepala-cetak">Sisa Menunggu</th>
                    <th class="kepala-cetak">Masa Menunggu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (!empty($data_estimasi)) {
                    foreach ($data_estimasi as $row) {
                        echo "<tr>";
                        echo "<td class='badan-cetak'>" . $no++ . "</td>";
                        $nomor_porsi = !empty($row['nomor_porsi']) ? htmlspecialchars($row['nomor_porsi']) : '-';
                        echo "<td class='badan-cetak'>$nomor_porsi</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['umur']) . "</td>";
                        echo "<td class='badan-cetak'>" . date('d-m-Y', strtotime($row['tgl_pendaftaran'])) . "</td>";
                        $tgl_daftar = new \DateTime($row['tgl_pendaftaran']);
                        $sekarang = new \DateTime();
                        $diff = $tgl_daftar->diff($sekarang);

                        if ($diff->y > 0) {
                            echo "<td class='badan-cetak'>" . $diff->y . " Tahun " . $diff->m . " Bulan</td>";
                        } else {
                            echo "<td class='badan-cetak'>" . $diff->m . " Bulan</td>";
                        }
                        echo "<td class='badan-cetak'>" . date('d-m-Y', strtotime($row['estimasi_berangkat'])) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['sisa_menunggu']) . " Tahun</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['masa_menunggu']) . " Tahun</td>";
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