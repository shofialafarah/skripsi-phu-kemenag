<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jamaah = $_POST['id_jamaah'];
    $nama = $_POST['nama'];
    $nomor_porsi = $_POST['nomor_porsi'];

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Update tabel jamaah
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoTmp = $_FILES['foto']['tmp_name'];
            $fotoName = uniqid() . '-' . $_FILES['foto']['name'];
            // Tambahkan titik koma di bawah ini
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/phu-kemenag-banjar-copy/uploads/akun-pengguna/jamaah/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $fotoPath = $upload_dir . $fotoName;
            move_uploaded_file($fotoTmp, $fotoPath);

            // Update jamaah dengan foto
            $sql = "UPDATE jamaah SET nama = ?, foto = ? WHERE id_jamaah = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("ssi", $nama, $fotoName, $id_jamaah);
        } else {
            // Update jamaah tanpa ganti foto
            $sql = "UPDATE jamaah SET nama = ? WHERE id_jamaah = ?";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("si", $nama, $id_jamaah);
        }

        if (!$stmt->execute()) {
            throw new Exception("Gagal update tabel jamaah");
        }

        // Update nomor_porsi di tabel pendaftaran (jika ada)
        $sql_pendaftaran = "UPDATE pendaftaran SET nomor_porsi = ? WHERE id_jamaah = ?";
        $stmt_pendaftaran = $koneksi->prepare($sql_pendaftaran);
        $stmt_pendaftaran->bind_param("si", $nomor_porsi, $id_jamaah);

        // Tidak error jika tidak ada data di pendaftaran
        $stmt_pendaftaran->execute();

        // Commit transaksi
        $koneksi->commit();

        $koneksi->commit();

        // Simpan status sukses ke session
        session_start();
        $_SESSION['update_status'] = 'success';
        header("Location: ../dashboard_jamaah.php");
        exit;
    } catch (Exception $e) {
        $koneksi->rollback();
        session_start();
        $_SESSION['update_status'] = 'error';
        $_SESSION['update_msg'] = $e->getMessage();
        header("Location: ../dashboard_jamaah.php");
        exit;
    }
}
