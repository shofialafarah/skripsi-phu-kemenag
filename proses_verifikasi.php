<?php
include 'koneksi.php';

header('Content-Type: application/json');

try {
    // Validasi input
    if (!isset($_POST['tipe']) || !isset($_POST['id']) || !isset($_POST['aksi'])) {
        throw new Exception('Parameter tidak lengkap');
    }

    $tipe = $_POST['tipe'];
    $id = (int)$_POST['id'];
    $aksi = (int)$_POST['aksi'];

    // Validasi tipe data
    if (!in_array($tipe, ['batal_ekonomi', 'batal_meninggal', 'pelimpahan_sakit', 'pelimpahan_meninggal'])) {
        throw new Exception('Tipe data tidak valid');
    }

    if (!in_array($aksi, [1, 2])) {
        throw new Exception('Aksi tidak valid');
    }

    // Tentukan nama kolom ID berdasarkan tipe
    $id_column = "id_" . $tipe;

    // Update database
    $query = "UPDATE {$tipe} SET verifikasi_kepala_seksi = ? WHERE {$id_column} = ?";
    $stmt = $koneksi->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Gagal mempersiapkan query');
    }

    $stmt->bind_param('ii', $aksi, $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal mengeksekusi query');
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Data tidak ditemukan');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>