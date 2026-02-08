<?php
session_start();
include 'koneksi.php';

// Cek apakah jamaah sudah login
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];

// Cek apakah ada ID pembatalan di URL
if (!isset($_GET['id_pembatalan']) || empty($_GET['id_pembatalan'])) {
    echo "ID pembatalan tidak ditemukan di URL.";
    exit();
}

$id_pembatalan = intval($_GET['id_pembatalan']);

// Ambil data pembatalan berdasarkan ID dan ID jamaah
$query_select = "SELECT * FROM pembatalan WHERE id_pembatalan = ? AND id_jamaah = ?";
$stmt_select = $koneksi->prepare($query_select);
$stmt_select->bind_param("ii", $id_pembatalan, $id_jamaah);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Data tidak ditemukan atau tidak memiliki akses.";
    header("Location: pembatalan_jamaah.php");
    exit();
}

$data = $result->fetch_assoc();

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ahliwaris = $_POST['nama_ahliwaris'] ?? '';
    $tempat_lahir_ahliwaris = $_POST['tempat_lahir_ahliwaris'] ?? '';
    $tanggal_lahir_ahliwaris = $_POST['tanggal_lahir_ahliwaris'] ?? '';
    $jenis_kelamin_ahliwaris = $_POST['jenis_kelamin_ahliwaris'] ?? '';
    $pekerjaan_ahliwaris = $_POST['pekerjaan_ahliwaris'] ?? '';
    $alamat_ahliwaris = $_POST['alamat_ahliwaris'] ?? '';
    $kabupaten_ahliwaris = $_POST['kabupaten_ahliwaris'] ?? '';
    $kecamatan_ahliwaris = $_POST['kecamatan_ahliwaris'] ?? '';
    $kelurahan_ahliwaris = $_POST['kelurahan_ahliwaris'] ?? '';
    $bank_ahliwaris = $_POST['bank_ahliwaris'] ?? '';
    $no_rekening_ahliwaris = $_POST['no_rekening_ahliwaris'] ?? '';
    $no_telepon_ahliwaris = $_POST['no_telepon_ahliwaris'] ?? '';
    $status_dengan_jamaah = $_POST['status_dengan_jamaah'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $alasan = $_POST['alasan'] ?? '';

    // Cek jika file diupload
    $dokumen_name = $data['dokumen']; // default file

    // Jika ada file baru diupload
    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
        $dokumen = $_FILES['dokumen']['name'];
        $unique_name = uniqid() . '.' . pathinfo($dokumen, PATHINFO_EXTENSION);
        $target_dir = "uploads/pendaftaran/pengajuan/";
        $target_file = $target_dir . $unique_name;

        if (move_uploaded_file($_FILES['dokumen']['tmp_name'], $target_file)) {
            // Hapus file lama jika ada
            if (!empty($data['dokumen']) && file_exists($target_dir . $data['dokumen'])) {
                unlink($target_dir . $data['dokumen']);
            }
            $dokumen_name = $target_dir . $unique_name;
        } else {
            $_SESSION['error_message'] = "Gagal mengunggah file baru.";
        }
    }

    $query_update = "UPDATE pembatalan SET
        nama_ahliwaris = ?, tempat_lahir_ahliwaris = ?, tanggal_lahir_ahliwaris = ?, jenis_kelamin_ahliwaris = ?,
        pekerjaan_ahliwaris = ?, alamat_ahliwaris = ?, kabupaten_ahliwaris = ?, kecamatan_ahliwaris = ?, kelurahan_ahliwaris = ?,
        bank_ahliwaris = ?, no_rekening_ahliwaris = ?, no_telepon_ahliwaris = ?, status_dengan_jamaah = ?,
        kategori = ?, alasan = ?, dokumen = ?
        WHERE id_pembatalan = ? AND id_jamaah = ?";

    $stmt_update = $koneksi->prepare($query_update);

    if ($stmt_update === false) {
        die("Prepare statement error: " . $koneksi->error);
    }
    // Binding data
    $types = str_repeat("s", 16) . "ii"; // 16 string + 2 integer
    $stmt_update->bind_param(
        $types,
        $nama_ahliwaris,
        $tempat_lahir_ahliwaris,
        $tanggal_lahir_ahliwaris,
        $jenis_kelamin_ahliwaris,
        $pekerjaan_ahliwaris,
        $alamat_ahliwaris,
        $kabupaten_ahliwaris,
        $kecamatan_ahliwaris,
        $kelurahan_ahliwaris,
        $bank_ahliwaris,
        $no_rekening_ahliwaris,
        $no_telepon_ahliwaris,
        $status_dengan_jamaah,
        $kategori,
        $alasan,
        $dokumen_name,
        $id_pembatalan,
        $id_jamaah
    );

    if ($stmt_update->execute()) {
        $_SESSION['success_message'] = "Data pembatalan berhasil diperbarui.";
        header("Location: pembatalan_jamaah.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data: " . $stmt_update->error;
    }

    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pengajuan Pembatalan</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="global_style.css">
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_jamaah.php'; ?>
        </div>

        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_jamaah.php'; ?>

            <main class="pPembatalan-wrapper">
                <div class="pPembatalan">
                    <div class="pPembatalan-header" style="background-color: #1b5e20; color: white;">
                        <i class="fas fa-edit me-1"></i> Edit Pembatalan Haji
                    </div>
                    <div class="pPembatalan-body" style="color: #1b5e20;">

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger">
                                <?php
                                echo $_SESSION['error_message'];
                                unset($_SESSION['error_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="section-title">Masukkan Data Pribadi</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label>Nama Lengkap</label>

                                    <input type="text" name="nama_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['nama_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin_ahliwaris" id="jenis_kelamin_ahliwaris" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-Laki" <?php echo (strtolower($data['jenis_kelamin_ahliwaris']) == 'Laki-Laki') ? 'selected' : ''; ?>>Laki-Laki</option>
                                        <option value="Perempuan" <?php echo (strtolower($data['jenis_kelamin_ahliwaris']) == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Pekerjaan</label>
                                    <select name="pekerjaan_ahliwaris" id="pekerjaan_ahliwaris" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Pekerjaan --</option>
                                        <option value="Mahasiswa" <?php echo ($data['pekerjaan_ahliwaris'] == 'Mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                                        <option value="Pegawai Negeri" <?php echo ($data['pekerjaan_ahliwaris'] == 'Pegawai Negeri') ? 'selected' : ''; ?>>Pegawai Negeri</option>
                                        <option value="Pegawai Swasta" <?php echo ($data['pekerjaan_ahliwaris'] == 'Pegawai Swasta') ? 'selected' : ''; ?>>Pegawai Swasta</option>
                                        <option value="Ibu Rumah Tangga" <?php echo ($data['pekerjaan_ahliwaris'] == 'Ibu Rumah Tangga') ? 'selected' : ''; ?>>Ibu Rumah Tangga</option>
                                        <option value="Pensiunan" <?php echo ($data['pekerjaan_ahliwaris'] == 'Pensiunan') ? 'selected' : ''; ?>>Pensiunan</option>
                                        <option value="Polri" <?php echo ($data['pekerjaan_ahliwaris'] == 'Polri') ? 'selected' : ''; ?>>Polri</option>
                                        <option value="Pedagang" <?php echo ($data['pekerjaan_ahliwaris'] == 'Pedagang') ? 'selected' : ''; ?>>Pedagang</option>
                                        <option value="Tani" <?php echo ($data['pekerjaan_ahliwaris'] == 'Tani') ? 'selected' : ''; ?>>Tani</option>
                                        <option value="Pegawai BUMN" <?php echo ($data['pekerjaan_ahliwaris'] == 'Pegawai BUMN') ? 'selected' : ''; ?>>Pegawai BUMN</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>No. Telepon</label>
                                    <input type="number" name="no_telepon_ahliwaris" class="form-control" value="<?php echo $data['no_telepon_ahliwaris']; ?>">
                                </div>


                                <div class="col-md-4">
                                    <label>Nama Bank</label>
                                    <input type="text" name="bank_ahliwaris" class="form-control" value="<?php echo $data['bank_ahliwaris']; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label>No. Rekening</label>
                                    <input type="text" name="no_rekening_ahliwaris" class="form-control" value="<?php echo $data['no_rekening_ahliwaris']; ?>">
                                </div>

                                <div class="col-md-4">
                                    <label>Status dengan Jamaah</label>
                                    <select name="status_dengan_jamaah" id="status_dengan_jamaah" class="select-daftar" required>
                                        <option value="" disabled selected>--- Pilih Status ---</option>
                                        <option value="Suami" <?php echo ($data['status_dengan_jamaah'] == 'Suami') ? 'selected' : ''; ?>>Suami</option>
                                        <option value="Istri" <?php echo ($data['status_dengan_jamaah'] == 'Istri') ? 'selected' : ''; ?>>Istri</option>
                                        <option value="Orang Tua Kandung" <?php echo ($data['status_dengan_jamaah'] == 'Orang Tua Kandung') ? 'selected' : ''; ?>>Orang Tua Kandung</option>
                                        <option value="Anak Kandung" <?php echo ($data['status_dengan_jamaah'] == 'Anak Kandung') ? 'selected' : ''; ?>>Anak Kandung</option>
                                        <option value="Saudara Kandung" <?php echo ($data['status_dengan_jamaah'] == 'Saudara Kandung') ? 'selected' : ''; ?>>Saudara Kandung</option>
                                    </select>
                                </div>
                            </div>
                            <!-- ================================================================================================= -->
                            <div class="section-title">Masukkan Data Tempat Tinggal Sekarang</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label>Alamat Tinggal</label>
                                    <input type="text" name="alamat_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['alamat_ahliwaris']); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label>Kabupaten/Kota</label>
                                    <select name="kabupaten_ahliwaris" id="kabupaten_ahliwaris" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Kabupaten/Kota --</option>
                                        <option value="Banjarmasin" <?php echo ($data['kabupaten_ahliwaris'] == 'Banjarmasin') ? 'selected' : ''; ?>>Kota Banjarmasin</option>
                                        <option value="Banjarbaru" <?php echo ($data['kabupaten_ahliwaris'] == 'Banjarbaru') ? 'selected' : ''; ?>>Kota Banjarbaru</option>
                                        <option value="Kabupaten Banjar" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Banjar') ? 'selected' : ''; ?>>Kabupaten Banjar</option>
                                        <option value="Kabupaten Barito Kuala" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Barito Kuala') ? 'selected' : ''; ?>>Kabupaten Barito Kuala</option>
                                        <option value="Kabupaten Tapin" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Tapin') ? 'selected' : ''; ?>>Kabupaten Tapin</option>
                                        <option value="Kabupaten Hulu Sungai Selatan" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Hulu Sungai Selatan') ? 'selected' : ''; ?>>Kabupaten Hulu Sungai Selatan</option>
                                        <option value="Kabupaten Hulu Sungai Tengah" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Hulu Sungai Tengah') ? 'selected' : ''; ?>>Kabupaten Hulu Sungai Tengah</option>
                                        <option value="Kabupaten Hulu Sungai Utara" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Hulu Sungai Utara') ? 'selected' : ''; ?>>Kabupaten Hulu Sungai Utara</option>
                                        <option value="Kabupaten Balangan" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Balangan') ? 'selected' : ''; ?>>Kabupaten Balangan</option>
                                        <option value="Kabupaten Tabalong" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Tabalong') ? 'selected' : ''; ?>>Kabupaten Tabalong</option>
                                        <option value="Kabupaten Tanah Laut" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Tanah Laut') ? 'selected' : ''; ?>>Kabupaten Tanah Laut</option>
                                        <option value="Kabupaten Tanah Bumbu" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Tanah Bumbu') ? 'selected' : ''; ?>>Kabupaten Tanah Bumbu</option>
                                        <option value="Kabupaten Kotabaru" <?php echo ($data['kabupaten_ahliwaris'] == 'Kabupaten Kotabaru') ? 'selected' : ''; ?>>Kabupaten Kotabaru</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Kecamatan</label>
                                    <select name="kecamatan_ahliwaris" id="kecamatan_ahliwaris" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Kecamatan --</option>
                                        <option value="Aluh-Aluh" <?php echo ($data['kecamatan_ahliwaris'] == 'Aluh-Aluh') ? 'selected' : ''; ?>>Aluh-Aluh</option>
                                        <option value="Aranio" <?php echo ($data['kecamatan_ahliwaris'] == 'Aranio') ? 'selected' : ''; ?>>Aranio</option>
                                        <option value="Astambul" <?php echo ($data['kecamatan_ahliwaris'] == 'Astambul') ? 'selected' : ''; ?>>Astambul</option>
                                        <option value="Beruntung Baru" <?php echo ($data['kecamatan_ahliwaris'] == 'Beruntung Baru') ? 'selected' : ''; ?>>Beruntung Baru</option>
                                        <option value="Cintapuri Darussalam" <?php echo ($data['kecamatan_ahliwaris'] == 'Cintapuri Darussalam') ? 'selected' : ''; ?>>Cintapuri Darussalam</option>
                                        <option value="Gambut" <?php echo ($data['kecamatan_ahliwaris'] == 'Gambut') ? 'selected' : ''; ?>>Gambut</option>
                                        <option value="Karang Intan" <?php echo ($data['kecamatan_ahliwaris'] == 'Karang Intan') ? 'selected' : ''; ?>>Karang Intan</option>
                                        <option value="Kertak Hanyar" <?php echo ($data['kecamatan_ahliwaris'] == 'Kertak Hanyar') ? 'selected' : ''; ?>>Kertak Hanyar</option>
                                        <option value="Mataraman" <?php echo ($data['kecamatan_ahliwaris'] == 'Mataraman') ? 'selected' : ''; ?>>Mataraman</option>
                                        <option value="Martapura" <?php echo ($data['kecamatan_ahliwaris'] == 'Martapura') ? 'selected' : ''; ?>>Martapura</option>
                                        <option value="Martapura Barat" <?php echo ($data['kecamatan_ahliwaris'] == 'Martapura Barat') ? 'selected' : ''; ?>>Martapura Barat</option>
                                        <option value="Martapura Timur" <?php echo ($data['kecamatan_ahliwaris'] == 'Martapura Timur') ? 'selected' : ''; ?>>Martapura Timur</option>
                                        <option value="Paramasan" <?php echo ($data['kecamatan_ahliwaris'] == 'Paramasan') ? 'selected' : ''; ?>>Paramasan</option>
                                        <option value="Pengaron" <?php echo ($data['kecamatan_ahliwaris'] == 'Pengaron') ? 'selected' : ''; ?>>Pengaron</option>
                                        <option value="Sambung Makmur" <?php echo ($data['kecamatan_ahliwaris'] == 'Sambung Makmur') ? 'selected' : ''; ?>>Sambung Makmur</option>
                                        <option value="Simpang Empat" <?php echo ($data['kecamatan_ahliwaris'] == 'Simpang Empat') ? 'selected' : ''; ?>>Simpang Empat</option>
                                        <option value="Sungai Pinang" <?php echo ($data['kecamatan_ahliwaris'] == 'Sungai Pinang') ? 'selected' : ''; ?>>Sungai Pinang</option>
                                        <option value="Sungai Tabuk" <?php echo ($data['kecamatan_ahliwaris'] == 'Sungai Tabuk') ? 'selected' : ''; ?>>Sungai Tabuk</option>
                                        <option value="Tatah Makmur" <?php echo ($data['kecamatan_ahliwaris'] == 'Tatah Makmur') ? 'selected' : ''; ?>>Tatah Makmur</option>
                                        <option value="Telaga Bauntung" <?php echo ($data['kecamatan_ahliwaris'] == 'Telaga Bauntung') ? 'selected' : ''; ?>>Telaga Bauntung</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Kelurahan/Desa</label>
                                    <select name="kelurahan_ahliwaris" id="kelurahan_ahliwaris" class="select-daftar" required>
                                        <option value="<?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?>" selected><?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?></option>
                                    </select>
                                </div>
                            </div>
                            <!-- ================================================================================================ -->
                            <div class="section-title">Keterangan Pembatalan</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Kategori</label>
                                    <select name="kategori" id="kategori" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                                        <option value="Keperluan Ekonomi" <?php echo (strtolower($data['kategori']) == 'Keperluan Ekonomi') ? 'selected' : ''; ?>>Keperluan Ekonomi</option>
                                        <option value="Meninggal Dunia" <?php echo (strtolower($data['kategori']) == 'Meninggal Dunia') ? 'selected' : ''; ?>>Meninggal Dunia</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Alasan</label>
                                    <input type="text" name="alasan" class="form-control" value="<?php echo $data['alasan']; ?>">
                                </div>
                            </div>
                            <div class="section-title">Upload Berkas Pembatalan</div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Dokumen</label><br>
                                <?php if (!empty($data['dokumen'])): ?>
                                    <small>File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen']); ?>" target="_blank"><?php echo basename($data['dokumen']); ?></a></small><br>
                                <?php else: ?>
                                    <small>Belum ada file yang diupload</small><br>
                                <?php endif; ?>
                                <input type="file" name="dokumen" class="form-control">
                            </div>

                            <div>
                                <button type="submit" class="btn btn-success">PERBARUI</button>
                                <a href="pembatalan_jamaah.php" class="btn btn-secondary">BATAL</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- DataTables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Pastikan file JS kustom dimuat setelah semua library -->
    <script src="pendaftaran_jamaah.js"></script>
    <script src="tambah_data_ahliwaris.js"></script>
</body>

</html>