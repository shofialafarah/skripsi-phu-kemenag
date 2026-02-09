<?php
ob_start(); // Baris ini untuk mengaktifkan output buffering

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "aplikasi_haji";

// Koneksi menggunakan MySQLi Object-Oriented
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Periksa koneksi
if ($koneksi->connect_error) {
    die('Gagal melakukan koneksi ke Database: ' . $koneksi->connect_error);
}

?>
