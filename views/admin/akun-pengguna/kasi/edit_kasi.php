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
$id_kepala = $_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM kepala_seksi WHERE id_kepala = ?");
$stmt->bind_param("i", $id_kepala);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kepala = $_POST['nama_kepala'];
    $nip = $_POST['nip'];
    $pangkat = $_POST['pangkat'];
    $jabatan = $_POST['jabatan'];
    $pend_terakhir = $_POST['pend_terakhir'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $no_telepon = $_POST['no_telepon'];
    $foto = $data['foto'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Proses upload foto jika ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_path = 'assets/img/' . basename($_FILES['foto']['name']);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                $foto = $target_path;
            } else {
                echo "<script>alert('Gagal mengunggah foto!');</script>";
            }
        }

        // Jika password diisi, hash password baru
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hashed_password = $data['password']; // Tetap gunakan password lama
        }

        // Update data
        $stmt = $koneksi->prepare("UPDATE kepala_seksi SET 
                  nama_kepala = ?, nip = ?, pangkat = ?, jabatan = ?, 
                  pend_terakhir = ?, tempat_lahir = ?, tgl_lahir = ?, alamat = ?, email = ?, 
                  username = ?, password = ?, no_telepon = ?, foto = ? 
                  WHERE id_kepala = ?");
        $stmt->bind_param("sssssssssssssi", $nama_kepala, $nip, $pangkat, $jabatan, $pend_terakhir, $tempat_lahir, $tgl_lahir, $alamat, $email, $username, $hashed_password, $no_telepon, $foto, $id_kepala);

        if ($stmt->execute()) {
            header('Location: manajemen_kasi.php?updated=1');
            exit();
        } else {
            header('Location: manajemen_kasi.php?updated=0');
            exit();
        }
    }
}
?>
<link rel="stylesheet" href="assets/css/kasi.css">
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>

        <main class="edit-kasi-wrapper">
            <div class="edit-kasi">
                <div class="edit-kasi-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Edit Akun Kepala Seksi
                </div>
                <div class="edit-kasi-body">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="card-kasi">
                            <div class="header">
                                <div class="isi-header">
                                    <h2 class="judul"><i class="fas fa-user"></i> Informasi Profil</h2>
                                    <p class="sub-judul">Lihat dan ubah informasi profil</p>
                                </div>
                                <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                                    <button type="button" class="btn-kembali-kasi" onclick="window.location.href='manajemen_kasi.php'">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                <!-- KIRI: FOTO + UPLOAD -->
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div>
                                        <img id="previewFoto" src="<?= htmlspecialchars($data['foto']) ?: 'assets/img/profil.jpg'; ?>"
                                            alt="Foto Kasi"
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
                                    <input type="text" name="nama_kepala" value="<?= htmlspecialchars($data['nama_kepala']) ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>NIP:</label>
                                    <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']) ?>" required>
                                </div>
                            </div>

                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>Pangkat:</label>
                                    <select name="pangkat" required>
                                        <option value="" disabled <?= empty($data['pangkat']) ? 'selected' : '' ?>>Pilih Pangkat</option>
                                        <optgroup label="Golongan I">
                                            <option value="Juru Muda/I a" <?= $data['pangkat'] === 'Juru Muda/I a' ? 'selected' : '' ?>>Juru Muda/I a</option>
                                            <option value="Juru Muda Tingkat I/I b" <?= $data['pangkat'] === 'Juru Muda Tingkat I/I b' ? 'selected' : '' ?>>Juru Muda Tingkat I/I b</option>
                                            <option value="Juru/I c" <?= $data['pangkat'] === 'Juru/I c' ? 'selected' : '' ?>>Juru/I c</option>
                                            <option value="Juru Tingkat I/I d" <?= $data['pangkat'] === 'Juru Tingkat I/I d' ? 'selected' : '' ?>>Juru Tingkat I/I d</option>
                                        </optgroup>
                                        <optgroup label="Golongan II">
                                            <option value="Pengatur Muda/II a" <?= $data['pangkat'] === 'Pengatur Muda/II a' ? 'selected' : '' ?>>Pengatur Muda/II a</option>
                                            <option value="Pengatur Muda Tingkat I/II b" <?= $data['pangkat'] === 'Pengatur Muda Tingkat I/II b' ? 'selected' : '' ?>>Pengatur Muda Tingkat I/II b</option>
                                            <option value="Pengatur/II c" <?= $data['pangkat'] === 'Pengatur/II c' ? 'selected' : '' ?>>Pengatur/II c</option>
                                            <option value="Pengatur Tingkat I/II d" <?= $data['pangkat'] === 'Pengatur Tingkat I/II d' ? 'selected' : '' ?>>Pengatur Tingkat I/II d</option>
                                        </optgroup>
                                        <optgroup label="Golongan III">
                                            <option value="Penata Muda/III a" <?= $data['pangkat'] === 'Penata Muda/III a' ? 'selected' : '' ?>>Penata Muda/III a</option>
                                            <option value="Penata Muda Tingkat I/III b" <?= $data['pangkat'] === 'Penata Muda Tingkat I/III b' ? 'selected' : '' ?>>Penata Muda Tingkat I/III b</option>
                                            <option value="Penata/III c" <?= $data['pangkat'] === 'Penata/III c' ? 'selected' : '' ?>>Penata/III c</option>
                                            <option value="Penata Tingkat I/III d" <?= $data['pangkat'] === 'Penata Tingkat I/III d' ? 'selected' : '' ?>>Penata Tingkat I/III d</option>
                                        </optgroup>
                                        <optgroup label="Golongan IV">
                                            <option value="Pembina/IV a" <?= $data['pangkat'] === 'Pembina/IV a' ? 'selected' : '' ?>>Pembina/IV a</option>
                                            <option value="Pembina Tingkat I/IV b" <?= $data['pangkat'] === 'Pembina Tingkat I/IV b' ? 'selected' : '' ?>>Pembina Tingkat I/IV b</option>
                                            <option value="Pembina Utama Muda /IV c" <?= $data['pangkat'] === 'Pembina Utama Muda/IV c' ? 'selected' : '' ?>>Pembina Utama Muda/IV c</option>
                                            <option value="Pembina Utama Madya /IV d" <?= $data['pangkat'] === 'Pembina Utama Madya/IV d' ? 'selected' : '' ?>>Pembina Utama Madya/IV d</option>
                                            <option value="Pembina Utama /IV e" <?= $data['pangkat'] === 'Pembina Utama /IV e' ? 'selected' : '' ?>>Pembina Utama /IV e</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div style="flex: 1;">
                                    <label>Jabatan:</label>
                                    <input type="text" name="jabatan" value="<?= htmlspecialchars($data['jabatan']) ?>" readonly>
                                </div>
                            </div>
                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>Pendidikan Terakhir:</label>
                                    <select name="pend_terakhir" required>
                                        <option value="" disabled <?= empty($data['pend_terakhir']) ? 'selected' : '' ?>>--- Pilih Pendidikan Terakhir ---</option>
                                        <option value="SMA/SMK" <?= $data['pend_terakhir'] === 'SMA/SMK' ? 'selected' : '' ?>>SMA/SMK</option>
                                        <option value="D3" <?= $data['pend_terakhir'] === 'D3' ? 'selected' : '' ?>>D3</option>
                                        <option value="S1" <?= $data['pend_terakhir'] === 'S1' ? 'selected' : '' ?>>S1</option>
                                        <option value="S2" <?= $data['pend_terakhir'] === 'S2' ? 'selected' : '' ?>>S2</option>
                                        <option value="S3" <?= $data['pend_terakhir'] === 'S3' ? 'selected' : '' ?>>S3</option>
                                    </select>
                                </div>
                                <div style="flex: 1;">
                                    <label>No. Telepon:</label>
                                    <input type="text" name="no_telepon" value="<?= htmlspecialchars($data['no_telepon']) ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                                </div>
                            </div>
                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>Tempat Lahir:</label>
                                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($data['tempat_lahir']) ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Tanggal Lahir:</label>
                                    <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($data['tgl_lahir']) ?>" required>
                                </div>
                            </div>

                            <div style="gap: 20px; align-items: center; margin-bottom: 10px;">
                                <label>Alamat:</label>
                                <textarea name="alamat" required><?= htmlspecialchars($data['alamat']) ?></textarea>
                            </div>

                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>Username:</label>
                                    <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Password:</label>
                                    <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password.">
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
<script src="assets/js/kasi.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation before submit
    var form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan perubahan?',
                text: 'Yakin ingin menyimpan perubahan data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }
});
</script>
</body>

</html>