<?php
include 'koneksi.php';

// ---------- 1. BELUM diverifikasi (status_verifikasi = 'Pending') ----------
$queryJamaahBelumVerifikasi = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM pendaftaran  WHERE status_verifikasi = 'Pending'
        UNION ALL
        SELECT COUNT(*) AS total FROM pembatalan    WHERE status_verifikasi = 'Pending'
        UNION ALL
        SELECT COUNT(*) AS total FROM pelimpahan    WHERE status_verifikasi = 'Pending'
    ) AS belum_verifikasi
";
$resultJamaahBelumVerifikasi  = $koneksi->query($queryJamaahBelumVerifikasi);
$totalJamaahBelumVerifikasi   = $resultJamaahBelumVerifikasi->fetch_assoc()['total_gabungan'] ?? 0;

// ---------- 2. SUDAH diverifikasi (status_verifikasi = 'Disetujui') ----------
$queryJamaahSudahVerifikasi = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM pendaftaran  WHERE status_verifikasi = 'Disetujui'
        UNION ALL
        SELECT COUNT(*) AS total FROM pembatalan    WHERE status_verifikasi = 'Disetujui'
        UNION ALL
        SELECT COUNT(*) AS total FROM pelimpahan    WHERE status_verifikasi = 'Disetujui'
    ) AS sudah_verifikasi
";
$resultJamaahSudahVerifikasi  = $koneksi->query($queryJamaahSudahVerifikasi);
$totalJamaahSudahVerifikasi   = $resultJamaahSudahVerifikasi->fetch_assoc()['total_gabungan'] ?? 0;

// ---------- 3. (Opsional) DITOLAK ----------
$queryJamaahDitolak = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM pendaftaran  WHERE status_verifikasi = 'Ditolak'
        UNION ALL
        SELECT COUNT(*) AS total FROM pembatalan    WHERE status_verifikasi = 'Ditolak'
        UNION ALL
        SELECT COUNT(*) AS total FROM pelimpahan    WHERE status_verifikasi = 'Ditolak'
    ) AS ditolak
";
$resultJamaahDitolak = $koneksi->query($queryJamaahDitolak);
$totalJamaahDitolak  = $resultJamaahDitolak->fetch_assoc()['total_gabungan'] ?? 0;

// Data contoh untuk status dokumen (sesuaikan dengan struktur database Anda)
$statusDokumen = [];

