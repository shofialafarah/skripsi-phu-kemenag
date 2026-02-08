<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data berdasarkan ID
$id_kepala = $_GET['id'];
$stmt = $koneksi->prepare("SELECT * FROM kepala_seksi WHERE id_kepala = ?");
$stmt->bind_param("i", $id_kepala);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kepala = $_POST['nama_kepala'];
    $nip = $_POST['nip'];
    $pangkat = $_POST['pangkat'];
    $jabatan = $_POST['jabatan'];
    $pend_terakhir = $_POST['pend_terakhir'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $no_telepon = $_POST['no_telepon'];
    $foto = $data['foto'];

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Proses upload foto jika ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_path = 'uploads_kepala/' . basename($_FILES['foto']['name']);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                $foto = $target_path;
            } else {
                echo "<script>alert('Gagal mengunggah foto!');</script>";
            }
        }

        // Jika password diisi, hash password baru
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hashed_password = $data['password']; // Tetap gunakan password lama
        }

        // Update data
        $stmt = $koneksi->prepare("UPDATE kepala_seksi SET 
                  nama_kepala = ?, nip = ?, pangkat = ?, jabatan = ?, 
                  pend_terakhir = ?, tempat_lahir = ?, tgl_lahir = ?, alamat = ?, email = ?, 
                  username = ?, password = ?, no_telepon = ?, foto = ? 
                  WHERE id_kepala = ?");
        $stmt->bind_param("sssssssssssssi", $nama_kepala, $nip, $pangkat, $jabatan, $pend_terakhir, $tempat_lahir, $tgl_lahir, $alamat, $email, $username, $hashed_password, $no_telepon, $foto, $id_kepala);

        if ($stmt->execute()) {
            echo "<script> window.location='manajemen_kasi.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data!');</script>";
        }
    }
}

