<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

// Sertakan library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../vendor/autoload.php';

$message = '';
$tabel_terpilih = $_POST['user_type'] ?? ''; // Default kosong saat pertama load

// Array untuk memetakan nama tabel ke nama kolom ID yang benar
$id_columns = [
    'jamaah' => 'id_jamaah',
    'staf' => 'id_staf',
    'kepala_seksi' => 'id_kepala',
    'administrator' => 'id_administrator'
];

// Pastikan tipe pengguna yang dipilih valid (hanya jika ada pilihan)
if (!empty($tabel_terpilih) && !in_array($tabel_terpilih, array_keys($id_columns))) {
    $tabel_terpilih = '';
}

// Logika untuk mereset kata sandi ketika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $username_target = $_POST['username'];
    $id_kolom = $id_columns[$tabel_terpilih];

    // Ambil data user yang akan di-reset password-nya
    $query_user = "SELECT $id_kolom, email FROM $tabel_terpilih WHERE username = ?";
    $stmt_user = $koneksi->prepare($query_user);
    $stmt_user->bind_param('s', $username_target);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user_data = $result_user->fetch_assoc();
        $email = $user_data['email'];

        // 1. Generate kata sandi baru yang acak dan aman
        $new_password = bin2hex(random_bytes(8)); // Menghasilkan 16 karakter acak
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // 2. Perbarui kata sandi di database
        $query_update = "UPDATE $tabel_terpilih SET password = ? WHERE username = ?";
        $stmt_update = $koneksi->prepare($query_update);
        $stmt_update->bind_param('ss', $hashed_password, $username_target);
        
        if ($stmt_update->execute()) {
            // 3. Kirim email notifikasi ke pengguna
            $mail = new PHPMailer(true);
            try {
                // Konfigurasi Server SMTP (sama seperti di lupa_password.php)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'phukemenagbanjar@gmail.com'; // Ganti dengan email Anda
                $mail->Password   = 'ewxc hjvt zqto axtg';   // Ganti dengan App Password Anda
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                // Pengaturan Penerima dan Pengirim
                $mail->setFrom('phukemenagbanjar@gmail.com', 'Admin PHU Kemenag Banjar');
                $mail->addAddress($email);
                $mail->isHTML(true);

                // Konten Email
                $mail->Subject = 'Kata Sandi Akun Anda Telah Direset';
                $mail->Body    = "Halo,<br><br>Kata sandi akun Anda telah diubah oleh administrator.<br><br>Kata sandi baru Anda adalah: <strong>$new_password</strong><br><br>Untuk alasan keamanan, silakan segera login dan ganti kata sandi ini.<br><br>Terima kasih,<br>Admin PHU Kemenag Banjar";

                $mail->send();
                $message = "Kata sandi berhasil direset untuk <b>$username_target</b>. Kata sandi baru telah dikirim via email.";
            } catch (Exception $e) {
                $message = "Gagal mengirim email. Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Error saat memperbarui database.";
        }
    } else {
        $message = "Username tidak ditemukan di tabel ini.";
    }
}
?>
<link rel="stylesheet" href="assets/css/pengaturan.css">
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
                    <div class="pengaturan-header">
                        <i class="fas fa-table me-1"></i> Reset Password Pengguna
                    </div>
                    <div class="pengaturan-body">
                        <?php if ($message): ?>
                            <p style="color: green;"><?= $message ?></p>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <label class="form-label">Pilih Role Pengguna:</label>
                            <select name="user_type" onchange="this.form.submit()" class="select-daftar" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                <?php foreach ($id_columns as $tabel_name => $id_name): ?>
                                    <option value="<?= $tabel_name ?>" <?= ($tabel_name == $tabel_terpilih) ? 'selected' : '' ?>>
                                        <?= ucwords(str_replace('_', ' ', $tabel_name)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br><br>
                            
                            <label class="form-label">Pilih Username:</label>
                            <select name="username" class="select-daftar" required>
                                <option value="" disabled selected>-- Pilih Username --</option>
                                <?php
                                // Tampilkan username hanya jika role sudah dipilih
                                if (!empty($tabel_terpilih)) {
                                    $query_users = "SELECT username FROM $tabel_terpilih";
                                    $result_users = $koneksi->query($query_users);
                                    while ($row = $result_users->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['username']) . "'>" . htmlspecialchars($row['username']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <br>
                            <div style="display: flex; justify-content: center; align-items: center; margin-top: 20px; margin-bottom: 20px;">
                                <button type="submit" name="reset_password" class="btn btn-success">Reset Password</button>
                            </div>
                            
                        </form>
                    </div>
                    <?php include_once __DIR__ . '/../includes/footer_admin.php'; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/sidebar.js"></script>
</body>
</html>