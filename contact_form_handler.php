<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Memuat autoloader PHPMailer jika menggunakan Composer
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Validasi data
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $mail = new PHPMailer(true);
        
        try {
            // Konfigurasi SMTP untuk Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'shofia.lafarah74.com';  // Ganti dengan email Gmail Anda
            $mail->Password = 'nellamyla17_';  // Ganti dengan password Gmail Anda (atau app password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Pengaturan email
            $mail->setFrom($email, $name);
            $mail->addAddress('shofialafarah@gmail.com');  // Ganti dengan alamat email tujuan
            $mail->addReplyTo($email, $name);

            // Konten email
            $mail->isHTML(true);
            $mail->Subject = "Pesan Baru dari: $name - $subject";
            $mail->Body = "Nama: $name<br>Email: $email<br><br>Pesan:<br>$message";

            // Kirim email
            if ($mail->send()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Gagal mengirim email."]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Pesan gagal dikirim. Kesalahan: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Semua kolom harus diisi."]);
    }
}

?>
