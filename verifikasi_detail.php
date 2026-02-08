<?php
include 'koneksi.php'; // Hubungkan ke database

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM pembatalan_ekonomi WHERE id_batal_ekonomi = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Detail</title>
    <style>
        label {
            display: block;
            margin-top: 10px;
        }
        .btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Verifikasi Permohonan</h1>
    <form action="proses_verifikasi.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $data['id_batal_ekonomi']; ?>">
        <p><strong>Nama Jamaah:</strong> <?php echo $data['nama_jamaah']; ?></p>
        <p><strong>Alasan:</strong> <?php echo $data['alasan']; ?></p>

        <label for="status">Status Verifikasi:</label>
        <select name="status_verifikasi" id="status" required>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
        </select>

        <label for="ttd">Unggah Tanda Tangan Kepala Seksi:</label>
        <input type="file" name="ttd_kepala_seksi" id="ttd" accept="image/*" required>

        <button class="btn" type="submit">Simpan</button>
    </form>
</body>
</html>
