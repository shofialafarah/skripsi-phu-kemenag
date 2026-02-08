<?php
// Menyiapkan data kecamatan dan kelurahan di Kabupaten Banjar
$data_kelurahan = [
    "Aluh-Aluh" => ["Kelurahan Aluh-Aluh 1", "Kelurahan Aluh-Aluh 2", "Kelurahan Aluh-Aluh 3"],
    "Aranio" => ["Kelurahan Aranio 1", "Kelurahan Aranio 2"],
    "Astambul" => ["Kelurahan Astambul 1", "Kelurahan Astambul 2", "Kelurahan Astambul 3"],
    "Beruntung Baru" => ["Kelurahan Beruntung Baru 1", "Kelurahan Beruntung Baru 2"],
    // Tambahkan kecamatan dan kelurahan lainnya
];

// Mendapatkan kecamatan yang dipilih melalui parameter
$kecamatan = $_GET['kecamatan'] ?? '';

// Mengecek jika kecamatan ada dalam data dan mengembalikan kelurahan terkait
if ($kecamatan && isset($data_kelurahan[$kecamatan])) {
    echo json_encode($data_kelurahan[$kecamatan]);
} else {
    echo json_encode([]);
}
?>
