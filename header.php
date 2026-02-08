<?php include 'koneksi.php';

// Ambil keyword pencarian dari URL (kalau ada)
$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);

// Ambil data staf (pastikan nama kolom di tabel 'staf' adalah 'nama' — cek pakai DESCRIBE staf di phpMyAdmin)
$staffQuery = $koneksi->query("SELECT nama_staf FROM staf WHERE id_staf = 1");
$staff = $staffQuery->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Header Staf</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <!-- css global -->
    <link rel="stylesheet" href="kumpulan-css/global_style.css">
    <!-- css header -->
    <link rel="stylesheet" href="kumpulan-css/header.css" />
    <!-- css sidebar -->
    <link rel="stylesheet" href="kumpulan-css/sidebar.css" />
    <!-- css konten -->
    <link rel="stylesheet" href="pembatalan.css">
    <link rel="stylesheet" href="pelimpahan.css">
</head>

<body>
    <!-- ====================================== header =========================================== -->
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($staff['nama_staf']) ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30:00</div>
                    </div>
                </div>
                <div class="header-actions">
                    <form method="GET" class="header-cari">
                        <span class="material-symbols-outlined search-icon">search</span>
                        <input type="text" name="search" id="searchInput" class="header-input-cari" placeholder="Cari Jamaah..." value="<?= htmlspecialchars($keyword) ?>" />
                    </form>
                    <button class="header-icon"><span class="material-symbols-outlined">mail</span></button>
                    <button class="header-icon"><span class="material-symbols-outlined">notifications</span></button>
                </div>
            </div>
        </header>
    </div>
    <!-- ========================================================================================================== -->
    <script src="dashboard_coba.js"></script>
    <script>
        const updateDateTime = () => {
            const now = new Date();
            const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const day = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            document.getElementById("currentDate").textContent = `${day}, ${date} ${month} ${year}`;
            document.getElementById("currentTime").textContent = `${hours}:${minutes}:${seconds}`;
        };
        setInterval(updateDateTime, 1000);
        updateDateTime(); // pertama kali load
    </script>

</body>

</html>