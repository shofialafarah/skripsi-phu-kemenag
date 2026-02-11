<?php
session_start();
include '../../../includes/koneksi.php';
include '../../partials/fungsi.php';

// Fungsi untuk format waktu dari hari (sama seperti di entry_estimasi.php)
function formatWaktuDariHari($total_hari)
{
    $total_hari = (int) $total_hari;

    if ($total_hari == 0) {
        return 'Hari ini';
    }

    $tahun = floor($total_hari / 365);
    $sisa_hari = $total_hari % 365;
    $bulan = floor($sisa_hari / 30);
    $hari = $sisa_hari % 30;

    $hasil = [];

    if ($tahun > 0) {
        $hasil[] = $tahun . ' tahun';
    }
    if ($bulan > 0) {
        $hasil[] = $bulan . ' bulan';
    }
    if ($hari > 0) {
        $hasil[] = $hari . ' hari';
    }

    return empty($hasil) ? '0 hari' : implode(', ', $hasil);
}

// Fungsi untuk menghitung selisih waktu dalam format yang lebih detail
function hitungSelisihWaktu($tanggal_awal, $tanggal_akhir)
{
    $awal = new DateTime($tanggal_awal);
    $akhir = new DateTime($tanggal_akhir);
    $selisih = $awal->diff($akhir);

    $hasil = [];

    if ($selisih->y > 0) {
        $hasil[] = $selisih->y . ' tahun';
    }
    if ($selisih->m > 0) {
        $hasil[] = $selisih->m . ' bulan';
    }
    if ($selisih->d > 0) {
        $hasil[] = $selisih->d . ' hari';
    }

    // Jika tidak ada selisih yang signifikan, tampilkan hari
    if (empty($hasil)) {
        $total_hari = $awal->diff($akhir)->days;
        if ($total_hari == 0) {
            return 'Hari ini';
        } else {
            return $total_hari . ' hari';
        }
    }

    return implode(', ', $hasil);
}
?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once '../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once '../includes/header_jamaah.php'; ?>
        <div class="estimasi-wrapper">
            <div class="estimasi">
                <h1 class="card-title">
                    <svg class="kaaba-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <rect x="6" y="6" width="12" height="12" rx="1" />
                        <path d="M9 6V4" />
                        <path d="M15 6V4" />
                        <path d="M9 18v2" />
                        <path d="M15 18v2" />
                        <path d="M6 9H4" />
                        <path d="M6 15H4" />
                        <path d="M18 9h2" />
                        <path d="M18 15h2" />
                    </svg>
                    INFORMASI ESTIMASI KEBERANGKATAN JAMAAH HAJI
                </h1>

                <form id="searchForm" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="search-container">
                        <label for="nomorPorsi">Nomor Porsi</label>
                        <input type="text" id="nomorPorsi" name="nomorPorsi" placeholder="Masukkan nomor porsi jamaah..." required>
                        <button type="submit" name="cariJamaah">
                            <i class="fas fa-search"></i> CARI JAMAAH
                        </button>
                    </div>
                </form>

                <?php
                if (isset($_POST['cariJamaah'])) {
                    $nomorPorsi = $koneksi->real_escape_string($_POST['nomorPorsi']);

                    $sql = "
                            SELECT 
                                p.nama_jamaah, p.nama_ayah, p.jenis_kelamin, p.tempat_lahir, p.tanggal_lahir, p.status_pergi_haji,
                                e.tgl_pendaftaran, e.telah_menunggu, e.umur, e.sisa_menunggu, e.masa_menunggu, e.estimasi_berangkat
                            FROM pendaftaran p
                            JOIN estimasi e ON p.id_pendaftaran = e.id_pendaftaran
                            WHERE p.nomor_porsi = '$nomorPorsi'
                            LIMIT 1
                        ";
                    $result = $koneksi->query($sql);

                    if ($result && $result->num_rows > 0) {
                        // ✅ Catat aktivitas hanya jika data berhasil ditemukan
                        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Estimasi', 'Mengecek estimasi keberangkatan haji');
                        $jamaahData = $result->fetch_assoc();

                        echo '<h2 class="subtitle">PERKIRAAN TAHUN KEBERANGKATAN JAMAAH HAJI</h2>';
                        echo '<div class="info-grid">';

                        // Kolom kiri
                        echo '<div>';
                        echo '<div class="info-estimasi"><label>NAMA JAMAAH</label><div class="value">' . htmlspecialchars($jamaahData['nama_jamaah']) . '</div></div>';
                        echo '<div class="info-estimasi"><label>NAMA AYAH</label><div class="value">' . htmlspecialchars($jamaahData['nama_ayah']) . '</div></div>';
                        echo '<div class="info-estimasi"><label>JENIS KELAMIN</label><div class="value">' . htmlspecialchars($jamaahData['jenis_kelamin']) . '</div></div>';

                        // Format tanggal lahir
                        $tanggal_lahir_formatted = !empty($jamaahData['tanggal_lahir']) ? date('d-m-Y', strtotime($jamaahData['tanggal_lahir'])) : '-';
                        echo '<div class="info-estimasi"><label>TEMPAT, TANGGAL LAHIR</label><div class="value">' . htmlspecialchars($jamaahData['tempat_lahir']) . ', ' . $tanggal_lahir_formatted . '</div></div>';

                        // Format tanggal pendaftaran
                        $tgl_pendaftaran_formatted = !empty($jamaahData['tgl_pendaftaran']) ? date('d-m-Y', strtotime($jamaahData['tgl_pendaftaran'])) : '-';
                        echo '<div class="info-estimasi"><label>TANGGAL PENDAFTARAN</label><div class="value">' . $tgl_pendaftaran_formatted . '</div></div>';

                        // Format telah menunggu menggunakan fungsi yang sama
                        $telah_menunggu_formatted = !empty($jamaahData['tgl_pendaftaran']) ? hitungSelisihWaktu($jamaahData['tgl_pendaftaran'], date('Y-m-d')) : '-';
                        echo '<div class="info-estimasi"><label>TELAH MENUNGGU</label><div class="value">' . $telah_menunggu_formatted . '</div></div>';
                        echo '</div>';

                        // Kolom kanan
                        echo '<div>';
                        echo '<div class="info-estimasi"><label>STATUS HAJI</label><div class="value">' . htmlspecialchars($jamaahData['status_pergi_haji']) . '</div></div>';
                        echo '<div class="info-estimasi"><label>UMUR</label><div class="value">' . htmlspecialchars($jamaahData['umur']) . ' th</div></div>';

                        echo '<div class="info-estimasi">';
                        echo '<label>INFORMASI WAKTU TUNGGU</label>';
                        echo '<div class="waiting-info">';

                        // Format sisa menunggu dan masa menunggu menggunakan fungsi yang sama
                        $sisa_menunggu_formatted = formatWaktuDariHari($jamaahData['sisa_menunggu'] ?? 0);
                        $masa_menunggu_formatted = formatWaktuDariHari($jamaahData['masa_menunggu'] ?? 0);

                        echo '<div class="waiting-estimasi"><span>Sisa Menunggu</span><span>' . $sisa_menunggu_formatted . '</span></div>';
                        echo '<div class="waiting-estimasi"><span>Masa Menunggu</span><span>' . $masa_menunggu_formatted . '</span></div>';
                        echo '</div>';
                        echo '</div>';

                        echo '<br>';

                        // Format estimasi berangkat
                        $estimasi_berangkat_formatted = !empty($jamaahData['estimasi_berangkat']) ? date('d-m-Y', strtotime($jamaahData['estimasi_berangkat'])) : '-';
                        echo '<div class="info-estimasi"><label>ESTIMASI BERANGKAT</label><div class="value"><span class="badge"><div class="pulse"></div>' . $estimasi_berangkat_formatted . '</span></div></div>';

                        echo '</div>';
                        echo '</div>'; // End info-grid
                    } else {
                        echo "<p>Data tidak ditemukan untuk Nomor Porsi: <strong>" . htmlspecialchars($nomorPorsi) . "</strong></p>";
                    }

                    $koneksi->close();
                }
                ?>
                <?php include_once __DIR__ . '/../includes/footer_jamaah.php'; ?>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/sidebar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('nomorPorsi');
        if (searchInput) {
            searchInput.addEventListener('focus', function() {
                this.parentElement.style.boxShadow = '0 0 0 3px rgba(109, 230, 109, 0.3)';
            });
            searchInput.addEventListener('blur', function() {
                this.parentElement.style.boxShadow = 'none';
            });
        }
    });
</script>
</body>

</html>