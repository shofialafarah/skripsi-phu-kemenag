<?php
session_start();
include_once __DIR__ . '/../../../../../includes/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_estimasi = $_POST['id_estimasi'];
    $id_pendaftaran = $_POST['id_pendaftaran'];
    $nomor_porsi = $_POST['nomor_porsi'];
    $tgl_pendaftaran = $_POST['tgl_pendaftaran'];

    // 1. Cek duplikat nomor porsi di tabel pendaftaran (kecuali milik jamaah ini sendiri)
    $cek_porsi = $koneksi->prepare("SELECT id_pendaftaran FROM pendaftaran WHERE nomor_porsi = ? AND id_pendaftaran != ?");
    $cek_porsi->bind_param("si", $nomor_porsi, $id_pendaftaran);
    $cek_porsi->execute();
    $cek_porsi->store_result();

    if ($cek_porsi->num_rows > 0) {
        echo "<script>alert('Nomor porsi sudah digunakan oleh jamaah lain.'); window.history.back();</script>";
        exit();
    }
    $cek_porsi->close();

    // 2. Logika Perhitungan Otomatis (Sesuai tambah_estimasi.php)
    $hariIni = new \DateTime();
    $tglDaftar = new \DateTime($tgl_pendaftaran);

    // Hitung telah menunggu
    $diff = $tglDaftar->diff($hariIni);
    $telah_menunggu = "{$diff->y} tahun, {$diff->m} bulan, {$diff->d} hari";

    // Estimasi berangkat (Masa tunggu 30 tahun sesuai file tambahmu)
    $masa_menunggu = 30;
    $estimasi_berangkat_date = (clone $tglDaftar)->modify("+$masa_menunggu year");
    $estimasi_berangkat = $estimasi_berangkat_date->format('Y-m-d');

    // Sisa menunggu (dalam hari)
    $sisa_menunggu = $hariIni->diff($estimasi_berangkat_date)->days;

    // Ambil tanggal lahir untuk hitung umur
    $query_lahir = $koneksi->prepare("SELECT tanggal_lahir FROM pendaftaran WHERE id_pendaftaran = ?");
    $query_lahir->bind_param("i", $id_pendaftaran);
    $query_lahir->execute();
    $query_lahir->bind_result($tanggal_lahir);
    $query_lahir->fetch();
    $query_lahir->close();

    $tglLahir = new \DateTime($tanggal_lahir);
    $umur = $tglLahir->diff($hariIni)->y;

    // 3. Update Tabel Estimasi
    $stmt = $koneksi->prepare("UPDATE estimasi SET 
        tgl_pendaftaran = ?, 
        telah_menunggu = ?, 
        estimasi_berangkat = ?, 
        umur = ?, 
        sisa_menunggu = ?, 
        masa_menunggu = ? 
        WHERE id_estimasi = ?");

    // Bind parameter (s = string, i = integer)
    // masa_menunggu di file tambahmu disimpan sebagai integer (30)
    $stmt->bind_param("ssssiii", $tgl_pendaftaran, $telah_menunggu, $estimasi_berangkat, $umur, $sisa_menunggu, $masa_menunggu, $id_estimasi);

    if ($stmt->execute()) {
        // 4. Update juga nomor_porsi di tabel pendaftaran
        $stmt_update = $koneksi->prepare("UPDATE pendaftaran SET nomor_porsi = ? WHERE id_pendaftaran = ?");
        $stmt_update->bind_param("si", $nomor_porsi, $id_pendaftaran);
        $stmt_update->execute();

        header("Location: ../../entry_estimasi.php?success=update");
        exit();
    } else {
        echo "Gagal memperbarui data: " . $stmt->error;
    }
}
