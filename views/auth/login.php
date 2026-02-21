<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * ==============================================================================
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../../includes/koneksi.php'; 
include '../partials/fungsi.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];

    $tables = [
        'administrator' => 'id_admin',
        'kepala_seksi' => 'id_kepala',
        'staf' => 'id_staf',
        'jamaah' => 'id_jamaah'
    ];

    $user_found = false;

    foreach ($tables as $table => $id_field) {
        $stmt = $koneksi->prepare("SELECT * FROM $table WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user_found = true;
            $user = $result->fetch_assoc();

            // Cek Status Banned/Nonaktif
            if ($table !== 'administrator') {
                if ($user['status_pengguna'] === 'banned') {
                    $_SESSION['error_message'] = "Akun Anda telah dibanned. Hubungi admin.";
                    header("Location: login.php");
                    exit();
                } elseif ($user['status_pengguna'] === 'nonaktif') {
                    $_SESSION['error_message'] = "Akun Anda nonaktif karena tidak aktif lebih dari seminggu.";
                    header("Location: login.php");
                    exit();
                }
            }

            if (password_verify($password, $user['password'])) {
                // Update login info
                if ($table !== 'administrator') {
                    $koneksi->query("UPDATE $table SET login_attempts = 0, last_login_at = NOW() WHERE $id_field = '{$user[$id_field]}'");
                }

                // Set session umum
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $table;
                $_SESSION['login_success_msg'] = "Selamat Datang, " . $username . "!";

                // Catat aktivitas & Redirect sesuai Role
                if ($table === 'administrator') {
                    $_SESSION['user_logged_in'] = true;
                    header("Location: ../admin/dashboard_administrator.php");
                } elseif ($table === 'kepala_seksi') {
                    $_SESSION['id_kepala'] = $user['id_kepala'];
                    header("Location: ../kepala-seksi/dashboard_kepala_seksi.php");
                } elseif ($table === 'staf') {
                    $_SESSION['id_staf'] = $user['id_staf'];
                    $_SESSION['user_logged_in'] = true;
                    header("Location: ../staf/dashboard_staf.php");
                } elseif ($table === 'jamaah') {
                    $_SESSION['id_jamaah'] = $user['id_jamaah'];
                    $_SESSION['nomor_porsi'] = $user['nomor_porsi'];
                    header("Location: ../jamaah/dashboard_jamaah.php");
                }
                exit();
            } else {
                // Gagal Password
                if ($table !== 'administrator') {
                    $attempts = $user['login_attempts'] + 1;
                    if ($attempts >= 4) {
                        $koneksi->query("UPDATE $table SET login_attempts = $attempts, status_pengguna = 'banned' WHERE $id_field = '{$user[$id_field]}'");
                        $_SESSION['error_message'] = "Akun Anda dibanned karena gagal login lebih dari 3 kali.";
                    } else {
                        $koneksi->query("UPDATE $table SET login_attempts = $attempts WHERE $id_field = '{$user[$id_field]}'");
                        $_SESSION['error_message'] = "Password salah! Percobaan ke-$attempts";
                    }
                } else {
                    $_SESSION['error_message'] = "Password Administrator salah!";
                }
                header("Location: login.php");
                exit();
            }
        }
    }

    if (!$user_found) {
        $_SESSION['error_message'] = "Username tidak ditemukan!";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <link rel="icon" href="../../assets/img/logo_kemenag.png" type="image/png">
    <link rel="stylesheet" href="assets/css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in-container">
            <form method="POST" action="login.php">
                <h1>Masuk</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>Atau gunakan akun Haji Anda</span>
                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder=" " required />
                    <label for="username">Username</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder=" " required />
                    <label for="password">Password</label>
                    <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                </div>
                <a href="lupa_password.php">Lupa Password?</a>
                <button type="submit">Masuk</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-right">
                    <img src="../../assets/img/logo_kemenag.png" height="100" width="100">
                    <h1>Assalamualaikum!</h1>
                    <p>Silakan masuk untuk mengakses sistem informasi pendaftaran haji.</p>
                    <button class="ghost" onclick="window.location.href='../../landing-page/index.php';">Kembali</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/login.js"></script>

    <?php if (isset($_SESSION['error_message'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= $_SESSION['error_message']; ?>',
            confirmButtonColor: '#2e7d32'
        });
    </script>
    <?php unset($_SESSION['error_message']); endif; ?>

    <?php if (isset($_SESSION['registrasi_success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Alhamdulillah',
            text: '<?= $_SESSION['registrasi_success']; ?>',
            confirmButtonColor: '#2e7d32'
        });
    </script>
    <?php unset($_SESSION['registrasi_success']); endif; ?>
</body>
</html>