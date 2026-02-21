<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

// Ambil data berdasarkan ID
$id_jamaah = $_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM jamaah WHERE id_jamaah = ?");
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $validasi_bank = $_POST['validasi_bank'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $foto = $data['foto'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Proses upload foto jika ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $file_size = $_FILES['foto']['size'];
            $file_type = $_FILES['foto']['type'];

            // Validasi tipe file
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file_type, $allowed_types)) {
                echo "<script>alert('Format file harus JPG atau PNG!');</script>";
            } else if ($file_size > 10 * 1024 * 1024) { // 10MB
                echo "<script>alert('Ukuran file maksimal 10MB!');</script>";
            } else {
                // Buat direktori jika belum ada
                $upload_dir = 'assets/img/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Dapatkan ekstensi file
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                // Buat nama file baru: Staf_username.extension
                $new_filename = 'Jamaah_' . $username . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;

                // Hapus foto lama jika ada dan berbeda dengan foto baru
                if (!empty($data['foto']) && file_exists($data['foto']) && $data['foto'] != $target_path) {
                    unlink($data['foto']);
                }

                // Upload file baru
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $foto = $target_path;
                } else {
                    echo "<script>alert('Gagal mengunggah foto!');</script>";
                }
            }
        }

        // Update data
        $stmt = $koneksi->prepare("UPDATE jamaah SET 
                  nama = ?, validasi_bank = ?, nomor_telepon = ?, email = ?,  
                  username = ?, foto = ? 
                  WHERE id_jamaah = ?");
        $stmt->bind_param("ssssssi", $nama, $validasi_bank, $nomor_telepon, $email, $username, $foto, $id_jamaah);

        if ($stmt->execute()) {
            header('Location: manajemen_jamaah.php?updated=1');
            exit();
        } else {
            header('Location: manajemen_jamaah.php?updated=0');
            exit();
        }
    }
}
?>
<link rel="stylesheet" href="assets/css/jamaah.css">
<?php include '../../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>
        
        <main class="jamaah-wrapper">
            <div class="jamaah">
                <div class="jamaah-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Edit Akun Jamaah
                </div>
                <div class="jamaah-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="card-jamaah">
                            <div class="header">
                                <div class="isi-header">
                                    <h2 class="judul"><i class="fas fa-user"></i> Informasi Profil</h2>
                                    <p class="sub-judul">Lihat dan ubah informasi profil</p>
                                </div>
                                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                                    <button type="button" class="btn-kembali" onclick="window.location.href='manajemen_jamaah.php'">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                <!-- KIRI: FOTO + UPLOAD -->
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div>
                                        <?php
                                        // Tampilkan foto sesuai dengan path yang benar
                                        if (!empty($data['foto']) && file_exists($data['foto'])) {
                                            $foto = $data['foto'];
                                        } else {
                                            $foto = 'assets/img/profil.jpg';
                                        }
                                        ?>
                                        <img id="previewFoto" src="<?= htmlspecialchars($foto) ?>"
                                            alt="Foto Jamaah"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                    </div>
                                    <div>
                                        <label for="foto" style="font-weight: bold;">Foto Profil</label>
                                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                            <label for="foto" class="btn-upload-foto">Upload</label>
                                            <input type="file" id="foto" name="foto" accept="image/*" onchange="previewGambar(this)" style="display: none;">
                                            <button type="button" onclick="hapusFoto()" class="btn-hapus-foto btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <p style="font-size: 0.75rem; color: #555;">JPG, PNG, max 10MB</p>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 20px; align-items: center; margin-top: 10px; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>Nama:</label>
                                    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>No. Validasi:</label>
                                    <input type="text" name="validasi_bank" value="<?= htmlspecialchars($data['validasi_bank']) ?>" required>
                                </div>
                            </div>
                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>No. Telepon:</label>
                                    <input type="text" name="nomor_telepon" value="<?= htmlspecialchars($data['nomor_telepon']) ?>" required>

                                </div>
                                <div style="flex: 1;">
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Username:</label>
                                    <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-simpan-perubahan">
                                <i class="fas fa-edit"></i> Edit Data
                            </button>
                        </div>
                    </form>
                </div>
                <?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
            </div>
        </main>
    </div>
</div>

<script src="../../assets/js/sidebar.js"></script>
<script src="assets/js/jamaah.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>