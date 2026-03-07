<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

// 1. Definisikan Path Fisik (Agar tidak error Undefined di HTML)
$dir_tujuan = $_SERVER['DOCUMENT_ROOT'] . '/phu-kemenag-banjar-copy/uploads/akun-pengguna/jamaah/';

// 2. Ambil data berdasarkan ID
if (!isset($_GET['id'])) {
    header('Location: manajemen_jamaah.php');
    exit();
}

$id_jamaah = $_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM jamaah WHERE id_jamaah = ?");
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// 3. Proses edit data (saat tombol simpan diklik)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $validasi_bank = $_POST['validasi_bank'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    
    $foto_lama = $data['foto'];
    $foto_db = $foto_lama; 
    $hapus_foto_status = isset($_POST['hapus_foto_status']) ? $_POST['hapus_foto_status'] : '0';

    // Logika HAPUS FOTO
    if ($hapus_foto_status == '1') {
        if (!empty($foto_lama) && file_exists($dir_tujuan . $foto_lama)) {
            unlink($dir_tujuan . $foto_lama);
        }
        $foto_db = NULL; 
    }

    // Logika UPLOAD FOTO BARU
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        // Nama file unik dengan time() agar tidak kena cache browser
        $new_filename = 'Jamaah_' . $username . '_' . time() . '.' . $file_extension;
        $target_path = $dir_tujuan . $new_filename;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $foto_db = $new_filename;
            // Hapus fisik foto lama jika user ganti foto (bukan cuma hapus)
            if (!empty($foto_lama) && file_exists($dir_tujuan . $foto_lama)) {
                unlink($dir_tujuan . $foto_lama);
            }
        }
    }

    // Update data ke Database
    $stmt_update = $koneksi->prepare("UPDATE jamaah SET nama=?, validasi_bank=?, nomor_telepon=?, email=?, username=?, foto=? WHERE id_jamaah=?");
    $stmt_update->bind_param("ssssssi", $nama, $validasi_bank, $nomor_telepon, $email, $username, $foto_db, $id_jamaah);

    if ($stmt_update->execute()) {
        echo "<script>window.location.href='manajemen_jamaah.php?updated=1';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}
?>

<link rel="stylesheet" href="assets/css/jamaah.css">
<?php include '../../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>

        <main class="jamaah-wrapper">
            <div class="jamaah">
                <div class="jamaah-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Edit Akun Jamaah
                </div>
                <div class="jamaah-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="hapus_foto_status" id="hapus_foto_status" value="0">

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
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div>
                                        <?php
                                        $path_preview = '../../../../uploads/akun-pengguna/jamaah/';
                                        // Cek apakah file ada di folder fisik
                                        if (!empty($data['foto']) && file_exists($dir_tujuan . $data['foto'])) {
                                            $img_src = $path_preview . $data['foto'];
                                        } else {
                                            $img_src = 'assets/img/profil.jpg';
                                        }
                                        ?>
                                        <img id="previewFoto" src="<?= htmlspecialchars($img_src) ?>"
                                            alt="Foto Jamaah"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                    </div>
                                    <div>
                                        <label for="foto" style="font-weight: bold;">Foto Profil</label>
                                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                            <label for="foto" class="btn-upload-foto" style="cursor: pointer; background: #0d6efd; color: white; padding: 5px 15px; border-radius: 5px;">Upload</label>
                                            <input type="file" id="foto" name="foto" accept="image/*" onchange="previewGambar(this)" style="display: none;">
                                            <button type="button" onclick="hapusFotoPreview()" class="btn-hapus-foto btn-danger" style="padding: 5px 10px;">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Fungsi Preview saat memilih file
function previewGambar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewFoto').src = e.target.result;
            document.getElementById('hapus_foto_status').value = "0"; // Batal hapus jika pilih file baru
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Fungsi Hapus Foto dengan konfirmasi
function hapusFotoPreview() {
    Swal.fire({
        title: 'Hapus Foto Profil?',
        text: "Foto akan dikembalikan ke setelan default!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Ubah preview ke default
            document.getElementById('previewFoto').src = 'assets/img/profil.jpg';
            // Kosongkan input file agar tidak mengupload apa pun
            document.getElementById('foto').value = "";
            // Tandai status hapus untuk diproses PHP
            document.getElementById('hapus_foto_status').value = "1";
        }
    })
}
</script>

</body>
</html>