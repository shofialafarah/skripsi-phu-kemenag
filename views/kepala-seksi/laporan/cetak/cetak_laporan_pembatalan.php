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

// Query yang sama dengan laporan_pembatalan.php
$query = "
    SELECT
        pb.tanggal_verifikasi,
        pb.kategori,
        pb.status_verifikasi,
        COALESCE(pe.nomor_porsi, pm.nomor_porsi) AS nomor_porsi,
        COALESCE(pe.nama_jamaah, pm.nama_ahliwaris) AS nama_pengaju,
        COALESCE(pe.no_telepon, pm.no_telepon_ahliwaris) AS no_telepon,
        COALESCE(pe.alamat, pm.alamat_ahliwaris) AS alamat,
        j.email
    FROM
        pembatalan pb
    LEFT JOIN pembatalan_ekonomi pe 
        ON pb.id_pembatalan = pe.id_pembatalan 
        AND pb.kategori = 'Keperluan Ekonomi'
    LEFT JOIN pembatalan_meninggal pm 
        ON pb.id_pembatalan = pm.id_pembatalan 
        AND pb.kategori = 'Meninggal Dunia'
    LEFT JOIN jamaah j 
        ON j.id_jamaah = pb.id_jamaah
";

$where_conditions = [];
$params = [];
$types = '';

// Kondisi wajib: kategori harus 'Keperluan Ekonomi' atau 'Meninggal Dunia'
$where_conditions[] = "pb.kategori IN ('Keperluan Ekonomi', 'Meninggal Dunia')";

// PERBAIKAN: Tambahkan filter tanggal dengan DATE() untuk akurasi
if ($start_date && $end_date) {
    $where_conditions[] = "DATE(pb.tanggal_verifikasi) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

// Gabungkan semua kondisi WHERE
if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(' AND ', $where_conditions);
}

$query .= " ORDER BY pb.tanggal_verifikasi DESC";

$stmt = null;
$result = null;

// Execute query
if (!empty($params)) {
    $stmt = $koneksi->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "Error preparing statement: " . $koneksi->error;
        $result = false;
    }
} else {
    $result = $koneksi->query($query);
    if ($result === false) {
        echo "Error executing query: " . $koneksi->error;
    }
}

$data_pembatalan = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pembatalan[] = $row;
    }
}

// Format tanggal untuk kop surat
$tanggal = date('d');
$bulan_angka = date('m');
$tahun = date('Y');
$bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
$format_tanggal = $tanggal . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;

// Fungsi format tanggal Indonesia
function formatTanggalIndo($tanggal_string)
{
    if (empty($tanggal_string)) return '';
    
    $timestamp = strtotime($tanggal_string);
    if ($timestamp === false) return '';
    
    $tanggal = date('d', $timestamp);
    $bulan_angka = date('m', $timestamp);
    $tahun = date('Y', $timestamp);

    $daftar_bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    return $tanggal . ' ' . $daftar_bulan[$bulan_angka] . ' ' . $tahun;
}

// Keterangan filter tanggal
$keterangan_tanggal_filter = '';
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pembatalan</title>
    <link rel="icon" href="../assets/img/logo_kemenag.png">
    <link rel="stylesheet" href="../assets/css/cetak.css">
</head>

<body>
    <div class="cetak-wrapper">
        <div class="kop-surat">
            <img align="left" src="../assets/img/logo_kemenag.png" alt="Logo Kemenag">
            <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
                KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
            <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
                Telp/Fax: (0511) 4721442 | Email: <a href="mailto:phukemenagbanjar@gmail.com">phukemenagbanjar@gmail.com</a>
            </p>
        </div>

        <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN DATA PEMBATALAN JAMAAH HAJI</h3>
        <p style="text-align: center; margin-top: 0; margin-bottom: 25px; font-size: 12px;">
            <?php echo $keterangan_tanggal_filter; ?>
        </p>

        <table class="tabel-cetak">
            <thead>
                <tr>
                    <th class="kepala-cetak">No.</th>
                    <th class="kepala-cetak">Kategori</th>
                    <th class="kepala-cetak">Nomor Porsi</th>
                    <th class="kepala-cetak">Nama Pengaju</th>
                    <th class="kepala-cetak">No. Telepon</th>
                    <th class="kepala-cetak">Alamat</th>
                    <th class="kepala-cetak">Tgl Verifikasi</th>
                    <th class="kepala-cetak">Status Pembatalan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (!empty($data_pembatalan)) {
                    foreach ($data_pembatalan as $row) {
                        $status_print = '';
                        $status_raw = strtolower($row['status_verifikasi']);
                        switch ($status_raw) {
                            case 'pending':
                                $status_print = "Pending";
                                break;
                            case 'disetujui':
                                $status_print = "Disetujui";
                                break;
                            case 'ditolak':
                                $status_print = "Ditolak";
                                break;
                            default:
                                $status_print = ucfirst($status_raw);
                                break;
                        }

                        echo "<tr>";
                        echo "<td class='badan-cetak'>" . $no++ . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['kategori']) . "</td>";
                        
                        $nomor_porsi = !empty($row['nomor_porsi']) ? htmlspecialchars($row['nomor_porsi']) : '-';
                        echo "<td class='badan-cetak'>$nomor_porsi</td>";
                        
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['nama_pengaju']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['no_telepon']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['alamat']) . "</td>";
                        
                        $tanggalVerifikasi = $row['tanggal_verifikasi'];
                        echo "<td class='badan-cetak'>";
                        if (!empty($tanggalVerifikasi)) {
                            echo date('d-m-Y', strtotime($tanggalVerifikasi));
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                        
                        echo "<td class='badan-cetak'>" . $status_print . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='badan-cetak'>Tidak ada data berkas pembatalan jamaah haji yang ditemukan.</td></tr>";
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
                            <img src="../assets/img/ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 80px; height: auto;">
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
            window.onload = function() {
                window.print();
            };
        </script>
    </div>
</body>
</html>