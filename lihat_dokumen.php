<?php
include 'koneksi.php';

if (!isset($_GET['id_pembatalan'])) {
    die('ID pembatalan tidak ditemukan.');
}

$id = intval($_GET['id_pembatalan']);

// Ambil path file dari database
$sql = "SELECT dokumen FROM pembatalan WHERE id_pembatalan = '$id'";
$result = mysqli_query($koneksi, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    $path = $row['dokumen'];
    
    if (!empty($path) && file_exists($path)) {
        // Redirect ke file PDF
        header("Location: " . $path);
        exit;
    } else {
        echo "File tidak ditemukan atau belum diunggah.";
    }
} else {
    echo "Data tidak ditemukan.";
}
?>
