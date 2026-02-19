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
    $id_batal_ekonomi = $_GET['id'];

    // Query untuk mengambil data
    $query = "SELECT pe.*, p.kategori 
          FROM pembatalan_ekonomi pe 
          LEFT JOIN pembatalan p ON pe.id_pembatalan = p.id_pembatalan 
          WHERE pe.id_batal_ekonomi = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_batal_ekonomi);
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
    <title>Surat Permohonan Pembatalan</title>
    <link rel="icon" href="../../../img/logo_kemenag.png">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 0;
        }

        .header h2,
        .header p {
            margin: 0;
            padding: 0;
        }

        .header p {
            margin-top: 5px;
        }

        .header img {
            align-items: left;
            width: 80px;
            height: auto;
        }

        .header p {
            font-size: 14px;
        }

        .content {
            line-height: 1.2;
        }

        .content table {
            width: 100%;
            border-collapse: collapse;

        }

        .content tr {
            line-height: 1.2;
        }

        .content td {
            padding: 3px;
            vertical-align: top;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .signature {
            margin-top: 60px;
            text-align: right;
        }

        .signature strong {
            display: block;
        }

        .container {
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .label {
            width: 80px;
        }

        .isi {
            flex: 1;
        }

        .date {
            text-align: right;
            white-space: nowrap;
        }

        hr {
            border: 2px solid black;
            border-top: 4px double black;
            margin: 20px 0;
        }

        @media print {
            body {
                font-size: 12px;
                line-height: 1.2;
                margin: 40px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            .container {
                line-height: 0.5;
            }

            td,
            th {
                padding: 0;
                vertical-align: top;
            }
        }
    </style>
</head>

<body onload="window.print();">
    <div class="header">
        <img align="left" src="../../../img/logo_kemenag.png" alt="Logo Kemenag">
        <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
            KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
        <p>Jl. Sekumpul No.72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
            Telp/Fax: (0511) 4721442 | Email: <a href="mailto:phukemenagbanjar@gmail.com">phukemenagbanjar@gmail.com</a>
        </p>

    </div>
    <hr>

    <div class="container">
        <div class="row">
            <div class="label">Nomor</div>
            <div class="isi">: B-<?php echo $data['nomor_surat']; ?>/Kk.17.03-5/Hj.<?php echo date('d/m/Y'); ?></div>
            <div class="date">
                Martapura, <?php echo formatTanggalIndonesia($data['tanggal_surat']); ?>
            </div>
        </div>

        <div class="row">
            <div class="label">Sifat</div>
            <div class="isi">: Penting</div>
        </div>

        <div class="row">
            <div class="label">Lampiran</div>
            <div class="isi">: 1 (satu) berkas</div>
        </div>

        <div class="row">
            <div class="label">Perihal</div>
            <div class="isi">: Mohon Pembatalan dan Pengembalian Uang Setoran Awal</div>
        </div>
    </div>
    <br>

    <p>Kepada Yth, <br>
        Kepala Kantor Wilayah Kementerian Agama <br>
        Provinsi Kalimantan Selatan<br>
        Up. Kabid Penyelenggaraan Haji dan Umrah<br>
        di Banjarmasin
    </p>

    <p>Assalamu’alaikum Wr. Wb. <br>
        Berdasarkan Surat Permohonan Pembatalan dan sekaligus pengembalian uang setoran awal Calon Jamaah Haji atas nama:</p>
    <div class="content">
        <table>
            <tr>
                <td style="width: 30%;">Nama / Bin</td>
                <td>: <?php echo $data['nama_jamaah']; ?></td>
            </tr>
            <tr>
                <td>TTL</td>
                <td>: <?php echo $data['tempat_lahir'] . ", " . formatTanggalIndonesia($data['tanggal_lahir']); ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: <?php echo $data['alamat'] . ', Kec. ' . $data['kecamatan'] . ', Kel. ' . $data['kelurahan'] . ', ' . $data['kode_pos'] . ', Kab. Banjar'; ?></td>
            </tr>
            <tr>
                <td>BPS-BPIH</td>
                <td>: <?php echo $data['bps']; ?></td>
            </tr>
            <tr>
                <td>Nomor Rekening</td>
                <td>: <?php echo $data['nomor_rek']; ?></td>
            </tr>
            <tr>
                <td>Nomor Porsi/Validasi</td>
                <td>: <?php echo $data['nomor_porsi'] . ' - ' . $data['spph_validasi']; ?></td>
            </tr>
            <tr>
                <td>Sebab Pembatalan</td>
                <td>: <?php echo $data['kategori']; ?></td>
            </tr>
        </table>

        <p style="text-align: justify;">Sehubungan dengan hal di atas, kiranya Bapak dapat memproses pembatalan sekaligus pengembalian uang setoran awal/tabungan BPIH calon jamaah haji tersebut, dan sebagai bahan administrasi disampaikan:</p>
        <ol>
            <li>Permohonan Pembatalan dan Pengembalian Tabungan;</li>
            <li>Surat Pernyataan Pembatalan bermaterai;</li>
            <li>Bukti Setoran BPS BPIH (asli, kuning dan biru);</li>
            <li>Bukti Transfer uang ke rekening Menteri Agama;</li>
            <li>Surat Pendaftaran Pergi Haji (SPPH);</li>
            <li>Foto Copy Kartu Tanda Penduduk(KTP);</li>
        </ol>

        <p style="text-align: justify;">Demikian disampaikan untuk diketahui dan sebagai bahan selanjutnya, serta mohon diproses penyelesaiannya.
            Atas perhatian kami ucapkan terima kasih. <br>
            Wassalamu’alaikum Wr. Wb.
        </p>
    </div>
    <div style="margin-top: 5px; display: flex; justify-content: flex-end;">
        <table style="border: none; text-align: left; width: auto;">
            <tr>
                <td style="border: none; padding: 10px; line-height: 1.2;">
                    <p style="margin: 0;">An. Kepala Kantor Kementerian Agama</p>
                    <p style="margin: 0;">Kabupaten Banjar,</p>
                    <p style="margin: 0;">Kasi Peny. Haji dan Umrah</p>
                    <div style="margin: 20px 0; text-align: center;">
                        <img src="../../../img/ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 50px; height: auto;">
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