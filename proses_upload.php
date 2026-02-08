<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role-nya staf
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php");
    exit();
}

// Debug mode - uncomment untuk melihat error
error_reporting(E_ALL);
ini_set('display_errors', 1);

function clean_input($data) {
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pendaftaran = clean_input($_POST['id_pendaftaran']);
    $keterangan = clean_input($_POST['keterangan']);

    // Debug
    echo "<pre>";
    echo "ID: " . $id_pendaftaran . "\n";
    echo "Keterangan: " . $keterangan . "\n";
    print_r($_FILES);
    echo "</pre>";

    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['dokumen']['tmp_name'];
        $fileName = basename($_FILES['dokumen']['name']);
        $fileSize = $_FILES['dokumen']['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi tipe dan ukuran file
        if ($fileExt !== 'pdf') {
            $_SESSION['error_message'] = "Hanya file PDF yang diperbolehkan.";
            header("Location: entry_pendaftaran.php");
            exit;
        }

        if ($fileSize > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "Ukuran file maksimal 5MB.";
            header("Location: entry_pendaftaran.php");
            exit;
        }

        // Direktori upload
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid() . "_" . $fileName;
        $targetPath = $uploadDir . $newFileName;

        // Upload dan simpan ke database
        if (move_uploaded_file($fileTmp, $targetPath)) {
            $sql = "UPDATE pendaftaran SET upload_doc = '$targetPath', keterangan = '$keterangan', tanggal_validasi = NOW() WHERE id_pendaftaran = '$id_pendaftaran'";
            
            echo "SQL Query: " . $sql . "<br>";
            
            if (mysqli_query($koneksi, $sql)) {
                $_SESSION['success_message'] = "Dokumen berhasil diupload.";
                echo "Upload berhasil!<br>";
            } else {
                $_SESSION['error_message'] = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
                echo "Error database: " . mysqli_error($koneksi) . "<br>";
            }
        } else {
            $_SESSION['error_message'] = "Gagal memindahkan file.";
            echo "Gagal memindahkan file<br>";
        }
    } else {
        $_SESSION['error_message'] = "File tidak terunggah dengan benar. Error: " . 
            ($_FILES['dokumen']['error'] ?? 'File tidak dikirim');
        echo "Error upload: " . ($_FILES['dokumen']['error'] ?? 'File tidak dikirim') . "<br>";
    }

    // Uncomment untuk mode debug
    // echo "<a href='entry_pendaftaran.php'>Kembali ke halaman Entry Pendaftaran</a>";
    
    // Comment ini untuk mode debug
    header("Location: entry_pendaftaran.php");
    exit();
} else {
    $_SESSION['error_message'] = "Metode tidak diizinkan.";
    header("Location: entry_pendaftaran.php");
    exit();
}
?>