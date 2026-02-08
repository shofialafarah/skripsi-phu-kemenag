<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Ambil pengaturan logo dan nama aplikasi
$result = $koneksi->query("SELECT * FROM pengaturan WHERE key_name IN ('app_name', 'app_logo')");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Ambil pengaturan warna teks
$result = $koneksi->query("SELECT value FROM pengaturan WHERE key_name='theme_text_color'");
$theme_text_color = $result->fetch_assoc()['value'] ?? '#333333'; // Default abu-abu gelap

// Periksa apakah tombol verifikasi diklik dan ID serta tipe ada di URL
if (isset($_GET['verifikasi_id']) && isset($_GET['tipe'])) {
    $verifikasiId = $_GET['verifikasi_id'];
    $tipe = $_GET['tipe'];
    $tanggalVerifikasi = date('Y-m-d'); // Mendapatkan tanggal saat ini

    // Proses verifikasi berdasarkan tipe
    if ($tipe == 'pembatalan_ekonomi') {
        $updateQuery = "UPDATE pembatalan_ekonomi SET tanggal_verifikasi_kasi = '$tanggalVerifikasi' WHERE id_batal_ekonomi = $verifikasiId";
    } else if ($tipe == 'pembatalan_meninggal') {
        $updateQuery = "UPDATE pembatalan_meninggal SET tanggal_verifikasi_kasi = '$tanggalVerifikasi' WHERE id_batal_meninggal = $verifikasiId";
    } else if ($tipe == 'pelimpahan_sakit') {
        $updateQuery = "UPDATE pelimpahan_sakit SET tanggal_verifikasi_kasi = '$tanggalVerifikasi' WHERE id_limpah_sakit = $verifikasiId";
    } else if ($tipe == 'pelimpahan_meninggal') {
        $updateQuery = "UPDATE pelimpahan_meninggal SET tanggal_verifikasi_kasi = '$tanggalVerifikasi' WHERE id_limpah_meninggal = $verifikasiId";
    }

    // Jalankan query untuk update verifikasi
    if ($koneksi->query($updateQuery)) {
        echo "<script>alert('Data berhasil diverifikasi.'); window.location.href='verifikasi_jamaah.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat verifikasi.');</script>";
    }
}

// Ambil nilai filter tipe dari request GET
$filterTipe = isset($_GET['filter_tipe']) ? $_GET['filter_tipe'] : 'semua';

// Query untuk mengambil data dari tabel pembatalan yang belum diverifikasi
$queryPembatalan = "
    SELECT 'pembatalan_ekonomi' AS tipe, id_batal_ekonomi AS id, nama_jamaah, nomor_porsi, alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pembatalan_ekonomi
    WHERE '$filterTipe' = 'semua' OR 'pembatalan' = '$filterTipe'

    UNION ALL
    SELECT 'pembatalan_meninggal' AS tipe, id_batal_meninggal AS id, nama_jamaah, nomor_porsi, alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pembatalan_meninggal
    WHERE '$filterTipe' = 'semua' OR 'pembatalan' = '$filterTipe'
";

// Query untuk mengambil data dari tabel pelimpahan yang belum diverifikasi
$queryPelimpahan = "
    SELECT 'pelimpahan_sakit' AS tipe, id_limpah_sakit AS id, nama_jamaah, nomor_porsi, alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pelimpahan_sakit
    WHERE '$filterTipe' = 'semua' OR 'pelimpahan' = '$filterTipe'
    
    UNION ALL
    SELECT 'pelimpahan_meninggal' AS tipe, id_limpah_meninggal AS id, nama_jamaah, nomor_porsi, alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pelimpahan_meninggal
    WHERE '$filterTipe' = 'semua' OR 'pelimpahan' = '$filterTipe'
";

// Jalankan query untuk mengambil data
if ($filterTipe == 'semua' || $filterTipe == 'pembatalan') {
    $resultPembatalan = $koneksi->query($queryPembatalan);
}

