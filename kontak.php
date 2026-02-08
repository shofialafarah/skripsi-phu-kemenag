<?php
// Pastikan PHPMailer sudah diunduh dan ditempatkan di folder yang benar
// Jalur ke file autoload.php di dalam folder PHPMailer yang Anda ekstrak
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sesuaikan path ini jika folder PHPMailer Anda ada di lokasi berbeda
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Inisialisasi variabel pesan status
$status_message = '';
$redirect_url = 'kontak.html'; // Ini sudah benar karena form akan kembali ke kontak.html

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan bersihkan data dari form
    // PASTIKAN NAMA ATTRIBUT 'name' DI HTML SAMA PERSIS DENGAN KUNCI ARRAY $_POST INI
    $logo_url = "https://cdn.kemenag.go.id/storage/archives/logo-kemenag-png-1png.png"; // <--- GANTI DENGAN URL LOGO ANDA YANG BISA DIAKSES PUBLIK!
    $kemenag_email = "banjarkalsel@kemenag.go.id"; // Ganti jika email ini berbeda
    $call_center = "(0511) 4721249";
    $alamat_kantor = "Jl. Sekumpul No. 72-73 Kelurahan Jawa <br> Martapura Banjar 70614";

    $nama_pengirim = htmlspecialchars(trim($_POST['nama_anda'])); // name="nama_anda" di HTML
    $email_pengirim = htmlspecialchars(trim($_POST['email_anda'])); // name="email_anda" di HTML
    $subjek_pesan = htmlspecialchars(trim($_POST['subjek']));       // name="subjek" di HTML
    $isi_pesan = htmlspecialchars(trim($_POST['pesan']));           // name="pesan" di HTML

    // 2. Validasi Input Sederhana
    if (empty($nama_pengirim) || empty($email_pengirim) || empty($subjek_pesan) || empty($isi_pesan)) {
        $status_message = 'invalid_input';
        header("Location: " . $redirect_url . "?status=" . $status_message);
        exit();
    }
    if (!filter_var($email_pengirim, FILTER_VALIDATE_EMAIL)) {
        $status_message = 'invalid_input';
        header("Location: " . $redirect_url . "?status=" . $status_message);
        exit();
    }

    // 3. Konfigurasi PHPMailer
    $mail = new PHPMailer(true);
    // Hapus debug untuk production
    // $mail->SMTPDebug = 2;
    // $mail->Debugoutput = 'html';

    try {
        // Konfigurasi Server SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'phukemenagbanjar@gmail.com'; // Email admin Gmail Anda
        $mail->Password   = 'hudvajeelntejdur'; // <--- GANTI INI dengan SANDI APLIKASI dari Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Penerima Email
        $mail->setFrom($email_pengirim, $nama_pengirim);
        $mail->addAddress('phukemenagbanjar@gmail.com', 'Admin PHU Kemenag Banjar'); // Ke email admin Anda

        // Konten Email - DIUBAH MENJADI HTML
        $mail->isHTML(true); // Set email format ke HTML
        $mail->Subject = $subjek_pesan;

        // Template HTML Email yang Modern dan Menarik
        $mail->Body = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kontak dari Website</title>
    <style>
        /* CSS Inline untuk Kompatibilitas Email Client */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #28a745; /* Warna hijau khas Kemenag */
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .header h1 {
            margin: 10px 0 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 30px;
            line-height: 1.6;
            color: #333333;
        }
        .content p {
            margin-bottom: 15px;
        }
        .footer {
            background-color: #f8f8f8;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #666666;
            border-top: 1px solid #eeeeee;
        }
        .footer a {
            color: #28a745;
            text-decoration: none;
        }
        .important-box {
            background-color: #fff3cd; /* Warna kuning untuk peringatan */
            border-left: 5px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 0.9em;
            color: #856404;
        }
        .otp-box {
            background-color: #e2f7e2; /* Warna hijau muda */
            border: 1px solid #28aa28;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-code {
            font-size: 2.5em;
            font-weight: bold;
            color: #28a745;
            margin: 10px 0;
        }
        .signature {
            text-align: center;
            margin-top: 30px;
        }
        .signature p {
            margin: 5px 0;
        }
        .contact-info {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
        }
        .contact-info p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="' . $logo_url . '" alt="Logo Kementerian Agama">
            <h1>KEMENTERIAN AGAMA <br> KABUPATEN BANJAR</h1>
        </div>
        <div class="content">
            <p><strong>Assalamu\'alaikum Wr. Wb.</strong></p>
            <p>Yth. Bapak/Ibu Admin,</p>
            <p>Anda telah menerima pesan baru dari pengunjung website PHU Kemenag Banjar melalui formulir kontak:</p>
            <p><strong>Nama Pengirim:</strong> ' . $nama_pengirim . '</p>
            <p><strong>Email Pengirim:</strong> <a href="mailto:' . $email_pengirim . '">' . $email_pengirim . '</a></p>
            <p><strong>Subjek:</strong> ' . $subjek_pesan . '</p>
            <hr>
            <p><strong>Isi Pesan:</strong></p>
            <p>' . nl2br($isi_pesan) . '</p>
            <hr>
            <p>Mohon segera tindak lanjuti pesan ini.</p>
        </div>
        <div class="signature">
            <p>Wassalamu\'alaikum Wr. Wb.</p>
            <p><strong>Direktorat Penyelenggara Haji dan Umrah</strong></p>
        </div>
        <div class="footer">
            <div class="contact-info">
                <p>Kementerian Agama Kabupaten Banjar</p>
                <p>' . $alamat_kantor . '</p>
                <p>Call Center: <a href="tel:' . str_replace([' ', '(', ')'], '', $call_center) . '">' . $call_center . '</a></p>
                <p>Email: <a href="mailto:' . $kemenag_email . '">' . $kemenag_email . '</a></p>
            </div>
            <p style="margin-top: 20px;"><i>Email ini dikirim otomatis melalui sistem form kontak website PHU Kemenag Banjar.</i></p>
        </div>
    </div>
</body>
</html>
';

        // Fallback text untuk email client yang tidak mendukung HTML
        $mail->AltBody = "Pesan dari: " . $nama_pengirim . "\n"
            . "Email: " . $email_pengirim . "\n"
            . "Subjek: " . $subjek_pesan . "\n\n"
            . "Pesan:\n" . $isi_pesan . "\n\n"
            . "Diterima pada: " . date('d F Y, H:i:s') . " WIB";

        $mail->send();
        $status_message = 'success';
    } catch (Exception $e) {
        $status_message = 'error';
        // Ini akan sangat membantu jika terjadi masalah pengiriman email
        error_log("Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}", 0);
    }

    // Redirect kembali ke halaman kontak dengan status
    header("Location: " . $redirect_url . "?status=" . $status_message);
    exit();
} else {
    // Jika ada yang mencoba mengakses kontak.php secara langsung tanpa POST
    header("Location: " . $redirect_url);
    exit();
}