if (isset($_GET['remove_foto']) && $_GET['remove_foto'] == 'true') {
    // Opsional: hapus file lama dari folder kalau tidak default.png
    if (!empty($data['foto']) && file_exists("uploads_kasi/" . $data['foto'])) {
        unlink("uploads/" . $data['foto']);
    }

    // Kosongkan field foto di database
    $stmt = $koneksi->prepare("UPDATE kasi SET foto = '' WHERE id_kepala = ?");
    $stmt->bind_param("i", $id_kepala);
    $stmt->execute();

    // Redirect untuk refresh tampilan
    echo "<script>window.location.href = 'edit_kasi.php?id=$id_kepala';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .edit-kasi {
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
                        <i class="fas fa-table me-1"></i> Manajemen Akun Kepala Seksi
                    </div>
                    <div class="pPendaftaran-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="edit-kasi">
                                <div class="bagian-atas">
                                    <div class="judul-keterangan">
                                        <h2 class="judul-edit"><i class="fas fa-user"></i> Informasi Profil</h2>
                                        <p class="subjudul">Lihat dan ubah informasi profil</p>
                                    </div>
                                    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                                        <button type="button" class="btn-kembali" onclick="window.location.href='manajemen_kasi.php'">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </button>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                    <!-- KIRI: FOTO + UPLOAD -->
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div>
                                            <img id="previewFoto" src="<?= htmlspecialchars($data['foto']) ?: 'default.png'; ?>"
                                                alt="Foto Kasi"
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                                        </div>
                                        <div>
                                            <label for="foto" style="font-weight: bold;">Foto Profil</label>
                                            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                                                <label for="foto" class="btn-upload-foto">Upload</label>
                                                <input type="file" id="foto" name="foto" accept="image/*" onchange="previewGambar(this)" style="display: none;">
                                                <a href="?id=<?= $id_kepala ?>&remove_foto=true"
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
                                        <input type="text" name="nama_kepala" value="<?= htmlspecialchars($data['nama_kepala']) ?>" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>NIP:</label>
                                        <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']) ?>" required>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label>Pangkat:</label>
                                        <select name="pangkat" required>
                                            <option value="" disabled <?= empty($data['pangkat']) ? 'selected' : '' ?>>Pilih Pangkat</option>
                                            <optgroup label="Golongan I">
                                                <option value="Juru Muda/I a" <?= $data['pangkat'] === 'Juru Muda/I a' ? 'selected' : '' ?>>Juru Muda/I a</option>
                                                <option value="Juru Muda Tingkat I/I b" <?= $data['pangkat'] === 'Juru Muda Tingkat I/I b' ? 'selected' : '' ?>>Juru Muda Tingkat I/I b</option>
                                                <option value="Juru/I c" <?= $data['pangkat'] === 'Juru/I c' ? 'selected' : '' ?>>Juru/I c</option>
                                                <option value="Juru Tingkat I/I d" <?= $data['pangkat'] === 'Juru Tingkat I/I d' ? 'selected' : '' ?>>Juru Tingkat I/I d</option>
                                            </optgroup>
                                            <optgroup label="Golongan II">
                                                <option value="Pengatur Muda/II a" <?= $data['pangkat'] === 'Pengatur Muda/II a' ? 'selected' : '' ?>>Pengatur Muda/II a</option>
                                                <option value="Pengatur Muda Tingkat I/II b" <?= $data['pangkat'] === 'Pengatur Muda Tingkat I/II b' ? 'selected' : '' ?>>Pengatur Muda Tingkat I/II b</option>
                                                <option value="Pengatur/II c" <?= $data['pangkat'] === 'Pengatur/II c' ? 'selected' : '' ?>>Pengatur/II c</option>
                                                <option value="Pengatur Tingkat I/II d" <?= $data['pangkat'] === 'Pengatur Tingkat I/II d' ? 'selected' : '' ?>>Pengatur Tingkat I/II d</option>
                                            </optgroup>
                                            <optgroup label="Golongan III">
                                                <option value="Penata Muda/III a" <?= $data['pangkat'] === 'Penata Muda/III a' ? 'selected' : '' ?>>Penata Muda/III a</option>
                                                <option value="Penata Muda Tingkat I/III b" <?= $data['pangkat'] === 'Penata Muda Tingkat I/III b' ? 'selected' : '' ?>>Penata Muda Tingkat I/III b</option>
                                                <option value="Penata/III c" <?= $data['pangkat'] === 'Penata/III c' ? 'selected' : '' ?>>Penata/III c</option>
                                                <option value="Penata Tingkat I/III d" <?= $data['pangkat'] === 'Penata Tingkat I/III d' ? 'selected' : '' ?>>Penata Tingkat I/III d</option>
                                            </optgroup>
                                            <optgroup label="Golongan IV">
                                                <option value="Pembina/IV a" <?= $data['pangkat'] === 'Pembina/IV a' ? 'selected' : '' ?>>Pembina/IV a</option>
                                                <option value="Pembina Tingkat I/IV b" <?= $data['pangkat'] === 'Pembina Tingkat I/IV b' ? 'selected' : '' ?>>Pembina Tingkat I/IV b</option>
                                                <option value="Pembina Utama Muda /IV c" <?= $data['pangkat'] === 'Pembina Utama Muda/IV c' ? 'selected' : '' ?>>Pembina Utama Muda/IV c</option>
                                                <option value="Pembina Utama Madya /IV d" <?= $data['pangkat'] === 'Pembina Utama Madya/IV d' ? 'selected' : '' ?>>Pembina Utama Madya/IV d</option>
                                                <option value="Pembina Utama /IV e" <?= $data['pangkat'] === 'Pembina Utama /IV e' ? 'selected' : '' ?>>Pembina Utama /IV e</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>Jabatan:</label>
                                        <input type="text" name="jabatan" value="<?= htmlspecialchars($data['jabatan']) ?>" readonly>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label>Pendidikan Terakhir:</label>
                                        <select name="pend_terakhir" required>
                                            <option value="" disabled <?= empty($data['pend_terakhir']) ? 'selected' : '' ?>>--- Pilih Pendidikan Terakhir ---</option>
                                            <option value="SMA/SMK" <?= $data['pend_terakhir'] === 'SMA/SMK' ? 'selected' : '' ?>>SMA/SMK</option>
                                            <option value="D3" <?= $data['pend_terakhir'] === 'D3' ? 'selected' : '' ?>>D3</option>
                                            <option value="S1" <?= $data['pend_terakhir'] === 'S1' ? 'selected' : '' ?>>S1</option>
                                            <option value="S2" <?= $data['pend_terakhir'] === 'S2' ? 'selected' : '' ?>>S2</option>
                                            <option value="S3" <?= $data['pend_terakhir'] === 'S3' ? 'selected' : '' ?>>S3</option>
                                        </select>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>No. Telepon:</label>
                                        <input type="text" name="no_telepon" value="<?= htmlspecialchars($data['no_telepon']) ?>" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>Email:</label>
                                        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label>Tempat Lahir:</label>
                                        <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($data['tempat_lahir']) ?>" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>Tanggal Lahir:</label>
                                        <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($data['tgl_lahir']) ?>" required>
                                    </div>
                                </div>

                                <label>Alamat:</label>
                                <textarea name="alamat" required><?= htmlspecialchars($data['alamat']) ?></textarea>

                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1;">
                                        <label>Username:</label>
                                        <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <label>Password:</label>
                                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password.">
                                    </div>
                                </div>
                                <button type="submit" class="btn-simpan-perubahan">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
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
        document.getElementById('previewFoto').src = 'default.png';
    }
</script>

</html>