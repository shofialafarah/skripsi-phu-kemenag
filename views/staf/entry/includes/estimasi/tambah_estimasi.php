<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../../includes/koneksi.php';

// Cek login
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pendaftaran = $_POST['id_pendaftaran'];
    $nomor_porsi = $_POST['nomor_porsi'];
    $tgl_pendaftaran = $_POST['tgl_pendaftaran'];

    // Cek apakah nomor porsi sudah digunakan oleh ID lain
    $cek_porsi = $koneksi->prepare("SELECT id_pendaftaran FROM pendaftaran WHERE nomor_porsi = ? AND id_pendaftaran != ?");
    $cek_porsi->bind_param("si", $nomor_porsi, $id_pendaftaran);
    $cek_porsi->execute();
    $cek_porsi->store_result();

    if ($cek_porsi->num_rows > 0) {
        echo "<script>
    alert('Nomor porsi sudah digunakan oleh pendaftaran lain.');
    window.location.href = 'entry_estimasi.php?error=duplikat';
</script>";

        exit();
    }

    $cek_porsi->close();

    // Tanggal hari ini
    $hariIni = new \DateTime();

    // Hitung telah menunggu
    $tglDaftar = new \DateTime($tgl_pendaftaran);
    $diff = $tglDaftar->diff($hariIni);
    $telah_menunggu = "{$diff->y} tahun, {$diff->m} bulan, {$diff->d} hari";

    // Estimasi berangkat dan masa tunggu
    $masa_menunggu = 30; // tahun
    $estimasi_berangkat_date = (clone $tglDaftar)->modify("+$masa_menunggu year");
    $estimasi_berangkat = $estimasi_berangkat_date->format('Y-m-d');

    // Sisa menunggu (dalam hari)
    $sisa_menunggu = $hariIni->diff($estimasi_berangkat_date)->days;

    // Ambil tanggal lahir dari pendaftaran
    $query = $koneksi->prepare("SELECT tanggal_lahir FROM pendaftaran WHERE id_pendaftaran = ?");
    $query->bind_param("i", $id_pendaftaran);
    $query->execute();
    $query->bind_result($tanggal_lahir);
    $query->fetch();
    $query->close();

    // Pastikan tanggal lahir tidak null
    if (!$tanggal_lahir) {
        die("Gagal: Tanggal lahir tidak ditemukan untuk ID pendaftaran ini.");
    }

    // Hitung umur
    $tglLahir = new \DateTime($tanggal_lahir);
    $umur = $tglLahir->diff($hariIni)->y;

    // Simpan ke tabel estimasi
    $stmt = $koneksi->prepare("INSERT INTO estimasi (id_pendaftaran, tgl_pendaftaran, telah_menunggu, estimasi_berangkat, umur, sisa_menunggu, masa_menunggu) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssii", $id_pendaftaran, $tgl_pendaftaran, $telah_menunggu, $estimasi_berangkat, $umur, $sisa_menunggu, $masa_menunggu);

    if ($stmt->execute()) {
        // Update nomor_porsi juga di tabel pendaftaran
        $stmt_update = $koneksi->prepare("UPDATE pendaftaran SET nomor_porsi = ? WHERE id_pendaftaran = ?");
        $stmt_update->bind_param("si", $nomor_porsi, $id_pendaftaran);
        $stmt_update->execute();

        header("Location: ../../entry_estimasi.php?success=tambah");
        exit();
    } else {
        echo "Gagal menambahkan data: " . $stmt->error;
    }
}
