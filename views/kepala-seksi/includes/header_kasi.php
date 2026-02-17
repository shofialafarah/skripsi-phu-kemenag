<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

// Ambil keyword pencarian dari URL (kalau ada)
$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);

// Ambil data kasi (pastikan nama kolom di tabel 'kasi' adalah 'nama' — cek pakai DESCRIBE kasi di phpMykepala-seksi)
$staffQuery = $koneksi->query("SELECT nama_kepala FROM kepala_seksi WHERE id_kepala = 1");
$staff = $staffQuery->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Kepala Seksi</title>
    <link rel="icon" href="../assets/img/logo_kemenag.png">
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
    <!-- css -->
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/global_style.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/header.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/sidebar.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/dashboard.css">
    <!-- css halaman tampil -->
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/entry.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/dashboard_kepala_seksi.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/verifikasi_kepala_seksi.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/laporan_kepala_seksi.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/css/estimasi.css">
</head>

<body>
    <!-- ====================================== header =========================================== -->
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($staff['nama_kepala']) ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30</div>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="header-icon"><span class="material-symbols-outlined">mail</span></button>
                    <button class="header-icon"><span class="material-symbols-outlined">notifications</span></button>
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