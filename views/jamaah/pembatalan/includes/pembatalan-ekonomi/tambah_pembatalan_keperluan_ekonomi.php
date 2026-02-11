<?php
session_start();
include '../../../../../includes/koneksi.php';
include '../../../../partials/fungsi.php';

// Cek apakah jamaah sudah login
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];

// Proses form ketika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data pribadi
    $nama_jamaah = $_POST['nama_jamaah'] ?? '';
    $tempat_lahir = $_POST['tempat_lahir'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $pekerjaan = $_POST['pekerjaan'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';
    $bps = $_POST['bps'] ?? '';
    $nomor_rek = $_POST['nomor_rek'] ?? '';
    $spph_validasi = $_POST['spph_validasi'] ?? '';
    $status_hubungan = $_POST['status_hubungan'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $kecamatan = $_POST['kecamatan'] ?? '';
    $kelurahan = $_POST['kelurahan'] ?? '';
    $kode_pos = $_POST['kode_pos'] ?? '';

    // Fungsi Upload
    function uploadFile($input_name, $folder)
    {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            $target_dir = "uploads/pembatalan/pengajuan/" . $folder . "/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_tmp = $_FILES[$input_name]['tmp_name'];
            $file_ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $unique_file_name = uniqid($folder . "_") . '.' . $file_ext;
            $target_path = $target_dir . $unique_file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                return $target_path;
            }
        }
        return null;
    }

    // Upload dokumen
    $dokumen_setor_awal = uploadFile('dokumen_setor_awal', 'setor_awal');
    $dokumen_spph = uploadFile('dokumen_spph', 'spph');
    $dokumen_ktp = uploadFile('dokumen_ktp', 'ktp');
    $dokumen_kk = uploadFile('dokumen_kk', 'kk');
    $dokumen_akta_kelahiran = uploadFile('dokumen_akta_kelahiran', 'akta_kelahiran');
    $dokumen_rekening = uploadFile('dokumen_rekening', 'rekening');
    $foto_wajah = uploadFile('foto_wajah', 'foto');

    // Insert data ke tabel pembatalan terlebih dahulu
    $stmt_pembatalan = $koneksi->prepare("INSERT INTO pembatalan (id_jamaah, kategori, tanggal_pengajuan, status_dokumen) VALUES (?, 'Keperluan Ekonomi', NOW(), 'pending')");
    $stmt_pembatalan->bind_param("i", $id_jamaah);

    if ($stmt_pembatalan->execute()) {
        $id_pembatalan = $koneksi->insert_id;

        // Insert data ke tabel pembatalan_ekonomi
        $stmt_insert = $koneksi->prepare("INSERT INTO pembatalan_ekonomi 
    (id_pembatalan, nama_jamaah, tempat_lahir, tanggal_lahir, jenis_kelamin, 
     pekerjaan, no_telepon, bps, nomor_rek, spph_validasi, alamat, kecamatan, 
     kelurahan, kode_pos, dokumen_setor_awal, dokumen_spph, dokumen_ktp, 
     dokumen_kk, dokumen_akta_kelahiran, dokumen_rekening, foto_wajah) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt_insert->bind_param(
            'issssssssssssssssssss',
            $id_pembatalan,
            $nama_jamaah,
            $tempat_lahir,
            $tanggal_lahir,
            $jenis_kelamin,
            $pekerjaan,
            $no_telepon,
            $bps,
            $nomor_rek,
            $spph_validasi,
            $alamat,
            $kecamatan,
            $kelurahan,
            $kode_pos,
            $dokumen_setor_awal,
            $dokumen_spph,
            $dokumen_ktp,
            $dokumen_kk,
            $dokumen_akta_kelahiran,
            $dokumen_rekening,
            $foto_wajah
        );

        if ($stmt_insert->execute()) {
            // ✅ Catat aktivitas hanya jika data berhasil ditemukan
            updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pembatalan', 'Menambahkan data pembatalan keperluan ekonomi');
            $_SESSION['success_message'] = "Data pembatalan berhasil ditambahkan.";
            header("Location: pembatalan_jamaah_ekonomi.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan data pembatalan ekonomi: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan data pembatalan: " . $stmt_pembatalan->error;
    }

    $stmt_pembatalan->close();
}
?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once '../../../includes/sidebar_jamaah.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once '../../../includes/header_jamaah.php'; ?>

            <main class="pPembatalan-wrapper">
                <div class="pPembatalan">
                    <div class="pPembatalan-header" style="background-color: #1b5e20; color: white;">
                        <i class="fas fa-plus me-1"></i> Tambah Pembatalan Haji - Keperluan Ekonomi
                    </div>
                    <div class="pPembatalan-body" style="color: #1b5e20;">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="section-title">Masukkan Data Jamaah</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap Jamaah</label>
                                    <input type="text" name="nama_jamaah" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nama Ayah</label>
                                    <input type="text" name="bin_binti" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="jenis_kelamin" class="select-daftar" required>
                                        <option value="" disable selected>-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <select name="pekerjaan" id="pekerjaan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                        <option value="Mahasiswa">Mahasiswa</option>
                                        <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                        <option value="Pensiunan">Pensiunan</option>
                                        <option value="Polri">Polri</option>
                                        <option value="Pedagang">Pedagang</option>
                                        <option value="Tani">Tani</option>
                                        <option value="Pegawai BUMN">Pegawai BUMN</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="number" name="no_telepon" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Bank</label>
                                    <select name="bps" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih Bank Syariah di Kalsel --</option>
                                        <option value="451">Bank Syariah Indonesia (451)</option>
                                        <option value="147">Bank Muamalat (147)</option>
                                        <option value="506">Bank Mega Syariah (506)</option>
                                        <option value="521">Bank Syariah Bukopin (521)</option>
                                        <option value="011">Bank Danamon (011)</option>
                                        <option value="122">BPD Kalsel Syariah (122)</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">No. Rekening</label>
                                    <input type="text" name="nomor_rek" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">No. Validasi Bank</label>
                                    <input type="text" name="spph_validasi" class="form-control" required>
                                </div>
                            </div>
                            <!-- ================================================================================================= -->
                            <div class="section-title">Masukkan Data Tempat Tinggal Sekarang</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Alamat Tinggal</label>
                                    <input type="text" name="alamat" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kecamatan</label>
                                    <select name="kecamatan" id="kecamatan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Kecamatan --</option>
                                        <option value="Aluh-Aluh">Aluh-Aluh</option>
                                        <option value="Aranio">Aranio</option>
                                        <option value="Astambul">Astambul</option>
                                        <option value="Beruntung Baru">Beruntung Baru</option>
                                        <option value="Cintapuri Darussalam">Cintapuri Darussalam</option>
                                        <option value="Gambut">Gambut</option>
                                        <option value="Karang Intan">Karang Intan</option>
                                        <option value="Kertak Hanyar">Kertak Hanyar</option>
                                        <option value="Mataraman">Mataraman</option>
                                        <option value="Martapura">Martapura</option>
                                        <option value="Martapura Barat">Martapura Barat</option>
                                        <option value="Martapura Timur">Martapura Timur</option>
                                        <option value="Paramasan">Paramasan</option>
                                        <option value="Pengaron">Pengaron</option>
                                        <option value="Sambung Makmur">Sambung Makmur</option>
                                        <option value="Simpang Empat">Simpang Empat</option>
                                        <option value="Sungai Pinang">Sungai Pinang</option>
                                        <option value="Sungai Tabuk">Sungai Tabuk</option>
                                        <option value="Tatah Makmur">Tatah Makmur</option>
                                        <option value="Telaga Bauntung">Telaga Bauntung</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kelurahan/Desa</label>
                                    <select name="kelurahan" id="kelurahan" class="select-daftar" required>
                                        <option value="" disabled selected>-- Pilih Kelurahan --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos" class="form-control" required>
                                </div>
                            </div>
                            <!-- ================================================================================================= -->
                            <div class="section-title">Upload Berkas Pembatalan - Keperluan Ekonomi</div>
                            <hr>
                            <div class="row g-3">

                                <!-- Bukti Setor Awal BIPH -->
                                <div class="col-md-4">
                                    <label class="form-label">Bukti Setor Awal BIPH (PDF)</label>
                                    <input type="file" name="dokumen_setor_awal" class="form-control" accept="application/pdf" required>
                                </div>

                                <!-- SPPH -->
                                <div class="col-md-4">
                                    <label class="form-label">SPPH (PDF)</label>
                                    <input type="file" name="dokumen_spph" class="form-control" accept="application/pdf" required>
                                </div>


                                <!-- KTP -->
                                <div class="col-md-4">
                                    <label class="form-label">KTP (PDF)</label>
                                    <input type="file" name="dokumen_ktp" class="form-control" accept="application/pdf" required>
                                </div>
                                <!-- Kartu Keluarga -->
                                <div class="col-md-4">
                                    <label class="form-label">Kartu Keluarga (PDF)</label>
                                    <input type="file" name="dokumen_kk" class="form-control" accept="application/pdf" required>
                                </div>

                                <!-- Akta Kelahiran -->
                                <div class="col-md-4">
                                    <label class="form-label">Akta Kelahiran (PDF)</label>
                                    <input type="file" name="dokumen_akta_kelahiran" class="form-control" accept="application/pdf" required>
                                </div>

                                <!-- Buku Rekening Haji -->
                                <div class="col-md-4">
                                    <label class="form-label">Buku Rekening Haji (PDF)</label>
                                    <input type="file" name="dokumen_rekening" class="form-control" accept="application/pdf" required>
                                </div>
                                <!-- Foto Wajah -->
                                <div class="col-md-12">
                                    <label class="form-label">Foto Wajah 80% (JPG/PNG)</label>
                                    <input type="file" name="foto_wajah" class="form-control" accept="image/jpeg, image/png" required>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn btn-success">TAMBAH PEMBATALAN</button>
                            </div>
                        </form>
                        <div class="footer" style="color: white; text-align: center;">
                            <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../../../assets/js/sidebar.js"></script>
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
    <script src="tambah_data_kelurahan.js"></script>
</body>

</html>