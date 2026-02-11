<?php
session_start();
include 'koneksi.php';
include 'fungsi.php';

if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

$id_jamaah = $_SESSION['id_jamaah'];
$id_pembatalan = $_GET['id_pembatalan'] ?? $_POST['id_pembatalan'] ?? null;

if (!$id_pembatalan) {
    die("ID pembatalan tidak ditemukan.");
}

// Ambil data lama dari database
$result = $koneksi->query("SELECT * FROM pembatalan_meninggal WHERE id_pembatalan = $id_pembatalan AND id_pembatalan IN (SELECT id_pembatalan FROM pembatalan WHERE id_jamaah = $id_jamaah)");

if (!$result || $result->num_rows == 0) {
    die("Data tidak ditemukan atau akses ditolak.");
}
$data = $result->fetch_assoc();

// Fungsi upload
function uploadFile($input_name, $folder, $old_path = null)
{
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../assets/berkas/pengajuan/$folder/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_tmp = $_FILES[$input_name]['tmp_name'];
        $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
        $new_name = uniqid($folder . "_") . '.' . $ext;
        $target_path = $target_dir . $new_name;

        if (move_uploaded_file($file_tmp, $target_path)) {
            if ($old_path && file_exists($old_path)) {
                unlink($old_path);
            }
            return $target_path;
        }
    }
    return $old_path;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data ahli waris
    $nama_ahliwaris = $_POST['nama_ahliwaris'] ?? '';
    $tempat_lahir_ahliwaris = $_POST['tempat_lahir_ahliwaris'] ?? '';
    $tanggal_lahir_ahliwaris = $_POST['tanggal_lahir_ahliwaris'] ?? '';
    $jenis_kelamin_ahliwaris = $_POST['jenis_kelamin_ahliwaris'] ?? '';
    $pekerjaan_ahliwaris = $_POST['pekerjaan_ahliwaris'] ?? '';
    $no_telepon_ahliwaris = $_POST['no_telepon_ahliwaris'] ?? '';
    $bank_ahliwaris = $_POST['bank_ahliwaris'] ?? '';
    $no_rekening_ahliwaris = $_POST['no_rekening_ahliwaris'] ?? '';
    $status_dengan_jamaah = $_POST['status_dengan_jamaah'] ?? '';
    $alamat_ahliwaris = $_POST['alamat_ahliwaris'] ?? '';
    $kecamatan_ahliwaris = $_POST['kecamatan_ahliwaris'] ?? '';
    $kelurahan_ahliwaris = $_POST['kelurahan_ahliwaris'] ?? '';
    $kode_pos_ahliwaris = $_POST['kode_pos_ahliwaris'] ?? '';


    // ✅ Upload dokumen (gunakan file lama jika tidak ada upload baru)
    $dokumen_akta_kematian = uploadFile('dokumen_akta_kematian', 'akta_kematian', $data['dokumen_akta_kematian']);
    $dokumen_setor_awal = uploadFile('dokumen_setor_awal', 'setor_awal', $data['dokumen_setor_awal']);
    $dokumen_spph = uploadFile('dokumen_spph', 'spph', $data['dokumen_spph']);
    $dokumen_ahli_waris = uploadFile('dokumen_ahli_waris', 'ahli_waris', $data['dokumen_ahli_waris']);
    $dokumen_surat_kuasa = uploadFile('dokumen_surat_kuasa', 'surat_kuasa', $data['dokumen_surat_kuasa']);
    $dokumen_ktp_ahliwaris = uploadFile('dokumen_ktp_ahliwaris', 'ktp_ahliwaris', $data['dokumen_ktp_ahliwaris']);
    $dokumen_ktp_penerima_kuasa = uploadFile('dokumen_ktp_penerima_kuasa', 'ktp_penerima_kuasa', $data['dokumen_ktp_penerima_kuasa']);
    $dokumen_kk_penerima_kuasa = uploadFile('dokumen_kk_penerima_kuasa', 'kk_penerima_kuasa', $data['dokumen_kk_penerima_kuasa']);
    $dokumen_akta_kelahiran = uploadFile('dokumen_akta_kelahiran', 'akta_kelahiran', $data['dokumen_akta_kelahiran']);
    $dokumen_buku_nikah = uploadFile('dokumen_buku_nikah', 'buku_nikah', $data['dokumen_buku_nikah']);
    $dokumen_rekening_kuasa = uploadFile('dokumen_rekening_kuasa', 'rekening_kuasa', $data['dokumen_rekening_kuasa']);
    $foto_wajah = uploadFile('foto_wajah', 'foto', $data['foto_wajah']);


    // Update query
    $stmt = $koneksi->prepare("UPDATE pembatalan_meninggal SET 
        nama_ahliwaris = ?, tempat_lahir_ahliwaris = ?, tanggal_lahir_ahliwaris = ?, 
        jenis_kelamin_ahliwaris = ?, pekerjaan_ahliwaris = ?, no_telepon_ahliwaris = ?, 
        bank_ahliwaris = ?, no_rekening_ahliwaris = ?, status_dengan_jamaah = ?, 
        alamat_ahliwaris = ?, kecamatan_ahliwaris = ?, kelurahan_ahliwaris = ?, 
        kode_pos_ahliwaris = ?, dokumen_akta_kematian = ?, dokumen_setor_awal = ?, 
        dokumen_spph = ?, dokumen_ahli_waris = ?, dokumen_surat_kuasa = ?, 
        dokumen_ktp_ahliwaris = ?, dokumen_ktp_penerima_kuasa = ?, dokumen_kk_penerima_kuasa = ?, 
        dokumen_akta_kelahiran = ?, dokumen_buku_nikah = ?, dokumen_rekening_kuasa = ?, foto_wajah = ?
        WHERE id_pembatalan = ?");

    $stmt->bind_param(
        'sssssssssssssssssssssssssi',
        $nama_ahliwaris,
        $tempat_lahir_ahliwaris,
        $tanggal_lahir_ahliwaris,
        $jenis_kelamin_ahliwaris,
        $pekerjaan_ahliwaris,
        $no_telepon_ahliwaris,
        $bank_ahliwaris,
        $no_rekening_ahliwaris,
        $status_dengan_jamaah,
        $alamat_ahliwaris,
        $kecamatan_ahliwaris,
        $kelurahan_ahliwaris,
        $kode_pos_ahliwaris,
        $dokumen_akta_kematian,
        $dokumen_setor_awal,
        $dokumen_spph,
        $dokumen_ahli_waris,
        $dokumen_surat_kuasa,
        $dokumen_ktp_ahliwaris,
        $dokumen_ktp_penerima_kuasa,
        $dokumen_kk_penerima_kuasa,
        $dokumen_akta_kelahiran,
        $dokumen_buku_nikah,
        $dokumen_rekening_kuasa,
        $foto_wajah,
        $id_pembatalan
    );

    if ($stmt->execute()) {
        // ✅ Catat aktivitas hanya jika data berhasil ditemukan
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pembatalan', 'Mengedit data pembatalan meninggal dunia');
        $_SESSION['success_message'] = "Data berhasil diubah.";
        header("Location: pembatalan_jamaah_meninggal.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal update: " . $stmt->error;
    }

    $stmt->close();
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
                         Edit Pembatalan Haji - Meninggal Dunia
                    </div>
                    <div class="pPembatalan-body" style="color: #1b5e20;">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="section-title">Edit Data Ahliwaris</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['nama_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="jenis_kelamin_ahliwaris" id_pembatalan="jenis_kelamin_ahliwaris" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-laki" <?php echo ($data['jenis_kelamin_ahliwaris'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="Perempuan" <?php echo ($data['jenis_kelamin_ahliwaris'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <select name="pekerjaan_ahliwaris" id_pembatalan="pekerjaan_ahliwaris" class="select-daftar" required>
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
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="no_telepon_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['no_telepon_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nama Bank</label>
                                    <select name="bank_ahliwaris" class="form-control" required>
                                        <option value="" disabled>-- Pilih Bank Syariah di Kalsel --</option>
                                        <option value="451" <?php if (($data['bank_ahliwaris'] ?? '') == '451') echo 'selected'; ?>>Bank Syariah Indonesia (451)</option>
                                        <option value="147" <?php if (($data['bank_ahliwaris'] ?? '') == '147') echo 'selected'; ?>>Bank Muamalat (147)</option>
                                        <option value="506" <?php if (($data['bank_ahliwaris'] ?? '') == '506') echo 'selected'; ?>>Bank Mega Syariah (506)</option>
                                        <option value="521" <?php if (($data['bank_ahliwaris'] ?? '') == '521') echo 'selected'; ?>>Bank Syariah Bukopin (521)</option>
                                        <option value="011" <?php if (($data['bank_ahliwaris'] ?? '') == '011') echo 'selected'; ?>>Bank Danamon (011)</option>
                                        <option value="122" <?php if (($data['bank_ahliwaris'] ?? '') == '122') echo 'selected'; ?>>BPD Kalsel Syariah (122)</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">No. Rekening</label>
                                    <input type="text" name="no_rekening_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['no_rekening_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Status dengan Jamaah</label>
                                    <select name="status_dengan_jamaah" id_pembatalan="status_dengan_jamaah" class="select-daftar" required>
                                        <option value="" disabled>--- Pilih Status ---</option>
                                        <option value="Suami" <?php echo ($data['status_dengan_jamaah'] == 'Suami') ? 'selected' : ''; ?>>Suami</option>
                                        <option value="Istri" <?php echo ($data['status_dengan_jamaah'] == 'Istri') ? 'selected' : ''; ?>>Istri</option>
                                        <option value="Orang Tua Kandung" <?php echo ($data['status_dengan_jamaah'] == 'Orang Tua Kandung') ? 'selected' : ''; ?>>Orang Tua Kandung</option>
                                        <option value="Anak Kandung" <?php echo ($data['status_dengan_jamaah'] == 'Anak Kandung') ? 'selected' : ''; ?>>Anak Kandung</option>
                                        <option value="Saudara Kandung" <?php echo ($data['status_dengan_jamaah'] == 'Saudara Kandung') ? 'selected' : ''; ?>>Saudara Kandung</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ================================================================================================= -->
                            <div class="section-title">Edit Data Tempat Tinggal Sekarang</div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Alamat Tinggal</label>
                                    <input type="text" name="alamat_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['alamat_ahliwaris']); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Kecamatan</label>
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
                                    <label class="form-label">Kelurahan/Desa</label>
                                    <select name="kelurahan_ahliwaris" id="kelurahan_ahliwaris" class="select-daftar" required>
                                        <option value="" disabled>-- Pilih Kelurahan --</option>
                                        <option value="<?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?>" selected><?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?></option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['kode_pos_ahliwaris']); ?>" required>
                                </div>
                            </div>

                            <!-- ================================================================================================= -->
                            <div class="section-title">Edit Berkas Pembatalan</div>
                            <hr>
                            <div class="row g-3">
                                <!-- Akta Kematian -->
                                <div class="col-md-4">
                                    <label class="form-label">Akta Kematian (PDF)</label>
                                    <input type="file" name="dokumen_akta_kematian" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_akta_kematian'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_akta_kematian'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_akta_kematian']); ?>" target="_blank"><?php echo basename($data['dokumen_akta_kematian']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Bukti Setor Awal BIPH -->
                                <div class="col-md-4">
                                    <label class="form-label">Bukti Setor Awal BIPH (PDF)</label>
                                    <input type="file" name="dokumen_setor_awal" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_setor_awal'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_setor_awal'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_setor_awal']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- SPPH -->
                                <div class="col-md-4">
                                    <label class="form-label">SPPH (PDF)</label>
                                    <input type="file" name="dokumen_spph" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_spph'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_spph'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_spph']); ?>" target="_blank"><?php echo basename($data['dokumen_spph']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Surat Keterangan Ahli Waris -->
                                <div class="col-md-6">
                                    <label class="form-label">Surat Keterangan Ahli Waris (PDF)</label>
                                    <input type="file" name="dokumen_ahli_waris" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_ahli_waris'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_ahli_waris'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_ahli_waris']); ?>" target="_blank"><?php echo basename($data['dokumen_ahli_waris']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Surat Kuasa -->
                                <div class="col-md-6">
                                    <label class="form-label">Surat Kuasa (PDF)</label>
                                    <input type="file" name="dokumen_surat_kuasa" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_surat_kuasa'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_surat_kuasa'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_surat_kuasa']); ?>" target="_blank"><?php echo basename($data['dokumen_surat_kuasa']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- KTP Ahli Waris -->
                                <div class="col-md-4">
                                    <label class="form-label">KTP Ahli Waris (PDF)</label>
                                    <input type="file" name="dokumen_ktp_ahliwaris" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_ktp_ahliwaris'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_ktp_ahliwaris'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_ktp_ahliwaris']); ?>" target="_blank"><?php echo basename($data['dokumen_ktp_ahliwaris']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- KTP Penerima Kuasa -->
                                <div class="col-md-4">
                                    <label class="form-label">KTP Penerima Kuasa (PDF)</label>
                                    <input type="file" name="dokumen_ktp_penerima_kuasa" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_ktp_penerima_kuasa'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_ktp_penerima_kuasa'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_ktp_penerima_kuasa']); ?>" target="_blank"><?php echo basename($data['dokumen_ktp_penerima_kuasa']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Kartu Keluarga Penerima Kuasa -->
                                <div class="col-md-4">
                                    <label class="form-label">Kartu Keluarga Penerima Kuasa (PDF)</label>
                                    <input type="file" name="dokumen_kk_penerima_kuasa" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_kk_penerima_kuasa'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_kk_penerima_kuasa'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_kk_penerima_kuasa']); ?>" target="_blank"><?php echo basename($data['dokumen_kk_penerima_kuasa']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Akta Kelahiran -->
                                <div class="col-md-4">
                                    <label class="form-label">Akta Kelahiran (PDF)</label>
                                    <input type="file" name="dokumen_akta_kelahiran" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_akta_kelahiran'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_akta_kelahiran'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_akta_kelahiran']); ?>" target="_blank"><?php echo basename($data['dokumen_akta_kelahiran']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Buku Nikah -->
                                <div class="col-md-4">
                                    <label class="form-label">Buku Nikah (PDF)</label>
                                    <input type="file" name="dokumen_buku_nikah" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_buku_nikah'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_buku_nikah'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_buku_nikah']); ?>" target="_blank"><?php echo basename($data['dokumen_buku_nikah']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>

                                <!-- Buku Rekening Penerima Kuasa -->
                                <div class="col-md-4">
                                    <label class="form-label">Buku Rekening Penerima Kuasa (PDF)</label>
                                    <input type="file" name="dokumen_rekening_kuasa" class="form-control" accept="application/pdf"
                                        <?php if (empty($data['dokumen_rekening_kuasa'])) echo 'required'; ?>>
                                    <?php if (!empty($data['dokumen_rekening_kuasa'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_rekening_kuasa']); ?>" target="_blank"><?php echo basename($data['dokumen_rekening_kuasa']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>
                                <!-- Foto Wajah -->
                                <div class="col-md-12">
                                    <label class="form-label">Foto Wajah 80% (JPG/PNG)</label>
                                    <input type="file" name="foto_wajah" class="form-control" accept="image/jpeg, image/png"
                                        <?php if (empty($data['foto_wajah'])) echo 'required'; ?>>
                                    <?php if (!empty($data['foto_wajah'])): ?>
                                        <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['foto_wajah']); ?>" target="_blank"><?php echo basename($data['foto_wajah']); ?></a></small><br>
                                    <?php else: ?>
                                        <small>Belum ada file yang diupload</small><br>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn btn-success"> <i class="fas fa-edit me-1"></i> EDIT DATA PEMBATALAN</button>
                                <a href="../pembatalan_jamaah_meninggal.php" class="btn btn-secondary">KEMBALI</a>
                            </div>
                        </form>
                        <?php include_once __DIR__ . '/../../../includes/footer_jamaah.php'; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../../../assets/js/sidebar.js"></script>
    <script src="../../../includes/link_script.php"></script>

    <script src="../../assets/js/tambah_data.js"></script>
    <script src="tambah_data_ahliwaris.js"></script>
</body>

</html>