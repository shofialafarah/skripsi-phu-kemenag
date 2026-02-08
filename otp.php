<?php
session_start();
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

if (!isset($_SESSION['temp_registration'])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION['temp_registration']['email'];

// Cek apakah OTP sudah dikirim sebelumnya
$stmt = $koneksi->prepare("SELECT otp_code, waktu FROM otp WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$otp_sent_time = 0;
$show_resend_button = true;

if ($row) {
    $otp_sent_time = strtotime($row['waktu']);
    $time_difference = time() - $otp_sent_time;

    if ($time_difference < 60) {
        $show_resend_button = false;
        $remaining_time = 60 - $time_difference;
    } else {
        $remaining_time = 60;
    }
}

// Kirim ulang OTP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resend_otp'])) {
    $otp_code = rand(100000, 999999);

    // Cek apakah email sudah ada di database
    $stmt = $koneksi->prepare("SELECT email FROM otp WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $koneksi->prepare("UPDATE otp SET otp_code = ?, waktu = current_timestamp(), attempts = 0 WHERE email = ?");
    } else {
        $stmt = $koneksi->prepare("INSERT INTO otp (email, otp_code, waktu, attempts, is_verified) VALUES (?, ?, current_timestamp(), 0, 0)");
    }

    $stmt->bind_param("ss", $otp_code, $email);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'remaining_time' => 60]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengirim OTP.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo_kemenag.png">
    <title>Verifikasi OTP</title>
    <link rel="stylesheet" href="reset_pass.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <form action="otp.php" method="POST">   
            <h1>Verifikasi OTP</h1>
            <p>Kode OTP telah dikirim ke: <?php echo $email; ?></p> 
            <div class="otp-container">
                <input type="text" class="otp-input" maxlength="1" oninput="moveNext(this, 1)">
                <input type="text" class="otp-input" maxlength="1" oninput="moveNext(this, 2)">
                <input type="text" class="otp-input" maxlength="1" oninput="moveNext(this, 3)">
                <input type="text" class="otp-input" maxlength="1" oninput="moveNext(this, 4)">
                <input type="text" class="otp-input" maxlength="1" oninput="moveNext(this, 5)">
                <input type="text" class="otp-input" maxlength="1" oninput="moveNext(this, 6)">
            </div>   
            <button type="submit" name="verify_otp">Verifikasi OTP</button>

            <p id="message"></p>

            <div id="resend-section">
                <p>Belum menerima kode OTP? 
                    <?php if ($show_resend_button): ?>
                        <a href="javascript:void(0);" onclick="resendOTP()" id="resendOtpBtn">Kirim ulang OTP</a>
                    <?php else: ?>
                        <p>Menunggu <span id="countdown"></span> untuk mengirim ulang OTP.</p>
                    <?php endif; ?>
                </p>
            </div>
        </form>  
    </div>
</div>

<script>
// Fungsi untuk memulai countdown timer dari 01:00 ke 00:00
function startCooldown(seconds) {
    const countdownEl = document.getElementById('countdown');
    const resendBtn = document.getElementById('resendOtpBtn');

    let remainingTime = seconds;

    function updateTimer() {
        let minutes = Math.floor(remainingTime / 60);
        let seconds = remainingTime % 60;

        countdownEl.textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (remainingTime <= 0) {
            clearInterval(cooldownTimer);
            countdownEl.style.display = 'none';
            resendBtn.style.display = 'inline-block'; // Aktifkan tombol kirim ulang OTP
            return;
        }

        remainingTime--;
    }

    updateTimer();
    cooldownTimer = setInterval(updateTimer, 1000);
}

// Fungsi untuk mengirim ulang OTP
async function resendOTP() {
    try {
        const response = await fetch('otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'resend_otp=1'
        });
        
        const data = await response.json();
        
        if (data.success) {
            startCooldown(60); // Mulai timer untuk 1 menit
            document.getElementById('resendOtpBtn').style.display = 'none';
            alert('OTP baru telah dikirim. Silakan cek email Anda.');
        } else {
            document.getElementById('message').textContent = data.message;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('message').textContent = "Terjadi kesalahan, coba lagi nanti.";
    }
}

// Fungsi untuk berpindah otomatis ke input berikutnya saat diisi
function moveNext(input, index) {
    const value = input.value;
    const maxLength = input.maxLength;

    if (value.length === maxLength) {
        const nextInput = document.querySelector(`.otp-input:nth-child(${index + 1})`);
        if (nextInput) nextInput.focus();
    }

    // Jika dihapus (Backspace), pindah ke input sebelumnya
    input.addEventListener("keydown", function (e) {
        if (e.key === "Backspace" && input.value === "") {
            const prevInput = document.querySelector(`.otp-input:nth-child(${index - 1})`);
            if (prevInput) prevInput.focus();
        }
    });
}

// Tambahkan event listener untuk semua OTP input
document.querySelectorAll(".otp-input").forEach((input, index) => {
    input.addEventListener("input", () => moveNext(input, index + 1));
});

// Jalankan countdown jika masih ada waktu tersisa
<?php if (isset($remaining_time)): ?>
    startCooldown(<?php echo $remaining_time; ?>);
<?php else: ?>
    startCooldown(60);
<?php endif; ?>

</script>
</body>
</html>
