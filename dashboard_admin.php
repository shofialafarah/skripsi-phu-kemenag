<?php
// Mulai session
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_admin']) || $_SESSION['role'] != 'administrator') {
    header("Location: login.php"); // Redirect ke login jika belum login atau session tidak ada
    exit();
}

// Ambil ID staf dari session
$id_admin = $_SESSION['id_admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="dashboard_admin.css"> <!-- Tambahkan file CSS -->
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include ('sidebar_admin.php'); ?>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Dashboard Administrator</h1>
                <p>Selamat datang, <?= htmlspecialchars($username); ?></p>
            </header>
            <main>
                <?php
                // Konten dinamis berdasarkan parameter 'page'
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                    switch ($page) {
                        case 'dashboard':
                            include 'dashboard_content.php';
                            break;
                        case 'manage_jamaah':
                            include 'manajemen_jamaah.php';
                            break;
                        case 'manage_staff':
                            include 'manajemen_staf.php';
                            break;
                        case 'manage_kasi':
                            include 'manajemen_kasi.php';
                            break;
                        case 'reset_password':
                            include 'reset_password.php';
                            break;
                        case 'settings':
                            include 'pengaturan.php';
                            break;                                
                        default:
                            echo "<p>Halaman tidak ditemukan.</p>";
                    }
                } else {
                    include 'dashboard_content.php'; // Default halaman dashboard
                }
                ?>
            </main>
            <footer>
                <p>&copy; Shofia Nabila Elfa Rahma. 2110010113.</p>
            </footer>
        </div>
    </div>
</body>
</html>
