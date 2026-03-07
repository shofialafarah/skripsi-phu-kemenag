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

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'jamaah') {
    header("Location: ../auth/login.php");
    exit;
}

$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);

if ($keyword !== '') {
    $sql = "SELECT nama FROM jamaah WHERE nama LIKE '%$keyword%'";
} else {
    $sql = "SELECT nama FROM jamaah";
}
$jamaahResult = $koneksi->query($sql);

$id_jamaah = $_SESSION['id_jamaah'];

$sql = "SELECT jamaah.nama, pendaftaran.nomor_porsi, jamaah.foto, jamaah.id_jamaah
        FROM jamaah
        LEFT JOIN pendaftaran ON pendaftaran.id_jamaah = jamaah.id_jamaah
        WHERE jamaah.id_jamaah = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();

$nama = "Nama tidak ditemukan";
$nomor_porsi = "Belum terdaftar";
$foto = 'profil.jpg';

if ($result && $row = $result->fetch_assoc()) {
    $nama = $row['nama'];
    $nomor_porsi = $row['nomor_porsi'] ?? 'Belum terdaftar';
    $nama_file_foto = $row['foto'];

    $upload_dir_fisik = $_SERVER['DOCUMENT_ROOT'] . '/phu-kemenag-banjar-copy/uploads/akun-pengguna/jamaah/';

    if (!empty($nama_file_foto) && file_exists($upload_dir_fisik . $nama_file_foto)) {
        $foto_tampil = '../../uploads/akun-pengguna/jamaah/' . $nama_file_foto;
    } else {
        $foto_tampil = 'assets/img/profil.jpg';
    }
}
$nomor_porsi_login = $nomor_porsi ?? '';

$sql_status = "
    SELECT 'Pendaftaran Haji' AS jenis_pelayanan, status_verifikasi FROM pendaftaran WHERE id_jamaah = ?
    UNION ALL
    SELECT 'Pembatalan Haji' AS jenis_pelayanan, pb.status_verifikasi FROM pembatalan_ekonomi pe 
    JOIN pembatalan pb ON pb.id_pembatalan = pe.id_pembatalan WHERE pe.nomor_porsi = ?
    UNION ALL
    SELECT 'Pembatalan Haji' AS jenis_pelayanan, pb.status_verifikasi FROM pembatalan_meninggal pm 
    JOIN pembatalan pb ON pb.id_pembatalan = pm.id_pembatalan WHERE pm.nomor_porsi = ?
    UNION ALL
    SELECT 'Pelimpahan Haji' AS jenis_pelayanan, pl.status_verifikasi FROM pelimpahan_sakit ps 
    JOIN pelimpahan pl ON pl.id_pelimpahan = ps.id_pelimpahan WHERE ps.nomor_porsi = ?
    UNION ALL
    SELECT 'Pelimpahan Haji' AS jenis_pelayanan, pl.status_verifikasi FROM pelimpahan_meninggal pm 
    JOIN pelimpahan pl ON pl.id_pelimpahan = pm.id_pelimpahan WHERE pm.nomor_porsi = ?
";

$stmt_status = $koneksi->prepare($sql_status);
$stmt_status->bind_param("issss", $id_jamaah, $nomor_porsi_login, $nomor_porsi_login, $nomor_porsi_login, $nomor_porsi_login);
$stmt_status->execute();
$res_status = $stmt_status->get_result();

// Inisialisasi default
$jenis = 'Belum Ada';
$verifikasi = 'Belum Ada Pengajuan';

if ($data_pelayanan = $res_status->fetch_assoc()) {
    $jenis = $data_pelayanan['jenis_pelayanan'];
    $verifikasi = $data_pelayanan['status_verifikasi'];
}

// Timeline logic
$s1 = ($jenis != 'Belum Ada') ? 'selesai' : 'proses';
$s2 = ($verifikasi == 'Disetujui') ? 'selesai' : (($verifikasi == 'Pending' || $verifikasi == 'Proses') ? 'proses' : 'belum');
$s3 = ($verifikasi == 'Disetujui') ? 'selesai' : 'belum';
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
                            <img src="<?= htmlspecialchars($foto_tampil) ?>" alt="Foto Jamaah" class="jamaah-foto" />
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

                <div class="card status-dokumen-individu">
                    <h3 style="margin-bottom: 20px; font-size: 1.1rem;">
                        <span class="material-symbols-outlined" style="vertical-align: bottom;">history_edu</span>
                        Layanan: <?= htmlspecialchars($jenis) ?>
                    </h3>

                    <div class="timeline-status">
                        <div class="status-item <?= $s1 ?>">
                            <div class="status-icon-wrapper"><span class="material-symbols-outlined">description</span></div>
                            <div class="status-detail">
                                <div class="status-tahap">Berkas Diterima</div>
                                <div class="status-badge-teks"><?= ($s1 == 'selesai') ? 'Sudah Diinput' : 'Menunggu Berkas' ?></div>
                            </div>
                            <div class="status-line"></div>
                        </div>

                        <div class="status-item <?= $s2 ?>">
                            <div class="status-icon-wrapper"><span class="material-symbols-outlined">fact_check</span></div>
                            <div class="status-detail">
                                <div class="status-tahap">Verifikasi Petugas</div>
                                <div class="status-badge-teks"><?= htmlspecialchars($verifikasi) ?></div>
                            </div>
                            <div class="status-line"></div>
                        </div>

                        <div class="status-item <?= $s3 ?>">
                            <div class="status-icon-wrapper"><span class="material-symbols-outlined">task_alt</span></div>
                            <div class="status-detail">
                                <div class="status-tahap">Proses Selesai</div>
                                <div class="status-badge-teks"><?= ($s3 == 'selesai') ? 'Disetujui & Selesai' : 'Belum Selesai' ?></div>
                            </div>
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
<?php unset($_SESSION['login_success_msg']);
endif; ?>

<?php if (isset($_SESSION['update_status'])): ?>
    <script>
        <?php if ($_SESSION['update_status'] === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data profil Anda telah diperbarui.',
                timer: 2000,
                showConfirmButton: false
            });
        <?php else: ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?= $_SESSION['update_msg'] ?? "Terjadi kesalahan sistem."; ?>'
            });
        <?php endif; ?>
    </script>
<?php unset($_SESSION['update_status'], $_SESSION['update_msg']);
endif; ?>
</body>

</html>