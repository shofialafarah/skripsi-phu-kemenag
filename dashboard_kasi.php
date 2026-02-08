<?php

include 'koneksi.php'; // Koneksi ke database

// Pastikan session sudah ada dan role adalah kepala_seksi
if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php"); // Redirect ke login jika belum login atau session tidak ada
    exit();
}

// Ambil ID Kepala dari session
$id_kepala = $_SESSION['id_kepala'];

// Query untuk mendapatkan nama kepala_seksi berdasarkan id_kepala
$query = "SELECT nama_kepala FROM kepala_seksi WHERE id_kepala = '$id_kepala'";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $namaKepala = $row['nama_kepala'];
} else {
    $namaKepala = "Pak Kepala Seksi"; // Default jika tidak ada data
}

// Ambil pengaturan logo dan nama aplikasi
$result = $koneksi->query("SELECT * FROM pengaturan WHERE key_name IN ('app_name', 'app_logo')");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Ambil pengaturan warna teks
$result = $koneksi->query("SELECT value FROM pengaturan WHERE key_name='theme_text_color'");
$theme_text_color = $result->fetch_assoc()['value'] ?? '#333333'; // Default abu-abu gelap

// Proses verifikasi
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
        echo "<script>alert('Data berhasil diverifikasi.'); window.location.href='dashboard_kasi.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat verifikasi.');</script>";
    }
}

// Inisialisasi filter tanggal mulai dan tanggal selesai
$filterStart = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filterEnd = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Query untuk mengambil data dari tabel pembatalan
$queryPembatalan = "
    SELECT 'pembatalan_ekonomi' AS tipe, id_batal_ekonomi AS id, nama_jamaah, nomor_porsi, 
           alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pembatalan_ekonomi
    WHERE 1=1 ";

if ($filterStart && $filterEnd) {
    $queryPembatalan .= " AND (tanggal_verifikasi_kasi BETWEEN '$filterStart' AND '$filterEnd')";
} elseif ($filterStart) {
    $queryPembatalan .= " AND tanggal_verifikasi_kasi >= '$filterStart'";
} elseif ($filterEnd) {
    $queryPembatalan .= " AND tanggal_verifikasi_kasi <= '$filterEnd'";
}

$queryPembatalan .= "
    UNION ALL
    SELECT 'pembatalan_meninggal' AS tipe, id_batal_meninggal AS id, nama_jamaah, nomor_porsi, 
           alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pembatalan_meninggal
    WHERE 1=1 ";

    if ($filterStart && $filterEnd) {
        $queryPembatalan .= " AND (tanggal_verifikasi_kasi BETWEEN '$filterStart' AND '$filterEnd')";
    } elseif ($filterStart) {
        $queryPembatalan .= " AND tanggal_verifikasi_kasi >= '$filterStart'";
    } elseif ($filterEnd) {
        $queryPembatalan .= " AND tanggal_verifikasi_kasi <= '$filterEnd'";
    }
    
    // Query untuk mengambil data dari tabel pelimpahan
    $queryPelimpahan = "
        SELECT 'pelimpahan_sakit' AS tipe, id_limpah_sakit AS id, nama_jamaah, nomor_porsi, 
               alamat, no_telepon, alasan, tanggal_verifikasi_kasi
        FROM pelimpahan_sakit
        WHERE 1=1 ";
    
    if ($filterStart && $filterEnd) {
        $queryPelimpahan .= " AND (tanggal_verifikasi_kasi BETWEEN '$filterStart' AND '$filterEnd')";
    } elseif ($filterStart) {
        $queryPelimpahan .= " AND tanggal_verifikasi_kasi >= '$filterStart'";
    } elseif ($filterEnd) {
        $queryPelimpahan .= " AND tanggal_verifikasi_kasi <= '$filterEnd'";
    }
    
    $queryPelimpahan .= "
    UNION ALL
    SELECT 'pelimpahan_meninggal' AS tipe, id_limpah_meninggal AS id, nama_jamaah, nomor_porsi, 
           alamat, no_telepon, alasan, tanggal_verifikasi_kasi
    FROM pelimpahan_meninggal
    WHERE 1=1 ";

