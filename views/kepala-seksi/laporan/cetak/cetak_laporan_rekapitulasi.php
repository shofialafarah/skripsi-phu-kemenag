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
        <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN REKAPITULASI JAMAAH HAJI</h3>

        <table class="tabel-cetak">
            <thead>
                <tr>
                    <th class="kepala-cetak">No</th>
                    <th class="kepala-cetak">Kecamatan</th>
                    <th class="kepala-cetak">Jenis Pelayanan</th>
                    <th class="kepala-cetak">Jumlah Jamaah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data_rekap)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data_rekap as $row): ?>
                        <tr>
                            <td class="badan-cetak"><?= $no++ ?></td>
                            <td class="badan-cetak"><?= htmlspecialchars($row['kecamatan']) ?></td>
                            <td class="badan-cetak"><?= htmlspecialchars($row['jenis_pelayanan']) ?></td>
                            <td class="badan-cetak"><?= $row['jumlah'] ?> Jamaah</td>
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
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
