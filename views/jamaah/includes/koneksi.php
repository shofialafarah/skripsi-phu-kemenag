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

$db_host = "sql100.infinityfree.com"; 
$db_user = "if0_41550943";
$db_pass = "7s2nhzU8ra5aHaP";
$db_name = "if0_41550943_aplikasi_haji";

$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($koneksi->connect_error) {
    die('Gagal melakukan koneksi ke Database: ' . $koneksi->connect_error);
}

// URL Website - otomatis detect HTTP/HTTPS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'phu-kemenag-banjar.infinityfreeapp.com';
define('BASE_URL', $protocol . '://' . $host . '/');
?>