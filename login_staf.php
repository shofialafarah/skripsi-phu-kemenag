<?php 
include 'koneksi.php';

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek di tabel staf
    $sql_staf = "SELECT * FROM staf WHERE username = ?";
    $stmt_staf = $koneksi->prepare($sql_staf);
    $stmt_staf->bind_param("s", $username);
    $stmt_staf->execute();
    $result_staf = $stmt_staf->get_result();

    if ($result_staf->num_rows > 0) {
        $staf = $result_staf->fetch_assoc();
        if (password_verify($password, $staf['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'staf';
            header("Location: dashboard_staf.php");
            exit();
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login staf</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="login_staf.css">
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <h1>Login Staf PHU</h1>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username anda" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password anda" required>
            </div>
            <div class="form-group">
                <button type="submit">Masuk</button>
            </div>
        </form>
    </div>
</body>
</html>
