<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
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
    // Data ahli waris
    $nama_ahliwaris = $_POST['nama_ahliwaris'] ?? '';
    $nama_ayah_ahliwaris = $_POST['nama_ayah_ahliwaris'] ?? '';
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
    $tanggal_pengajuan = date('d-m-Y');

    // ✅ Fungsi Upload
    function uploadFile($input_name, $folder)
    {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../../assets/berkas/pengajuan/" . $folder . "/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_tmp = $_FILES[$input_name]['tmp_name'];
            $file_ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $unique_file_name = uniqid($folder . "_") . '.' . $file_ext;
            $target_path = $target_dir . $unique_file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                return $target_path; // ✅ Ini akan return full path seperti yang Anda inginkan
            }
        }
        return null;
    }

    // ✅ Upload dokumen - DIPERBAIKI nama input sesuai HTML form
    $dokumen_akta_kematian = uploadFile('dokumen_akta_kematian', 'surat_sakit');
    $dokumen_setor_awal = uploadFile('dokumen_setor_awal', 'setor_awal');
    $dokumen_spph = uploadFile('dokumen_spph', 'spph');
    $dokumen_ahli_waris = uploadFile('dokumen_ahli_waris', 'ahli_waris');
    $dokumen_surat_kuasa = uploadFile('dokumen_surat_kuasa', 'surat_kuasa');
    $dokumen_ktp_ahliwaris = uploadFile('dokumen_ktp_ahliwaris', 'ktp_ahliwaris');
    $dokumen_ktp_penerima_kuasa = uploadFile('dokumen_ktp_penerima_kuasa', 'ktp_penerima_kuasa');
    $dokumen_kk_penerima_kuasa = uploadFile('dokumen_kk_penerima_kuasa', 'kk_penerima_kuasa');
    $dokumen_akta_kelahiran = uploadFile('dokumen_akta_kelahiran', 'akta_kelahiran');
    $dokumen_buku_nikah = uploadFile('dokumen_buku_nikah', 'buku_nikah');
    $dokumen_rekening_kuasa = uploadFile('dokumen_rekening_kuasa', 'rekening_kuasa');
    $foto_wajah = uploadFile('foto_wajah', 'foto');

    // Insert data ke tabel pelimpahan terlebih dahulu
    $stmt_pelimpahan = $koneksi->prepare("INSERT INTO pelimpahan (id_jamaah, kategori, tanggal_pengajuan, status_dokumen) VALUES (?, 'Meninggal Dunia', NOW(), 'pending')");
    $stmt_pelimpahan->bind_param("i", $id_jamaah);

    if ($stmt_pelimpahan->execute()) {
        $id_pelimpahan = $koneksi->insert_id;

        // Insert data ke tabel pembatalan_ekonomi
        $stmt_insert = $koneksi->prepare("INSERT INTO pelimpahan_meninggal (
        id_pelimpahan,
        nama_ahliwaris, nama_ayah_ahliwaris, tempat_lahir_ahliwaris, tanggal_lahir_ahliwaris, jenis_kelamin_ahliwaris,
        pekerjaan_ahliwaris, no_telepon_ahliwaris, bank_ahliwaris, no_rekening_ahliwaris,
        status_dengan_jamaah, alamat_ahliwaris, kecamatan_ahliwaris, kelurahan_ahliwaris, 
        kode_pos_ahliwaris, dokumen_akta_kematian, dokumen_setor_awal, dokumen_spph, dokumen_ahli_waris,
        dokumen_surat_kuasa, dokumen_ktp_ahliwaris, dokumen_ktp_penerima_kuasa, dokumen_kk_penerima_kuasa, 
        dokumen_akta_kelahiran, dokumen_buku_nikah, dokumen_rekening_kuasa, foto_wajah
        ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )");
        $stmt_insert->bind_param(
            'issssssssssssssssssssssssss',
            $id_pelimpahan,
            $nama_ahliwaris,
            $nama_ayah_ahliwaris,
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
            $foto_wajah
        );

        if ($stmt_insert->execute()) {
            // ✅ Catat aktivitas hanya jika data berhasil ditemukan
            updateAktivitasPengguna($id_jamaah, 'jamaah', 'Pelimpahan', 'Menambahkan data pelimpahan meninggal dunia');
            $_SESSION['success_message'] = "Data pelimpahan berhasil ditambahkan.";
            header("Location: pelimpahan_jamaah_meninggal.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan data pelimpahan meninggal: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan data pelimpahan: " . $stmt_pelimpahan->error;
    }

    $stmt_pelimpahan->close();
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

        <main class="pPelimpahan-wrapper">
            <div class="pPelimpahan">
                <div class="pPelimpahan-header" style="background-color: #1b5e20; color: white;">
                    Tambah Pelimpahan Haji - Meninggal Dunia
                </div>
                <div class="pPelimpahan-body" style="color: #1b5e20;">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="section-title">Masukkan Data Ahli Waris</div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label>Nama Ayah</label>
                                <input type="text" name="nama_ayah_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin_ahliwaris" id="jenis_kelamin_ahliwaris" class="select-daftar" required>
                                    <option value="" disable selected>-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Pekerjaan</label>
                                <select name="pekerjaan_ahliwaris" id="pekerjaan_ahliwaris" class="select-daftar" required>
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
                                <label>No. Telepon</label>
                                <input type="text" name="no_telepon_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>Nama Bank</label>
                                <select name="bank_ahliwaris" class="form-control" required>
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
                                <label>No. Rekening</label>
                                <input type="text" name="no_rekening_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>Status dengan Jamaah</label>
                                <select name="status_dengan_jamaah" id="status_dengan_jamaah" class="select-daftar" required>
                                    <option value="" disabled selected>--- Pilih Status ---</option>
                                    <option value="Suami">Suami</option>
                                    <option value="Istri">Istri</option>
                                    <option value="Orang Tua Kandung">Orang Tua Kandung</option>
                                    <option value="Anak Kandung">Anak Kandung</option>
                                    <option value="Saudara Kandung">Saudara Kandung</option>
                                </select>
                            </div>
                        </div>
                        <!-- ================================================================================================= -->
                        <div class="section-title">Masukkan Data Tempat Tinggal Sekarang</div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label>Alamat Tinggal</label>
                                <input type="text" name="alamat_ahliwaris" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label>Kecamatan</label>
                                <select name="kecamatan_ahliwaris" id="kecamatan_ahliwaris" class="select-daftar" required>
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
                                <label>Kelurahan/Desa</label>
                                <select name="kelurahan_ahliwaris" id="kelurahan_ahliwaris" class="select-daftar" required>
                                    <option value="" disabled selected>-- Pilih Kelurahan --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Kode Pos</label>
                                <input type="text" name="kode_pos_ahliwaris" class="form-control" required>
                            </div>
                        </div>
                        <!-- ================================================================================================= -->
                        <div class="section-title">Upload Berkas Pelimpahan</div>
                        <hr>
                        <div class="row g-3">
                            <!-- Akta Kematian -->
                            <div class="col-md-4">
                                <label class="form-label">Akta Kematian (PDF)</label>
                                <input type="file" name="dokumen_akta_kematian" class="form-control" accept="application/pdf" required>
                            </div>
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

                            <!-- Surat Keterangan Ahli Waris -->
                            <div class="col-md-6">
                                <label class="form-label">Surat Keterangan Ahli Waris (PDF)</label>
                                <input type="file" name="dokumen_ahli_waris" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- Surat Kuasa -->
                            <div class="col-md-6">
                                <label class="form-label">Surat Kuasa (PDF)</label>
                                <input type="file" name="dokumen_surat_kuasa" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- KTP Ahli Waris -->
                            <div class="col-md-4">
                                <label class="form-label">KTP Ahli Waris (PDF)</label>
                                <input type="file" name="dokumen_ktp_ahliwaris" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- KTP Penerima Kuasa -->
                            <div class="col-md-4">
                                <label class="form-label">KTP Penerima Kuasa (PDF)</label>
                                <input type="file" name="dokumen_ktp_penerima_kuasa" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- Kartu Keluarga Penerima Kuasa -->
                            <div class="col-md-4">
                                <label class="form-label">Kartu Keluarga Penerima Kuasa (PDF)</label>
                                <input type="file" name="dokumen_kk_penerima_kuasa" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- Akta Kelahiran -->
                            <div class="col-md-4">
                                <label class="form-label">Akta Kelahiran (PDF)</label>
                                <input type="file" name="dokumen_akta_kelahiran" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- Buku Nikah -->
                            <div class="col-md-4">
                                <label class="form-label">Buku Nikah (PDF)</label>
                                <input type="file" name="dokumen_buku_nikah" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- Buku Rekening Penerima Kuasa -->
                            <div class="col-md-4">
                                <label class="form-label">Buku Rekening Penerima Kuasa (PDF)</label>
                                <input type="file" name="dokumen_rekening_kuasa" class="form-control" accept="application/pdf" required>
                            </div>
                            <!-- Foto Wajah -->
                            <div class="col-md-12">
                                <label class="form-label">Foto Wajah 80% (JPG/PNG)</label>
                                <input type="file" name="foto_wajah" class="form-control" accept="image/jpeg, image/png" required>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus me-1"></i> TAMBAH DATA PELIMPAHAN</button>
                            <a href="../../tambah_pelimpahan.php" class="btn btn-secondary">KEMBALI</a>
                        </div>
                    </form>
                    <?php include_once __DIR__ . '/../../../includes/footer_jamaah.php'; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="../../../assets/js/sidebar.js"></script>
<script src="../../assets/js/tambah_data_ahliwaris.js"></script>
<script src="/phu-kemenag-banjar/views/jamaah/pelimpahan/assets/js/tambah_data_ahliwaris.js"></script>
</body>

</html>