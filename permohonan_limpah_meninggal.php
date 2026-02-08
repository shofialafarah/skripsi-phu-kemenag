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

if (isset($_GET['id'])) {
    $id_limpah_meninggal = $_GET['id'];

    // Query untuk mendapatkan data berdasarkan ID
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
    echo "ID pelimpahan tidak ditemukan.";
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
        h1, h3 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 5px;
            line-height: 15px;
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
        @media print {
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- <h1>Cetak Permohonan meninggal</h1> -->
    <table>
        <tr>
            <td>Lampiran</td>
            <td>: 1 (satu) berkas</td>
            <td class="date" style="text-align: right;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_masuk_surat']); ?></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: Mohon Pelimpahan Nomor Porsi <br>Jemaah Haji Yang <?php echo $data['alasan']; ?></td>
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
            <td>Nama</td>
            <td style="text-transform: uppercase;">: <?php echo $data['nama_ahli_waris']; ?></td>
        </tr>
        <tr>
            <td>Bin/ Binti</td>
            <td>: <?php echo $data['nama_ayah']; ?></td>
        </tr>
        <tr>
            <td>TTL</td>
            <td>: <?php echo $data['tempat_lahir'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir']); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat'] . ', Kec. ' . $data['kecamatan'] . ', Kel. ' . $data['kelurahan'] . ', Kab. Banjar' ; ?></td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>: <?php echo $data['provinsi']; ?></td>
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
            <td>Telp/ Hp</td>
            <td>: <?php echo $data['no_telepon']; ?></td>
        </tr>
        <tr>
            <td>Adalah</td>
            <td>: <?php echo $data['status']; ?></td>
        </tr>
        
        <tr>
            <td style="line-height: 25px;" colspan="2" > <b>Dari:</b></td>
        </tr>
        <tr>
            <td>Nama/ Binti</td>
            <td style="text-transform: uppercase;">: <?php echo $data['nama_jamaah']; ?> </td>
        </tr>
        <tr>
            <td>TTL</td>
            <td>: <?php echo $data['tempat_lahir_jamaah'];?>, <?php echo formatTanggalIndonesia($data['tanggal_lahir_jamaah']);?>  </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat_jamaah'] . ', Kec. ' . $data['kecamatan'] . ', Kel. ' . $data['kelurahan'] . ', Kab. Banjar' ; ?></td>
        </tr>
        <tr>
            <td>Provinsi</td>
            <td>: <?php echo $data['provinsi_jamaah']; ?> </td>
        </tr>
        <tr>
            <td>BPS-BIPIH</td>
            <td>: <?php echo $data['bps']; ?></td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: <?php echo $data['nomor_rekening']; ?></td>
        </tr>
        <tr>
            <td>Nomor Porsi/Validasi</td>
            <td>: <?php echo $data['nomor_porsi']; ?> - <?php echo $data['spph_validasi']; ?></td>
        </tr>
    </table>

    <p>
        Sebagai bahan pertimbangan (terlampir) <br>
        Demikian permohonan ini saya lampirkan, atas perhatian saya ucapkan terima kasih. <br>
        Wassalamu’alaikum Wr. Wb.
    </p>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">Hormat Saya,</p>
                        <br><br><br><br><br>
                        <strong>
                            <p style="margin: 0;"><?php echo $data['nama_ahliwaris']; ?></p>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
    
    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>
</html>
