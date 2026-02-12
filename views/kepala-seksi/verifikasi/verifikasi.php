<?php
session_start();
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';
if (!isset($_SESSION['temp_registration'])) {
    header("Location: register.php");
    exit;
}

$nomor_telepon = $_SESSION['temp_registration']['nomor_telepon'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $otp = $_POST['otp'];

    // Ambil OTP dari database
    $stmt = $koneksi->prepare("SELECT otp_code, waktu FROM otp WHERE nomor_telepon = ?");
    $stmt->bind_param("s", $nomor_telepon);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['otp_code'] == $otp) {
        if (time() - strtotime($row['waktu']) <= 300) { 
            // Hapus OTP setelah verifikasi berhasil
            $stmt = $koneksi->prepare("DELETE FROM otp WHERE nomor_telepon = ?");
            $stmt->bind_param("s", $nomor_telepon);
            $stmt->execute();

            // Simpan data user ke tabel jamaah
            $data = $_SESSION['temp_registration'];
            $stmt = $koneksi->prepare("INSERT INTO jamaah (nama, nomor_porsi, nomor_telepon, email, username, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $data['nama'], $data['nomor_porsi'], $data['nomor_telepon'], $data['email'], $data['username'], $data['password']);
            $stmt->execute();

            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('OTP kedaluwarsa!');</script>";
        }
    } else {
        echo "<script>alert('OTP salah!');</script>";
    }
}
?>
