<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php");
    exit();
}

$id_staf = $_SESSION['id_staf'];

$sql_staf = "SELECT nama_staf FROM staf WHERE id_staf = ?";
$stmt = $koneksi->prepare($sql_staf);
$stmt->bind_param("i", $id_staf);
$stmt->execute();
$result = $stmt->get_result();
$staf = $result->fetch_assoc();

// ======================================
// Notifikasi untuk berkas yang belum divalidasi
// ======================================
$sql_notif_validasi = "
    -- Notifikasi untuk Pendaftaran yang belum divalidasi
    SELECT nama_jamaah, 'Pendaftaran' AS jenis_pelayanan
    FROM pendaftaran
    WHERE tanggal_validasi IS NULL

    UNION ALL

    -- Notifikasi untuk Pembatalan yang belum divalidasi
    SELECT j.nama AS nama_jamaah, 'Pembatalan' AS jenis_pelayanan
    FROM pembatalan b
    JOIN jamaah j ON b.id_jamaah = j.id_jamaah
    WHERE b.tanggal_validasi IS NULL

    UNION ALL

    -- Notifikasi untuk Pelimpahan yang belum divalidasi
    SELECT j.nama AS nama_jamaah, 'Pelimpahan' AS jenis_pelayanan
    FROM pelimpahan l
    JOIN jamaah j ON l.id_jamaah = j.id_jamaah
    WHERE l.tanggal_validasi IS NULL
";
$stmt_notif = $koneksi->prepare($sql_notif_validasi);

$stmt_notif->execute();
$result_notif = $stmt_notif->get_result();

$notifikasi_validasi = [];
while ($row = $result_notif->fetch_assoc()) {
    $notifikasi_validasi[] = $row;
}
$jumlah_notif_validasi = count($notifikasi_validasi);

// ======================================
// Notifikasi untuk berkas yang ditolak kepala seksi
// ======================================
$sql_notif_ditolak = "
    SELECT nama_jamaah, 'Pendaftaran' AS jenis_pelayanan
    FROM pendaftaran
    WHERE status_verifikasi = 'Ditolak'

    UNION ALL

    SELECT j.nama AS nama_jamaah, 'Pembatalan' AS jenis_pelayanan
    FROM pembatalan b
    JOIN jamaah j ON b.id_jamaah = j.id_jamaah
    WHERE b.status_verifikasi = 'Ditolak'

    UNION ALL

    SELECT j.nama AS nama_jamaah, 'Pelimpahan' AS jenis_pelayanan
    FROM pelimpahan l
    JOIN jamaah j ON l.id_jamaah = j.id_jamaah
    WHERE l.status_verifikasi = 'Ditolak'
";
$stmt_notif_ditolak = $koneksi->prepare($sql_notif_ditolak);
$stmt_notif_ditolak->execute();
$result_notif_ditolak = $stmt_notif_ditolak->get_result();

$notifikasi_ditolak = [];
while ($row = $result_notif_ditolak->fetch_assoc()) {
    $notifikasi_ditolak[] = $row;
}
$jumlah_notif_ditolak = count($notifikasi_ditolak);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Header Staf</title>
    <link rel="icon" href="/phu-kemenag-banjar-copy/views/staf/assets/img/logo_kemenag.png">
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
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/global_style.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/header.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/sidebar.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/dashboard.css">

    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/dashboard_staf.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/monitoring_pendaftaran.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/monitoring_pembatalan.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/monitoring_pelimpahan.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/entry.css">
    <link rel="stylesheet" href="/phu-kemenag-banjar-copy/views/staf/assets/css/notifikasi.css">
    <style>
        .dropdown-menu.scrollable-dropdown {
            max-height: 300px;
            /* batas tinggi dropdown */
            overflow-y: auto;
            /* bikin scroll kalau isi banyak */
        }

        /* Hover badge notifikasi (lonceng) */
        .header-actions .dropdown:hover .badge-notif {
            color: #fff;
            /* warna teks */
        }
    </style>
</head>

<body>
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($staf['nama_staf']) ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30</div>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="dropdown">
                        <button class="header-icon position-relative" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-outlined">mail</span><?php if ($jumlah_notif_validasi > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success badge-notif">
                                    <?= $jumlah_notif_validasi ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end scrollable-dropdown">
                            <?php if ($jumlah_notif_validasi > 0): ?>
                                <?php foreach ($notifikasi_validasi as $notif): ?>
                                    <li>
                                        <span class="dropdown-item">
                                            <?php
                                            $warnaBadge = '';
                                            switch ($notif['jenis_pelayanan']) {
                                                case 'Pendaftaran':
                                                    $warnaBadge = 'bg-success'; // hijau
                                                    break;
                                                case 'Pembatalan':
                                                    $warnaBadge = 'bg-danger'; // merah
                                                    break;
                                                case 'Pelimpahan':
                                                    $warnaBadge = 'bg-warning text-dark'; // kuning
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $warnaBadge ?>">
                                                <?= htmlspecialchars($notif['jenis_pelayanan']) ?>
                                            </span>
                                            Berkas atas nama <strong><?= htmlspecialchars($notif['nama_jamaah']) ?></strong> perlu divalidasi.
                                        </span>
                                    </li>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Tidak ada notifikasi baru</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="header-icon position-relative" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-outlined">notifications</span>
                            <?php if ($jumlah_notif_ditolak > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-notif">
                                    <?= $jumlah_notif_ditolak ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end scrollable-dropdown">
                            <?php if ($jumlah_notif_ditolak > 0): ?>
                                <?php foreach ($notifikasi_ditolak as $notif): ?>
                                    <li>
                                        <span class="dropdown-item">
                                            <?php
                                            $warnaBadge = '';
                                            switch ($notif['jenis_pelayanan']) {
                                                case 'Pendaftaran':
                                                    $warnaBadge = 'bg-success'; // hijau
                                                    break;
                                                case 'Pembatalan':
                                                    $warnaBadge = 'bg-danger'; // merah
                                                    break;
                                                case 'Pelimpahan':
                                                    $warnaBadge = 'bg-warning text-dark'; // kuning
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $warnaBadge ?>">
                                                <?= htmlspecialchars($notif['jenis_pelayanan']) ?>
                                            </span>
                                            Berkas atas nama <strong><?= htmlspecialchars($notif['nama_jamaah']) ?></strong> ditolak oleh Kepala Seksi.
                                        </span>
                                    </li>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Tidak ada berkas yang ditolak</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </header>
    </div>
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

            let hours = now.getHours();
            let minutes = now.getMinutes();

            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;

            const timeString = `${hours}:${minutes}`;

            document.getElementById('currentTime').textContent = timeString;
        }

        updateTime();
        setInterval(updateTime, 30000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>