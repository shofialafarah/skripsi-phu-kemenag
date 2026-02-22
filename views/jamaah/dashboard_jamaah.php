<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include '../../includes/koneksi.php';

//Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'jamaah') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil keyword pencarian dari URL (kalau ada)
$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);

// Ambil daftar jamaah (filtered atau tidak)
if ($keyword !== '') {
    $sql = "SELECT nama FROM jamaah WHERE nama LIKE '%$keyword%'";
} else {
    $sql = "SELECT nama FROM jamaah";
}
$jamaahResult = $koneksi->query($sql);

// PERBAIKAN: Ambil id_jamaah dari session, bukan hardcode
$id_jamaah = $_SESSION['id_jamaah']; // Ambil dari session login

// Query untuk mengambil data jamaah yang sedang login
$sql = "SELECT jamaah.nama, pendaftaran.nomor_porsi, jamaah.foto, jamaah.id_jamaah
        FROM jamaah
        LEFT JOIN pendaftaran ON pendaftaran.id_jamaah = jamaah.id_jamaah
        WHERE jamaah.id_jamaah = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();

// Inisialisasi variabel default
$nama = "Nama tidak ditemukan";
$nomor_porsi = "Belum terdaftar";
$foto = 'profil.jpg';

if ($result && $row = $result->fetch_assoc()) {
    $nama = $row['nama'];
    $nomor_porsi = $row['nomor_porsi'] ?? 'Belum terdaftar';
    $foto_path = 'assets/img/' . $row['foto'];
    if (!empty($row['foto']) && file_exists($foto_path)) {
        $foto = $row['foto'];
    } else {
        $foto = 'profil.jpg'; // fallback ke default
    }
} else {
    // Jika data tidak ditemukan, ambil data dasar dari tabel jamaah saja
    $sql_basic = "SELECT nama, foto FROM jamaah WHERE id_jamaah = ?";
    $stmt_basic = $koneksi->prepare($sql_basic);
    $stmt_basic->bind_param("i", $id_jamaah);
    $stmt_basic->execute();
    $result_basic = $stmt_basic->get_result();

    if ($result_basic && $row_basic = $result_basic->fetch_assoc()) {
        $nama = $row_basic['nama'];
        $nomor_porsi = 'Belum terdaftar';
        $foto_path = 'assets/img/' . $row_basic['foto'];
        if (!empty($row_basic['foto']) && file_exists($foto_path)) {
            $foto = $row_basic['foto'];
        } else {
            $foto = 'profil.jpg';
        }
    }
}
?>
<?php include_once 'includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include 'includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include 'includes/header_jamaah.php'; ?>
        <main class="dashboard-wrapper">

            <div class="jamaah-grid">
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

                <!-- Cuaca -->
                <div class="card cuaca">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div class="temperature">24°C</div>
                            <div class="date-info" style="font-size: 1rem; color: gray;">Martapura, Kalimantan Selatan</div>
                        </div>
                        <span class="material-symbols-outlined" style="font-size: 48px; color: #f39c12;">wb_sunny</span>
                    </div>
                    <div class="pembatas">
                    </div>
                    <div class="profil-jamaah">
                        <div class="profil-jamaah-card">
                            <img src="assets/img/<?= htmlspecialchars($foto) ?>" alt="Foto Jamaah" class="jamaah-foto" />
                            <div class="jamaah-info">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <div class="nama-jamaah"><?= htmlspecialchars($nama) ?></div>
                                        <div class="nomor-porsi">No. Porsi <b><?= htmlspecialchars($nomor_porsi) ?></b></div>
                                    </div>
                                    <span class="material-symbols-outlined titik-tiga" onclick="togglePopup()">more_vert</span>
                                </div>
                            </div>
                        </div>
                        <!-- Popup Form -->
                        <div class="popup-form" id="popupForm">
                            <form method="POST" action="includes/popup_edit_profil.php" enctype="multipart/form-data">
                                <input type="hidden" name="id_jamaah" value="<?= $id_jamaah ?>">
                                <label>Nama:</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>

                                <label>Nomor Porsi:</label>
                                <input type="text" name="nomor_porsi" value="<?= htmlspecialchars($nomor_porsi) ?>" required>

                                <label>Foto Baru (opsional):</label>
                                <input type="file" name="foto">

                                <button type="submit">Simpan</button>
                                <button type="button" onclick="togglePopup()">Batal</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Waktu Kerja -->
                <div class="card waktu-kerja">
                    <span class="material-symbols-outlined icon-waktuKerja">more_horiz</span>
                    <div class="working-time-value">450 H</div>
                    <div class="working-time-label">Working Time</div>
                    <div class="farming-activities">
                        <div class="farming-activity">
                            <div class="working-bar-wrapper">
                                <div class="working-bar bar-base"></div>
                                <div class="working-bar bar-overlay height-medium"></div>
                            </div>
                            <div class="working-name">Plowing</div>
                        </div>
                        <div class="farming-activity">
                            <div class="working-bar-wrapper">
                                <div class="working-bar bar-base"></div>
                                <div class="working-bar bar-overlay height-low"></div>
                            </div>
                            <div class="working-name">Spraying</div>
                        </div>
                        <div class="farming-activity">
                            <div class="working-bar-wrapper">
                                <div class="working-bar bar-base"></div>
                                <div class="working-bar bar-overlay height-medium"></div>
                            </div>
                            <div class="working-name">Fertilization</div>
                        </div>
                        <div class="farming-activity">
                            <div class="working-bar-wrapper">
                                <div class="working-bar bar-base"></div>
                                <div class="working-bar bar-overlay height-high"></div>
                            </div>
                            <div class="working-name">Harvesting</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once __DIR__ . '../includes/footer_jamaah.php'; ?>
        </main>
    </div>
</div>

<script src="assets/js/sidebar.js"></script>
<script src="assets/js/kalender_dashboard.js"></script>
<script src="assets/js/cuaca_dashboard.js"></script>
<script>
    function togglePopup() {
        const popup = document.getElementById("popupForm");
        popup.style.display = (popup.style.display === "block") ? "none" : "block";
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['login_success_msg'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil',
        text: '<?= $_SESSION['login_success_msg']; ?>',
        timer: 2000,
        showConfirmButton: false
    });
</script>
<?php unset($_SESSION['login_success_msg']); endif; ?>
</body>

</html>