<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_pembatalan = $_GET['id'];

    // Query untuk mendapatkan data berdasarkan ID
    $query = "SELECT * FROM pembatalan_haji WHERE id_pembatalan = '$id_pembatalan'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
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
    <title>Cetak Pembatalan Keperluan Ekonomi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            padding: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Cetak Pembatalan Haji - Keperluan Ekonomi</h1>

    <p>
        <strong>Martapura</strong>, <?php echo date('d F Y'); ?><br>
        Kepada Yth,<br>
        Kepala Kantor Kementerian Agama<br>
        Kabupaten Banjar<br>
        Up. Kasi Penyelenggaraan Haji dan Umrah<br>
        di Martapura
    </p>

    <p>Assalamu’alaikum Wr. Wb.</p>
    <p>Saya yang bertanda tangan di bawah ini:</p>

    <table>
        <tr>
            <td>Nama / Bin</td>
            <td>: <?php echo $data['nama_jamaah'] . ' / ' . $data['bin_binti']; ?></td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>: <?php echo $data['tempat_lahir'] . ', ' . date('d F Y', strtotime($data['tanggal_lahir'])); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat']; ?></td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>: <?php echo $data['kecamatan']; ?></td>
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
            <td>BPS</td>
            <td>: <?php echo $data['bps']; ?></td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: <?php echo $data['nomor_rek']; ?></td>
        </tr>
        <tr>
            <td>Nomor Porsi</td>
            <td>: <?php echo $data['nomor_porsi']; ?></td>
        </tr>
        <tr>
            <td>SPPH / Validasi</td>
            <td>: <?php echo $data['spph_validasi']; ?></td>
        </tr>
        <tr>
            <td>Tanggal Surat</td>
            <td>: <?php echo date('d F Y', strtotime($data['tanggal_surat'])); ?></td>
        </tr>
        <tr>
            <td>Tanggal Register</td>
            <td>: <?php echo date('d F Y', strtotime($data['tanggal_register'])); ?></td>
        </tr>
        <tr>
            <td>Nomor Surat</td>
            <td>: <?php echo $data['nomor_surat']; ?></td>
        </tr>
        <tr>
            <td>Nominal Setoran</td>
            <td>: Rp <?php echo number_format($data['nominal_setoran'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Alasan Pembatalan</td>
            <td>: <?php echo $data['alasan']; ?></td>
        </tr>
    </table>

    <p>
        Dengan ini menyampaikan permohonan pembatalan keberangkatan haji dan sekaligus memohon pengembalian Uang Setoran Awal/Lunas BPIH.
        Sebagai bahan pertimbangan, terlampir dokumen pendukung.
    </p>
    <p>Demikian permohonan ini saya sampaikan. Mohon kiranya dapat diproses penyelesaiannya. Atas perhatian, saya ucapkan terima kasih.</p>
    <p>Wassalamu’alaikum Wr. Wb.</p>

    <p style="text-align: right;">Hormat saya,<br><br><br><br><strong><?php echo $data['nama_jamaah']; ?></strong></p>
</body>
</html>
