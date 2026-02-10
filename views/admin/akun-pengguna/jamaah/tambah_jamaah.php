<?php
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
    $nomor_telepon = $_POST['nomor_telepon'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $foto = ''; // default kosong

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Cek duplikasi username atau email sebelum upload
        $cek = $koneksi->prepare("SELECT id_jamaah FROM jamaah WHERE username=?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
            exit;
        }

        // Upload foto jika ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $file_info = pathinfo($_FILES['foto']['name']);
            $file_extension = strtolower($file_info['extension']);

            // Validasi ekstensi file
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($file_extension, $allowed_extensions)) {
                echo "<script>alert('Format file harus JPG atau PNG!');</script>";
            } else {
                // Nama file baru berdasarkan username
                $new_filename = 'Jamaah_' . $username . '.' . $file_extension;
                $target_path = 'assets/img/' . $new_filename;

                // Pastikan direktori img ada
                if (!is_dir('assets/img/')) {
                    mkdir('assets/img/', 0777, true);
                }

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                    $foto = $target_path;
                } else {
                    echo "<script>alert('Gagal mengunggah foto!');</script>";
                }
            }
        }

        // Generate password acak
        $password_plain = generateRandomPassword();
        $hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

        $stmt = $koneksi->prepare("INSERT INTO jamaah (nama, validasi_bank, nomor_telepon, email, username, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssss", $nama, $validasi_bank, $nomor_telepon, $email, $username, $hashed_password, $foto);

        if ($stmt->execute()) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'phukemenagbanjar@gmail.com';  // Email pengirim
                $mail->Password = 'fulp gmia ztvl mjxz';            // App Password dari Gmail
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('phukemenagbanjar@gmail.com', 'Admin PHU');
                $mail->addAddress($email, $nama);

                $mail->isHTML(true);
                // Kirim email ke jamaah dengan template HTML yang menarik
                $mail->Subject = "Akun Anda Telah Dibuat!";

                $mail->Body = <<<HTML

                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
                    .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
                    .header { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 40px 30px; text-align: center; position: relative; }
                    .header::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"20\" cy=\"20\" r=\"2\" fill=\"rgba(255,255,255,0.1)\"/><circle cx=\"80\" cy=\"40\" r=\"3\" fill=\"rgba(255,255,255,0.1)\"/><circle cx=\"40\" cy=\"80\" r=\"2\" fill=\"rgba(255,255,255,0.1)\"/></svg>'); }
                    .logo { width: 80px; height: 80px; background: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); position: relative; z-index: 1; }
                    .logo img { width: 50px; height: 50px; object-fit: contain; }
                    .welcome-text { font-size: 28px; font-weight: bold; margin: 0; position: relative; z-index: 1; }
                    .subtitle { font-size: 16px; opacity: 0.9; margin: 10px 0 0 0; position: relative; z-index: 1; }
                    .content { padding: 40px 30px; }
                    .greeting { font-size: 20px; color: #333; margin-bottom: 20px; }
                    .highlight-name { color: #4CAF50; font-weight: bold; }
                    .credentials-box { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 25px; margin: 25px 0; border-left: 5px solid #4CAF50; }
                    .credentials-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 15px; display: flex; align-items: center; }
                    .credentials-title::before { content: '🔐'; margin-right: 10px; }
                    .credential-item { display: flex; justify-content: space-between; align-items: center; margin: 12px 0; padding: 10px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                    .credential-label { font-weight: bold; color: #555; }
                    .credential-value { background: #4CAF50; color: white; padding: 5px 12px; border-radius: 6px; font-family: monospace; font-size: 14px; }
                    .warning-box { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 5px solid #ffc107; padding: 20px; border-radius: 10px; margin: 20px 0; }
                    .warning-text { color: #856404; font-weight: 500; display: flex; align-items: center; }
                    .warning-text::before { content: '⚠️'; margin-right: 10px; }
                    .action-button { display: inline-block; background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; margin: 20px 0; box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3); transition: all 0.3s ease; }
                    .footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef; }
                    .footer-text { color: #666; font-size: 14px; margin: 5px 0; }
                    .social-links { margin: 20px 0; }
                    .social-links a { display: inline-block; margin: 0 10px; padding: 10px; background: #4CAF50; color: white; border-radius: 50%; text-decoration: none; width: 40px; height: 40px; line-height: 20px; }
                </style>

                <div class='container'>
                    <div class='header'>
                        <div class='logo'>
                            <img src='https://cdn.kemenag.go.id/storage/archives/logo-kemenag-png-1png.png' alt='Logo Kemenag'>
                        </div>
                        <h1 class='welcome-text'>Assalamu'alaikum Jamaah!</h1>
                        <p class='subtitle'>Akun Haji Anda Telah Berhasil Dibuat</p>
                    </div>
                    
                    <div class='content'>
                        <div class='greeting'>
                            Halo, <span class='highlight-name'>$nama</span>!
                        </div>
                        
                        <p style='color: #666; line-height: 1.6; margin-bottom: 25px;'>
                            Selamat! Akun Anda untuk sistem pelayanan Penyelenggaraan Haji dan Umroh (PHU) telah berhasil dibuat. 
                            Anda sekarang dapat mengakses sistem dengan kredensial berikut:
                        </p>
                        
                        <div class='credentials-box'>
                            <div class='credentials-title'>Informasi Login Anda</div>
                            <div class='credential-item'>
                                <span class='credential-label'>Username:</span>
                                <span class='credential-value'>$username</span>
                            </div>
                            <div class='credential-item'>
                                <span class='credential-label'>Password:</span>
                                <span class='credential-value'>$password_plain</span>
                            </div>
                        </div>
                        
                        <div class='warning-box'>
                            <div class='warning-text'>
                                Demi keamanan, silakan login dan segera ubah password Anda setelah login pertama kali.
                            </div>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='http://localhost/phu-kemenag-banjar/login.php' class='action-button'>Login Sekarang</a>
                        </div>
                        
                        <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 30px;'>
                            <h3 style='color: #333; margin-top: 0;'>Langkah Selanjutnya:</h3>
                            <ul style='color: #666; line-height: 1.6;'>
                                <li>Login menggunakan kredensial di atas</li>
                                <li>Ubah password default Anda</li>
                                <li>Lengkapi profil Anda jika diperlukan</li>
                                <li>Mulai menggunakan sistem pelayanan</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class='footer'>
                        <div class='footer-text'>
                            <strong>Kementerian Agama Republik Indonesia</strong><br>
                            Direktorat Jenderal Penyelenggaraan Haji dan Umroh
                        </div>
                        <div class='footer-text' style='margin-top: 15px;'>
                            Email ini dikirim secara otomatis, mohon tidak membalas.<br>
                            Jika ada pertanyaan, silakan hubungi administrator sistem.
                        </div>
                        <div class='footer-text' style='margin-top: 20px; font-size: 12px; color: #999;'>
                            © UNISKA_2025 Shofia Nabila Elfa Rahma. Semua hak dilindungi.
                        </div>
                    </div>
                </div>
            </body>
            </html>
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
                                <!-- KIRI: FOTO + UPLOAD -->
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div>
                                        <img id="previewFoto" src="assets/img/profil.jpg"
                                            alt="Foto Jamaah"
                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                    </div>
                                    <div>
                                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                            <label for="foto" class="btn-upload-foto" style="margin: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 38px;">
                                                Upload
                                            </label>
                                            <input type="file" id="foto" name="foto" accept="image/*" onchange="previewGambar(this)" style="display: none;">

                                            <button type="button" onclick="hapusFoto()" class="btn-hapus-foto btn-danger" style="width: 40px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 5px; border: none;">
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

<script src="../../assets/js/sidebar.js"></script>
<script src="assets/js/jamaah.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>