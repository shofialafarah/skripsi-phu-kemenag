<?php
include 'koneksi.php';

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
    $id = $_GET['id'];

    $data = null;

    // Coba ambil dari pelimpahan_sakit dulu
    $query1 = "SELECT *, 'Sakit Permanen' AS kategori FROM pelimpahan_sakit WHERE id_limpah_sakit = ?";
    $stmt1 = $koneksi->prepare($query1);
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $data = $result1->fetch_assoc();

    if (!$data) {
        // Kalau tidak ditemukan di sakit, coba di meninggal
        $query2 = "SELECT *, 'Meninggal Dunia' AS kategori FROM pelimpahan_meninggal WHERE id_limpah_meninggal = ?";
        $stmt2 = $koneksi->prepare($query2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data = $result2->fetch_assoc();
    }

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
    <title>SPTJM Pelimpahan</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 20px;
        }

        h1 {
            font-size: 16pt;
            /* Ukuran font lebih kecil */
            text-align: center;
            max-width: 100%;
            /* Pastikan muat di halaman */
            word-wrap: break-word;
            /* Jika sangat panjang, bisa terputus dengan baik */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 0;
            line-height: 30px;
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
                font-size: 14px;
                /* Atur ukuran font menjadi lebih kecil */
                line-height: 1.2;
                /* Kurangi jarak antar baris */
                margin: 40px;
                /* Kurangi margin halaman */
            }

            table td,
            table th {
                line-height: 1.6;
                /* Atur juga untuk teks dalam tabel */
            }

            ol li,
            p {
                line-height: 1.6;
                /* Untuk paragraf dan daftar */
            }

            .btn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">SURAT PERNYATAAN TANGGUNG JAWAB MUTLAK</h1>

    <div class="content">
        <p>Saya yang bertanda tangan di bawah ini:</p>
        <table>
            <tr>
                <td>Nama</td>
                <td>: <?php echo $data['nama_ahliwaris']; ?> </td>
            </tr>
            <tr>
                <td>Bin/Binti</td>
                <td>: <?php echo $data['nama_ayah_ahliwaris']; ?></td>
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
                <td>Provinsi</td>
                <td>: Kalimantan Selatan</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: <?php echo $data['jenis_kelamin_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>: <?php echo $data['pekerjaan_ahliwaris']; ?></td>
            </tr>
            <tr>
                <td>Telp/Hp</td>
                <td>: <?php echo $data['no_telepon_ahliwaris']; ?></td>
            </tr>
        </table>

        <p>
            Dengan ini menyatakan:
        </p>
        <ol>
            <li>Bertanggung jawab atas pelimpahan nomor porsi jemaah haji regular yang <?php echo $data['kategori']; ?>:<br>
                Nama/ Bin/ Binti :<b style="text-transform: uppercase;"> <?php echo $data['nama_jamaah']; ?></b> <br>
                No. Porsi/ SPPH : <?php echo $data['nomor_porsi']; ?> / <?php echo $data['spph_validasi']; ?> sesuai dengan surat kuasa yang telah diberikan oleh para pemberi kuasa.</li>
            <li>Bertanggung jawab sepenuhnya bahwa saya tidak akan melibatkan pihak Kantor Kementerian Agama Kab. Banjar, apabila terjadi sesuatu hal dan lainnya yang dapat menyebabkan adanya kerugian Negara atau kerugian lainnya.</li>
            <li>Bertanggung jawab sepenuhnya bahwa apabila dikemudian hari ditemukan data yang tidak benar atau timbul gugatan atas kuasa penerima pelimpahan nomor porsi jemaah haji sakit dunia, maka saya siap bertanggung jawab secara administrasi dan atau pidana.</li>
        </ol>
        <p>
            Demikian pernyataan tanggung jawab mutlak ini saya buat dengan kesadaran dan penuh tanggung jawab.
        </p>

        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">Martapura, <?php echo formatTanggalIndonesia($data['tanggal_masuk_surat']); ?></p>
                        <p style="margin: 0;">Yang Membuat Pernyataan,</p>
                        <br><br><br><br><br>
                        <strong>
                            <p style="margin: 0;"><?php echo $data['nama_ahliwaris']; ?></p>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>

</html>