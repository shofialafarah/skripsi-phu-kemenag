<?php
include('koneksi.php');

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    
    if ($type === 'pelimpahan') {
        $query = "UPDATE pelimpahan SET is_read_staf = 1 WHERE id_pelimpahan = ?";
    } else {
        $query = "UPDATE pembatalan SET is_read_staf = 1 WHERE id_pembatalan = ?";
    }
    
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    
    if ($success) {
        echo "success";
    } else {
        http_response_code(400);
        echo "error: failed to update";
    }
} else {
    http_response_code(400);
    echo "error: missing parameters";
}
?>