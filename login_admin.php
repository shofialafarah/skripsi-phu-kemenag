<?php
// Mulai session
session_start();

// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Pendaftaran akun
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'register') {
//     $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
//     $nomor_porsi = filter_input(INPUT_POST, 'nomor_porsi', FILTER_SANITIZE_STRING);
//     $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
//     $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
//     $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
//     $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

//     // Simpan data ke database
//     $sql = "INSERT INTO jamaah (nama, nomor_porsi, alamat, email, username, password) VALUES (?, ?, ?, ?, ?, ?)";
//     $stmt = $koneksi->prepare($sql);
//     $stmt->bind_param("ssssss", $nama, $nomor_porsi, $alamat, $email, $username, $password);

//     if ($stmt->execute()) {
//         echo "<script>alert('Pendaftaran berhasil. Silakan login.');</script>";
//     } else {
//         echo "<script>alert('Pendaftaran gagal!');</script>";
//     }
//     $stmt->close();
// }

// Login akun
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Cek di tabel jamaah untuk login
    $sql_jamaah = "SELECT * FROM jamaah WHERE username = ?";
    $stmt_jamaah = $koneksi->prepare($sql_jamaah);
    $stmt_jamaah->bind_param("s", $username);
    $stmt_jamaah->execute();
    $result_jamaah = $stmt_jamaah->get_result();

    if ($result_jamaah->num_rows > 0) {
        $jamaah = $result_jamaah->fetch_assoc();
        if (password_verify($password, $jamaah['password'])) {
            // Simpan id_jamaah ke session setelah login berhasil
            $_SESSION['id_jamaah'] = $jamaah['id_jamaah'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'jamaah';  // Role jamaah
            header("Location: jamaah.php"); // Redirect ke dashboard
            exit();
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
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
    <title>Login Jamaah</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="login_jamaah.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container" id="container">
  <!-- Daftar Form -->
  <!-- <div class="form-container sign-up-container">
    <form method="POST" action="login_jamaah.php">
    <input type="hidden" name="action" value="register">
      <h1>Buat Akun</h1>
      <div class="social-container">
        <a href="https://www.instagram.com/shofialafarah" class="social" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="https://x.com/nellamyla17" class="social" target="_blank"><i class="fab fa-twitter"></i></a>
        <a href="https://www.linkedin.com/in/shofia-nabila-elfa-rahma" class="social" target="_blank"><i class="fab fa-linkedin-in"></i></a>
      </div>
      <span>Atau gunakan email kamu untuk pendaftaran</span>
      <input type="text" name="nama" placeholder="Masukkan Nama" required>
      <input type="text" name="nomor_porsi" placeholder="Masukkan Nomor Porsi" required>
      <input type="text" name="alamat" placeholder="Masukkan Alamat" required>
      <input type="email" name="email" placeholder="Masukkan Email" required>
      <input type="text" name="username" placeholder="Masukkan Username" required>
      <input type="password" name="password" placeholder="Masukkan Password" required>
      <button type="submit">Daftar</button>
    </form>
  </div> -->
  
  <!-- Masuk Form -->
  <div class="form-container sign-in-container">
    <form method="POST" action="login_jamaah.php">
    <input type="hidden" name="action" value="login">
      <h1>Masuk</h1>
      <div class="social-container">
        <a href="https://www.instagram.com/shofialafarah" class="social" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="https://x.com/nellamyla17" class="social" target="_blank"><i class="fab fa-twitter"></i></a>
        <a href="https://www.linkedin.com/in/shofia-nabila-elfa-rahma" class="social" target="_blank"><i class="fab fa-linkedin-in"></i></a>
      </div>
      <span>Atau gunakan akun Haji Anda</span>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <a href="https://wa.me/6285101336711?text=Halo%20Admin%2C%20saya%20lupa%20password%20saya.%20Saya%20ingin%20mereset%20atas%20Nama%20%3D%20.....%20Password%20baru%20%3D%20.....">Lupa Password?</a>
      <button type="submit">Masuk</button>
    </form>
  </div>
  
  <!-- Overlay Content -->
  <div class="overlay-container">
    <div class="overlay">
      <div class="overlay-panel overlay-left">
      <img src="logo_kemenag.png" height="100" width="100" alt="" srcset="">
        <h1>Selamat Datang!</h1>
        <p>Silahkan Masuk dengan Akun Anda</p>
        <button class="ghost" id="signIn">Masuk</button>
      </div>
      <div class="overlay-panel overlay-right">
      <img src="logo_kemenag.png" height="100" width="100" alt="" srcset="">
        <h1>Assalamualaikum, Jamaah!</h1>
        <p>Daftarkan diri Anda jika belum punya akun Haji.</p>
        
        <button class="ghost" id="signUp">Daftar</button>
      </div>
    </div>
  </div>
</div>
<script src="login_jamaah.js"></script>
</body>
</html>