if ($filterTipe == 'semua' || $filterTipe == 'pelimpahan') {
    $resultPelimpahan = $koneksi->query($queryPelimpahan);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kepala Seksi</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f9f9f9;
        }
        .sidebar {
            width: 250px;
            background-color: #007b3e;
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: white;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 5px 0;
        }
        .sidebar a:hover {
            background-color: #005a2c;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007b3e;
            color: white;
        }
        h1, h2 {
            color: #007b3e;
        }
        /* a.logout {
            display: block;
            margin-top: 20px;
            color: #e74c3c;
            text-decoration: none;
        }
        a.logout:hover {
            text-decoration: underline;
        } */
        .filter-form {
            margin-bottom: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .filter-form input, .filter-form button {
            padding: 10px;
            margin-right: 10px;
        }
        button {
            background-color: #007b3e;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #005a2c;
        }
        .btn-verifikasi {
            background-color: #007b3e;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn-verifikasi:hover {
            background-color: #005a2c;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="display: flex; align-items: center;">
            <!-- Logo Kemenag di samping kiri -->
            <img src="settings/<?= htmlspecialchars($settings['app_logo']); ?>" alt="Logo Kemenag" style="width: 50px; height: 50px; margin-right: 10px; margin-left: 20;">
            <h2><?= htmlspecialchars($settings['app_name']); ?></h2>
        </div>
        <a href="dashboard_kasi.php">Dashboard Utama</a>
        <a href="verifikasi_jamaah.php">Verifikasi Jamaah</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="content">
        <h1>Verifikasi Jamaah</h1>

        <form method="GET" class="filter-form">
            <label for="filter_tipe">Tampilkan:</label>
            <select id="filter_tipe" name="filter_tipe">
                <option value="semua" <?= isset($_GET['filter_tipe']) && $_GET['filter_tipe'] == 'semua' ? 'selected' : '' ?>>Semua</option>
                <option value="pembatalan" <?= isset($_GET['filter_tipe']) && $_GET['filter_tipe'] == 'pembatalan' ? 'selected' : '' ?>>Pembatalan</option>
                <option value="pelimpahan" <?= isset($_GET['filter_tipe']) && $_GET['filter_tipe'] == 'pelimpahan' ? 'selected' : '' ?>>Pelimpahan</option>
            </select>
            <button type="submit"><i class="fas fa-filter"></i>  Filter</button>
        </form>

        <!-- Daftar Jamaah Pembatalan -->
        <?php if (isset($resultPembatalan) && $resultPembatalan->num_rows > 0): ?>
        <h2>Daftar Jamaah Pembatalan</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Jamaah</th>
                <th>Nomor Porsi</th>
                <th>Alamat</th>
                <th>Nomor Telepon</th>
                <th>Alasan</th>
                <th>Status Verifikasi</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = $resultPembatalan->fetch_assoc()) {
                $isVerified = isset($row['tanggal_verifikasi_kasi']) && $row['tanggal_verifikasi_kasi'];
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nomor_porsi']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alasan']) . "</td>";
                echo "<td>" . ($isVerified ? 'Terverifikasi' : 'Belum Diverifikasi') . "</td>";
                if (!$isVerified) {
                    echo "<td><a href='verifikasi_jamaah.php?verifikasi_id=" . $row['id'] . "&tipe=" . $row['tipe'] . "' class='btn-verifikasi'>Verifikasi</a></td>";
                } else {
                    echo "<td><button class='btn-verifikasi' style='pointer-events: none; color: gray;' disabled>Terverifikasi</button></td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
        <?php endif; ?>

        <!-- Daftar Jamaah Pelimpahan -->
        <?php if (isset($resultPelimpahan) && $resultPelimpahan->num_rows > 0): ?>
        <h2>Daftar Jamaah Pelimpahan</h2>
        <table>
            <tr>
                <th>No</th>
                <th>Nama Jamaah</th>
                <th>Nomor Porsi</th>
                <th>Alamat</th>
                <th>Nomor Telepon</th>
                <th>Alasan</th>
                <th>Status Verifikasi</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = $resultPelimpahan->fetch_assoc()) {
                $isVerified = isset($row['tanggal_verifikasi_kasi']) && $row['tanggal_verifikasi_kasi'];
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nomor_porsi']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alasan']) . "</td>";
                echo "<td>" . ($isVerified ? 'Terverifikasi' : 'Belum Diverifikasi') . "</td>";
                if (!$isVerified) {
                    echo "<td><a href='verifikasi_jamaah.php?verifikasi_id=" . $row['id'] . "&tipe=" . $row['tipe'] . "' class='btn-verifikasi'>Verifikasi</a></td>";
                } else {
                    echo "<td><button class='btn-verifikasi' style='pointer-events: none; color: gray;' disabled>Terverifikasi</button></td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>