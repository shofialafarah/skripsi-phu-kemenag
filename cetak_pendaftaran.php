<?php
include 'koneksi.php';

// Validasi dan ambil data berdasarkan ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_pendaftaran = $_GET['id'];
    $query = "SELECT * FROM pendaftaran WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_pendaftaran);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        die("Data tidak ditemukan.");
    }
} else {
    die("ID Pendaftaran tidak valid.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pendaftaran Haji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .container {
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .header .logo img {
            width: 100px;
        }
        .header .title {
            flex-grow: 1;
            margin-left: 20px;
        }
        .header .barcode {
            text-align: right;
        }
        .content {
            margin-top: 20px;
        }
        .content .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            text-transform: uppercase;
        }
        .info-table td {
            padding: 5px;
        }
        .info-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .info-table td:last-child {
            text-align: left;
        }
        .email {
            text-transform: lowercase;
        }
        .photo {
            margin-top: 20px;
            text-align: left;
        }
        .photo img {
            border: 1px solid #000;
            width: 120px;
            height: 160px;
            object-fit: cover;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .footer .signature {
            text-align: right;
        }
        .footer .signature p {
            margin: 0;
        }
        @media print {
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="logo_kemenag.png" alt="Logo Kemenag">
            </div>
            <div class="title">
                <h5>KANTOR KEMENTERIAN AGAMA</h5>
                <h5>KABUPATEN BANJAR</h5>
            </div>
            <div class="barcode">
                <p><strong>NOMOR PORSI</strong></p>
                <svg id="barcode"></svg>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h4 class="text-center"><b>SURAT PENDAFTARAN HAJI (SPH)</b></h4>
            <br><br>
            <table class="info-table">
                <tr>
                    <td>Nomor KTP</td>
                    <td>: <?php echo $data['nik']; ?></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>: <b><?php echo $data['nama']; ?></b></td>
                </tr>
                <tr>
                    <td>Nama Ayah Kandung</td>
                    <td>: <?php echo $data['nama_ayah_kandung']; ?></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>: <?php echo $data['jenis_kelamin']; ?></td>
                </tr>
                <tr>
                    <td>Tempat Lahir</td>
                    <td>: <?php echo $data['tempat_lahir']; ?></td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:  <?php echo date('d F Y', strtotime($data['tanggal_lahir'])); ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: <?php echo $data['alamat']; ?></td>
                </tr>
                <tr>
                    <td>Kode Pos</td>
                    <td>: <?php echo $data['kode_pos']; ?></td>
                </tr>
                <tr>
                    <td>Desa/Kelurahan</td>
                    <td>: <?php echo $data['desa']; ?></td>
                </tr>
                <tr>
                    <td>Kecamatan</td>
                    <td>: <?php echo $data['kecamatan']; ?></td>
                </tr>
                <tr>
                    <td>Kabupaten/Kota</td>
                    <td>: <?php echo $data['kabupaten']; ?></td>
                </tr>
                <tr>
                    <td>Provinsi</td>
                    <td>: <?php echo $data['provinsi']; ?></td>
                </tr>
                <tr>
                    <td>No Handphone</td>
                    <td>: <?php echo $data['no_handphone']; ?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td class="email">: <?php echo $data['email']; ?></td>
                </tr>
                <tr>
                    <td>Status Haji</td>
                    <td>: <?php echo $data['status_haji']; ?></td>
                </tr>
                <tr>
                    <td>Status Perkawinan</td>
                    <td>: <?php echo $data['status_perkawinan']; ?></td>
                </tr>
            </table>
        </div>

        <!-- Photo -->
        <div class="photo">
            <img src="uploads/<?php echo $data['foto_biometrik']; ?>" alt="Foto">
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>
                <p>Tanggal Set  : <?php echo date('d F Y', strtotime($data['tanggal_input'])); ?></p>
                <p>Bank         : <b><?php echo $data['bank']; ?></b></p>
                <p>No. Rekening : <?php echo $data['no_rekening']; ?></p>
            </div>
            <div class="signature">
                <p>Kab. Banjar, <?php echo date('d F Y'); ?></p>
                <p>Kantor Kementerian Agama Kab. Banjar</p>
                <p>Kasi Penyelenggaraan Haji dan Umrah</p>
                <br><br><br><br><br>
                <p><strong>Erfan Maulana</strong></p>
                <p>NIP: 198311202003121004</p>
            </div>
        </div>
        <button class="btn btn-primary" onclick="window.print();">Cetak</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "<?php echo $data['id']; ?>", {
    format: "CODE128",
    displayValue: true, // Menampilkan teks di bawah barcode
    fontSize: 14,       // Ukuran teks di bawah barcode
    height: 60,         // Tinggi barcode
    width: 1.5,         // Lebar tiap garis barcode
    margin: 5           // Margin di sekitar barcode
});
    </script>
</body>
</html>
