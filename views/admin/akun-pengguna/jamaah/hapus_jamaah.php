<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';

// Hapus jamaah berdasarkan ID
if (isset($_GET['id'])) {
    $id_jamaah = $_GET['id'];

    try {
        $koneksi->begin_transaction();

        // Hapus entri yang bergantung di tabel pendaftaran terlebih dahulu
        $stmt = $koneksi->prepare("DELETE FROM pendaftaran WHERE id_jamaah = ?");
        $stmt->bind_param('s', $id_jamaah);
        $stmt->execute();
        $stmt->close();

        // Lalu hapus jamaah
        $stmt2 = $koneksi->prepare("DELETE FROM jamaah WHERE id_jamaah = ?");
        $stmt2->bind_param('s', $id_jamaah);
        $stmt2->execute();

        if ($stmt2->affected_rows > 0) {
            $koneksi->commit();
            $stmt2->close();
            header('Location: manajemen_jamaah.php?deleted=1');
            exit();
        } else {
            // Tidak ada baris dihapus pada tabel jamaah
            $koneksi->rollback();
            $stmt2->close();
            header('Location: manajemen_jamaah.php?deleted=0');
            exit();
        }
    } catch (Exception $e) {
        if ($koneksi->errno) {
            $koneksi->rollback();
        }
        header('Location: manajemen_jamaah.php?deleted=0');
        exit();
    }
} else {
    header('Location: manajemen_jamaah.php?deleted=0');
    exit();
}
?>
