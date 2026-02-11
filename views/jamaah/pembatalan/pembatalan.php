<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../../includes/koneksi.php';

// Cek apakah jamaah sudah login
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit;
}

$id_jamaah = $_SESSION['id_jamaah'];

// Cek apakah jamaah ini sudah melakukan pembatalan
$query = "SELECT kategori FROM pembatalan WHERE id_jamaah = ? ORDER BY tanggal_pengajuan DESC LIMIT 1";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $_SESSION['id_jamaah']);
$stmt->execute();
$result = $stmt->get_result();
    
if ($result->num_rows > 0) {
    // Sudah pernah membatalkan
    $data = $result->fetch_assoc();
    $kategori = $data['kategori'];// misalnya kolomnya bernama 'kategori'

    if ($kategori == 'Meninggal Dunia') {
        header("Location: includes/pembatalan_jamaah_meninggal.php");
    } elseif ($kategori == 'Keperluan Ekonomi') {
        header("Location: includes/pembatalan_jamaah_ekonomi.php");
    }
    exit;
} else {
    header("Location: tambah_pembatalan.php");
    exit;
}

?>
