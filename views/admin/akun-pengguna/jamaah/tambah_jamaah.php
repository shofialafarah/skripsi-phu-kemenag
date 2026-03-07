<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2026. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../../includes/koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../../../vendor/autoload.php';

function generateRandomPassword($length = 12)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }
    return $password;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $validasi_bank = $_POST['validasi_bank'];
    $nomor_telepon = $_POST['no_telepon']; // Sesuaikan dengan atribut name di input
    $email = $_POST['email'];
    $username = $_POST['username'];
    $foto_db = NULL; // default di database NULL

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Cek duplikasi username
        $cek = $koneksi->prepare("SELECT id_jamaah FROM jamaah WHERE username=?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
            exit;
        }

        // --- PENYESUAIAN PENYIMPANAN FOTO ---
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $file_info = pathinfo($_FILES['foto']['name']);
            $file_extension = strtolower($file_info['extension']);
            $allowed_extensions = ['jpg', 'jpeg', 'png'];

            if (!in_array($file_extension, $allowed_extensions)) {
                echo "<script>alert('Format file harus JPG atau PNG!');</script>";
            } else {
                // Path tujuan sesuai folder upload terpusat
                $dir_tujuan = $_SERVER['DOCUMENT_ROOT'] . '/phu-kemenag-banjar-copy/uploads/akun-pengguna/jamaah/';
                
                if (!is_dir($dir_tujuan)) {
                    mkdir($dir_tujuan, 0777, true);
                }

                $new_filename = 'Jamaah_' . $username . '_' . time() . '.' . $file_extension;
                $target_path = $dir_tujuan . $new_filename;

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                    $foto_db = $new_filename; // Simpan NAMA FILE saja ke DB
                } else {
                    echo "<script>alert('Gagal mengunggah foto!');</script>";
                }
            }
        }

        // Generate password acak
        $password_plain = generateRandomPassword();
        $hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

        $stmt = $koneksi->prepare("INSERT INTO jamaah (nama, validasi_bank, nomor_telepon, email, username, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nama, $validasi_bank, $nomor_telepon, $email, $username, $hashed_password, $foto_db);

        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'phukemenagbanjar@gmail.com';
                $mail->Password = 'fulp gmia ztvl mjxz';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('phukemenagbanjar@gmail.com', 'Admin PHU');
                $mail->addAddress($email, $nama);

                $mail->isHTML(true);
                $mail->Subject = "Akun Anda Telah Dibuat!";
                $mail->Body = <<<HTML
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
                    .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
                    .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 40px 30px; text-align: center; }
                    .content { padding: 40px 30px; }
                    .credentials-box { background: #f8f9fa; border-radius: 15px; padding: 25px; margin: 25px 0; border-left: 5px solid #4CAF50; }
                    .credential-item { display: flex; justify-content: space-between; margin: 10px 0; }
                    .credential-value { font-weight: bold; color: #4CAF50; }
                    .action-button { display: inline-block; background: #4CAF50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; }
                </style>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin:0;'>Assalamu'alaikum!</h1>
                        <p>Akun Pelayanan Haji Anda Siap Digunakan</p>
                    </div>
                    <div class='content'>
                        <p>Halo <strong>$nama</strong>, akun Anda telah berhasil didaftarkan oleh Admin.</p>
                        <div class='credentials-box'>
                            <div class='credential-item'><span>Username:</span> <span class='credential-value'>$username</span></div>
                            <div class='credential-item'><span>Password:</span> <span class='credential-value'>$password_plain</span></div>
                        </div>
                        <p style='text-align:center;'><a href='http://localhost/phu-kemenag-banjar/login.php' class='action-button'>Login Sekarang</a></p>
                    </div>
                </div>
HTML;
                $mail->send();
                header('Location: manajemen_jamaah.php?created=1&mail=1');
                exit();
            } catch (Exception $e) {
                header('Location: manajemen_jamaah.php?created=1&mail=0');
                exit();
            }
        } else {
            header('Location: manajemen_jamaah.php?created=0');
            exit();
        }
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
                    <i class="fas fa-table me-1"></i> Tambah Akun Jamaah
                </div>
                <div class="jamaah-body">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="card-jamaah">
                            <div class="header">
                                <div class="isi-header">
                                    <h2 class="judul"><i class="fas fa-user"></i> Informasi Profil</h2>
                                    <p class="sub-judul">Lihat dan input informasi profil</p>
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
                                        <img id="previewFoto" src="assets/img/profil.jpg"
                                            alt="Foto Jamaah"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                    </div>
                                    <div>
                                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                            <label for="foto" class="btn-upload-foto" style="margin: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 38px; background: #0d6efd; color: white; padding: 0 15px; border-radius: 5px;">
                                                Upload
                                            </label>
                                            <input type="file" id="foto" name="foto" accept="image/*" onchange="previewGambar(this)" style="display: none;">

                                            <button type="button" onclick="hapusFoto()" class="btn-hapus-foto btn-danger" style="width: 40px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 5px; border: none; background: #dc3545; color: white;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">JPG, PNG, max 10MB</small>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px; margin-top: 10px;">
                                <div style="flex: 1;">
                                    <label>Nama:</label>
                                    <input type="text" name="nama" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>No. Validasi:</label>
                                    <input type="text" name="validasi_bank" required>
                                </div>
                            </div>

                            <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                                <div style="flex: 1;">
                                    <label>No. Telepon:</label>
                                    <input type="text" name="no_telepon" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Email:</label>
                                    <input type="email" name="email" required>
                                </div>
                                <div style="flex: 1;">
                                    <label>Username:</label>
                                    <input type="text" name="username" required>
                                </div>
                            </div>

                            <button type="submit" class="btn-simpan-perubahan">
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>
                        </div>
                    </form>
                </div>
                <?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function previewGambar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewFoto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function hapusFoto() {
    document.getElementById('previewFoto').src = 'assets/img/profil.jpg';
    document.getElementById('foto').value = "";
}
</script>
</body>
</html>