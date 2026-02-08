<?php
include 'koneksi.php';

$role = $_POST['role'];
$table = '';
$id_column = '';
$name_column = '';

switch ($role) {
    case 'jamaah':
        $table = 'jamaah';
        $id_column = 'id_jamaah';
        $name_column = 'nama';
        break;
    case 'staf':
        $table = 'staf';
        $id_column = 'id_staf';
        $name_column = 'nama_staf';
        break;
    case 'kepala_seksi':
        $table = 'kepala_seksi';
        $id_column = 'id_kepala';
        $name_column = 'nama_kepala';
        break;
}

if ($table) {
    $result = mysqli_query($koneksi, "SELECT $id_column AS id, username FROM $table");

    // ✅ Tambahkan option default di atas
    echo '<option value="" disabled selected>-- Pilih Username --</option>';

    while ($row = mysqli_fetch_assoc($result)) {
        $id = htmlspecialchars($row['id']);
        $username = htmlspecialchars($row['username']);
        echo "<option value='$id'>$username</option>";
    }
}
?>
