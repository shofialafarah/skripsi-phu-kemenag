<?php 
include 'koneksi.php';

if (isset($_GET['id']) && isset($_GET['status']) && isset($_GET['type'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $type = $_GET['type']; // Menambahkan parameter 'type' untuk menentukan tabel

    // Tentukan query dan tabel yang akan diperbarui
    if ($type == 'pembatalan') {
        $query = "UPDATE pembatalan SET status = ? WHERE id_pembatalan = ?";
        $redirectPage = 'monitor_pembatalan.php';
    } elseif ($type == 'pelimpahan') {
        $query = "UPDATE pelimpahan SET status = ? WHERE id_pelimpahan = ?";
        $redirectPage = 'monitor_pelimpahan.php';
    } else {
        // Jika type tidak valid
        echo 'Tipe pengajuan tidak valid';
        exit();
    }

    // Persiapkan dan jalankan query
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Jika berhasil, redirect ke halaman yang sesuai
        header("Location: $redirectPage");
        exit();
    } else {
        // Jika tidak ada perubahan (tidak ada baris yang terpengaruh)
        echo 'Gagal memperbarui status atau status sudah sesuai';
    }
} else {
    echo 'Data tidak lengkap.';
}
?>
