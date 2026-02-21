<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include '../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
    $validasi_bank = filter_input(INPUT_POST, 'validasi_bank', FILTER_SANITIZE_STRING);
    $nomor_telepon = filter_input(INPUT_POST, 'nomor_telepon', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if (!$email || !$nama || !$validasi_bank || !$nomor_telepon || !$username) {
        $_SESSION['error_message'] = "Semua field harus diisi dengan benar!";
    } else {
        // Cek Duplikasi Username atau No Telepon
        $stmt = $koneksi->prepare("SELECT id_jamaah FROM jamaah WHERE username = ? OR nomor_telepon = ?");
        $stmt->bind_param("ss", $username, $nomor_telepon);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['error_message'] = "Username atau Nomor Telepon sudah terdaftar!";
        } else {
            // Proses Insert
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $koneksi->prepare("INSERT INTO jamaah (nama, validasi_bank, nomor_telepon, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nama, $validasi_bank, $nomor_telepon, $email, $username, $hashed_password);

            if ($stmt->execute()) {
                // Simpan pesan sukses untuk ditampilkan di login.php
                $_SESSION['registrasi_success'] = "Alhamdulillah, registrasi berhasil! Silakan login dengan akun Anda.";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['error_message'] = "Terjadi kesalahan sistem, silakan coba lagi.";
            }
        }
    }
    // Jika ada error, tetap di halaman register
    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Register</title>
    <link rel="icon" href="../../assets/img/logo_kemenag.png" type="image/png">
    <link rel="stylesheet" href="assets/css/register.css">
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
                    <img src="../../assets/img/logo_kemenag.png" height="100" width="100" alt="">
                    <h1>Selamat Datang!</h1>
                    <p>Silakan isi data pribadi Anda untuk memulai proses pendaftaran.</p>
                    <button class="ghost" onclick="window.location.href='../../landing-page/index.php';">Kembali</button>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/register.js"></script>
    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Mohon Maaf',
                text: '<?= $_SESSION['error_message']; ?>',
                confirmButtonColor: '#2e7d32'
            });
        </script>
    <?php unset($_SESSION['error_message']);
    endif; ?>
</body>

</html>