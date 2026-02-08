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
            body {
                font-size: 16px; /* Atur ukuran font menjadi lebih kecil */
                line-height: 1.2; /* Kurangi jarak antar baris */
                margin: 40px; /* Kurangi margin halaman */
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            td, th {
                padding: 2px; /* Kurangi padding di tabel */
                vertical-align: top;
            }
            .btn {
                display: none;
            }
            .date {
                text-align: right;
                white-space: nowrap; /* Agar teks tanggal tidak terputus */
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
            <td class="date" style="text-align: right;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_surat']); ?></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: Mohon Pembatalan dan Pengembalian Uang Setoran Haji</td>
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
            <td style="text-transform: uppercase;">: <b><?php echo $data['nama_ahliwaris']; ?></b></td>
        </tr>
        <tr>
            <td>TTL</td>
            <td>: <?php echo $data['tempat_lahir_ahliwaris'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir_ahliwaris']); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat_ahliwaris'] . ', Kec. ' . $data['kecamatan_ahliwaris'] . ', Kel. ' . $data['kelurahan_ahliwaris'] . ', ' . $data['kode_pos_ahliwaris'] . ', Kab. Banjar' ; ?></td>
        </tr>
        <tr>
            <td>Nama di BPS</td>
            <td style="text-transform: uppercase;">: <?php echo $data['nama_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td>BPS/BIPIH</td>
            <td>: <?php echo $data['bank_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: <?php echo $data['no_rekening_ahliwaris']; ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: <?php echo $data['status_dengan_jamaah']; ?></td>
        </tr>
        <tr>
            <td style="line-height: 20px;" colspan="2">dengan ini menyampaikan permohonan pembatalan berangkat haji dan sekaligus memohon pengembalian Uang Setoran Awal/lunas BIPIH Saya, atas nama:</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td style="text-transform: uppercase;">: <b><?php echo $data['nama_jamaah']; ?></b> </td>
        </tr>
        <tr>
            <td>Bin / Binti</td>
            <td style="text-transform: uppercase;">: <?php echo $data['bin_binti']; ?></td>
        </tr>
        <tr>
            <td>TTL</td>
            <td>: <?php echo $data['tempat_lahir'] . ', ' . formatTanggalIndonesia($data['tanggal_lahir']); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: <?php echo $data['alamat'] . ', Kec. ' . $data['kecamatan'] . ', Kel. ' . $data['kelurahan'] . ', ' . $data['kode_pos'] . ', Kab. Banjar' ; ?></td>
        </tr>
        <tr>
            <td>BPS/BIPIH</td>
            <td>: <?php echo $data['bps']; ?></td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: <?php echo $data['nomor_rek']; ?></td>
        </tr>
        <tr>
            <td>Nomor Porsi/Validasi</td>
            <td>: <?php echo $data['nomor_porsi']; ?> - <?php echo $data['spph_validasi']; ?></td>
        </tr>
        <tr>
            <td>Sebab Pembatalan</td>
            <td>: <?php echo $data['kategori']; ?></td>
        </tr>
    </table>

    <p>
        Sebagai bahan pertimbangan, terlampir dokumen pendukung. <br>
        Demikian permohonan ini saya sampaikan. Mohon kiranya dapat diproses penyelesaiannya. <br>
        Atas perhatian, saya ucapkan terima kasih.
    </p>
        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
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
