<?php
// get_settings.php

// Pastikan koneksi ke database sudah dibuat (misalnya dari koneksi.php)
include 'koneksi.php';

// Ambil semua pengaturan dari tabel 'pengaturan'
$result = $koneksi->query("SELECT * FROM pengaturan");

// Simpan pengaturan dalam array asosiatif
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['key_name']] = $row['value'];
}

// Kamu bisa tutup koneksi jika tidak diperlukan lagi di file ini
// $koneksi->close();
?>