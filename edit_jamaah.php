<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data berdasarkan ID
$id_jamaah = $_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM jamaah WHERE id_jamaah = ?");
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $validasi_bank = $_POST['validasi_bank'];
    $nomor_telepon = $_POST['nomor_telepon'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $foto = $data['foto'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Proses upload foto jika ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $file_tmp = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $file_size = $_FILES['foto']['size'];
            $file_type = $_FILES['foto']['type'];

            // Validasi tipe file
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file_type, $allowed_types)) {
                echo "<script>alert('Format file harus JPG atau PNG!');</script>";
            } else if ($file_size > 10 * 1024 * 1024) { // 10MB
                echo "<script>alert('Ukuran file maksimal 10MB!');</script>";
            } else {
                // Buat direktori jika belum ada
                $upload_dir = 'uploads/jamaah/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                // Dapatkan ekstensi file
                $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

                // Buat nama file baru: Staf_username.extension
                $new_filename = 'Jamaah_' . $username . '.' . $file_extension;
                $target_path = $upload_dir . $new_filename;

                // Hapus foto lama jika ada dan berbeda dengan foto baru
                if (!empty($data['foto']) && file_exists($data['foto']) && $data['foto'] != $target_path) {
                    unlink($data['foto']);
                }

                // Upload file baru
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $foto = $target_path;
                } else {
                    echo "<script>alert('Gagal mengunggah foto!');</script>";
                }
            }
        }

        // Update data
        $stmt = $koneksi->prepare("UPDATE jamaah SET 
                  nama = ?, validasi_bank = ?, nomor_telepon = ?, email = ?,  
                  username = ?, foto = ? 
                  WHERE id_jamaah = ?");
        $stmt->bind_param("ssssssi", $nama, $validasi_bank, $nomor_telepon, $email, $username, $foto, $id_jamaah);

        if ($stmt->execute()) {
            echo "<script> window.location='manajemen_jamaah.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data!');</script>";
        }
    }
}

// Hapus foto
if (isset($_GET['remove_foto']) && $_GET['remove_foto'] == 'true') {
    if (!empty($data['foto']) && file_exists($data['foto'])) {
        unlink($data['foto']);
    }
    // Kosongkan field foto di database
    $stmt = $koneksi->prepare("UPDATE jamaah SET foto = '' WHERE id_jamaah = ?");
    $stmt->bind_param("i", $id_jamaah);
    $stmt->execute();

    // Redirect untuk refresh tampilan
    echo "<script>window.location.href = 'edit_jamaah.php?id=$id_jamaah';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        .edit-jamaah {
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border: 2px solid #4caf50;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4caf50;
        }

        form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        form input,
        form textarea,
        form select,
        form button {
            width: 100%;
            margin-top: 5px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form button {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
            border: none;
            margin-top: 20px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }

        .btn-upload-foto {
            background-color: rgb(214, 217, 219);
            padding: 6px 12px;
            font-size: 0.8rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
        }

        .btn-upload-foto:hover {
            background-color: rgb(126, 129, 131);
        }

        .btn-hapus-foto {
            color: black;
            padding: 6px 12px;
            font-size: 0.8rem;
            border: 1px solid;
            border-color: #555;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-hapus-foto:hover {
            background-color: #d32f2f;
        }

        .bagian-atas {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .judul-keterangan {
            display: flex;
            flex-direction: column;
        }

        .judul-edit {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .subjudul {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }

        .btn-simpan-perubahan {
            background-color: #4caf50;
            color: white;
            padding: 10px 16px;
            font-size: 0.9rem;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-simpan-perubahan:hover {
            background-color: #388e3c;
        }

        .btn-kembali {
            background-color: #f44336;
            /* Merah lembut */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-kembali:hover {
            background-color: #d32f2f;
            /* Merah sedikit lebih gelap saat hover */
        }
    </style>
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_admin.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_admin.php'; ?>

            <main class="pPendaftaran-wrapper">
                <div class="pPendaftaran">
                    <div class="pPendaftaran-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Manajemen Akun Jamaah PHU
                    </div>
                    <div class="pPendaftaran-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="edit-jamaah">
                                <div class="bagian-atas">
                                    <div class="judul-keterangan">
                                        <h2 class="judul-edit"><i class="fas fa-user"></i> Informasi Profil</h2>
                                        <p class="subjudul">Lihat dan ubah informasi profil</p>
                                    </div>
                                    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                                        <button type="button" class="btn-kembali" onclick="window.location.href='manajemen_jamaah.php'">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </button>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                    <!-- KIRI: FOTO + UPLOAD -->
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div>
                                            <?php
                                            // Tampilkan foto sesuai dengan path yang benar
                                            if (!empty($data['foto']) && file_exists($data['foto'])) {
                                                $foto = $data['foto'];
                                            } else {
                                                $foto = 'uploads/jamaah/profil.jpg';
                                            }
                                            ?>
                                            <img id="previewFoto" src="<?= htmlspecialchars($foto) ?>"
                                                alt="Foto Jamaah"
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                        </div>
                                        <div>
                                            <label for="foto" style="font-weight: bold;">Foto Profil</label>
                                            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                                <label for="foto" class="btn-upload-foto">Upload</label>
                                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewGambar(this)" style="display: none;">
                                                <a href="?id=<?= $id_jamaah ?>&remove_foto=true"
                                                    onclick="return confirm('Yakin ingin menghapus foto ini?');"
                                                    class="btn-hapus-foto">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                            <p style="font-size: 0.75rem; color: #555;">JPG, PNG, max 10MB</p>
                                        </div>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label>Nama:</label>
                                        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>No. Validasi:</label>
                                        <input type="text" name="validasi_bank" value="<?= htmlspecialchars($data['validasi_bank']) ?>" required>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label>No. Telepon:</label>
                                        <input type="text" name="nomor_telepon" value="<?= htmlspecialchars($data['nomor_telepon']) ?>" required>

                                    </div>
                                    <div style="flex: 1;">
                                        <label>Email:</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>Username:</label>
                                        <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn-simpan-perubahan">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function previewGambar(input) {
            const file = input.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 10 * 1024 * 1024; // 10MB

                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    alert("Format file harus JPG atau PNG!");
                    input.value = "";
                    return;
                }

                // Validasi ukuran file
                if (file.size > maxSize) {
                    alert("Ukuran gambar maksimal 10MB!");
                    input.value = "";
                    return;
                }

                // Preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function setDefaultFoto() {
            document.getElementById('previewFoto').src = 'uploads/jamaah/profil.jpg';
        }
    </script>
</body>

</html>