// Gabungkan semua nama dari tabel-tabel terkait
$sql = "
    SELECT 
        p.nama_jamaah, 
        p.nomor_porsi, 
        'Pendaftaran Haji' AS jenis_pelayanan,
        NULL AS dokumen_akta_kematian_status,
        p.dokumen_setor_awal_status,
        NULL AS dokumen_spph_status,
        NULL AS dokumen_ahli_waris_status,
        NULL AS dokumen_surat_kuasa_status,
        p.dokumen_ktp_status AS dokumen_ktp_ahliwaris_status,
        NULL AS dokumen_ktp_penerima_kuasa_status,
        p.dokumen_kk_status,
        NULL AS dokumen_akta_kelahiran_status,
        NULL AS dokumen_buku_nikah_status,
        p.dokumen_lain AS dokumen_rekening_status,
        p.foto_wajah_status,
        p.status_verifikasi
    FROM pendaftaran p

    UNION ALL

    SELECT 
        pe.nama_jamaah, 
        pe.nomor_porsi, 
        'Pembatalan Haji' AS jenis_pelayanan,
        NULL AS dokumen_akta_kematian_status,
        pe.dokumen_setor_awal_status,
        pe.dokumen_spph_status,
        NULL AS dokumen_ahli_waris_status,
        NULL AS dokumen_surat_kuasa_status,
        pe.dokumen_ktp_status AS dokumen_ktp_ahliwaris_status,
        NULL AS dokumen_ktp_penerima_kuasa_status,
        pe.dokumen_kk_status,
        pe.dokumen_akta_kelahiran_status,
        NULL AS dokumen_buku_nikah_status,
        pe.dokumen_rekening_status,
        pe.foto_wajah_status,
        pb.status_verifikasi
    FROM pembatalan_ekonomi pe
    JOIN pembatalan pb ON pb.id_pembatalan = pe.id_pembatalan

    UNION ALL

    SELECT 
        pm.nama_jamaah, 
        pm.nomor_porsi, 
        'Pembatalan Haji' AS jenis_pelayanan,
        pm.dokumen_akta_kematian_status,
        pm.dokumen_setor_awal_status,
        pm.dokumen_spph_status,
        pm.dokumen_ahli_waris_status,
        pm.dokumen_surat_kuasa_status,
        pm.dokumen_ktp_ahliwaris_status,
        pm.dokumen_ktp_penerima_kuasa_status,
        pm.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        pm.dokumen_akta_kelahiran_status,
        pm.dokumen_buku_nikah_status,
        pm.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        pm.foto_wajah_status,
        pb.status_verifikasi
    FROM pembatalan_meninggal pm
    JOIN pembatalan pb ON pb.id_pembatalan = pm.id_pembatalan

    UNION ALL

    SELECT 
        ps.nama_jamaah, 
        ps.nomor_porsi, 
        'Pelimpahan Haji' AS jenis_pelayanan,
        NULL AS dokumen_akta_kematian_status,
        ps.dokumen_setor_awal_status,
        ps.dokumen_spph_status,
        ps.dokumen_ahli_waris_status,
        ps.dokumen_surat_kuasa_status,
        ps.dokumen_ktp_ahliwaris_status,
        ps.dokumen_ktp_penerima_kuasa_status,
        ps.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        ps.dokumen_akta_kelahiran_status,
        ps.dokumen_buku_nikah_status,
        ps.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        ps.foto_wajah_status,
        pl.status_verifikasi
    FROM pelimpahan_sakit ps
    JOIN pelimpahan pl ON pl.id_pelimpahan = ps.id_pelimpahan

    UNION ALL

    SELECT 
        pm.nama_jamaah, 
        pm.nomor_porsi, 
        'Pelimpahan Haji' AS jenis_pelayanan,
        pm.dokumen_akta_kematian_status,
        pm.dokumen_setor_awal_status,
        pm.dokumen_spph_status,
        pm.dokumen_ahli_waris_status,
        pm.dokumen_surat_kuasa_status,
        pm.dokumen_ktp_ahliwaris_status,
        pm.dokumen_ktp_penerima_kuasa_status,
        pm.dokumen_kk_penerima_kuasa_status AS dokumen_kk_status,
        pm.dokumen_akta_kelahiran_status,
        pm.dokumen_buku_nikah_status,
        pm.dokumen_rekening_kuasa_status AS dokumen_rekening_status,
        pm.foto_wajah_status,
        pl.status_verifikasi
    FROM pelimpahan_meninggal pm
    JOIN pelimpahan pl ON pl.id_pelimpahan = pm.id_pelimpahan
";


$result = $koneksi->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Daftar semua kemungkinan kolom dokumen
        $dokumenKeys = [
            'dokumen_akta_kematian_status',
            'dokumen_setor_awal_status',
            'dokumen_spph_status',
            'dokumen_ahli_waris_status',
            'dokumen_surat_kuasa_status',
            'dokumen_ktp_ahliwaris_status',
            'dokumen_ktp_penerima_kuasa_status',
            'dokumen_kk_penerima_kuasa_status',
            'dokumen_kk_status',
            'dokumen_ktp_status',
            'dokumen_akta_kelahiran_status',
            'dokumen_buku_nikah_status',
            'dokumen_rekening_status',
            'dokumen_rekening_kuasa_status',
            'dokumen_surat_sakit_status',
            'foto_wajah_status'
        ];

        // Ambil hanya kolom yang tersedia dari hasil $row
        $dokumenStatus = [];
        foreach ($dokumenKeys as $key) {
            if (isset($row[$key])) {
                $dokumenStatus[$key] = strtolower($row[$key]);
            }
        }

        $statusDokumen[] = [
            'nama'    => $row['nama_jamaah'],
            'nomor_porsi'        => $row['nomor_porsi'],
            'jenis_pelayanan' => $row['jenis_pelayanan'],
            'dokumen_status'    => $dokumenStatus,
            'verifikasi_status' => strtolower($row['status_verifikasi'] ?? 'pending') // fallback pending
        ];
    }
}


