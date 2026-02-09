<?php
include 'koneksi.php'; // pastikan koneksi database terhubung

function updateAktivitasPengguna($id_pengguna, $role, $tipe_aktivitas, $deskripsi) {
    global $koneksi;

    $table_map = [
        'jamaah' => 'id_jamaah',
        'staf' => 'id_staf',
        'kepala_seksi' => 'id_kepala'
    ];

    if (!isset($table_map[$role])) return;

    $id_field = $table_map[$role];
    $stmt = $koneksi->prepare("UPDATE $role SET tipe_aktivitas = ?, deskripsi_aktivitas = ?, waktu_aktivitas = NOW() WHERE $id_field = ?");
    $stmt->bind_param("ssi", $tipe_aktivitas, $deskripsi, $id_pengguna);
    $stmt->execute();
}
