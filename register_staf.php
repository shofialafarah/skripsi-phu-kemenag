<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    die("Akses ditolak. Hanya admin yang dapat menambahkan staf.");
}

// Koneksi ke database
$koneksi = new mysqli('localhost', 'root', '', 'aplikasi_haji');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $role = $_POST['role']; // Role yang dipilih (admin atau staf)

    // Cek apakah username sudah ada di tabel staf
    $checkUserQuery = "SELECT id_staf FROM staf WHERE username = ?";
    $stmt = $koneksi->prepare($checkUserQuery);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username sudah terdaftar!";
    } else {
        // Menyimpan data baru staf ke database
        $sql = "INSERT INTO staf (username, password, role) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            echo "Registrasi staf berhasil! Silakan login.";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    $stmt->close();
    $koneksi->close();
}
?>

<form method="POST" action="">
    <label>Username:</label>
    <input type="text" name="username" required>
    <br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br>
    <label>Role:</label>
    <select name="role">
        <option value="admin">Administrator</option>
        <option value="staf">Staf</option>
    </select>
    <br>
    <button type="submit">Register Staf</button>
</form>
