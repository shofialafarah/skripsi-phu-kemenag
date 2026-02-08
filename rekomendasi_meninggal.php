<?php
// Koneksi ke database
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
    <title>Surat Rekomendasi</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; line-height: 1.3; margin: 20px; }
        .header { text-align: center; }
        .header h2, .header p { margin: 5px 0;}
        .header img {
            width: 100px;
            height: auto;
        }
        hr {border: 1px solid black; margin: 10px 0;}       
        .content { margin-top: 20px; }
        .content p { margin: 5px 0; }
        
        table { width: 100%; border-collapse: collapse; }
        td { padding: 0; line-height: 30px; vertical-align: top; }
        .footer { margin-top: 60px; text-align: right; line-height: 0.5;}
        .footer strong {display: block;}
        .signature { text-align: right; margin-top: 50px; }
        
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
        body {
            font-size: 12px; /* Atur ukuran font menjadi lebih kecil */
            line-height: 1.5; /* Kurangi jarak antar baris */
            margin: 0; /* Kurangi margin halaman */

        }
        table {
        width: 100%;
        border-collapse: collapse;
    }
        table td {
            padding: 1px; /* Kurangi padding pada tabel */
            line-height: 2.1;
            
        }
        .btn {
            display: none; /* Sembunyikan tombol */
        }
        .signature {
            text-align: right;
            margin-top: 20px; /* Atur margin tanda tangan */
        }
    }
        
    </style>
</head>
<body>

    <div class="header">
        <img align="left" src="logo_kemenag.png" alt="Logo Kemenag">
        <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
        KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
        <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
            Telp/Fax: (0511) 4721442 | Email: banjarkalsel@kemenag.go.id
        </p>
        <hr>
        <h2>REKOMENDASI <br>
        Nomor: <?php echo $data['nomor_surat']; ?>/Kk.17.03.5/Hj.<?php echo date('d/m/Y'); ?></h2>
    </div>

    <div class="content">
        <p>Berdasarkan surat Keputusan Direktur Jenderal Penyelenggaraan Haji dan Umrah Nomor: 130 tahun 2020 tanggal 28 Februari 2020 tentang Petunjuk Pelimpahan Nomor Porsi Jamaah Haji Meninggal Dunia atau Sakit Permanen Kepala Kantor Kementerian Agama Kabupaten Banjar dengan ini menerangkan bahwa:</p>
        <table>
            <tr><td>Nama</td><td style="text-transform: uppercase;">: <?php echo $data['nama_ahli_waris']; ?></td></tr>
            <tr><td>Bin/Binti</td><td style="text-transform: uppercase;">: <?php echo $data['nama_ayah']; ?></td></tr>
            <tr><td>TTL</td><td>: <?php echo $data['tempat_lahir']; ?>, <?php echo formatTanggalIndonesia($data['tanggal_lahir']); ?></td></tr>
            <tr><td>Alamat</td><td>: <?php echo $data['alamat']; ?>, Kec. <?php echo $data['kecamatan']; ?>, Kel. <?php echo $data['kelurahan']; ?>, kab. Banjar</td></tr>
            <tr><td>Provinsi</td><td>: <?php echo $data['provinsi']; ?></td></tr>
            <tr><td>Jenis Kelamin</td><td>: <?php echo $data['jenis_kelamin']; ?></td></tr>
            <tr><td>Pekerjaan</td><td>: <?php echo $data['pekerjaan']; ?></td></tr>
            <tr><td>Nomor HP</td><td>: <?php echo $data['no_telepon']; ?></td></tr>
            <tr><td>Adalah</td><td>: <?php echo $data['status']; ?></td></tr>
            <tr><td style="line-height: 25px;" colspan="2">dari:</td></tr>
            <tr><td>Nama bin/binti</td><td style="text-transform: uppercase;">: <?php echo $data['nama_jamaah']; ?></td></tr>
            <tr><td>TTL</td><td>: <?php echo $data['tempat_lahir_jamaah']; ?>, <?php echo formatTanggalIndonesia($data['tanggal_lahir_jamaah']); ?></td></tr>
            <tr><td>Alamat</td><td>: <?php echo $data['alamat_jamaah']; ?>, Kec. <?php echo $data['kecamatan']; ?>, Kel. <?php echo $data['kelurahan']; ?>, kab. Banjar</td></tr>
            <tr><td>Provinsi</td><td>: <?php echo $data['provinsi_jamaah']; ?></td></tr>
            <tr><td>BPS-BPIH</td><td>: <?php echo $data['bps']; ?></td></tr>
            <tr><td>Nomor Rekening</td><td>: <?php echo $data['nomor_rekening']; ?></td></tr>
            <tr><td>No Porsi - SPPH</td><td>: <?php echo $data['nomor_porsi']; ?> - <?php echo $data['spph_validasi']; ?></td></tr>
            <tr><td>Keterangan</td><td>: Untuk keperluan Pelimpahan nomor Porsi Jemaah Haji Kab. Banjar Tahun <?php echo date('Y'); ?> Karena <?php echo $data['alasan']; ?>.</td></tr>
        </table> 
        <p>Demikian rekomendasi ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
    </div>
    <br><br>
    <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
        <table style="border: none; text-align: left; width: auto;">
            <tr>
                <td style="border: none; padding: 10px; line-height: 1.2;">
                    <p style="margin: 0;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_masuk_surat']); ?></p>
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
    
    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>
</html>
