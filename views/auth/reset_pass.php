<?php
session_start();
include '../../includes/koneksi.php';

$message = '';
$token = $_GET['token'] ?? '';
$user_table = $_GET['type'] ?? '';
$user_exists = false; // Definisikan di awal biar aman

$id_columns = [
    'jamaah' => 'id_jamaah',
    'staf' => 'id_staf',
    'kepala_seksi' => 'id_kepala',
    'administrator' => 'id_admin'
];

if (!in_array($user_table, array_keys($id_columns))) {
    die("Tipe pengguna tidak valid.");
}

$id_kolom = $id_columns[$user_table];

$query = "SELECT $id_kolom FROM $user_table WHERE reset_token = ? AND token_expires_at > NOW()";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 's', $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $message = "Tautan reset tidak valid atau sudah kedaluwarsa.";
} else {
    $user_exists = true; // Tandai kalau user ditemukan
    $data = mysqli_fetch_assoc($result);
    $user_id = $data[$id_kolom];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "Kata sandi tidak cocok.";
        } else {
            // Gunakan BCRYPT agar sinkron dengan register
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            $query_update = "UPDATE $user_table SET password = ?, reset_token = NULL, token_expires_at = NULL WHERE $id_kolom = ?";
            $stmt_update = mysqli_prepare($koneksi, $query_update);
            mysqli_stmt_bind_param($stmt_update, 'si', $hashed_password, $user_id);
            
            if (mysqli_stmt_execute($stmt_update)) {
                $message = "Kata sandi Anda berhasil diubah. Silakan <a href='login.php'>masuk</a>.";
                $show_form = false;
                header("refresh:3;url=login.php");
            } else {
                $message = "Gagal memperbarui kata sandi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="icon" href="../assets/logo_kemenag.png">
    <link rel="stylesheet" href="assets/css/reset_pass.css">
</head>
<body>
    <div class="reset-container">
        <h1>Atur Ulang Password</h1>

        <?php if ($message): ?>
            <p style="color: <?= (strpos($message, 'berhasil') !== false) ? 'green' : 'red' ?>;">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <?php if ($user_exists && !isset($show_form)): ?>
            <form method="POST">
                <input type="password" name="new_password" placeholder="Masukkan Password Baru" required>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password Baru" required>
                <button type="submit">Perbarui Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>