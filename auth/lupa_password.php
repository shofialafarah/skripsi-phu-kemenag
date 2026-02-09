<?php
session_start();
include 'koneksi.php';

// library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$message = ''; // Variabel untuk menyimpan pesan notifikasi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['username_or_email'];

    // Cari pengguna di setiap tabel
    $user_found = false;
    $tabel_pengguna = ['jamaah', 'staf', 'kepala_seksi', 'administrator'];

    // Mapping nama tabel ke nama kolom ID yang benar
    $id_columns = [
        'jamaah' => 'id_jamaah',
        'staf' => 'id_staf',
        'kepala_seksi' => 'id_kepala',
        'administrator' => 'id_administrator'
    ];

    foreach ($tabel_pengguna as $tabel) {
        $id_kolom = $id_columns[$tabel]; // Dapatkan nama kolom ID yang sesuai

        // Perbaikan: Ganti 'id' dengan nama kolom ID yang benar dari array
        $query = "SELECT $id_kolom, email FROM $tabel WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $input, $input);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $user_id = $user[$id_kolom]; // Perbaikan: Ambil nilai ID dari kolom yang benar
            $email = $user['email'];
            $user_table = $tabel; // Simpan nama tabel pengguna

            // Buat token baru yang unik dan tidak mudah ditebak
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Update token dan waktu kedaluwarsa di tabel pengguna yang ditemukan
            $query_update = "UPDATE $user_table SET reset_token = ?, token_expires_at = ? WHERE $id_kolom = ?";
            $stmt_update = mysqli_prepare($koneksi, $query_update);
            mysqli_stmt_bind_param($stmt_update, 'ssi', $token, $expires, $user_id);
            mysqli_stmt_execute($stmt_update);

            // link reset password
            $reset_link = "http://localhost/phu-kemenag-banjar-copy/auth/reset_pass.php?token=" . $token . "&type=" . $user_table;

            // --- Bagian Pengiriman Email dengan PHPMailer ---
            $mail = new PHPMailer(true);

            try {
                // Konfigurasi Server SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'phukemenagbanjar@gmail.com'; // Ganti dengan email Anda
                $mail->Password   = 'ewxc hjvt zqto axtg';   // PASTE KODE APP PASSWORD DI SINI
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8'; // Tambahkan ini untuk dukungan karakter UTF-8

                // Pengaturan Penerima dan Pengirim
                $mail->setFrom('no-reply@aplikasianda.com', 'Admin PHU Kemenag Banjar');
                $mail->addAddress($email);

                // Konten Email
                $mail->isHTML(true);
                $mail->Subject = 'Permintaan Atur Ulang Password';
                $mail->Body    = "Halo,<br><br>Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda. Silakan klik tautan di bawah ini untuk melanjutkan:<br><br><a href='$reset_link'>$reset_link</a><br><br>Tautan ini hanya berlaku selama 1 jam. Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.<br><br>Terima kasih,<br>Admin PHU Kemenag Banjar";

                $mail->send();
                $message = "Tautan reset kata sandi telah dikirim ke email Anda.";
            } catch (Exception $e) {
                $message = "Gagal mengirim email. Error: {$mail->ErrorInfo}";
            }

            $user_found = true;
            break; // Keluar dari loop setelah menemukan pengguna
        }
    }

    if (!$user_found) {
        $message = "Username atau email tidak terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="../landing-page/assets/img/logo_kemenag.png">
    <link rel="stylesheet" href="assets/css/lupa_password.css">
</head>

<body>
    <div class="reset-container">
        <h1>Lupa Password?</h1>
        <p>Masukkan username atau email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>

        <?php if ($message): ?>
            <p style="color: green;"><?= $message ?></p>
        <?php endif; ?>

        <form action="lupa_password.php" method="post">
            <label for="username_or_email">Username atau Email:</label>
            <input type="text" id="username_or_email" name="username_or_email" required>

            <button type="submit">Kirim Tautan Reset</button>
        </form>
    </div>
</body>

</html>