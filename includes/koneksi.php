<?php
/* ===========================================================
  SISTEM INFORMASI PELAYANAN IBADAH HAJI BERBASIS WEB PADA KEMENTERIAN AGAMA KABUPATEN BANJAR
  AUTHOR    : SHOFIA NABILA ELFA RAHMA
  NIM       : 2110010113
  COPYRIGHT : (c) 2025 - Hak Cipta Dilindungi Undang-Undang
===========================================================
  File ini adalah file inti koneksi. 
  Dilarang menghapus header ini tanpa izin author.
===========================================================
*/
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
define('BASE_URL', 'http://localhost/phu-kemenag-banjar-copy/');
?>
