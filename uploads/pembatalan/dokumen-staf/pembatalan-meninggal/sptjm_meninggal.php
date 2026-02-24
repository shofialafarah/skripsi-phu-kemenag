<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../includes/koneksi.php';

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
    $id_batal_meninggal = $_GET['id'];

    // Query untuk mengambil data
    $query = "SELECT pm.*, p.kategori 
          FROM pembatalan_meninggal pm 
          LEFT JOIN pembatalan p ON pm.id_pembatalan = p.id_pembatalan 
          WHERE pm.id_batal_meninggal = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_batal_meninggal);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

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
    <title>SPTJM Pembatalan</title>
    <link rel="icon" href="../../../../assets/img/logo_kemenag.png">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        h1 {
            font-size: 16pt;
            text-align: center;
            max-width: 100%;
            word-wrap: break-word;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 0;
            line-height: 25px;
        }

        ol {
            padding-left: 20px;
        }

        ol li {
            text-align: justify;
            line-height: 1.5;
        }

        .signature {
            text-align: right;
        }

        .signature .ttd {
            text-transform: uppercase;
        }

        @media print {
            body {
                margin: 40px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td,
            th {
                padding: 0.5px;
                vertical-align: top;
            }
        }
    </style>
</head>

<body onload="window.print();">
    <h1 style="text-align: center;">SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</h1>

    <p>Saya yang bertanda tangan di bawah ini:</p>
    <table>
        <tr>
            <td>Nama</td>
            <td style="text-transform: uppercase;">: <b><?php echo $data['nama_ahliwaris']; ?></b> </td>
        </tr>
        <tr>
            <td>TTL</td>
            <td>: <?php echo $data['tempat_lahir_ahliwaris'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir_ahliwaris']); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat_ahliwaris'] . ', Kec. ' . $data['kecamatan_ahliwaris'] . ', Kel. ' . $data['kelurahan_ahliwaris'] . ', ' . $data['kode_pos_ahliwaris'] . ', Kab. Banjar'; ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: <?php echo $data['jenis_kelamin']; ?></td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>: <?php echo $data['pekerjaan_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: <?php echo $data['status_dengan_jamaah']; ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <b>Dari</b>
            </td>
        </tr>
        <tr>
            <td>Nama</td>
            <td style="text-transform: uppercase;">: <?php echo $data['nama_jamaah'] ?></td>
        </tr>
        <tr>
            <td>Bin/Binti</td>
            <td style="text-transform: uppercase;">: <?php echo $data['bin_binti']; ?></td>
        </tr>
        <tr>
            <td>Nomor Porsi/Validasi</td>
            <td>: <?php echo $data['nomor_porsi']; ?> - <?php echo $data['spph_validasi']; ?></td>
        </tr>
        <tr>
            <td>Sebab Pembatalan</td>
            <td>: <?php echo $data['kategori']; ?></td>
        </tr>
        <tr>
            <td>Telp/HP</td>
            <td>: <?php echo $data['no_telepon_ahliwaris']; ?></td>
        </tr>
    </table>

    <p>
        Dengan ini menyatakan:
    </p>
    <ol>
        <li>Bertanggung jawab atas kebenaran dan keabsahan seluruh dokumen pembatalan yang saya sampaikan kepada Kantor Kementerian Agama Kabupaten Banjar.</li>
        <li>Bertanggung jawab sepenuhnya bahwa saya tidak akan melibatkan pihak Kantor Kementerian Agama Kab. Banjar, apabila terjadi sesuatu hal dan lainnya yang dapat menyebabkan adanya kerugian Negara atau kerugian lainnya.</li>
        <li>Bertanggung jawab sepenuhnya bahwa apabila dikemudian hari ditemukan data yang tidak benar atau timbul gugatan atas keabsahan seluruh dokumen yang ada, maka saya siap bertanggung jawab secara administrasi dan atau pidana.</li>
    </ol>
    <p style="text-align: justify;">
        Demikian pernyataan tanggung jawab mutlak ini saya buat dengan keadaaan dan penuh tanggung jawab.
    </p>
    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
        <table style="border: none; text-align: left; width: auto;">
            <tr>
                <td style="border: none; padding: 10px; line-height: 1.2;">
                    <p style="margin: 0;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_surat']); ?></p>
                    <p style="margin: 0;">Yang Membuat Pernyataan,</p>
                    <br><br><br><br>
                    <strong>
                        <p style="margin: 0;"><?php echo $data['nama_ahliwaris']; ?></p>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>