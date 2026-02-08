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
            width: 100px;
            height: auto;
        }
        .header h2, .header p { margin: 5px 0;}
        .content {
            line-height: 1.6;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
        }
        .content td {
            vertical-align: top;
            padding: 3px;
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
        hr {
            border: 2px solid black;
            border-top: 4px double black;
            margin: 20px 0;
        }

        .email {
            color: #007bff;
        }
        @media print {
            body {
            font-size: 12px; /* Atur ukuran font menjadi lebih kecil */
            line-height: 1.2; /* Kurangi jarak antar baris */
            margin: 40px; /* Kurangi margin halaman */
            }
            table {
                width: 100%;
                border-collapse: collapse;
                line-height: 20px;
            }
            td, th {
                padding: 2px; /* Kurangi padding di tabel */
                vertical-align: top;
            }
            h1, h2, h3, h4, h5, h6 {
                margin: 0; /* Hilangkan margin pada heading */
            }
            .header h2 {
                font-size: 20px;
            }
            .header p {
                font-size: 16px;
            }
            .kop-surat {
                line-height: 1.0;
            }
            .isi {
                line-height: 1.2;
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
        Telp/Fax: (0511) 4721442 | Email: <u class="email">banjarkalsel@kemenag.go.id</u> </p>
        <hr>
    </div>

    

    <div class="content">
        <table class="kop-surat">
            <tr>
                <td style="width: 20%;">Nomor</td>
                <td>: B-<?php echo $data['nomor_surat']; ?> /Kk.17.03-5/Hj.<?php echo date('d/m/Y'); ?></td>
                <td style="text-align: right;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_surat']); ?></td>
            </tr>
            <tr>
                <td>Sifat</td>
                <td>: -</td> 
            </tr>
            <tr>                
                <td>Lampiran</td>
                <td>: 1 (satu) berkas</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: Mohon Pembatalan dan Pengembalian Uang Setoran Awal</td>
            </tr>
        </table>

        <br>
        
        <p>Kepada Yth, <br>
        Kepala Kantor Wilayah Kementerian Agama Provinsi Kalimantan Selatan<br>
            Up. Kabid Penyelenggaraan Haji dan Umrah<br>
            di Banjarmasin</p>
        <br>

        <p>Assalamu’alaikum Wr. Wb. <br>

        Berdasarkan Surat Permohonan Pembatalan dan sekaligus pengembalian uang setoran awal Calon Jamaah Haji atas nama:</p>

        <table class="isi">
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
                <td>: <?php echo $data['alamat'] . ', Kec. ' . $data['kecamatan'] . ', Kel. ' . $data['kelurahan'] . ', ' . $data['kode_pos'] . ', Kab. Banjar' ; ?></td>
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

        <p>Sehubungan dengan hal di atas, kiranya Bapak dapat memproses pembatalan sekaligus pengembalian uang setoran awal/tabungan BPIH calon jamaah haji tersebut, dan sebagai bahan administrasi disampaikan:</p>
        <ol>
            <li>Permohonan Pembatalan dan Pengembalian Tabungan;</li>
            <li>Surat Pernyataan Pembatalan bermaterai;</li>
            <li>Bukti Setoran BPS BPIH (asli, kuning dan biru);</li>
            <li>Bukti Transfer uang ke rekening Menteri Agama;</li>
            <li>Surat Pendaftaran Pergi Haji (SPPH);</li>
            <li>Foto Copy Kartu Tanda Penduduk(KTP);</li>
        </ol>

        <p>Demikian disampaikan untuk diketahui dan sebagai bahan selanjutnya, serta mohon diproses penyelesaiannya.
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

    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>
</html>
