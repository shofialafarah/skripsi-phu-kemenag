<?php session_start();
// Koneksi ke database
include 'koneksi.php';

// Fungsi untuk mengubah bulan ke Bahasa Indonesia
function formatTanggalIndonesia($tanggal) {
    if (!$tanggal || !strpos($tanggal, '-')) {
        return 'Tanggal tidak valid';
    }

    $tanggalSplit = explode('-', $tanggal);

    if (count($tanggalSplit) !== 3) {
        return 'Format tanggal salah';
    }

    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );

    $tahun = $tanggalSplit[0];
    $bulanNum = (int)$tanggalSplit[1];
    $hari = $tanggalSplit[2];

    return $hari . ' ' . $bulan[$bulanNum] . ' ' . $tahun;
}

// Cek ID
if (isset($_GET['id'])) {
    $id_limpah_sakit = $_GET['id'];

    // Coba ambil dari pelimpahan_sakit
    $query = "
        SELECT ps.*, pb.kategori 
        FROM pelimpahan_sakit ps
        LEFT JOIN pelimpahan pb ON pb.id_pelimpahan = ps.id_pelimpahan
        WHERE ps.id_limpah_sakit = ?
    ";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_limpah_sakit);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Jika tidak ditemukan di sakit, coba di meninggal
    if (!$data) {
        $query = "
            SELECT pm.*, pb.kategori 
            FROM pelimpahan_meninggal pm
            LEFT JOIN pelimpahan pb ON pb.id_pelimpahan = pm.id_pelimpahan
            WHERE pm.id_limpah_meninggal = ?
        ";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_limpah_sakit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
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
    <title>Surat Pengantar</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; line-height: 1.6; margin: 20px; }
        .header { text-align: center; }
        .header h2, .header p { margin: 5px 0; }
        .header img { display: block; margin: 0 auto; width: 80px; height: auto; }  
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: left; }
        .content h3 {
            margin: 5px 0;
            text-align: center;
        }
        .date {
            text-align: right;
            white-space: nowrap; /* Agar teks tanggal tidak terputus */
        }
        .footer {
            margin-top: 50px;
            padding-top: 30px;
        }
        .footer .signature {
            text-align: right;
            margin-top: 30px;
        }
        .footer .signature p {
            margin: 5px 0;
        }
        .footer .received {
            margin-top: 50px;
            border-top: 1px dashed black;
            padding-top: 10px;
        }
        .footer .received p {
            margin: 5px 0;
        }
        .nip-container {
            margin: 0;
            text-align: center; /* Teks berada di tengah */
            border-top: 1px dashed black; /* Garis atas */
            display: inline-block; /* Membuat elemen menyesuaikan teks */
            width: 150px; /* Panjang garis sesuai kebutuhan */
            margin-top: 10px; /* Memberikan jarak dari tanda tangan */
        }
        .nip-line {
            border-top: 1px dashed black; /* Garis putus-putus */
            flex-grow: 1; /* Membuat garis meluas */
        }

        .nip-text {
            margin-right: 120px; /* Jarak antara garis dan teks */
            white-space: nowrap; /* Mencegah teks terpotong */
            text-align: left;
        }
        .nip { margin: 0;
            text-align: center; /* Teks berada di tengah */
            display: inline-block; /* Membuat elemen menyesuaikan teks */
            width: 150px; /* Panjang garis sesuai kebutuhan */
            margin-top: 10px; /* Memberikan jarak dari tanda tangan */}
            .nip-satu{margin-right: 120px; /* Jarak antara garis dan teks */
            white-space: nowrap; /* Mencegah teks terpotong */
            text-align: left;}
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
        
        .address-date {
            display: flex; /* Membuat elemen anak di dalamnya tersusun secara horizontal */
            justify-content: space-between; /* Menyebarkan elemen di ujung kiri dan kanan */
            align-items: flex-start; /* Menyelaraskan elemen di bagian atas */
        }

        .address {
            width: 70%; /* Lebar bagian alamat */
            text-align: left;
        }

        .date {
            width: 30%; /* Lebar bagian tanggal */
            text-align: right; /* Teks tanggal di rata kanan */
        }
        hr {border: 2px solid black; border-top: 4px double black; margin: 20px 0;}
        .email {color: #007bff;}
        @media print {
            body {
                font-size: 12px; /* Ukuran font lebih kecil */
                line-height: 1.3; /* Jarak baris lebih rapat */
                margin: 0; /* Margin halaman */
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .btn {
                display: none; /* Sembunyikan tombol */
            }
            .signature {
                text-align: right;
                margin-top: 50px; /* Atur margin tanda tangan */
            }
        }

    </style>
</head>
<body>

<div class="header">
    <img align="left" src="logo_kemenag.png" alt="Logo Kementerian Agama">
    <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
        KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
    <p>Jl. Sekumpul No. 72-23 Kelurahan Jawa Martapura Banjar 70614 <br>
    Telp/Fax: (0511) 4721442 | Email: <u class="email">banjarkalsel@kemenag.go.id</u>
    </p>
    <hr>
    <div class="address-date">
        <div class="address">
            <p>Kepada Yth.<br>
            Kepala Kantor Wilayah Kementerian Agama<br>
            Provinsi Kalimantan Selatan<br>
            Up. Kabid Penyelenggaraan Haji dan Umrah <br> di Banjarmasin</p>
        </div>
        <div class="date">
            Martapura, <?php echo formatTanggalIndonesia($data['tanggal_masuk_surat']); ?>
        </div>
</div>

<div class="content">
    
    <h3>SURAT PENGANTAR</h3>
    <h3>Nomor: <?php echo $data['nomor_surat']; ?>/Kk.17.03-5/Hj.<?php echo date('d/m/Y'); ?></h3>
    <table>
        <tr>
            <th>No</th>
            <th>Jenis yang dikirim</th>
            <th>Banyaknya</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Dokumen / berkas Pelimpahan Nomor Porsi Jamaah Calon Haji Kabupaten Banjar yang <?php echo $data['kategori']; ?> Tahun <?php echo date('Y'); ?></td>
            <td>1 Bundel</td>
            <td>Assalamu’alaikum Wr. Wb.<br>
                Dengan hormat,<br>
                Dengan ini disampaikan untuk dapat diketahui dan diproses sebagaimana mestinya.<br>
                Terima kasih.<br>
                Wassalam.
            </td>
        </tr>
    </table>
    </div> 
    <br>
    <div class="footer">
        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
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

        <div class="received">
            <p>Telah diterima dokumen / berkas Ganti Nomor Porsi Jamaah Calon Haji Kabupaten Banjar yang <?php echo $data['kategori']; ?> Tahun <?php echo date('Y'); ?> M.</p>
            <div style="text-align: center; margin-top: 30px;">
            <div class="nip">
                <span class="nip-satu">Tanggal, </span>
                <span class="nip-satu">Penerima </span>
            </div>
            <br><br><br> <!-- Ruang untuk tanda tangan -->
            <div class="nip-container">
                <span class="nip-line"></span>
                <span class="nip-text">NIP.</span>
            </div>
        </div>
        </div>
    </div>

    <button class="btn btn-primary" onclick="window.print();">Cetak</button>
</body>
</html>
