<?php
include('koneksi.php');

// Update pengaturan sistem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengupdate pengaturan lainnya (termasuk warna teks)
    if (isset($_POST['update_settings'])) {
        foreach ($_POST as $key => $value) {
            if ($key !== 'update_settings') {
                // Cek apakah key sudah ada di database
                $check_stmt = $koneksi->prepare("SELECT COUNT(*) FROM pengaturan WHERE key_name = ?");
                $check_stmt->bind_param('s', $key);
                $check_stmt->execute();
                $check_stmt->bind_result($count);
                $check_stmt->fetch();
                $check_stmt->close();

                if ($count > 0) {
                    // Update jika sudah ada
                    $stmt = $koneksi->prepare("UPDATE pengaturan SET value=? WHERE key_name=?");
                    $stmt->bind_param('ss', $value, $key);
                } else {
                    // Insert jika belum ada
                    $stmt = $koneksi->prepare("INSERT INTO pengaturan (key_name, value) VALUES (?, ?)");
                    $stmt->bind_param('ss', $key, $value);
                }
                $stmt->execute();
                $stmt->close();
            }
        }

        // Mengunggah logo aplikasi jika ada file yang diupload
        if (!empty($_FILES['app_logo']['name'])) {
            $upload_dir = 'uploads/';

            // Buat direktori settings jika belum ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = basename($_FILES['app_logo']['name']);
            $target_file = $upload_dir . $file_name;

            // Validasi tipe file (hanya gambar)
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (in_array($file_extension, $allowed_types)) {
                // Pindahkan file yang diunggah
                if (move_uploaded_file($_FILES['app_logo']['tmp_name'], $target_file)) {
                    // Cek apakah key app_logo sudah ada
                    $check_stmt = $koneksi->prepare("SELECT COUNT(*) FROM pengaturan WHERE key_name = 'app_logo'");
                    $check_stmt->execute();
                    $check_stmt->bind_result($count);
                    $check_stmt->fetch();
                    $check_stmt->close();

                    if ($count > 0) {
                        // Update path logo di database
                        $stmt = $koneksi->prepare("UPDATE pengaturan SET value=? WHERE key_name='app_logo'");
                    } else {
                        // Insert jika belum ada
                        $stmt = $koneksi->prepare("INSERT INTO pengaturan (key_name, value) VALUES ('app_logo', ?)");
                    }
                    $stmt->bind_param('s', $file_name);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    echo "<script>alert('Gagal mengunggah logo baru.');</script>";
                }
            } else {
                echo "<script>alert('Tipe file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF.');</script>";
            }
        }

        echo "<script>alert('Pengaturan berhasil diperbarui!'); window.location.href='pengaturan.php';</script>";
    }
}

// Ambil semua pengaturan
$result = $koneksi->query("SELECT * FROM pengaturan");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Default nilai jika belum diatur
$app_name = $settings['app_name'] ?? 'PHU KEMENAG';
$app_logo = $settings['app_logo'] ?? 'logo_kemenag.png';
$theme_text_color = $settings['theme_text_color'] ?? '#ffffff';

if (isset($_POST['reset_default'])) {
    // Set default nilai
    $default_logo = 'uploads/sistem.png'; // nama file saja
    $default_color = '#7bd247';

    // Update ke database
    $koneksi->query("UPDATE pengaturan SET value = '$default_logo' WHERE key_name = 'app_logo'");
    $koneksi->query("UPDATE pengaturan SET value = '$default_color' WHERE key_name = 'theme_text_color'");

    // Salin file default jika perlu, atau pastikan file sudah ada di /uploads/
    // redirect ulang agar refresh tidak mengulangi POST
    header("Location: pengaturan.php?reset=success");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="jamaah.css">

    <!-- Terapkan warna teks dari pengaturan -->
    <style>
        :root {
            --sidebar-text-color: <?= htmlspecialchars($theme_text_color); ?>;
        }

        /* Override warna teks sidebar */
        .sidebar h1,
        .sidebar .menu a p,
        .sidebar .menu a .material-symbols-outlined {
            color: var(--sidebar-text-color) !important;
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_admin.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_admin.php'; ?>

            <main class="pPembatalan-wrapper">
                <div class="pPembatalan">
                    <div class="pPembatalan-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Pengaturan Sistem
                    </div>
                    <div class="pPembatalan-body">
                        <?php if (isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                            <div class="alert alert-success">Pengaturan berhasil dikembalikan ke default.</div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <label>Nama Aplikasi:</label>
                            <input type="text" name="app_name" value="<?= htmlspecialchars($app_name); ?>" required>

                            <label>Logo Aplikasi:</label>
                            <input type="file" name="app_logo" accept="image/*">
                            <p>Logo saat ini:
                                <img class="logo"
                                    src="<?= file_exists('uploads/' . $app_logo) ? 'uploads/' . htmlspecialchars($app_logo) : htmlspecialchars($app_logo); ?>"
                                    alt="Logo"
                                    style="width: 50px; height: 50px; object-fit: contain;">
                            </p>

                            <label>Warna Teks Sidebar:</label>
                            <input type="color" name="theme_text_color" value="<?= htmlspecialchars($theme_text_color); ?>">

                            <br><br>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" name="update_settings" class="btn btn-success" style="align-items: center; justify-content: center;">Simpan</button>
                                <button type="submit" name="reset_default" class="btn btn-secondary" style="align-items: center; justify-content: center;">Default</button>
                            </div>

                        </form>
                    </div>
                    <div class="footer" style="color: white; text-align: center;">
                        <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>