<?php
session_start();
include 'koneksi.php';  // Pastikan koneksi ke database sudah benar

// Periksa apakah session id_jamaah ada
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");  // Arahkan ke halaman login jika tidak login
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];  // Ambil id_jamaah dari session

function formatTanggalIndonesia($tanggal) {
    $bulan = [
        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April',
        'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus',
        'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
    ];

    $formatInggris = date('d F Y', strtotime($tanggal));
    return strtr($formatInggris, $bulan);
}

// Query untuk mendapatkan data berdasarkan id_jamaah
$query = "SELECT * FROM detail_jamaah WHERE id_jamaah = ?";
$stmt = $koneksi->prepare($query);

// Bind ID parameter dan eksekusi query
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();

// Jika data ditemukan, simpan ke dalam $data
if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $id_jamaah = $data['id_jamaah'];  // Ambil id_jamaah
    $nomor_porsi = $data['nomor_porsi'];  // Ambil nomor_porsi
} else {
    echo "Data tidak ditemukan.";
    exit();  // Jika data tidak ditemukan, hentikan eksekusi
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Jamaah</title>
    <link rel="icon" href="logo_kemenag.png">
</head>
<body>
    <?php include 'header_jamaah.php';?>
    <div class="container">
        <h1>Detail Informasi Jamaah Haji</h1>
        <!-- Nomor Porsi Box -->
        <div class="nomor-porsi">
            Nomor Porsi 
            <div class="porsi"><?= htmlspecialchars($data['nomor_porsi']); ?></div>
        </div>
        <table>
            <tr>
                <th colspan="2">Informasi Pribadi</th>
            </tr>            
            <tr>
                <td>Nomor KTP</td>
                <td>: <?= htmlspecialchars($data['nik']); ?></td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td>: <b><?= htmlspecialchars($data['nama']); ?></b></td>
            </tr>
            <tr>
                <td>Nama Ayah Kandung</td>
                <td>: <?= htmlspecialchars($data['nama_ayah']); ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>: <?= htmlspecialchars($data['jenis_kelamin']); ?></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>: <?= htmlspecialchars($data['tempat_lahir']) . ", " . formatTanggalIndonesia($data['tanggal_lahir']); ?></td>
            </tr>
            <tr>
                <td>No HP</td>
                <td>: <?= htmlspecialchars($data['no_telepon']); ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td style="text-transform: lowercase;">: <?= htmlspecialchars($data['email']); ?></td>
            </tr>
            <tr>
                <td>Status Perkawinan</td>
                <td>: <?= htmlspecialchars($data['status_perkawinan']); ?></td>
            </tr>
            <tr>
                    <th colspan="2">Informasi Alamat</th>
                </tr>

            <tr>
                <td>Alamat</td>
                <td>: <?= htmlspecialchars($data['alamat']); ?></td>
            </tr>
            <tr>
                <td>Kode Pos</td>
                <td>: <?= htmlspecialchars($data['kode_pos']); ?></td>
            </tr>
            <tr>
                <td>Desa/Kelurahan</td>
                <td>: <?= htmlspecialchars($data['desa']); ?></td>
            </tr>
            <tr>
                <td>Kecamatan</td>
                <td>: <?= htmlspecialchars($data['kecamatan']); ?></td>
            </tr>
            <tr>
                <td>Kabupaten/Kota</td>
                <td>: <?= htmlspecialchars($data['kabupaten']); ?></td>
            </tr>
            <tr>
                <td>Provinsi</td>
                <td>: <?= htmlspecialchars($data['provinsi']); ?></td>
            </tr>
            <tr>
                <th colspan="2">Informasi Keberangkatan</th>
            </tr>
            <tr>
                <td>Status Haji</td>
                <td>: <?= htmlspecialchars($data['status_pergi_haji']); ?></td>
            </tr>
            <tr>
                <td>Tanggal Daftar</td>
                <td>: <?= formatTanggalIndonesia($data['tanggal_daftar']); ?></td>
            </tr>
            <tr>
                <th colspan="2">Informasi Bank</th>
            </tr>
            <tr>
                <td>Nama Bank</td>
                <td>: <?= htmlspecialchars($data['bank']); ?></td>
            </tr>
            <tr>
                <td>No Rekening</td>
                <td>: <?= htmlspecialchars($data['no_rekening']); ?></td>
            </tr>

        </table>
    </div>
</body>
</html>