<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../includes/koneksi.php';

// Fungsi untuk mengubah bulan ke Bahasa Indonesia
function formatTanggalIndonesia($tanggal)
{
    $bulan = array(
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
    );
    $tanggalSplit = explode('-', $tanggal);
    return $tanggalSplit[2] . ' ' . $bulan[(int)$tanggalSplit[1]] . ' ' . $tanggalSplit[0];
}

// Cek ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data
    $data = null;

    // Coba ambil dari pelimpahan_sakit dulu
    $query1 = "SELECT *, 'Sakit Permanen' AS kategori FROM pelimpahan_sakit WHERE id_limpah_sakit = ?";
    $stmt1 = $koneksi->prepare($query1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $data = $result1->fetch_assoc();

    if (!$data) {
        // Kalau tidak ditemukan di sakit, coba di meninggal
        $query2 = "SELECT *, 'Meninggal Dunia' AS kategori FROM pelimpahan_meninggal WHERE id_limpah_meninggal = ?";
        $stmt2 = $koneksi->prepare($query2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data = $result2->fetch_assoc();
    }

    if (!$data) {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    echo "ID tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Rekomendasi</title>
    <link rel="icon" href="../../../assets/img/logo_kemenag.png">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px;
        }

        .header {
            text-align: center;
        }

        .header h2,
        .header p {
            margin: 5px 0;
        }

        .header img {
            width: 80px;
            height: auto;
        }

        .content {
            margin-top: 20px;
        }

        .content p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 0;
            line-height: 1.2;
            font-size: 14px;
            vertical-align: top;
        }

        td:first-child {
            width: 150px;
            white-space: nowrap;
        }

        .footer {
            margin-top: 60px;
            text-align: right;
            line-height: 0.5;
        }

        .footer strong {
            display: block;
        }

        .signature {
            text-align: right;
        }

        hr {
            border: 2px solid black;
            border-top: 4px double black;
            margin: 20px 0;
        }

        @media print {
            body {
                margin: 40px;
            }

            .header h2 {
                font-size: 20px;
            }

            .header p {
                font-size: 16px;
            }

            .content p {
                font-size: 14px;
                text-align: justify;
            }

            .signature {
                text-align: right;
                margin-top: 20px;
                /* Atur margin tanda tangan */
            }
        }
    </style>
</head>

<body onload="window.print();">
    <div class="header">
        <img align="left" src="../../../assets/img/logo_kemenag.png" alt="Logo Kemenag">
        <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
            KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
        <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
            Telp/Fax: (0511) 4721442 | Email: <a href="mailto:phukemenagbanjar@gmail.com">phukemenagbanjar@gmail.com</a>
        </p>
        <hr>
        <h2>REKOMENDASI <br>
            Nomor: <?php echo $data['nomor_surat']; ?>/Kk.17.03.5/Hj.<?php echo date('d/m/Y'); ?></h2>
    </div>

    <div class="content">
        <p>Berdasarkan surat Keputusan Direktur Jenderal Penyelenggaraan Haji dan Umrah Nomor: 130 tahun 2020 tanggal 28 Februari 2020 tentang Petunjuk Pelimpahan Nomor Porsi Jamaah Haji Meninggal Dunia atau Sakit Permanen. Kepala Kantor Kementerian Agama Kabupaten Banjar dengan ini menerangkan bahwa:</p>
        <table>
            <tr>
                <td>Nama</td>
                <td style="text-transform: uppercase; font-weight:bold;">: <?php echo $data['nama_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>Bin/Binti</td>
                <td style="text-transform: uppercase;">: <?php echo $data['nama_ayah_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>TTL</td>
                <td>: <?php echo $data['tempat_lahir_ahliwaris']; ?>, <?php echo formatTanggalIndonesia($data['tanggal_lahir_ahliwaris']); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: <?php echo $data['alamat_ahliwaris']; ?>, Kec. <?php echo $data['kecamatan_ahliwaris']; ?>, Kel. <?php echo $data['kelurahan_ahliwaris']; ?>, <?php echo $data['kode_pos_ahliwaris']; ?>, kab. Banjar</td>
            </tr>
            <tr>
                <td>Provinsi</td>
                <td>: Kalimantan Selatan</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: <?php echo $data['jenis_kelamin_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>: <?php echo $data['pekerjaan_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>Nomor HP</td>
                <td>: <?php echo $data['no_telepon_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>Adalah</td>
                <td>: <?php echo $data['status_dengan_jamaah']; ?></td>
            </tr>
            <tr>
                <td style="line-height: 30px; font-weight: bold;" colspan="2">Dari:</td>
            </tr>
            <tr>
                <td>Nama bin/binti</td>
                <td style="text-transform: uppercase;">: <?php echo $data['nama_jamaah']; ?></td>
            </tr>
            <tr>
                <td>TTL</td>
                <td>: <?php echo $data['tempat_lahir_jamaah']; ?>, <?php echo formatTanggalIndonesia($data['tanggal_lahir_jamaah']); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: <?php echo $data['alamat_jamaah']; ?>, Kec. <?php echo $data['kecamatan_jamaah']; ?>, Kel. <?php echo $data['kelurahan_jamaah']; ?>, <?php echo $data['kode_pos_jamaah']; ?>, kab. Banjar</td>
            </tr>
            <tr>
                <td>Provinsi</td>
                <td>: Kalimantan Selatan</td>
            </tr>
            <tr>
                <td>BPS-BPIH</td>
                <td>: <?php echo $data['bps']; ?></td>
            </tr>
            <tr>
                <td>Nomor Rekening</td>
                <td>: <?php echo $data['nomor_rekening']; ?></td>
            </tr>
            <tr>
                <td>No Porsi - SPPH</td>
                <td>: <?php echo $data['nomor_porsi']; ?> - <?php echo $data['spph_validasi']; ?></td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>: Untuk keperluan Pelimpahan nomor Porsi Jemaah Haji Kab. Banjar Tahun <?php echo date('Y'); ?> Karena <?php echo $data['kategori']; ?>.</td>
            </tr>
        </table>
        <p>Demikian rekomendasi ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>
    <br><br>
    <div style="margin-top: 5px; display: flex; justify-content: flex-end;">
        <table style="border: none; text-align: left; width: auto;">
            <tr>
                <td style="border: none; padding: 10px; line-height: 1.2;">
                    <p style="margin: 0;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_masuk_surat']); ?></p>
                    <p style="margin: 0;">An. Kepala Kantor Kementerian Agama</p>
                    <p style="margin: 0;">Kabupaten Banjar,</p>
                    <p style="margin: 0;">Kepala Seksi Peny. Haji dan Umrah</p>
                    <div style="margin: 20px 0; text-align: center;">
                        <img src="../../../assets/img/ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 50px; height: auto;">
                    </div>
                    <strong>
                        <p style="margin: 0;"><u>Erfan Maulana</u></p>
                        <p style="margin: 0;">NIP.198411042003121004</p>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>