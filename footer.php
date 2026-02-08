<?php 
include('koneksi.php');

// Ambil informasi footer dari tabel pengaturan
$result = $koneksi->query("SELECT value FROM pengaturan WHERE key_name='footer_alamat'");
$footer_alamat = $result->fetch_assoc()['value'];

$result = $koneksi->query("SELECT value FROM pengaturan WHERE key_name='footer_email'");
$footer_email = $result->fetch_assoc()['value'];

$result = $koneksi->query("SELECT value FROM pengaturan WHERE key_name='footer_telp'");
$footer_telp = $result->fetch_assoc()['value'];
?>

<link rel="stylesheet" href="footer.css">
<footer class="site-footer">
    <div class="footer-bottom">
        <p>&copy; Shofia Nabila Elfa Rahma. 2110010113.</p>
    </div>
</footer>

</body>
</html>