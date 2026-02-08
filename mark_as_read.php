<?php
include('koneksi.php');

if (isset($_POST['id']) && isset($_POST['type'])) {
    $id = $_POST['id'];
    $type = $_POST['type'];
    
    if ($type === 'pelimpahan') {
        $query = "UPDATE pelimpahan SET is_read = 1 WHERE id_pelimpahan = ?";
    } else {
        $query = "UPDATE pembatalan SET is_read = 1 WHERE id_pembatalan = ?";
    }
    
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo "success";
    } else {
        http_response_code(400);
        echo "error: no rows updated";
    }
} else {
    http_response_code(400);
    echo "error: missing parameters";
}
?>