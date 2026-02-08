<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'staf') {
    die('Akses tidak sah!');
}


// Pastikan ada id_pendaftaran di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID pendaftaran tidak ditemukan!";
    exit;
}

$id_pendaftaran = intval($_GET['id']);

// Ambil data pendaftaran untuk ditampilkan (opsional)
$query = "SELECT * FROM pendaftaran WHERE id_pendaftaran = $id_pendaftaran";
$result = mysqli_query($koneksi, $query);
$pendaftar = mysqli_fetch_assoc($result);

if (!$pendaftar) {
    echo "Data pendaftar tidak ditemukan!";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Dokumen Pendaftaran</title>
</head>
<body>
    <h2>Upload Dokumen untuk: <?php echo htmlspecialchars($pendaftar['nama_jamaah']); ?></h2>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p style="color: red;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>

    <form action="entry_pendaftaran.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_pendaftaran" value="<?php echo $id_pendaftaran; ?>">
        
        <label for="dokumen">Pilih Dokumen (PDF):</label><br>
        <input type="file" name="dokumen" id="dokumen" accept=".pdf" required><br><br>

        <button type="submit">Upload</button>
    </form>

    <p><a href="entry_pendaftaran.php">← Kembali</a></p>
</body>
</html>
