<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';
// Fungsi untuk mengubah tanggal menjadi format Indonesia
function formatTanggalIndonesia($tanggal) {
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
    header("Location: profil_staf.php?id=?");
    exit();
}

$id_staf = $_GET['id'];

// Gunakan prepared statement untuk menghindari SQL Injection
$query = "SELECT * FROM staf WHERE id_staf = ?";
$stmt = $koneksi->prepare($query);

if ($stmt === false) {
    echo "Gagal menyiapkan query.";
    exit();
}

// Bind parameter dan eksekusi
$stmt->bind_param("i", $id_staf);
$stmt->execute();

// Ambil hasil query
$result = $stmt->get_result();

// Jika data ditemukan, simpan ke dalam array $staf
if ($result && $result->num_rows > 0) {
    $staf = $result->fetch_assoc();
} else {
    echo "Data staf tidak ditemukan.";
    exit();
}
?>
<link rel="stylesheet" href="assets/css/staf.css">
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>
    <div class="container">
        <div class="profile-card">
            <div class="profile-content">
                <div class="profile-image">
                    <img src="<?php echo htmlspecialchars($staf['foto']); ?>" alt="Foto Staf">
                    <!-- QR Code -->
                    <?php
                    $id = urlencode($staf['id_staf']);
                    $qr_data = "Nama: {$staf['nama_staf']}\nNIP: {$staf['nip']}\nPosisi: {$staf['posisi']}";
                    $qr_img = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($qr_data);
                    ?>
                    <div style="margin-top: 10px; text-align: center;">
                        <img src="<?php echo $qr_img; ?>" alt="QR Code Profil Staf" style="width: 80px; height: 80px;">
                        <p style="font-size: 0.75rem; color: #555;">Scan Profil</p>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="info-row">
                        <div class="info-group">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['nama_staf']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">NIP</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['nip']); ?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-group">
                            <div class="info-label">Pangkat/Golongan</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['pangkat']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Posisi</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['posisi']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Pendidikan Terakhir</div>
                        <div class="info-value"><?php echo htmlspecialchars($staf['pend_terakhir']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Tempat dan Tanggal Lahir</div>
                        <div class="info-value"><?php echo htmlspecialchars($staf['tempat_lahir']); ?>, <?php echo formatTanggalIndonesia($staf['tgl_lahir']); ?></div>
                    </div>

                    <div class="contact-info">
                        <h3>Informasi Kontak</h3>
                        <div class="info-group">
                            <div class="info-label">Alamat</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['alamat']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['email']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['username']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Nomor Telepon</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['no_telepon']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../assets/js/sidebar_staf.js"></script>
</body>
</html>