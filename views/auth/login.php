<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include '../../includes/koneksi.php'; // Panggil koneksinya dulu
include 'fungsi.php';  // Baru panggil fungsinya

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

            if ($table !== 'administrator') {
                if ($user['status_pengguna'] === 'banned') {
                    echo "<script>alert('Akun Anda telah dibanned. Hubungi admin.');  window.location.href='login.php';</script>";
                    exit();
                } elseif ($user['status_pengguna'] === 'nonaktif') {
                    echo "<script>alert('Akun Anda nonaktif karena tidak aktif lebih dari seminggu.');  window.location.href='login.php';</script>";
                    exit();
                }
            }
            if (password_verify($password, $user['password'])) {
                // Reset login_attempts dan update last_login_at (kecuali administrator)
                if ($table !== 'administrator') {
                    $koneksi->query("UPDATE $table SET login_attempts = 0, last_login_at = NOW() WHERE $id_field = '{$user[$id_field]}'");
                }

                // Set session
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $table;

                // Catat aktivitas login
                if ($table !== 'administrator') {
                    $id_pengguna = $user[$id_field];
                    updateAktivitasPengguna($id_pengguna, $table, 'Login', 'Pengguna berhasil login ke sistem');
                }

                if ($table === 'administrator') {
                    $_SESSION['user_logged_in'] = true;
                    header("Location: dashboard_administrator.php");
                } elseif ($table === 'kepala_seksi') {
                    $_SESSION['id_kepala'] = $user['id_kepala'];
                    header("Location: dashboard_kepala_seksi.php");
                } elseif ($table === 'staf') {
                    $_SESSION['id_staf'] = $user['id_staf'];
                    $_SESSION['user_logged_in'] = true;
                    header("Location: dashboard_staf.php");
                } elseif ($table === 'jamaah') {
                    $_SESSION['id_jamaah'] = $user['id_jamaah'];
                    $_SESSION['nomor_porsi'] = $user['nomor_porsi'];
                    header("Location: dashboard_jamaah.php");
                }
                exit();
            } else {
                // Salah password
                if ($table !== 'administrator') {
                    $attempts = $user['login_attempts'] + 1;
                    if ($attempts >= 4) {
                        $koneksi->query("UPDATE $table SET login_attempts = $attempts, status_pengguna = 'banned' WHERE $id_field = '{$user[$id_field]}'");
                        echo "<script>alert('Akun Anda dibanned karena gagal login lebih dari 3 kali.'); window.location.href='login.php';</script>";
                        exit;
                    } else {
                        $koneksi->query("UPDATE $table SET login_attempts = $attempts WHERE $id_field = '{$user[$id_field]}'");
                        echo "<script>alert('Password salah! Percobaan ke-$attempts'); window.location.href='login.php';</script>";
                        exit;
                    }
                } else {
                    echo "<script>alert('Password salah!'); window.location.href='login.php';</script>";
                    exit;
                }

                exit();
            }
        }
    }

    if (!$user_found) {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}

$koneksi->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <link rel="icon" href="../../assets/logo_kemenag.png">
    <link rel="stylesheet" href="assets/css/login.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container" id="container">
        <!-- Masuk Form -->
        <div class="form-container sign-in-container">

            <!-- Menampilkan pesan sukses registrasi jika ada -->
            <?php
            if (isset($_SESSION['registrasi_success'])) {
                echo '<div class="success-message">' . $_SESSION['registrasi_success'] . '</div>';
                unset($_SESSION['registrasi_success']); // Hapus session setelah ditampilkan
            }
            ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="action" value="login">
                <h1>Masuk</h1>
                <div class="social-container">
                    <a href="https://www.instagram.com/shofialafarah" class="social" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://x.com/nellamyla17" class="social" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/in/shofia-nabila-elfa-rahma" class="social" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>Atau gunakan akun Haji Anda</span>
                <div class="form-group">
                    <input type="text" id="username" name="username" placeholder=" " required />
                    <label for="username">Username</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder=" " required />
                    <label for="password">Password</label>
                    <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i> <!-- Icon Mata -->
                </div>
                <a href="lupa_password.php">Lupa Password?</a>
                <button type="submit">Masuk</button>
            </form>
        </div>

        <!-- Overlay Content -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <img src="../../assets/logo_kemenag.png" height="100" width="100" alt="" srcset="">
                    <h1>Selamat Datang!</h1>
                    <p>Silakan masuk dengan akun yang sudah terdaftar.</p>
                    <button class="ghost" id="signIn">Masuk</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <img src="../../assets/logo_kemenag.png" height="100" width="100" alt="" srcset="">
                    <h1>Assalamualaikum!</h1>
                    <p>Silakan masuk dengan akun yang sudah terdaftar.</p>

                    <button class="ghost" onclick="window.location.href='../../landing-page/index.php';">Kembali</button>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/login.js"></script>
</body>

</html>