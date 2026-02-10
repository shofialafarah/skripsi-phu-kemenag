<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';

$role = $_POST['role'];
$id = $_POST['id_pengguna'];
$status = $_POST['status_pengguna'];

$table = '';
$id_column = '';

switch ($role) {
    case 'jamaah':
        $table = 'jamaah';
        $id_column = 'id_jamaah';
        break;
    case 'staf':
        $table = 'staf';
        $id_column = 'id_staf';
        break;
    case 'kepala_seksi':
        $table = 'kepala_seksi';
        $id_column = 'id_kepala';
        break;
}

if ($table) {
    $stmt = $koneksi->prepare("UPDATE $table SET status_pengguna = ?, updated_at = NOW() WHERE $id_column = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    echo "sukses";
} else {
    echo "gagal";
}
?>