if ($filterStart && $filterEnd) {
    $queryPelimpahan .= " AND (tanggal_verifikasi_kasi BETWEEN '$filterStart' AND '$filterEnd')";
} elseif ($filterStart) {
    $queryPelimpahan .= " AND tanggal_verifikasi_kasi >= '$filterStart'";
} elseif ($filterEnd) {
    $queryPelimpahan .= " AND tanggal_verifikasi_kasi <= '$filterEnd'";
}

//Eksekusi Query
$resultPembatalan = $koneksi->query($queryPembatalan);
$resultPelimpahan = $koneksi->query($queryPelimpahan);


function formatTanggal($tanggal) {
    $bulan = [
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
    ];
    $tanggal = strtotime($tanggal);
    $tgl = date('d', $tanggal);
    $bln = date('n', $tanggal); // Ambil angka bulan (1-12)
    $thn = date('Y', $tanggal);
    return "$tgl {$bulan[$bln]} $thn";
}

$filterStartFormatted = $filterStart ? formatTanggal($filterStart) : '-';
$filterEndFormatted = $filterEnd ? formatTanggal($filterEnd) : '-';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kepala Seksi</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard_kasi.css">
    <style>
        /* Terapkan warna teks ke elemen tertentu */
        .logo-text {
            color: <?= htmlspecialchars($theme_text_color); ?>;
        }
        .nav-links a {
            color: <?= htmlspecialchars($theme_text_color); ?>;
        }
        .dropdown .dropbtn {
            color: <?= htmlspecialchars($theme_text_color); ?>;
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
        <a href="#">Dashboard Utama</a>
        <a href="verifikasi_jamaah.php">Verifikasi Jamaah</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="header">
        <div class="welcome">
            <h1>Selamat datang Pak <?php echo htmlspecialchars($namaKepala); ?>!</h1>
        </div>
        <a href="profil_kasi.php" class="profile-btn">Lihat Profil</a>
    </div>

    <div class="content">
        <h1>Dashboard Kepala Seksi</h1>

        <!-- Filter Tanggal -->
        <form method="GET" class="filter-form">
            <label for="start_date">Tanggal Awal:</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($filterStart) ?>">

            <label for="end_date">Tanggal Akhir:</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($filterEnd) ?>">

            <button type="submit"><i class="fas fa-filter"></i>  Filter</button>
        </form>

        <!-- Tabel Pembatalan -->
        <h2>Daftar Jamaah Pembatalan</h2>
        <button class="btn-cetak" onclick="printTable('tablePembatalan', 'Pembatalan')"><i class="fas fa-print"></i>  Cetak Data Pembatalan</button>
        <table id="tablePembatalan">
            <tr>
                <th>No</th>
                <th>Nama Jamaah</th>
                <th>Nomor Porsi</th>
                <th>Alamat</th>
                <th>Nomor Telepon</th>
                <th>Alasan</th>
                <th>Tanggal Verifikasi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = $resultPembatalan->fetch_assoc()) {
                $isVerified = !empty($row['tanggal_verifikasi_kasi']); // Cek jika sudah diverifikasi
                echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomor_porsi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alasan']) . "</td>";
                    echo "<td>" . ($row['tanggal_verifikasi_kasi'] ? htmlspecialchars(formatTanggal($row['tanggal_verifikasi_kasi'])) : '-') . "</td>";
                echo "</tr>";
            }
            ?>
        </table>

        <!-- Tabel Pelimpahan -->
        <h2>Daftar Jamaah Pelimpahan</h2>
        <button class="btn-cetak" onclick="printTable('tablePelimpahan', 'Pelimpahan')"><i class="fas fa-print"></i>  Cetak Data Pelimpahan</button>
        <table id="tablePelimpahan">
            <tr>
                <th>No</th>
                <th>Nama Jamaah</th>
                <th>Nomor Porsi</th>
                <th>Alamat</th>
                <th>Nomor Telepon</th>
                <th>Alasan</th>
                <th>Tanggal Verifikasi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = $resultPelimpahan->fetch_assoc()) {
                $isVerified = !empty($row['tanggal_verifikasi_kasi']); // Cek jika sudah diverifikasi
                echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nomor_porsi']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['alasan']) . "</td>";
                    echo "<td>" . ($row['tanggal_verifikasi_kasi'] ? htmlspecialchars(formatTanggal($row['tanggal_verifikasi_kasi'])) : '-') . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
<script>
function printTable(tableId, type) {
    const table = document.getElementById(tableId).outerHTML;

    // Membuat header surat resmi
    const headerHTML = `
        <div style="text-align: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: center;">
            <img src="logo_kemenag.png" alt="Logo Kemenag" style="width: 100px; height: auto; margin-right: 20px;">
            <div>
                <h2 style="margin: 0; font-size: 24px;">KEMENTERIAN AGAMA REPUBLIK INDONESIA  </br>
                    KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
                    <p style="margin: 0; font-size: 16px;">Jalan Sekumpul Nomor 19 Martapura Kode Pos 70614 </br>
                    Telp (0511) 4721249; Faximile (0511) 4721249 </br> Email: banjarkalsel@kemenag.go.id</p>
            </div>
        </div>
    </div>
    <hr style="border: 2px solid black; border-top: 4px double black; margin: 20px 0;">

    `;

    // Deskripsi data
    const filterStart = "<?php echo htmlspecialchars($filterStartFormatted); ?>";
    const filterEnd = "<?php echo htmlspecialchars($filterEndFormatted); ?>";
    const year = new Date().getFullYear();
    const dataTitle = `
        <h3 style="margin-top: 10px;">
            Data ${type} Jamaah Haji Tahun ${year} <br>
            Periode ${filterStart || '-'} s/d ${filterEnd || '-'}
        </h3>
    `;

    // Tambahkan pengesahan
    const today = new Date();
    const dateString = `${today.getDate()} ${today.toLocaleString('id-ID', { month: 'long' })} ${today.getFullYear()}`;
    const pengesahanHTML = `
    <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
    <table style="border: none; text-align: left; width: auto;">
        <tr>
            <td style="border: none; padding: 10px;">
                    <p style="margin: 0;">KAB.BANJAR, ${dateString}</p>
                    <p style="margin: 0;">KANTOR KEMENTERIAN AGAMA</p>
                    <p style="margin: 0;">KABUPATEN BANJAR</p>
                    <p style="margin: 0;">Kasi Penyelenggara Haji dan Umrah</p>
                    <div style="margin: 20px 0; text-align: center;">
                        <img src="ttd_kasi.jpg" alt="Tanda Tangan" style="width: 50px; height: auto;"align=center;>
                    </div>
                    <strong>
                        <p style="margin: 0;"><u>${"<?php echo htmlspecialchars($namaKepala); ?>"}</u></p>
                        <p style="margin: 0;">NIP.198411042003121004</p>
                    </strong>
                </td>
            </tr>
        </table>
    </div>
`;


    // Membuka jendela cetak
    const printWindow = window.open('', '', 'width=1200,height=800');
    printWindow.document.write('<html><head><title>Cetak Pelaporan Data Jamaah</title><style>');
    printWindow.document.write(`
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #4CAF50; /* Hijau */
            text-align: center;
            padding: 10px;
        }
        table tr:hover {
            background-color: #ddd;
        }
        hr {
            border: 2px solid black;
        }
        @media print {
            @page {
                size: A4 landscape; /* Landscape mode */
                margin: 20mm;
            }
        }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(headerHTML); // Menambahkan header
    printWindow.document.write(dataTitle); // Menambahkan deskripsi data
    printWindow.document.write(table); // Menambahkan tabel
    printWindow.document.write(pengesahanHTML); // Menambahkan pengesahan
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>
</html>