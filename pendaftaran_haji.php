<?php
include 'koneksi.php';
include 'header.php';
?>

<h2>Data Pendaftaran Haji</h2>
<hr>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="pendaftaran.css">

<!-- Form Pencarian -->
<form method="GET" action="pendaftaran_haji.php">
    <div class="form-group">
        <input type="text" class="form-control" name="search" placeholder="Cari berdasarkan Nama atau NIK" value="<?= isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Cari</button>
</form>
<br>

<div class="form-group">
    <a href="tambah_pendaftaran.php" class="btn btn-primary">Tambah Pendaftaran</a>
</div>
<br>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>No Validasi</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Nama Ayah Kandung</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>No Handphone</th>
                <th>Email</th>
                <th>Pekerjaan</th>
                <th>Pendidikan</th>
                <th>Status Perkawinan</th>
                <th>Golongan Darah</th>
                <th>Status Haji</th>
                <th>Bank</th>
                <th>No Rekening</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Menangkap kata kunci pencarian
            $search = isset($_GET['search']) ? $_GET['search'] : '';

            // Query untuk mengambil data berdasarkan pencarian
            $query = "SELECT * FROM pendaftaran WHERE nama LIKE ? OR nik LIKE ? ORDER BY id DESC";
            $stmt = mysqli_prepare($koneksi, $query);
            $search_term = "%$search%";  // Menambahkan wildcard untuk pencarian fleksibel
            mysqli_stmt_bind_param($stmt, "ss", $search_term, $search_term);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $no = 1;

            // Menampilkan data hasil pencarian
            while ($data = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$data['no_validasi']}</td>
                        <td>{$data['nik']}</td>
                        <td>{$data['nama']}</td>
                        <td>{$data['nama_ayah_kandung']}</td>
                        <td>{$data['tempat_lahir']}</td>
                        <td>{$data['tanggal_lahir']}</td>
                        <td>{$data['jenis_kelamin']}</td>
                        <td>{$data['alamat']}</td>
                        <td>{$data['no_handphone']}</td>
                        <td>{$data['email']}</td>
                        <td>{$data['pekerjaan']}</td>
                        <td>{$data['pendidikan']}</td>
                        <td>{$data['status_perkawinan']}</td>
                        <td>{$data['golongan_darah']}</td>
                        <td>{$data['status_haji']}</td>
                        <td>{$data['bank']}</td>
                        <td>{$data['no_rekening']}</td>
                        <td>
                            <a href='edit_pendaftaran.php?id={$data['id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='hapus_pendaftaran.php?id={$data['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus?')\">Hapus</a>
                            <a href='cetak_pendaftaran.php?id={$data['id']}' class='btn btn-success btn-sm' target='_blank'>Cetak</a>
                        </td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
