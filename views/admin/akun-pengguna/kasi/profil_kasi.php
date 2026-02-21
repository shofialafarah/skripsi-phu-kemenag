<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

// Fungsi untuk mengubah tanggal menjadi format Indonesia
function formatTanggalIndonesia($tanggal)
{
    // Set locale ke bahasa Indonesia
    setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian_indonesia.1252');

    // Pastikan tanggal tidak kosong dan valid
    if ($tanggal && strtotime($tanggal)) {
        return strftime('%d %B %Y', strtotime($tanggal));
    }
    return '-'; // Jika tanggal kosong, tampilkan tanda '-'
}

// Cek apakah parameter 'id' ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada parameter 'id', arahkan ke profil dengan ID 1
    header("Location: profil_kasi.php?id=1");
    exit();
}

$id_kepala = $_GET['id'];

// Gunakan prepared statement untuk menghindari SQL Injection
$query = "SELECT * FROM kepala_seksi WHERE id_kepala = ?";
$stmt = $koneksi->prepare($query);

if ($stmt === false) {
    echo "Gagal menyiapkan query.";
    exit();
}

// Bind parameter dan eksekusi
$stmt->bind_param("i", $id_kepala);
$stmt->execute();

// Ambil hasil query
$result = $stmt->get_result();

// Jika data ditemukan, simpan ke dalam array $kasi
if ($result && $result->num_rows > 0) {
    $kasi = $result->fetch_assoc();
} else {
    echo "Data kepala seksi tidak ditemukan.";
    exit();
}
?>
<link rel="stylesheet" href="assets/css/kasi.css">
<?php include '../../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>
        
        <div class="profil-kasi-wrapper">
            <div class="profil-kasi-card">
                <div class="profil-kasi-content">
                    <div class="informasi-kasi">
                        <div class="kasi-baris">
                            <div class="info-kasi">
                                <div class="kasi-label">Nama Lengkap</div>
                                <div class="kasi-value"><?php echo htmlspecialchars($kasi['nama_kepala']); ?></div>
                            </div>

                            <div class="info-kasi">
                                <div class="kasi-label">NIP</div>
                                <div class="kasi-value"><?php echo htmlspecialchars($kasi['nip']); ?></div>
                            </div>
                        </div>

                        <div class="kasi-baris">
                            <div class="info-kasi">
                                <div class="kasi-label">Pangkat/Golongan</div>
                                <div class="kasi-value"><?php echo htmlspecialchars($kasi['pangkat']); ?></div>
                            </div>

                            <div class="info-kasi">
                                <div class="kasi-label">Jabatan</div>
                                <div class="kasi-value"><?php echo htmlspecialchars($kasi['jabatan']); ?></div>
                            </div>
                        </div>

                        <div class="kasi-baris">
                            <div class="info-kasi">
                                <div class="kasi-label">Pendidikan Terakhir</div>
                                <div class="kasi-value"><?php echo htmlspecialchars($kasi['pend_terakhir']); ?></div>
                            </div>

                            <div class="info-kasi">
                                <div class="kasi-label">Tempat dan Tanggal Lahir</div>
                                <div class="kasi-value"><?php echo htmlspecialchars($kasi['tempat_lahir']); ?>, <?php echo formatTanggalIndonesia($kasi['tgl_lahir']); ?></div>
                            </div>
                        </div>


                        <div class="kontak-kasi">
                            <h3>Informasi Kontak</h3>
                            <div class="info-kasi">
                                <div class="kasi-label">Alamat</div>
                                <div class="info-kontak-kasi"><?php echo htmlspecialchars($kasi['alamat']); ?></div>
                            </div>

                            <div class="info-kasi">
                                <div class="kasi-label">Email</div>
                                <div class="info-kontak-kasi"><?php echo htmlspecialchars($kasi['email']); ?></div>
                            </div>

                            <div class="info-kasi">
                                <div class="kasi-label">Username</div>
                                <div class="info-kontak-kasi"><?php echo htmlspecialchars($kasi['username']); ?></div>
                            </div>

                            <div class="info-kasi">
                                <div class="kasi-label">Nomor Telepon</div>
                                <div class="info-kontak-kasi"><?php echo htmlspecialchars($kasi['no_telepon']); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="kasi-image">
                        <button type="button" class="btn-kembali-kasi" onclick="window.location.href='manajemen_kasi.php'">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>

                        <img src="<?php echo htmlspecialchars($kasi['foto']); ?>" alt="Foto Kasi">
                        <!-- QR Code -->
                        <?php
                        $id = urlencode($kasi['id_kepala']);
                        $qr_data = "Nama: {$kasi['nama_kepala']}\nNIP: {$kasi['nip']}\nJabatan: {$kasi['jabatan']}";
                        $qr_img = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($qr_data);
                        ?>
                        <div style="margin-top: 10px; text-align: center;">
                            <img src="<?php echo $qr_img; ?>" alt="QR Code Profil Kasi" style="width: 100px; height: 100px;">
                            <p style="font-size: 0.75rem;">Scan Profil</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
            </div>
        </div>
    </div>
</div>
<script src="../../assets/js/sidebar.js"></script>
</body>

</html>