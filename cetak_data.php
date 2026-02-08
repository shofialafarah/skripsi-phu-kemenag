<?php

include 'koneksi.php';

$id_jamaah = $_SESSION['id_jamaah'];
$query = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE id_jamaah = '$id_jamaah'");
$data = mysqli_fetch_assoc($query);

// Tampilkan data pendaftaran untuk dicetak
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Data Pendaftaran</title>
    <style>
        body { font-family: Arial; margin: 40px; }
    </style>
</head>
<body>
    <h2>Data Pendaftaran Haji</h2>
    <p><strong>Nama:</strong> <?= $data['nama'] ?></p>
    <p><strong>Tanggal Daftar:</strong> <?= $data['tanggal_daftar'] ?></p>
    <!-- Tambahkan info lain -->
    <script>
        window.print(); // otomatis buka dialog print
    </script>
</body>
</html>
