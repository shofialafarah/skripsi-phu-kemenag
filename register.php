<?php
session_start();
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
    $validasi_bank = filter_input(INPUT_POST, 'validasi_bank', FILTER_SANITIZE_STRING);
    $nomor_telepon = filter_input(INPUT_POST, 'nomor_telepon', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if (!$email || !$nama || !$validasi_bank || !$nomor_telepon || !$username) {
        echo "<script>alert('Semua field harus diisi dengan benar!'); window.history.back();</script>";
        exit;
    }

    $stmt = $koneksi->prepare("SELECT id_jamaah FROM jamaah WHERE username = ? OR nomor_telepon = ?");
    $stmt->bind_param("ss", $username, $nomor_telepon);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo "<script>alert('Username atau Nomor Telepon sudah terdaftar!'); window.history.back();</script>";
        exit;
    }

    $stmt = $koneksi->prepare("INSERT INTO jamaah (nama, validasi_bank, nomor_telepon, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $validasi_bank, $nomor_telepon, $email, $username, $password);
    
    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan, coba lagi.'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Register</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="register.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form method="POST" action="register.php">
            <h1>Buat Akun</h1>
            <div class="social-container">
                <a href="https://www.instagram.com/shofialafarah" class="social" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://x.com/nellamyla17" class="social" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://www.linkedin.com/in/shofia-nabila-elfa-rahma" class="social" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <span>Atau isi form pendaftaran akun berikut:</span>
            <div class="form-group">
                <input type="text" id="nama" name="nama" placeholder=" " required />
                <label for="nama">Nama Lengkap</label>
            </div>
            <div class="form-group">
                <input type="number" id="validasi_bank" name="validasi_bank" placeholder=" " required />
                <label for="validasi_bank">Nomor Validasi Bank</label>
            </div>
            <div class="form-group">
                <input type="text" id="nomor_telepon" name="nomor_telepon" placeholder=" " required />
                <label for="nomor_telepon">Nomor Telp</label>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder=" " required />
                <label for="email">Email</label>
            </div>
            <div class="form-group">
                <input type="text" id="username" name="username" placeholder=" " required />
                <label for="username">Username</label>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder=" " required />
                <label for="password">Password</label>
                <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
            <button type="submit" name="register">Daftar</button>
        </form>
    </div>
    
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <img src="logo_kemenag.png" height="100" width="100" alt="">
                <h1>Selamat Datang!</h1>
                <p>Silakan isi data pribadi Anda untuk memulai proses pendaftaran.</p>
                <button class="ghost" onclick="window.location.href='index.html';">Kembali</button>
            </div>
        </div>
    </div>
</div>
<script src="register.js"></script>
</body>
</html>