function getStatusClass($status)
{
    switch (strtolower($status)) {
        case 'disetujui':
            return 'status-complete';
        case 'pending':
            return 'status-pending';
        case 'ditolak':
            return 'status-missing';
        default:
            return 'status-pending';
    }
}


function getStatusIcon($status)
{
    switch (strtolower($status)) {
        case 'disetujui':
            return '✓';
        case 'pending':
            return '⏳';
        case 'ditolak':
            return '✗';
        default:
            return '⏳';
    }
}

function calculateProgress($dokumen)
{
    $dokumenStatuses = $dokumen['dokumen_status'];
    $complete = 0;
    $total = 0;

    foreach ($dokumenStatuses as $status) {
        if ($status === null) {
            continue; // lewati dokumen yang tidak relevan (NULL)
        }
        $total++;
        if (strtolower($status) === 'terverifikasi') {
            $complete++;
        }
    }

    // Hindari pembagian nol
    if ($total === 0) return 0;

    return ($complete / $total) * 100;
}

function getBadgeClass($jenis)
{
    switch (strtolower($jenis)) {
        case 'pendaftaran haji':
            return 'badge bg-success'; // hijau
        case 'pembatalan haji':
            return 'badge bg-danger';  // merah
        case 'pelimpahan haji':
            return 'badge bg-warning text-dark'; // kuning
        default:
            return 'badge bg-secondary';
    }
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Kepala Seksi</title>
    <link rel="icon" href="logo_kemenag.png">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        .dokumen-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .dokumen-tag {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
            border: 1px solid #bbdefb;
        }

        .dokumen-tag:hover {
            background-color: #bbdefb;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_kasi.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_kasi.php'; ?>

            <!-- Ganti bagian <main class="dashboardKasi-wrapper"> dengan kode ini -->
            <main class="dashboardKasi-wrapper">
                <div class="kasi-grid">

                    <!-- Kalender - Kolom 1, Baris 1-2 -->
                    <div class="card kalender-modern-fix">
                        <div class="kalender-header-fix">
                            <span>May 2025</span>
                        </div>
                        <div class="kalender-grid-fix">
                            <div class="kalender-day-fix">Sun</div>
                            <div class="kalender-day-fix">Mon</div>
                            <div class="kalender-day-fix">Tue</div>
                            <div class="kalender-day-fix">Wed</div>
                            <div class="kalender-day-fix">Thu</div>
                            <div class="kalender-day-fix">Fri</div>
                            <div class="kalender-day-fix">Sat</div>
                            <!-- contoh isi tanggal -->
                            <div class="kalender-date-fix">1</div>
                            <div class="kalender-date-fix">2</div>
                            <div class="kalender-date-fix today-fix">3</div>
                            <div class="kalender-date-fix">4</div>
                            <!-- lanjutkan sesuai kebutuhan -->
                        </div>
                    </div>

                    <!-- Stats Cards - Kolom 2, Baris 1-2 -->
                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Jamaah Ditolak</div>
                                <span class="material-symbols-outlined stat-icon">close</span>
                            </div>
                            <div class="stat-value"><?= number_format($totalJamaahDitolak) ?></div>
                            <div class="stat-description">
                                Jamaah tidak diverifikasi
                                <span class="stat-trend">❌</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-title">Jamaah Sudah Diverifikasi</div>
                                <span class="material-symbols-outlined stat-icon">verified</span>
                            </div>
                            <div class="stat-value"><?= number_format($totalJamaahSudahVerifikasi) ?></div>
                            <div class="stat-description">
                                Jamaah terverifikasi
                                <span class="stat-trend">✅</span>
                            </div>
                        </div>
                    </div>

                    <!-- Cuaca - Kolom 3, Baris 1 -->
                    <div class="cuaca">
                        <div class="cuaca-card">
                            <div class="cuaca-content">
                                <div class="cuaca-info">
                                    <div class="temperature">24°C</div>
                                    <div class="date-info" style="font-size: 1rem; color: rgba(255,255,255,0.8);">
                                        Martapura, Kalimantan Selatan
                                    </div>
                                </div>
                                <span class="material-symbols-outlined cuaca-icon">wb_sunny</span>
                            </div>
                        </div>
                    </div>
                    <div class="status-card">
                        <div class="stat-header">
                            <div class="stat-title">Jamaah Belum Diverifikasi</div>
                            <span class="material-symbols-outlined stat-icon">pending</span>
                        </div>
                        <div class="stat-value"><?= number_format($totalJamaahBelumVerifikasi) ?></div>
                        <div class="stat-description">
                            Jamaah menunggu verifikasi
                            <span class="stat-trend">📋</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="sidebar_jamaah.js"></script>
    <script>
        const grid = document.querySelector('.kalender-grid-fix');
        const header = document.querySelector('.kalender-header-fix span:first-child');

        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth(); // 0-indexed

        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        header.textContent = `${monthNames[month]} ${year}`;

        // Clear previous dates if needed
        grid.innerHTML = '';

        // Days of the week
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        for (const day of dayNames) {
            const dayEl = document.createElement('div');
            dayEl.className = 'kalender-day-fix';
            dayEl.textContent = day;
            grid.appendChild(dayEl);
        }

        // Find first day and number of days in month
        const firstDay = new Date(year, month, 1).getDay(); // 0 (Sun) to 6 (Sat)
        const lastDate = new Date(year, month + 1, 0).getDate(); // Last date of current month

        // Fill empty cells before 1st
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'kalender-date-fix empty';
            grid.appendChild(emptyCell);
        }

        // Fill the dates
        for (let i = 1; i <= lastDate; i++) {
            const dateCell = document.createElement('div');
            dateCell.className = 'kalender-date-fix';
            dateCell.textContent = i;

            if (
                i === today.getDate() &&
                month === today.getMonth() &&
                year === today.getFullYear()
            ) {
                dateCell.classList.add('today-fix');
            }

            grid.appendChild(dateCell);
        }
    </script>
    <script>
        function togglePopup() {
            const popup = document.getElementById("popupForm");
            popup.style.display = (popup.style.display === "block") ? "none" : "block";
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const apiKey = "10fc461d891b997b984bfd3e8114334b";
            const city = "Martapura,ID"; // Kota dan kode negara (ID untuk Indonesia)
            const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const temp = Math.round(data.main.temp);
                    const humidity = data.main.humidity;
                    const pressure = data.main.pressure;
                    const wind = data.wind.speed;
                    const icon = data.weather[0].icon;

                    document.querySelector(".temperature").textContent = `${temp}°C`;

                    // Menampilkan nama kota dan provinsi
                    document.querySelector(".date-info").textContent = "Martapura, Kalimantan Selatan";

                    // Update elemen lainnya (jika perlu)
                    // document.querySelectorAll(".weather-details div")[0].lastElementChild.textContent = `Humidity: ${humidity}%`;
                    // document.querySelectorAll(".weather-details div")[1].lastElementChild.textContent = `Pressure: ${pressure} hPa`;
                    // document.querySelectorAll(".weather-details div")[2].lastElementChild.textContent = `Wind: ${wind} Km/h`;
                })
                .catch(error => {
                    console.error("Gagal ambil data cuaca:", error);
                });
        });
    </script>


</body>

</html>