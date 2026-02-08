<?php
// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Proses tambah data
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

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Format email tidak valid!');</script>";
    } else {
        // Proses upload foto jika ada
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $target_path = 'uploads_kepala/' . basename($_FILES['foto']['name']);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                $foto = $target_path;
            } else {
                echo "<script>alert('Gagal mengunggah foto!');</script>";
            }
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Masukkan data ke database
        $stmt = $koneksi->prepare("INSERT INTO kepala_seksi (nama_kepala, nip, pangkat, jabatan, pend_terakhir, tempat_lahir, tgl_lahir, alamat, email, username, password, no_telepon, foto) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $nama_kepala, $nip, $pangkat, $jabatan, $pend_terakhir, $tempat_lahir, $tgl_lahir, $alamat, $email, $username, $hashed_password, $no_telepon, $foto);

        if ($stmt->execute()) {
            echo "<script>alert('Data berhasil ditambahkan!'); window.location='dashboard_admin.php?page=manage_kasi';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f9f0;
            margin: 0;
            padding: 0;
        }

        .container, .form-container {
            max-width: 800px;
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

        form input, form textarea, form select, form button {
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
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Tambah Data Kepala Seksi</h1>
        <form method="post" action="" enctype="multipart/form-data">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="flex: 1;">
                    <label>Nama:</label>
                    <input type="text" name="nama_staf" required>
                </div>
                <div style="flex: 1;">
                    <label>NIP:</label>
                    <input type="text" name="nip" required>
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="flex: 1;">
                    <label>Pangkat:</label>
                    <select name="pangkat" required>
                        <option value="" disabled selected>--- Pilih Pangkat ---</option>
                        <optgroup label="Golongan I">
                            <option value="Juru Muda/I a">Juru Muda/I a</option>
                            <option value="Juru Muda Tingkat I/I b">Juru Muda Tingkat I/I b</option>
                            <option value="Juru/I c">Juru/I c</option>
                            <option value="Juru Tingkat I/I d">Juru Tingkat I/I d</option>
                        </optgroup>
                        <optgroup label="Golongan II">
                            <option value="Pengatur Muda/II a">Pengatur Muda/II a</option>
                            <option value="Pengatur Muda Tingkat I/II b">Pengatur Muda Tingkat I/II b</option>
                            <option value="Pengatur/II c">Pengatur/II c</option>
                            <option value="Pengatur Tingkat I/II d">Pengatur Tingkat I/II d</option>
                        </optgroup>
                        <optgroup label="Golongan III">
                            <option value="Penata Muda/III a">Penata Muda/III a</option>
                            <option value="Penata Muda Tingkat I/III b">Penata Muda Tingkat I/III b</option>
                            <option value="Penata/III c">Penata/III c</option>
                            <option value="Penata Tingkat I/III d">Penata Tingkat I/III d</option>
                        </optgroup>
                        <optgroup label="Golongan IV">
                            <option value="Pembina/IV a">Pembina/IV a</option>
                            <option value="Pembina Tingkat I/IV b">Pembina Tingkat I/IV b</option>
                            <option value="Pembina Utama Muda /IV c">Pembina Utama Muda/IV c</option>
                            <option value="Pembina Utama Madya /IV d">Pembina Utama Madya/IV d</option>
                            <option value="Pembina Utama /IV e">Pembina Utama /IV e</option>
                        </optgroup>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Jabatan:</label>
                    <input type="text" name="jabatan" value="Kepala Seksi Penyelenggara Haji dan Umrah" readonly>
                </div>
            </div>

            <label>Pendidikan Terakhir:</label>
            <input type="text" name="pend_terakhir" required>

            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="flex: 1;">
                    <label>Tempat Lahir:</label>
                    <input type="text" name="tempat_lahir" required>
                </div>
                <div style="flex: 1;">
                    <label>Tanggal Lahir:</label>
                    <input type="date" name="tgl_lahir" required>
                </div>
            </div>

            <label>Alamat:</label>
            <textarea name="alamat" required></textarea>

            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="flex: 1;">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div style="flex: 1;">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
            </div>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>No. Telepon:</label>
            <input type="text" name="no_telepon" required>

            <label for="foto">Foto:</label>
            <input type="file" id="foto" name="foto" accept="image/*">

            <button type="submit">Tambah Kasi</button>
        </form>
    </div>
</body>
</html>