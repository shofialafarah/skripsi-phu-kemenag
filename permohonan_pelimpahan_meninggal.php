<?php
include 'koneksi.php';

// Fungsi untuk mengubah bulan ke Bahasa Indonesia
function formatTanggalIndonesia($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $tanggalSplit = explode('-', $tanggal);
    return $tanggalSplit[2] . ' ' . $bulan[(int)$tanggalSplit[1]] . ' ' . $tanggalSplit[0];
}

// Cek ID
if (isset($_GET['id'])) {
    $id_limpah_meninggal = $_GET['id'];

    // Query untuk mengambil data
    $query = "SELECT * FROM pelimpahan_meninggal WHERE id_limpah_meninggal = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_limpah_meninggal);
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
    <title>Permohonan Pelimpahan</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            align-items: left;
            width: 100px;
            height: auto;
        }       
        .header p {
            margin: 0;
        }
        .content {
            line-height: 1.6;
        }
        .content table {
            width: 100%;
            border-collapse: collapse; /* Menghilangkan jarak antar border */

        }
        .content tr {
            line-height: 1.2; /* Kurangi tinggi baris */
        }
        .content td {
            padding: 3px; /* Kurangi padding untuk merapatkan */
            vertical-align: top; /* Buat konten rata atas */
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .signature {
            margin-top: 60px;
            text-align: center;
        }
        .signature strong {
            display: block;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            font-size: 14px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-primary {
            background: linear-gradient(145deg, #0c5c2c 0%, #4caf50 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(12, 92, 44, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(12, 92, 44, 0.3);
            background: linear-gradient(145deg, #0a4f25 0%, #43a047 100%);
        }
        
        .container {
            /* width: 100%;
            max-width: 600px; */
            margin: auto;
            line-height: 1;
        }
        .row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .label {
            width: 80px; /* Atur lebar label */
        }
        .isi {
            flex: 1;
        }
        .date {
            text-align: right;
            white-space: nowrap; /* Agar teks tanggal tidak terputus */
        }
        @media print {
            body {
            font-size: 12px; /* Atur ukuran font menjadi lebih kecil */
            line-height: 1.2; /* Kurangi jarak antar baris */
            margin: 0.5cm; /* Kurangi margin halaman */
            }
            hr {
            margin: 10px 0; /* Mengurangi margin atas dan bawah */
            border: none; /* Hilangkan border default */
            border-top: 1px solid #000; /* Garis tipis hitam */
            height: 1px; /* Tinggi garis */
            }
            .header {
                line-height: 1.5;
            }
            .header h2 {
                font-size: 20px;
            }
            .header p {
                font-size: 16px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                margin-bottom: 10px;
            }
            td, th {
                padding: 2px; /* Kurangi padding di tabel */
                vertical-align: top;
            }
            h1, h2, h3, h4, h5, h6 {
                margin: 0; /* Hilangkan margin pada heading */
            }
            p {
                margin: 0; /* Hilangkan margin pada paragraf */
            }
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img align="left" src="logo_kemenag.png" alt="Logo Kemenag">
        <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
            KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
        <p>Jl. Sekumpul No.72 Kelurahan Jawa Martapura, Banjar 70614 <br>
            Telp/Fax: (0511) 4721442 | Email: banjarkalsel@kemenag.go.id
        </p>
    </div>

    <hr>

    <div class="container">
        <div class="row">
            <div class="label">Nomor</div>
            <div class="isi">: <?php echo $data['nomor_surat']; ?>/Kk.17.03-5/Hj.<?php echo date('d/m/Y'); ?></div>
            <div class="date">
                Martapura, <?php echo formatTanggalIndonesia($data['tanggal_masuk_surat']); ?>
            </div>
        </div>
        <div class="row">
            <div class="label">Sifat</div>
            <div class="isi">: Segera</div>
        </div>
        <div class="row">
            <div class="label">Lampiran</div>
            <div class="isi">: 1 (satu) berkas</div>
        </div>
        <div class="row">
            <div class="label">Perihal</div>
            <div class="isi">: Permohonan Pelimpahan Porsi</div>
        </div>
    </div>
        <br>
    <div class="content">
        <p>Kepada Yth,.<br>
            Direktur Pelayanan Dalam Negeri <br>
            Ditje<br>
            Kementerian Agama Republik Indonesia<br>
            Jalan Lapangan Banteng No. 3 - 4 <br>
            Jakarta
        </p>

        <p>Assalamu’alaikum Wr. Wb. <br>
            Bersama ini kami sampaikan permohonan Pelimpahan Porsi calon Jamaah Haji Kabupaten Banjar Provinsi Kalimantan Selatan
            untuk dapat diproses sebagaimana daftar berikut:</p>
        <?php $nomor = 1; // Nomor urut dimulai dari 1 ?>
        <table border="1">
            <tr>
                <th rowspan="2">NO.</th>
                <th rowspan="2">NO. PORSI - SPPH</th>
                <th rowspan="2">NAMA</th>
                <th rowspan="2">KETERANGAN PELIMPAHAN PORSI</th>
                <th colspan="3">REKENING BARU</th>
            </tr>
            <tr>                
                <th>BANK</th>
                <th>NAMA</th>
                <th>REKENING</th>
            </tr>
            <tr>
                <td><?php echo $nomor; ?></td>
                <td><?php echo $data['nomor_porsi'] . ' - ' . $data['spph_validasi']; ?></td>
                <td><?php echo $data['nama_jamaah']; ?></td>
                <td><?php echo $data['alasan']; ?></td>
                <td><?php echo $data['bank_ahli_waris']; ?></td>
                <td><?php echo $data['nama_ahli_waris']; ?></td>
                <td><?php echo $data['no_rekening_ahli_waris']; ?></td>
            </tr>
        </table>
            <p>Wassalamu’alaikum Wr. Wb.</p>
    </div>

    <div class="signature">
        <div style="margin-top: 50px; display: flex; justify-content: center;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">An. Kepala Kantor Kementerian Agama</p>
                        <p style="margin: 0;">Kabupaten Banjar,</p>
                        <p style="margin: 0;">Kepala Seksi Peny. Haji dan Umrah</p>
                        <div style="margin: 20px 0; text-align: center;">
                            <img src="ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 50px; height: auto;">
                        </div>
                        <strong>
                            <p style="margin: 0;"><u>Erfan Maulana</u></p>
                            <p style="margin: 0;">NIP.198411042003121004</p>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
        <br> <br> <br>
        <p align="left">Tembusan : Kepada Yth, <br>
                        1. Kepala Kantor Wilayah Kementerian Agama <br>
                        Provinsi Kalimantan Selatan</p>
    </div>

    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>
</html>
