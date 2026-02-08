<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login sebagai jamaah
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_jamaah = $_POST['id_jamaah'];
    $nama = $_POST['nama'];
    $nomor_porsi = $_POST['nomor_porsi'];
    
    // Pastikan jamaah hanya bisa edit data dirinya sendiri
    if ($id_jamaah != $_SESSION['id_jamaah']) {
        echo "<script>alert('Anda tidak memiliki akses untuk mengedit data ini!'); window.location.href='dashboard_jamaah.php';</script>";
        exit();
    }
    
    // Mulai transaksi
    $koneksi->begin_transaction();
    
    try {
        // Update tabel jamaah
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoTmp = $_FILES['foto']['tmp_name'];
            $fotoName = uniqid() . '-' . $_FILES['foto']['name'];
            $fotoPath = 'uploads/' . $fotoName;

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
        
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='dashboard_jamaah.php';</script>";
        
    } catch (Exception $e) {
        // Rollback jika ada error
        $koneksi->rollback();
        echo "<script>alert('Gagal memperbarui data: " . $e->getMessage() . "'); window.location.href='dashboard_jamaah.php';</script>";
    }
}
?>