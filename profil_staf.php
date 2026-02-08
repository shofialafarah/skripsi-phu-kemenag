<?php
include 'koneksi.php';  // Memastikan koneksi berhasil

// Fungsi untuk mengubah tanggal menjadi format Indonesia
function formatTanggalIndonesia($tanggal) {
    // Set locale ke bahasa Indonesia
    setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'Indonesian_indonesia.1252');
    
    // Pastikan tanggal tidak kosong dan valid
    if ($tanggal && strtotime($tanggal)) {
        return strftime('%d %B %Y', strtotime($tanggal));
    }
    return '-'; // Jika tanggal kosong, tampilkan tanda '-'
}

// Cek apakah parameter 'id' ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada parameter 'id', arahkan ke profil dengan ID 1
    header("Location: profil_staf.php?id=?");
    exit();
}

$id_staf = $_GET['id'];

// Gunakan prepared statement untuk menghindari SQL Injection
$query = "SELECT * FROM staf WHERE id_staf = ?";
$stmt = $koneksi->prepare($query);

if ($stmt === false) {
    echo "Gagal menyiapkan query.";
    exit();
}

// Bind parameter dan eksekusi
$stmt->bind_param("i", $id_staf);
$stmt->execute();

// Ambil hasil query
$result = $stmt->get_result();

// Jika data ditemukan, simpan ke dalam array $staf
if ($result && $result->num_rows > 0) {
    $staf = $result->fetch_assoc();
} else {
    echo "Data staf tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo_kemenag.png">
    <title>Profil Staf - PHU KEMENAG</title>
    <style>
        :root {
            --kemenag-green: #00923F;
            --kemenag-dark: #006128;
            --kemenag-light: #e8f5e9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--kemenag-green);
            color: #333;
            line-height: 1.6;
        }

        .container {
            margin: 2rem auto;
            padding: 0 20px;
        }

        .profile-card {
            max-width: 700px !important;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-content {
            padding: 1.5rem; /* sebelumnya 2rem */
    gap: 1.5rem;     /* lebih rapat */
        }

        .profile-image {
            flex: 0 0 250px;
        }

        .profile-image img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-details {
            flex: 1;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .info-row .info-group {
            flex: 1;
        }

        .info-group {
            margin-bottom: 1px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--kemenag-light);
        }

        .info-group:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--kemenag-green);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.3rem;
        }

        .info-value {
            font-size: 1.1rem;
        }

        .contact-info {
            background-color: var(--kemenag-light);
            padding: 1.5rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .contact-info h3 {
            color: var(--kemenag-green);
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .profile-content {
                flex-direction: column;
            }

            .profile-image {
                flex: 0 0 auto;
                text-align: center;
            }

            .profile-image img {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <div class="profile-content">
                <div class="profile-image">
                    <img src="<?php echo htmlspecialchars($staf['foto']); ?>" alt="Foto Staf">
                    <!-- QR Code -->
                    <?php
                    $id = urlencode($staf['id_staf']);
                    $qr_data = "Nama: {$staf['nama_staf']}\nNIP: {$staf['nip']}\nPosisi: {$staf['posisi']}";
                    $qr_img = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($qr_data);
                    ?>
                    <div style="margin-top: 10px; text-align: center;">
                        <img src="<?php echo $qr_img; ?>" alt="QR Code Profil Staf" style="width: 80px; height: 80px;">
                        <p style="font-size: 0.75rem; color: #555;">Scan Profil</p>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="info-row">
                        <div class="info-group">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['nama_staf']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">NIP</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['nip']); ?></div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-group">
                            <div class="info-label">Pangkat/Golongan</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['pangkat']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Posisi</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['posisi']); ?></div>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Pendidikan Terakhir</div>
                        <div class="info-value"><?php echo htmlspecialchars($staf['pend_terakhir']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Tempat dan Tanggal Lahir</div>
                        <div class="info-value"><?php echo htmlspecialchars($staf['tempat_lahir']); ?>, <?php echo formatTanggalIndonesia($staf['tgl_lahir']); ?></div>
                    </div>

                    <div class="contact-info">
                        <h3>Informasi Kontak</h3>
                        <div class="info-group">
                            <div class="info-label">Alamat</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['alamat']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['email']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['username']); ?></div>
                        </div>

                        <div class="info-group">
                            <div class="info-label">Nomor Telepon</div>
                            <div class="info-value"><?php echo htmlspecialchars($staf['no_telepon']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>