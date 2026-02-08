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
    <title>SPTJM Keperluan Ekonomi</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        h1 {
            font-size: 16pt; /* Ukuran font lebih kecil */
            text-align: center;
            max-width: 100%; /* Pastikan muat di halaman */
            word-wrap: break-word; /* Jika sangat panjang, bisa terputus dengan baik */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 8px;
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
        .signature {
            text-align: right;
        }
        .signature .ttd {
            text-transform: uppercase;
        }
        @media print {
            body {
            font-size: 16px; /* Atur ukuran font menjadi lebih kecil */
            line-height: 1.2; /* Kurangi jarak antar baris */
            margin: 0.5cm; /* Kurangi margin halaman */
            }
            table {
                width: 100%;
                border-collapse: collapse;
                line-height: 1.0;
            }
            td, th {
                padding: 3px; /* Kurangi padding di tabel */
                vertical-align: top;
            }
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</h1>

    <p>Saya yang bertanda tangan di bawah ini:</p>
    <table>
        <tr>
            <td>Nama</td>
            <td>: <?php echo $data['nama_jamaah']; ?></td>
        </tr>
        <tr>
            <td>Bin/Binti</td>
            <td>: <?php echo $data['bin_binti']; ?></td>
        </tr>
        <tr>
            <td>TTL</td>
            <td>: <?php echo $data['tempat_lahir'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir']); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat'] . ', Kec. ' . $data['kecamatan'] . ',  Kel. ' .  $data['kelurahan'] . ', ' . $data['kode_pos'] . ', Kab. Banjar'; ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: <?php echo $data['jenis_kelamin']; ?></td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>: <?php echo $data['pekerjaan']; ?></td>
        </tr>
        <tr>
            <td>Nomor Porsi/Validasi</td>
            <td>: <?php echo $data['nomor_porsi']; ?> / <?php echo $data['spph_validasi']; ?></td>
        </tr>
        <tr>
            <td>BPS/BPIH</td>
            <td>: <?php echo $data['bps']; ?></td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: <?php echo $data['nomor_rek']; ?></td>
        </tr>
        <tr>
            <td>Sebab Pembatalan</td>
            <td>: <?php echo $data['kategori']; ?></td>
        </tr>
        <tr>
            <td>Telp/HP</td>
            <td >: <?php echo $data['no_telepon']; ?></td>
        </tr>
    </table>

    <p>
        Dengan ini menyatakan:
    </p>
    <ol>
        <li>Bertanggung jawab atas kebenaran dan keabsahan seluruh dokumen pembatalan yang saya sampaikan kepada Kantor Kementerian Agama Kabupaten Banjar;</li>
        <li>Bertanggung jawab sepenuhnya apabila tidak ada kelalaian pihak Kantor Kementerian Agama Kab. Banjar, apabila terjadi sesuatu hal dan lainnya yang dapat menyebabkan adanya kerugian Negara atau kerugian lainnya;</li>
        <li>Bertanggung jawab sepenuhnya bahwa apabila dikemudian hari ditemukan data yang tidak benar atau timbul gugatan atas keabsahan seluruh dokumen yang ada, maka saya siap bertanggung jawab secara administrasi dan atau pidana.</li>
    </ol>
        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_surat']); ?></p>
                        <p style="margin: 0;">Yang Membuat Pernyataan,</p>
                        <br><br><br><br><br>
                        <strong>
                            <p style="margin: 0;"><?php echo $data['nama_jamaah']; ?></p>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
    
    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>
</html>
