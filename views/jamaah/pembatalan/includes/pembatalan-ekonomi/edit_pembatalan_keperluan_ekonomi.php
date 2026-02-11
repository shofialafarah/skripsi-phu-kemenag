<?php
session_start();
include '../../../../../includes/koneksi.php';
include '../../../../partials/fungsi.php';

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
$result = $koneksi->query("SELECT * FROM pembatalan_ekonomi WHERE id_pembatalan = $id_pembatalan AND id_pembatalan IN (SELECT id_pembatalan FROM pembatalan WHERE id_jamaah = $id_jamaah)");

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
    $nama_jamaah = $_POST['nama_jamaah'] ?? '';
    $tempat_lahir = $_POST['tempat_lahir'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $pekerjaan = $_POST['pekerjaan'] ?? '';
    $no_telepon = $_POST['no_telepon'] ?? '';
    $bps = $_POST['bps'] ?? '';
    $nomor_rek = $_POST['nomor_rek'] ?? '';
    $spph_validasi = $_POST['spph_validasi'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $kecamatan = $_POST['kecamatan'] ?? '';
    $kelurahan = $_POST['kelurahan'] ?? '';
    $kode_pos = $_POST['kode_pos'] ?? '';

    // Upload dokumen baru jika ada
    $dokumen_setor_awal = uploadFile('dokumen_setor_awal', 'setor_awal', $data['dokumen_setor_awal']);
    $dokumen_spph = uploadFile('dokumen_spph', 'spph', $data['dokumen_spph']);
    $dokumen_ktp = uploadFile('dokumen_ktp', 'ktp', $data['dokumen_ktp']);
    $dokumen_kk = uploadFile('dokumen_kk', 'kk', $data['dokumen_kk']);
    $dokumen_akta_kelahiran = uploadFile('dokumen_akta_kelahiran', 'akta_kelahiran', $data['dokumen_akta_kelahiran']);
    $dokumen_rekening = uploadFile('dokumen_rekening', 'rekening', $data['dokumen_rekening']);
    $foto_wajah = uploadFile('foto_wajah', 'foto', $data['foto_wajah']);

    // Update query
    $stmt = $koneksi->prepare("UPDATE pembatalan_ekonomi SET 
        nama_jamaah = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?,
        pekerjaan = ?, no_telepon = ?, bps = ?, nomor_rek = ?, spph_validasi = ?,
        alamat = ?, kecamatan = ?, kelurahan = ?, kode_pos = ?,
        dokumen_setor_awal = ?, dokumen_spph = ?, dokumen_ktp = ?, dokumen_kk = ?,
        dokumen_akta_kelahiran = ?, dokumen_rekening = ?, foto_wajah = ?
        WHERE id_pembatalan = ?");

    $stmt->bind_param(
        'ssssssssssssssssssssi',
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
        $foto_wajah,
        $id_pembatalan
    );

    if ($stmt->execute()) {
        // ✅ Catat aktivitas hanya jika data berhasil ditemukan
        updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pembatalan', 'Mengedit data pembatalan keperluan ekonomi');
        $_SESSION['success_message'] = "Data berhasil diubah.";
        header("Location: pembatalan_jamaah_ekonomi.php");
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
                        Edit Pembatalan Haji - Keperluan Ekonomi
                    </div>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="section-title">Edit Data Pribadi</div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_jamaah" class="form-control" value="<?= htmlspecialchars($data['nama_jamaah']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="select-daftar" required>
                                    <option value="" disabled selected>--- Pilih Jenis Kelamin ---</option>
                                    <option value="Laki-Laki" <?php if ($data['jenis_kelamin'] == 'Laki-Laki') echo 'selected'; ?>>Laki-Laki</option>
                                    <option value="Perempuan" <?php if ($data['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pekerjaan</label>
                                <select name="pekerjaan" class="select-daftar" required>
                                    <option value="" disabled selected>--- Pilih Pekerjaan ---</option>
                                    <option value="Pelajar/Mahasiswa" <?php if ($data['pekerjaan'] == 'Pelajar/Mahasiswa') echo 'selected'; ?>>Pelajar/Mahasiswa</option>
                                    <option value="Pegawai Negeri" <?php if ($data['pekerjaan'] == 'Pegawai Negeri') echo 'selected'; ?>>Pegawai Negeri</option>
                                    <option value="Pegawai Swasta" <?php if ($data['pekerjaan'] == 'Pegawai Swasta') echo 'selected'; ?>>Pegawai Swasta</option>
                                    <option value="Ibu Rumah Tangga" <?php if ($data['pekerjaan'] == 'Ibu Rumah Tangga') echo 'selected'; ?>>Ibu Rumah Tangga</option>
                                    <option value="Pensiunan" <?php if ($data['pekerjaan'] == 'Pensiunan') echo 'selected'; ?>>Pensiunan</option>
                                    <option value="Polri" <?php if ($data['pekerjaan'] == 'Polri') echo 'selected'; ?>>Polri</option>
                                    <option value="Pedagang" <?php if ($data['pekerjaan'] == 'Pedagang') echo 'selected'; ?>>Pedagang</option>
                                    <option value="Tani" <?php if ($data['pekerjaan'] == 'Tani') echo 'selected'; ?>>Tani</option>
                                    <option value="Pegawai BUMN" <?php if ($data['pekerjaan'] == 'Pegawai BUMN') echo 'selected'; ?>>Pegawai BUMN</option>
                                </select>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">No. Telepon</label>
                                <input type="number" name="no_telepon" class="form-control" value="<?php echo htmlspecialchars($data['no_telepon']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nama Bank</label>
                                <select name="bps" class="form-control" required>
                                    <option value="" disabled>-- Pilih Bank Syariah di Kalsel --</option>
                                    <option value="451" <?php if (($data['bps'] ?? '') == '451') echo 'selected'; ?>>Bank Syariah Indonesia (451)</option>
                                    <option value="147" <?php if (($data['bps'] ?? '') == '147') echo 'selected'; ?>>Bank Muamalat (147)</option>
                                    <option value="506" <?php if (($data['bps'] ?? '') == '506') echo 'selected'; ?>>Bank Mega Syariah (506)</option>
                                    <option value="521" <?php if (($data['bps'] ?? '') == '521') echo 'selected'; ?>>Bank Syariah Bukopin (521)</option>
                                    <option value="011" <?php if (($data['bps'] ?? '') == '011') echo 'selected'; ?>>Bank Danamon (011)</option>
                                    <option value="122" <?php if (($data['bps'] ?? '') == '122') echo 'selected'; ?>>BPD Kalsel Syariah (122)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">No. Rekening</label>
                                <input type="number" name="nomor_rek" class="form-control" value="<?php echo htmlspecialchars($data['nomor_rek']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">No. Validasi Bank</label>
                                <input type="text" name="spph_validasi" class="form-control" value="<?php echo htmlspecialchars($data['spph_validasi']); ?>" required>
                            </div>
                        </div>

                        <!-- ================================================================================================= -->
                        <div class="section-title">Edit Data Tempat Tinggal Sekarang</div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Alamat Tinggal</label>
                                <input type="text" name="alamat" class="form-control" value="<?php echo htmlspecialchars($data['alamat']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Kecamatan</label>
                                <select name="kecamatan" id="kecamatan" class="select-daftar" required>
                                    <option value="" disabled selected>--- Pilih Kecamatan ---</option>
                                    <option value="Aluh-Aluh" <?php if ($data['kecamatan'] == 'Aluh-Aluh') echo 'selected'; ?>>Aluh-Aluh</option>
                                    <option value="Aranio" <?php if ($data['kecamatan'] == 'Aranio') echo 'selected'; ?>>Aranio</option>
                                    <option value="Astambul" <?php if ($data['kecamatan'] == 'Astambul') echo 'selected'; ?>>Astambul</option>
                                    <option value="Beruntung Baru" <?php if ($data['kecamatan'] == 'Beruntung Baru') echo 'selected'; ?>>Beruntung Baru</option>
                                    <option value="Cintapuri Darussalam" <?php if ($data['kecamatan'] == 'Cintapuri Darussalam') echo 'selected'; ?>>Cintapuri Darussalam</option>
                                    <option value="Gambut" <?php if ($data['kecamatan'] == 'Gambut') echo 'selected'; ?>>Gambut</option>
                                    <option value="Karang Intan" <?php if ($data['kecamatan'] == 'Karang Intan') echo 'selected'; ?>>Karang Intan</option>
                                    <option value="Kertak Hanyar" <?php if ($data['kecamatan'] == 'Kertak Hanyar') echo 'selected'; ?>>Kertak Hanyar</option>
                                    <option value="Mataraman" <?php if ($data['kecamatan'] == 'Mataraman') echo 'selected'; ?>>Mataraman</option>
                                    <option value="Martapura" <?php if ($data['kecamatan'] == 'Martapura') echo 'selected'; ?>>Martapura</option>
                                    <option value="Martapura Barat" <?php if ($data['kecamatan'] == 'Martapura Barat') echo 'selected'; ?>>Martapura Barat</option>
                                    <option value="Martapura Timur" <?php if ($data['kecamatan'] == 'Martapura Timur') echo 'selected'; ?>>Martapura Timur</option>
                                    <option value="Paramasan" <?php if ($data['kecamatan'] == 'Paramasan') echo 'selected'; ?>>Paramasan</option>
                                    <option value="Pengaron" <?php if ($data['kecamatan'] == 'Pengaron') echo 'selected'; ?>>Pengaron</option>
                                    <option value="Sambung Makmur" <?php if ($data['kecamatan'] == 'Sambung Makmur') echo 'selected'; ?>>Sambung Makmur</option>
                                    <option value="Simpang Empat" <?php if ($data['kecamatan'] == 'Simpang Empat') echo 'selected'; ?>>Simpang Empat</option>
                                    <option value="Sungai Pinang" <?php if ($data['kecamatan'] == 'Sungai Pinang') echo 'selected'; ?>>Sungai Pinang</option>
                                    <option value="Sungai Tabuk" <?php if ($data['kecamatan'] == 'Sungai Tabuk') echo 'selected'; ?>>Sungai Tabuk</option>
                                    <option value="Tatah Makmur" <?php if ($data['kecamatan'] == 'Tatah Makmur') echo 'selected'; ?>>Tatah Makmur</option>
                                    <option value="Telaga Bauntung" <?php if ($data['kecamatan'] == 'Telaga Bauntung') echo 'selected'; ?>>Telaga Bauntung</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Kelurahan/Desa</label>
                                <select name="kelurahan" id="kelurahan" class="select-daftar" required>
                                    <option value="" disabled selected>-- Pilih Kelurahan --</option>
                                    <option value="<?php echo htmlspecialchars($data['kelurahan']); ?>" selected><?php echo htmlspecialchars($data['kelurahan']); ?></option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Kode Pos</label>
                                <input type="number" name="kode_pos" class="form-control" value="<?php echo htmlspecialchars($data['kode_pos']); ?>" required>
                            </div>
                        </div>

                        <!-- ================================================================================================= -->
                        <div class="section-title">Edit Berkas Pembatalan</div>
                        <hr>
                        <div class="row g-3">
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
                                    <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_spph']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
                                <?php else: ?>
                                    <small>Belum ada file yang diupload</small><br>
                                <?php endif; ?>
                            </div>

                            <!-- KTP -->
                            <div class="col-md-4">
                                <label class="form-label">KTP (PDF)</label>
                                <input type="file" name="dokumen_ktp" class="form-control" accept="application/pdf"
                                    <?php if (empty($data['dokumen_ktp'])) echo 'required'; ?>>
                                <?php if (!empty($data['dokumen_ktp'])): ?>
                                    <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_ktp']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
                                <?php else: ?>
                                    <small>Belum ada file yang diupload</small><br>
                                <?php endif; ?>
                            </div>

                            <!-- Kartu Keluarga -->
                            <div class="col-md-4">
                                <label class="form-label">Kartu Keluarga (PDF)</label>
                                <input type="file" name="dokumen_kk" class="form-control" accept="application/pdf"
                                    <?php if (empty($data['dokumen_kk'])) echo 'required'; ?>>
                                <?php if (!empty($data['dokumen_kk'])): ?>
                                    <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_kk']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
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
                                    <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_akta_kelahiran']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
                                <?php else: ?>
                                    <small>Belum ada file yang diupload</small><br>
                                <?php endif; ?>
                            </div>

                            <!-- Buku Rekening -->
                            <div class="col-md-4">
                                <label class="form-label">Buku Rekening (PDF)</label>
                                <input type="file" name="dokumen_rekening" class="form-control" accept="application/pdf"
                                    <?php if (empty($data['dokumen_rekening'])) echo 'required'; ?>>
                                <?php if (!empty($data['dokumen_rekening'])): ?>
                                    <small style="color: #1b5e20;">File saat ini: <a href="<?php echo htmlspecialchars($data['dokumen_rekening']); ?>" target="_blank"><?php echo basename($data['dokumen_setor_awal']); ?></a></small><br>
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
                            <button type="submit" class="btn btn-success"><i class="fas fa-edit me-1"></i> EDIT DATA PEMBATALAN</button>
                            <a href="../pembatalan_jamaah_ekonomi.php" class="btn btn-secondary">KEMBALI</a>
                        </div>
                    </form>
                    <?php include_once __DIR__ . '/../../../includes/footer_jamaah.php'; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="../../../assets/js/sidebar.js"></script>
    <script src="../../../includes/link_script.php"></script>
    <script src="../../assets/js/tambah_data_kelurahan.js"></script>
</body>

</html>