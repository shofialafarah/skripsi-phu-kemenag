<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

// Update pengaturan sistem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        foreach ($_POST as $key => $value) {
            if ($key !== 'update_settings' && $key !== 'app_logo') {
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
            // Gunakan path absolut pada filesystem agar move_uploaded_file bekerja
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/phu-kemenag-banjar-copy/assets/img/';

            // Buat direktori jika belum ada
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
                    $check_stmt = $koneksi->prepare("SELECT COUNT(*) FROM pengaturan WHERE key_name = 'app_logo'");
                    $check_stmt->execute();
                    $check_stmt->bind_result($count);
                    $check_stmt->fetch();
                    $check_stmt->close();

                    if ($count > 0) {
                        $stmt = $koneksi->prepare("UPDATE pengaturan SET value=? WHERE key_name='app_logo'");
                    } else {
                        $stmt = $koneksi->prepare("INSERT INTO pengaturan (key_name, value) VALUES ('app_logo', ?)");
                    }
                    $stmt->bind_param('s', $file_name);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    header('Location: pengaturan.php?updated=0');
                    exit();
                }
            } else {
                header('Location: pengaturan.php?updated=0');
                exit();
            }
        }

        header('Location: pengaturan.php?updated=1');
        exit();
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
$app_logo = $settings['app_logo'] ?? '';
$theme_text_color = $settings['theme_text_color'] ?? '#ffffff';

// Path dasar folder image (relatif dari file pengaturan.php ini)
$base_img_path = '../../../assets/img/';

if (empty($app_logo)) {
    $app_logo_src = $base_img_path . 'sistem.png';
} elseif (filter_var($app_logo, FILTER_VALIDATE_URL)) {
    $app_logo_src = $app_logo;
} else {
    // Jika hanya nama file (hasil upload baru atau reset default)
    $app_logo_src = $base_img_path . $app_logo;

    // Opsional: Cek apakah filenya benar-benar ada di folder
    if (!file_exists(__DIR__ . '/' . $base_img_path . $app_logo)) {
        $app_logo_src = $base_img_path . 'sistem.png';
    }
}

if (isset($_POST['reset_default'])) {
    $default_logo = 'sistem.png';
    $default_color = '#7bd247';

    $koneksi->query("UPDATE pengaturan SET value = '$default_logo' WHERE key_name = 'app_logo'");
    $koneksi->query("UPDATE pengaturan SET value = '$default_color' WHERE key_name = 'theme_text_color'");

    header("Location: pengaturan.php?reset=success");
    exit();
}

?>
<link rel="stylesheet" href="assets/css/pengaturan.css">

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
<?php include '../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../includes/header_admin.php'; ?>

        <main class="pengaturan-wrapper">
            <div class="pengaturan">
                <div class="pengaturan-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Pengaturan Sistem
                </div>
                <div class="pengaturan-body">
                    <form class="pengaturan-form" method="POST" action="" enctype="multipart/form-data">
                        <label class="label-pengaturan">Nama Aplikasi:</label>
                        <input type="text" name="app_name" value="<?= htmlspecialchars($app_name); ?>" required>

                        <label class="label-pengaturan">Logo Aplikasi:</label>
                        <input type="file" name="app_logo" accept="image/*">
                        <p>Logo saat ini:
                            <img class="logo"
                                src="<?= htmlspecialchars($app_logo_src); ?>"
                                alt="Logo Kemenag"
                                style="width: 50px; height: 50px; object-fit: contain;">
                        </p>

                        <label class="label-pengaturan">Warna Teks Sidebar:</label>
                        <input type="color" name="theme_text_color" value="<?= htmlspecialchars($theme_text_color); ?>">

                        <br><br>
                        <div style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                            <button type="submit" name="update_settings" class="btn btn-success" style="align-items: center; justify-content: center;">Simpan</button>
                            <button type="submit" name="reset_default" class="btn btn-secondary" style="align-items: center; justify-content: center;">Default</button>
                        </div>

                    </form>
                </div>
                <?php include_once __DIR__ . '/../includes/footer_admin.php'; ?>
            </div>
        </main>
    </div>
</div>
<script src="../assets/js/sidebar.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/pengaturan.js"></script>
</body>

</html>