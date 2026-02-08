<?php
include 'koneksi.php';

// Cek apakah session id_admin sudah diset
if (!isset($_SESSION['id_admin'])) {

    $id = 1; // sementara fallback (tidak disarankan untuk live)
} else {
    $id = $_SESSION['id_admin'];
}

// Ambil keyword pencarian dari URL (kalau ada)
$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);

// Ambil data admin
$staffQuery = $koneksi->query("SELECT nama_admin FROM administrator WHERE id_admin = $id");
$adminstrator = $staffQuery->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Header Administrator</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- css global -->
    <link rel="stylesheet" href="kumpulan-css/global_style.css">
    <!-- css header -->
    <link rel="stylesheet" href="kumpulan-css/header.css" />
    <!-- css sidebar -->
    <link rel="stylesheet" href="kumpulan-css/sidebar.css" />
    <!-- css dashboard -->
    <link rel="stylesheet" href="kumpulan-css/dashboard.css">

    <link rel="stylesheet" href="dashboard_administrator.css">
    <link rel="stylesheet" href="pendaftaran_jamaah.css">
    <link rel="stylesheet" href="pembatalan_jamaah.css">
</head>

<body>
    <!-- ====================================== header =========================================== -->
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($adminstrator['nama_admin']) ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30</div>
                    </div>
                </div>
            </div>
        </header>
    </div>
    <!-- ========================================================================================================== -->
    <script>
        function updateDate() {
            const now = new Date();
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const namaHari = hari[now.getDay()];
            const tanggal = now.getDate();
            const namaBulan = bulan[now.getMonth()];
            const tahun = now.getFullYear();

            document.getElementById('currentDate').textContent = `${namaHari}, ${tanggal} ${namaBulan} ${tahun}`;
        }

        updateDate();
    </script>

    <script>
        function updateTime() {
            const now = new Date();

            // Ambil jam dan menit
            let hours = now.getHours();
            let minutes = now.getMinutes();

            // Tambahkan 0 di depan jika hanya 1 digit
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;

            // Format jam:menit
            const timeString = `${hours}:${minutes}`;

            // Masukkan ke elemen
            document.getElementById('currentTime').textContent = timeString;
        }

        // Panggil pertama kali
        updateTime();

        // Update setiap 30 detik (atau 60*1000 jika mau tiap menit saja)
        setInterval(updateTime, 30000);
    </script>
    
</body>

</html>