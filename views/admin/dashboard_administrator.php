<?php
session_start();
include '../../includes/koneksi.php';

//Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    header("Location: ../auth/login.php");
    exit;
}

// Query Statistik Pengguna
$queryTotalPengguna = "
    SELECT SUM(total) AS total_gabungan
    FROM (
        SELECT COUNT(*) AS total FROM administrator
        UNION ALL
        SELECT COUNT(*) AS total FROM staf
        UNION ALL
        SELECT COUNT(*) AS total FROM jamaah
        UNION ALL
        SELECT COUNT(*) AS total FROM kepala_seksi
    ) AS all_users;
";
$totalPengguna = $koneksi->query($queryTotalPengguna)->fetch_assoc()['total_gabungan'] ?? 0;
$totalJamaah = $koneksi->query("SELECT COUNT(*) as total FROM jamaah")->fetch_assoc()['total'] ?? 0;

// Data contoh untuk status dokumen (sesuaikan dengan struktur database Anda)
$statusDokumen = [];

// Query Dokumen Jamaah dengan berbagai jenis pelayanan
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
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include 'includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include 'includes/header_admin.php'; ?>

        <main class="dashboard-wrapper">
            <div class="administrator-grid">
                <!-- Kalender -->
                <div class="card kalender">
                    <div class="header-kalender text-center">
                        <span>May 2025</span>
                    </div>
                    <div class="grid-kalender">
                        <div class="hari">Sun</div>
                        <div class="hari">Mon</div>
                        <div class="hari">Tue</div>
                        <div class="hari">Wed</div>
                        <div class="hari">Thu</div>
                        <div class="hari">Fri</div>
                        <div class="hari">Sat</div>

                        <!-- contoh isi tanggal -->
                        <div class="tanggal">1</div>
                        <div class="tanggal">2</div>
                        <div class="tanggal today-fix">3</div>
                        <div class="tanggal">4</div>
                        <!-- lanjutkan sesuai kebutuhan -->
                    </div>
                </div>

                <!-- Stats Cards - Total Pengguna & Total Jamaah -->
                <div class="card stats-cards">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Pengguna</div>
                            <span class="material-symbols-outlined stat-icon">group</span>
                        </div>
                        <div class="stat-value"><?= number_format($totalPengguna) ?></div>
                        <div class="stat-description">
                            Pengguna aktif sistem
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Jamaah</div>
                            <span class="material-symbols-outlined stat-icon">mosque</span>
                        </div>
                        <div class="stat-value"><?= number_format($totalJamaah) ?></div>
                        <div class="stat-description">
                            Jamaah terdaftar
                        </div>
                    </div>
                </div>

                <!-- Cuaca -->
                <div class="card cuaca">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div class="temperature">24°C</div>
                            <div class="date-info" style="font-size: 1rem; color: gray;">Martapura, Kalimantan Selatan</div>
                        </div>
                        <span class="material-symbols-outlined" style="font-size: 48px; color: #f39c12;">wb_sunny</span>
                    </div>
                </div>

                <!-- Status Dokumen Jamaah -->
                <div class="card status-dokumen">
                    <h3 class="text-center">Status Dokumen Jamaah</h3>
                    <table class="dokumen-table">
                        <thead>
                            <tr>
                                <th>NO.</th>
                                <th>NAMA JAMAAH</th>
                                <th>NOMOR PORSI</th>
                                <th>JENIS PELAYANAN</th>
                                <th>KELENGKAPAN</th>
                                <th>STATUS VERIFIKASI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($statusDokumen as $dokumen): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($dokumen['nama']) ?></strong></td>
                                    <td><?= htmlspecialchars($dokumen['nomor_porsi']) ?></td>
                                    <td>
                                        <span class="<?= getBadgeClass($dokumen['jenis_pelayanan']) ?>">
                                            <?= htmlspecialchars($dokumen['jenis_pelayanan']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= calculateProgress($dokumen) ?>%"></div>
                                        </div>
                                        <small><?= round(calculateProgress($dokumen)) ?>%</small>
                                    </td>
                                    <td>
                                        <?php
                                        $progress = calculateProgress($dokumen);
                                        $statusTeks = $progress == 100 ? 'Disetujui' : ($progress >= 50 ? 'Pending' : 'Ditolak');
                                        ?>
                                        <span class="status-badge <?= getStatusClass($statusTeks) ?>">
                                            <span class="status-icon"><?= getStatusIcon($statusTeks) ?></span>
                                            <?= $statusTeks ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="footer" style="color: white; text-align: center;">
                <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
            </div>
        </main>
    </div>
</div>

<script src="assets/js/sidebar_jamaah.js"></script>
<script>
    const grid = document.querySelector('.grid-kalender');
    const header = document.querySelector('.header-kalender span:first-child');

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
        dayEl.className = 'hari';
        dayEl.textContent = day;
        grid.appendChild(dayEl);
    }

    // Find first day and number of days in month
    const firstDay = new Date(year, month, 1).getDay(); // 0 (Sun) to 6 (Sat)
    const lastDate = new Date(year, month + 1, 0).getDate(); // Last date of current month

    // Fill empty cells before 1st
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'tanggal empty';
        grid.appendChild(emptyCell);
    }

    // Fill the dates
    for (let i = 1; i <= lastDate; i++) {
        const dateCell = document.createElement('div');
        dateCell.className = 'tanggal';
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
            })
            .catch(error => {
                console.error("Gagal ambil data cuaca:", error);
            });
    });
</script>