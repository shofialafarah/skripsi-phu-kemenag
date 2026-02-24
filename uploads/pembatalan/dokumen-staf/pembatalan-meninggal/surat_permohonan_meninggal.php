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
function formatTanggalIndonesia($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $tanggalSplit = explode('-', $tanggal);
    return $tanggalSplit[2] . ' ' . $bulan[(int)$tanggalSplit[1]] . ' ' . $tanggalSplit[0];
}

if (isset($_GET['id'])) {
    $id_batal_meninggal = $_GET['id'];

    // Query untuk mendapatkan data berdasarkan ID
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
    echo "ID pembatalan tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Permohonan Jamaah Pembatalan</title>
    <link rel="icon" href="../../../../assets/img/logo_kemenag.png">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        h1, h3 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        @media print {
            body {
                margin: 40px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            td, th {
                padding: 0.5px;
                line-height: 25px;
                vertical-align: top;
            }
            
        .isi-data {
            padding: 0px;
            line-height: 25px;
            vertical-align: top;
        }
            .date {
                text-align: right;
                white-space: nowrap; /* Agar teks tanggal tidak terputus */
            }
            .perihal {
                line-height: normal;/* Agar teks perihal tidak terputus */
            }
        }
    </style>
</head>
<body onload="window.print();">
    <table>
        <tr>
            <td>Lampiran</td>
            <td>: 1 (satu) berkas</td>
            <td class="date" style="text-align: right;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_surat']); ?></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td class="perihal">: Mohon Pembatalan dan Pengembalian Uang Setoran Haji</td>
        </tr>
    </table>
    <p>
        Kepada Yth. <br>
        Kepala Kantor Kementrian Agama<br>
        Kabupaten Banjar<br>
        Up. Kasi Penyelenggara Haji dan Umrah <br>
        di Martapura
    </p>

    <p>Assalamu’alaikum Wr. Wb.</p>
    <p>Saya yang bertanda tangan di bawah ini:</p>

    <table>
        <!-- data ahliwaris -->
        <tr>
            <td class="isi-data">Nama</td>
            <td class="isi-data" style="text-transform: uppercase;">: <b><?php echo $data['nama_ahliwaris']; ?></b></td>
        </tr>
        <tr>
            <td class="isi-data">TTL</td>
            <td class="isi-data">: <?php echo $data['tempat_lahir_ahliwaris'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir_ahliwaris']); ?></td>
        </tr>
        <tr>
            <td class="isi-data">Alamat</td>
            <td class="isi-data">: <?php echo $data['alamat_ahliwaris'] . ', Kec. ' . $data['kecamatan_ahliwaris'] . ', Kel. ' . $data['kelurahan_ahliwaris'] . ', ' . $data['kode_pos_ahliwaris'] . ', Kab. Banjar' ; ?></td>
        </tr>
        <tr>
            <td class="isi-data">Nama di BPS</td>
            <td class="isi-data" style="text-transform: uppercase;">: <?php echo $data['nama_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">BPS/BIPIH</td>
            <td class="isi-data">: <?php echo $data['bank_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">Nomor Rekening</td>
            <td class="isi-data">: <?php echo $data['no_rekening_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">Status</td>
            <td class="isi-data">: <?php echo $data['status_dengan_jamaah']; ?></td>
        </tr>
        <tr>
            <td style="line-height: 20px;" colspan="2">dengan ini menyampaikan permohonan pembatalan berangkat haji dan sekaligus memohon pengembalian Uang Setoran Awal/lunas BIPIH Saya, atas nama:</td>
        </tr>
        <tr>
            <td class="isi-data">Nama</td>
            <td class="isi-data" style="text-transform: uppercase;">: <b><?php echo $data['nama_jamaah']; ?></b> </td>
        </tr>
        <tr>
            <td class="isi-data">Bin / Binti</td>
            <td class="isi-data" style="text-transform: uppercase;">: <?php echo $data['bin_binti']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">TTL</td>
            <td class="isi-data">: <?php echo $data['tempat_lahir'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir']); ?></td>
        </tr>
        <tr>
            <td class="isi-data">Alamat</td>
            <td class="isi-data">: <?php echo $data['alamat'] . ', Kec. ' . $data['kecamatan'] . ', Kel. ' . $data['kelurahan'] . ', ' . $data['kode_pos'] . ', Kab. Banjar' ; ?></td>
        </tr>
        <tr>
            <td class="isi-data">BPS/BIPIH</td>
            <td class="isi-data">: <?php echo $data['bps']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">Nomor Rekening</td>
            <td class="isi-data">: <?php echo $data['nomor_rek']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">Nomor Porsi/Validasi</td>
            <td class="isi-data">: <?php echo $data['nomor_porsi']; ?> - <?php echo $data['spph_validasi']; ?></td>
        </tr>
        <tr>
            <td class="isi-data">Sebab Pembatalan</td>
            <td class="isi-data">: <?php echo $data['kategori']; ?></td>
        </tr>
    </table>

    <p style="text-align: justify;">
         Permohonan ini saya sampaikan dengan alasan bahwa saya mengalami musibah meninggal dunia.
        Sebagai bahan pertimbangan, terlampir dokumen pendukung. 
        Demikian permohonan ini saya sampaikan. Mohon kiranya dapat diproses penyelesaiannya. <br>
        Atas perhatian, saya ucapkan terima kasih.
    </p>
        <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">Hormat Saya,</p>
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
