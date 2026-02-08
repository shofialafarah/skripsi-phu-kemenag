<?php
session_start();
include 'koneksi.php';
include 'fungsi.php';

// Ambil ID tergantung dari metode request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_limpah_meninggal = $_POST['id_limpah_meninggal'];
} else {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("⚠️ Parameter ID tidak valid.");
    }
    $id_limpah_meninggal = $_GET['id'];
}

// Ambil data dari database untuk ditampilkan atau diproses
$query = "SELECT pm.*, p.kategori, p.tanggal_validasi
          FROM pelimpahan_meninggal pm
          JOIN pelimpahan p ON pm.id_pelimpahan = p.id_pelimpahan
          WHERE pm.id_limpah_meninggal = '$id_limpah_meninggal'";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) == 0) {
    die("⚠️ Data tidak ditemukan. Pastikan id_limpah_meninggal = $id_limpah_meninggal ada di tabel dan join cocok.");
}

$data = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    // Ambil data dari form
    $nama_ahliwaris = $_POST['nama_ahliwaris'];
    $nama_ayah_ahliwaris = $_POST['nama_ayah_ahliwaris'];
    $tempat_lahir_ahliwaris = $_POST['tempat_lahir_ahliwaris'];
    $tanggal_lahir_ahliwaris = $_POST['tanggal_lahir_ahliwaris'];
    $alamat_ahliwaris = $_POST['alamat_ahliwaris'];
    $kecamatan_ahliwaris = $_POST['kecamatan_ahliwaris'];
    $kelurahan_ahliwaris = $_POST['kelurahan_ahliwaris'];
    $kode_pos_ahliwaris = $_POST['kode_pos_ahliwaris'];
    $jenis_kelamin_ahliwaris = $_POST['jenis_kelamin_ahliwaris'];
    $pekerjaan_ahliwaris = $_POST['pekerjaan_ahliwaris'];
    $no_telepon_ahliwaris = $_POST['no_telepon_ahliwaris'];
    $status_dengan_jamaah = $_POST['status_dengan_jamaah'];
    $nama_jamaah = $_POST['nama_jamaah'];
    $bin_binti = $_POST['bin_binti'];
    $tempat_lahir_jamaah = $_POST['tempat_lahir_jamaah'];
    $tanggal_lahir_jamaah = $_POST['tanggal_lahir_jamaah'];
    $alamat_jamaah = $_POST['alamat_jamaah'];
    $kecamatan_jamaah = $_POST['kecamatan_jamaah'];
    $kelurahan_jamaah = $_POST['kelurahan_jamaah'];
    $kode_pos_jamaah = $_POST['kode_pos_jamaah']; // ✅ (benar)
    $bps = $_POST['bps'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $nomor_porsi = $_POST['nomor_porsi'];
    $spph_validasi = $_POST['spph_validasi'];
    $tanggal_sptjm = $_POST['tanggal_sptjm'];
    $tanggal_rekomendasi = $_POST['tanggal_rekomendasi'];
    $tanggal_masuk_surat = $_POST['tanggal_masuk_surat'];
    $tanggal_register = $_POST['tanggal_register'];
    $nomor_surat = $_POST['nomor_surat'];
    $nominal_setoran = $_POST['nominal_setoran'];
    $no_rekening_ahliwaris = $_POST['no_rekening_ahliwaris'];
    $bank_ahliwaris = $_POST['bank_ahliwaris'];
    $nama_kuasa_waris = $_POST['nama_kuasa_waris'];
    $tahun_wafat = $_POST['tahun_wafat'];
    $tanggal_wafat = $_POST['tanggal_wafat'];

    // Update data ke database menggunakan prepared statement
    $query = "UPDATE pelimpahan_meninggal SET 
    nama_ahliwaris = ?, 
    nama_ayah_ahliwaris = ?,
    tempat_lahir_ahliwaris = ?, 
    tanggal_lahir_ahliwaris = ?, 
    alamat_ahliwaris = ?,
    kecamatan_ahliwaris = ?, 
    kelurahan_ahliwaris = ?, 
    kode_pos_ahliwaris = ?,
    jenis_kelamin_ahliwaris = ?, 
    pekerjaan_ahliwaris = ?, 
    no_telepon_ahliwaris = ?,
    status_dengan_jamaah = ?, 
    nama_jamaah = ?, 
    bin_binti = ?, 
    tempat_lahir_jamaah = ?,
    tanggal_lahir_jamaah = ?, 
    alamat_jamaah = ?, 
    kecamatan_jamaah = ?, 
    kelurahan_jamaah = ?,
    kode_pos_jamaah = ?, 
    bps = ?, 
    nomor_rekening = ?, 
    nomor_porsi = ?, 
    spph_validasi = ?,
    tanggal_sptjm = ?, 
    tanggal_rekomendasi = ?, 
    tanggal_masuk_surat = ?, 
    tanggal_register = ?,
    nomor_surat = ?, 
    nominal_setoran = ?, 
    no_rekening_ahliwaris = ?, 
    bank_ahliwaris = ?,
    nama_kuasa_waris = ?, 
    tahun_wafat = ?, 
    tanggal_wafat = ?
WHERE id_limpah_meninggal = ?";


    $stmt = mysqli_prepare($koneksi, $query);
    // Mengikat parameter untuk query UPDATE
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssssssssssssssssssssssssssssi",
        $nama_ahliwaris,
        $nama_ayah_ahliwaris,
        $tempat_lahir_ahliwaris,
        $tanggal_lahir_ahliwaris,
        $alamat_ahliwaris,
        $kecamatan_ahliwaris,
        $kelurahan_ahliwaris,
        $kode_pos_ahliwaris,
        $jenis_kelamin_ahliwaris,
        $pekerjaan_ahliwaris,
        $no_telepon_ahliwaris,
        $status_dengan_jamaah,
        $nama_jamaah,
        $bin_binti,
        $tempat_lahir_jamaah,
        $tanggal_lahir_jamaah,
        $alamat_jamaah,
        $kecamatan_jamaah,
        $kelurahan_jamaah,
        $kode_pos_jamaah,
        $bps,
        $nomor_rekening,
        $nomor_porsi,
        $spph_validasi,
        $tanggal_sptjm,
        $tanggal_rekomendasi,
        $tanggal_masuk_surat,
        $tanggal_register,
        $nomor_surat,
        $nominal_setoran,
        $no_rekening_ahliwaris,
        $bank_ahliwaris,
        $nama_kuasa_waris,
        $tahun_wafat,
        $tanggal_wafat,
        $id_limpah_meninggal // ini integer
    );

    // PERBAIKAN: Gunakan mysqli_stmt_execute() bukan mysqli_query()
    if (mysqli_stmt_execute($stmt)) {
        // ✅ Catat aktivitas hanya jika data berhasil ditemukan
        updateAktivitasPengguna($id_staf, 'staf', 'Pelimpahan', 'Menginput data pelimpahan meninggal dunia');
        echo "<script>alert('Data berhasil diupdate!'); window.location.href='entry_pelimpahan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error saat update: " . mysqli_error($koneksi) . "');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Staf</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="tambah_pelimpahan.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="background-atas">
        <div class="header">
            <h1>Pelimpahan - Meninggal Dunia</h1>
            <div class="button-group">
                <button type="reset" form="form-data" class="btn btn-reset"><i class="fas fa-rotate-left"></i></button>
                <button type="button" class="btn btn-back" onclick="window.location.href='entry_pelimpahan.php'">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
        </div>
        <form action="edit_pelimpahan_meninggal.php" method="POST" id="form-data">
            <input type="hidden" name="id_limpah_meninggal" value="<?php echo htmlspecialchars($data['id_limpah_meninggal']); ?>">
            <p>DATA AHLI WARIS</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['nama_ahliwaris']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Ayah:</label>
                    <input type="text" name="nama_ayah_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['nama_ayah_ahliwaris']); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir_ahliwaris']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir_ahliwaris']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Jenis Kelamin:</label>
                    <select name="jenis_kelamin_ahliwaris" class="form-control" required>
                        <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-Laki" <?php if ($data['jenis_kelamin_ahliwaris'] == 'Laki-Laki') echo 'selected'; ?>>Laki-Laki</option>
                        <option value="Perempuan" <?php if ($data['jenis_kelamin_ahliwaris'] == 'Perempuan') echo 'selected'; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pekerjaan:</label>
                    <select name="pekerjaan_ahliwaris" class="form-control" required>
                        <option value="" disabled>-- Pilih Pekerjaan --</option>
                        <option value="Mahasiswa" <?php if ($data['pekerjaan_ahliwaris'] == 'Mahasiswa') echo 'selected'; ?>>Mahasiswa</option>
                        <option value="Pegawai Negeri" <?php if ($data['pekerjaan_ahliwaris'] == 'Pegawai Negeri') echo 'selected'; ?>>Pegawai Negeri</option>
                        <option value="Pegawai Swasta" <?php if ($data['pekerjaan_ahliwaris'] == 'Pegawai Swasta') echo 'selected'; ?>>Pegawai Swasta</option>
                        <option value="Ibu Rumah Tangga" <?php if ($data['pekerjaan_ahliwaris'] == 'Ibu Rumah Tangga') echo 'selected'; ?>>Ibu Rumah Tangga</option>
                        <option value="Pensiunan" <?php if ($data['pekerjaan_ahliwaris'] == 'Pensiunan') echo 'selected'; ?>>Pensiunan</option>
                        <option value="Polri" <?php if ($data['pekerjaan_ahliwaris'] == 'Polri') echo 'selected'; ?>>Polri</option>
                        <option value="Pedagang" <?php if ($data['pekerjaan_ahliwaris'] == 'Pedagang') echo 'selected'; ?>>Pedagang</option>
                        <option value="Tani" <?php if ($data['pekerjaan_ahliwaris'] == 'Tani') echo 'selected'; ?>>Tani</option>
                        <option value="Pegawai BUMN" <?php if ($data['pekerjaan_ahliwaris'] == 'Pegawai BUMN') echo 'selected'; ?>>Pegawai BUMN</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon:</label>
                    <input type="text" name="no_telepon_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['no_telepon_ahliwaris']); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Bank:</label>
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
                <div class="form-group">
                    <label>Nomor Rekening:</label>
                    <input type="text" name="no_rekening_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['no_rekening_ahliwaris']); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Kuasa Waris:</label>
                    <input type="text" name="nama_kuasa_waris" class="form-control" value="<?php echo htmlspecialchars($data['nama_kuasa_waris']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Status dengan Jamaah:</label>
                    <select name="status_dengan_jamaah" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Status ---</option>
                        <option value="Suami" <?php if ($data['status_dengan_jamaah'] == 'Suami') echo 'selected'; ?>>Suami</option>
                        <option value="Istri" <?php if ($data['status_dengan_jamaah'] == 'Istri') echo 'selected'; ?>>Istri</option>
                        <option value="Orang Tua Kandung" <?php if ($data['status_dengan_jamaah'] == 'Orang Tua Kandung') echo 'selected'; ?>>Orang Tua Kandung</option>
                        <option value="Anak Kandung" <?php if ($data['status_dengan_jamaah'] == 'Anak Kandung') echo 'selected'; ?>>Anak Kandung</option>
                        <option value="Saudara Kandung" <?php if ($data['status_dengan_jamaah'] == 'Saudara Kandung') echo 'selected'; ?>>Saudara Kandung</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Alamat:</label>
                    <textarea name="alamat_ahliwaris" class="form-control" required><?php echo htmlspecialchars($data['alamat_ahliwaris']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Kecamatan:</label>
                    <select name="kecamatan_ahliwaris" id="kecamatan_ahliwaris" class="form-control" required>
                        <option value="" disabled>-- Pilih Kecamatan --</option>
                        <option value="Aluh-Aluh" <?php if ($data['kecamatan_ahliwaris'] == 'Aluh-Aluh') echo 'selected'; ?>>Aluh-Aluh</option>
                        <option value="Aranio" <?php if ($data['kecamatan_ahliwaris'] == 'Aranio') echo 'selected'; ?>>Aranio</option>
                        <option value="Astambul" <?php if ($data['kecamatan_ahliwaris'] == 'Astambul') echo 'selected'; ?>>Astambul</option>
                        <option value="Beruntung Baru" <?php if ($data['kecamatan_ahliwaris'] == 'Beruntung Baru') echo 'selected'; ?>>Beruntung Baru</option>
                        <option value="Cintapuri Darussalam" <?php if ($data['kecamatan_ahliwaris'] == 'Cintapuri Darussalam') echo 'selected'; ?>>Cintapuri Darussalam</option>
                        <option value="Gambut" <?php if ($data['kecamatan_ahliwaris'] == 'Gambut') echo 'selected'; ?>>Gambut</option>
                        <option value="Karang Intan" <?php if ($data['kecamatan_ahliwaris'] == 'Karang Intan') echo 'selected'; ?>>Karang Intan</option>
                        <option value="Kertak Hanyar" <?php if ($data['kecamatan_ahliwaris'] == 'Kertak Hanyar') echo 'selected'; ?>>Kertak Hanyar</option>
                        <option value="Mataraman" <?php if ($data['kecamatan_ahliwaris'] == 'Mataraman') echo 'selected'; ?>>Mataraman</option>
                        <option value="Martapura" <?php if ($data['kecamatan_ahliwaris'] == 'Martapura') echo 'selected'; ?>>Martapura</option>
                        <option value="Martapura Barat" <?php if ($data['kecamatan_ahliwaris'] == 'Martapura Barat') echo 'selected'; ?>>Martapura Barat</option>
                        <option value="Martapura Timur" <?php if ($data['kecamatan_ahliwaris'] == 'Martapura Timur') echo 'selected'; ?>>Martapura Timur</option>
                        <option value="Paramasan" <?php if ($data['kecamatan_ahliwaris'] == 'Paramasan') echo 'selected'; ?>>Paramasan</option>
                        <option value="Pengaron" <?php if ($data['kecamatan_ahliwaris'] == 'Pengaron') echo 'selected'; ?>>Pengaron</option>
                        <option value="Sambung Makmur" <?php if ($data['kecamatan_ahliwaris'] == 'Sambung Makmur') echo 'selected'; ?>>Sambung Makmur</option>
                        <option value="Simpang Empat" <?php if ($data['kecamatan_ahliwaris'] == 'Simpang Empat') echo 'selected'; ?>>Simpang Empat</option>
                        <option value="Sungai Pinang" <?php if ($data['kecamatan_ahliwaris'] == 'Sungai Pinang') echo 'selected'; ?>>Sungai Pinang</option>
                        <option value="Sungai Tabuk" <?php if ($data['kecamatan_ahliwaris'] == 'Sungai Tabuk') echo 'selected'; ?>>Sungai Tabuk</option>
                        <option value="Tatah Makmur" <?php if ($data['kecamatan_ahliwaris'] == 'Tatah Makmur') echo 'selected'; ?>>Tatah Makmur</option>
                        <option value="Telaga Bauntung" <?php if ($data['kecamatan_ahliwaris'] == 'Telaga Bauntung') echo 'selected'; ?>>Telaga Bauntung</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelurahan:</label>
                    <select name="kelurahan_ahliwaris" id="kelurahan_ahliwaris" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kelurahan ---</option>
                    </select>

                    <script>
                        // Data kelurahan_ahliwaris berdasarkan kecamatan_ahliwaris
                        const kelurahanData = {
                            "Aluh-Aluh": ["Aluh-Aluh Besar", "Aluh-Aluh Kecil", "Aluh-Aluh Kecil Muara", "Bakambat", "Balimau", "Bunipah", "Handil Baru", "Handil Bujur", "Kuin Besar", "Kuin Kecil", "Labat Muara", "Pemurus", "Podok", "Pulantan", "Simpang Warga", "Simpang Warga Dalam", "Sungai Musang", "Tanipah", "Terapu"],
                            "Aranio": ["Apuai", "Aranio", "Artain", "Belangian", "Benua Riam", "Kalaan", "Paau", "Rantau Balai", "Rantau Bujur", "Tiwingan Baru", "Tiwingan Lama"],
                            "Astambul": ["Astambul Kota", "Astambul Seberang", "Banua Anyar DS", "Banua Anyar ST", "Danau Salak", "Jati", "Kalampaian Tengah", "Kalampaian Ilir", "Kalampaian Ulu", "Kaliukan", "Limamar", "Lok Gabang", "Munggu Raya", "Pasar Jati", "Pematang Hambawang", "Pingaran Ilir", "Pingaran Ulu", "Sungai Alat", "Sungai Tuan Ulu", "Sungai Tuan Ilir", "Tambak Danau", "Tambangan"],
                            "Beruntung Baru": ["Babirik", "Handil Purai", "Haur Kuning", "Jambu Burung", "Jambu Raya", "Kampung Baru", "Lawahan", "Muara Halayung", "Pindahan Baru", "Rumpiang", "Selat Makmur", "Tambak Padi"],
                            "Cintapuri Darussalam": ["Alalak Padang", "Benua Anyar", "Cintapuri", "Garis Hanyar", "Karya Makmur", "Keramat Mina", "Makmur Karya", "Simpang Lima", "Sindang Jaya", "Sumber Sari", "Surian Hanyar"],
                            "Gambut": ["Gambut", "Gambut Barat", "Banyu Hirang", "Guntung Papuyu", "Guntung Ujung", "Kayu Bawang", "Keladan Baru", "Makmur", "Malintang", "Malintang Baru", "Sungai Kupang", "Tambak Sirang Baru", "Tambak Sirang Darat", "Tambak Sirang Laut"],
                            "Karang Intan": ["Abirau", "Awang Bangkal Barat", "Awang Bangkal Timur", "Balau", "Bi'ih", "Jingah Habang Ilir", "Jingah Habang Ulu", "Karang Intan", "Kiram", "Lihung", "Lok Tangga", "Mali-Mali", "Mandi Angin Barat", "Mandi Angin Timur", "Mandi Kapau Barat", "Mandi Kapau Timur", "Padang Panjang", "Pandak Daun", "Pasar Lama", "Penyambaran", "Pulau Nyiur", "Sungai Alang", "Sungai Arfat", "Sungai Asam", "Sungai Besar", "Sungai Landas"],
                            "Kertak Hanyar": ["Kertak Hanyar I", "Manarap Lama", "Mandar Sari", "Banua Hanyar", "Kertak Hanyar II", "Manarap Baru", "Manarap Tengah", "Mekar Raya", "Pasar Kemis", "Simpang Empat", "Sungai Lakum", "Tatah Belayung Baru", "Tatah Pemangkih Laut"],
                            "Mataraman": ["Baru", "Bawahan Pasar", "Bawahan Seberang", "Bawahan Selan", "Gunung Ulin", "Lok Tamu", "Mangkalawat", "Mataraman", "Pasiraman", "Pematang Danau", "Simpang Tiga", "Sungai Jati", "Surian", "Takuti", "Tanah Abang"],
                            "Martapura": ["Jawa", "Keraton", "Murung Keraton", "Pasayangan", "Sekumpul", "Sungai Paring", "Tanjung Rema Darat", "Bincau", "Bincau Muara", "Cindai Alus", "Indra Sari", "Jawa Laut", "Labuan Tabu", "Murung Kenanga", "Pasayangan Barat", "Pasayangan Selatan", "Pasayangan Utara", "Sungai Sipai", "Tambak Baru", "Tambak Baru Ilir", "Tambak Baru Ulu", "Tanjung Rema", "Tunggul Irang", "Tunggul Irang Ilir", "Tunggul Irang Ulu", "Tungkaran"],
                            "Martapura Barat": ["Antasan Sutun", "Keliling Benteng Tengah", "Keliling Benteng Ulu", "Penggalaman", "Sungai Batang", "Sungai Batang Ilir", "Sungai Rangas", "Sungai Rangas Hambuku", "Sungai Rangas Tengah", "Sungai Rangas Ulu", "Tangkas", "Teluk Selong", "Teluk Selong Ulu"],
                            "Martapura Timur": ["Akar Bagantung", "Akar Baru", "Antasan Senor", "Antasan Senor Ilir", "Dalam Pagar", "Dalam Pagar Ulu", "Keramat", "Keramat Baru", "Mekar", "Melayu Ilir", "Melayu Tengah", "Melayu Ulu", "Pekauman", "Pekauman Dalam", "Pekauman Ulu", "Pematang Baru", "Sungai Kitano", "Tambak Anyar", "Tambak Anyar Ilir", "Tambak Anyar Ulu"],
                            "Paramasan": ["Angkipih", "Paramasan Atas", "Paramasan Bawah", "Remo"],
                            "Pengaron": ["Alimukim", "Antaraku", "Ati'im", "Benteng", "Kertak Empat", "Lobang Baru", "Lok Tunggul", "Lumpangi", "Mangkauk", "Maniapun", "Panyiuran", "Pengaron"],
                            "Sambung Makmur": ["Baliangin", "Batang Banyu", "Batu Tanam", "Gunung Batu", "Madurejo", "Pasar Baru", "Sungai Lurus"],
                            "Simpang Empat": ["Batu Balian", "Berkat Mulia", "Cabi", "Lawiran", "Lok Cantung", "Paku", "Paring Tali", "Pasar Lama", "Simpang Empat", "Sungai Langsat", "Sungai Raya", "Sungai Tabuk", "Sungkai", "Sungkai Baru", "Tanah Intan"],
                            "Sungai Pinang": ["Belimbing Baru", "Belimbing Lama", "Hakim Makmur", "Kahelaan", "Kupang Rejo", "Pakutik", "Rantau Bakula", "Rantau Nangka", "Sumber Baru", "Sumber Harapan", "Sungai Pinang"],
                            "Sungai Tabuk": ["Sungai Lulut", "Abumbun Jaya", "Gudang Hirang", "Gudang Tengah", "Keliling Benteng Hilir", "Lok Baintan", "Lok Baintan Dalam", "Lok Buntar", "Paku Alam", "Pejambuan", "Pemakuan", "Pematang Panjang", "Pembantanan", "Sungai Bakung", "Sungai Bangkal", "Sungai Pinang Baru", "Sungai Pinang Lama", "Sungai Tabuk Keramat", "Sungai Tabuk Kota", "Sungai Tandipah", "Tajau Landung"],
                            "Tatah Makmur": ["Jaruju Laut", "Layap Baru", "Mekar Sari", "Pandan Sari", "Pemangkih Baru", "Taibah Raya", "Tampang Awang", "Tatah Bangkal", "Tatah Bangkal Tengah", "Tatah Jaruju", "Tatah Layap", "Tatah Pemangkih Darat", "Tatah Pemangkih Tengah"],
                            "Telaga Bauntung": ["Lok Tanah", "Rampah", "Rantau Bujur", "Telaga Baru"]
                        };

                        // Mendapatkan elemen select
                        const kecamatanSelect = document.getElementById('kecamatan_ahliwaris');
                        const kelurahanSelect = document.getElementById('kelurahan_ahliwaris');

                        // Fungsi untuk memuat kelurahan_ahliwaris berdasarkan kecamatan_ahliwaris yang dipilih
                        function loadKelurahan(selectedKecamatan) {
                            // Mengosongkan dropdown kelurahan_ahliwaris
                            kelurahanSelect.innerHTML = '<option value="" disabled selected>--- Pilih Kelurahan ---</option>';

                            // Jika ada kecamatan_ahliwaris yang dipilih
                            if (selectedKecamatan) {
                                // Mengambil data kelurahan_ahliwaris untuk kecamatan_ahliwaris yang dipilih
                                const kelurahanList = kelurahanData[selectedKecamatan];

                                // Menambahkan opsi kelurahan_ahliwaris ke dropdown
                                kelurahanList.forEach(kelurahan_ahliwaris => {
                                    const option = document.createElement('option');
                                    option.value = kelurahan_ahliwaris;
                                    option.textContent = kelurahan_ahliwaris;
                                    kelurahanSelect.appendChild(option);
                                });
                            }
                        }

                        // Event listener untuk perubahan pada select kecamatan_ahliwaris
                        kecamatanSelect.addEventListener('change', function() {
                            const selectedKecamatan = this.value;
                            loadKelurahan(selectedKecamatan);
                        });

                        // Mengatur nilai default jika ada data sebelumnya (misalnya dari database)
                        const selectedKecamatan = "<?php echo htmlspecialchars($data['kecamatan_ahliwaris']); ?>";
                        if (selectedKecamatan) {
                            kecamatanSelect.value = selectedKecamatan;
                            loadKelurahan(selectedKecamatan);
                        }

                        // Mengatur nilai default kelurahan_ahliwaris berdasarkan data yang ada
                        const selectedKelurahan = "<?php echo htmlspecialchars($data['kelurahan_ahliwaris']); ?>";
                        if (selectedKelurahan) {
                            kelurahanSelect.value = selectedKelurahan;
                        }
                    </script>
                </div>
                <div class="form-group">
                    <label>Kode Pos</label>
                    <input type="text" name="kode_pos_ahliwaris" class="form-control" value="<?php echo htmlspecialchars($data['kode_pos_ahliwaris']); ?>" required>
                </div>
            </div>

            <p>DATA JAMAAH</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_jamaah" class="form-control" value="<?php echo htmlspecialchars($data['nama_jamaah']); ?>" required>
                </div>
                <div class="form-group">
                    <label>BIN/BINTI:</label>
                    <input type="text" name="bin_binti" class="form-control" value="<?php echo htmlspecialchars($data['bin_binti']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nomor Porsi:</label>
                    <input type="text" name="nomor_porsi" class="form-control" value="<?php echo htmlspecialchars($data['nomor_porsi']); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tempat Lahir:</label>
                    <input type="text" name="tempat_lahir_jamaah" class="form-control" value="<?php echo htmlspecialchars($data['tempat_lahir_jamaah']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir:</label>
                    <input type="date" name="tanggal_lahir_jamaah" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_lahir_jamaah']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Bank Penerima Setoran (BPS):</label>
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
                <div class="form-group">
                    <label>Nomor Rekening:</label>
                    <input type="text" name="nomor_rekening" class="form-control" value="<?php echo htmlspecialchars($data['nomor_rekening']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nominal Setoran:</label>
                    <input type="text" name="nominal_setoran" class="form-control" value="25.000.000" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>SPPH/Validasi:</label>
                    <input type="text" name="spph_validasi" class="form-control" value="<?php echo htmlspecialchars($data['spph_validasi']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori:</label>
                    <input type="text" name="kategori" class="form-control" value="<?php echo htmlspecialchars($data['kategori']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Tanggal Register:</label>
                    <input type="date" name="tanggal_register" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_register']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Wafat:</label>
                    <input type="date" name="tanggal_wafat" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_wafat']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tahun Wafat:</label>
                    <input type="text" name="tahun_wafat" class="form-control" value="<?php echo htmlspecialchars($data['tahun_wafat']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Alamat:</label>
                    <textarea name="alamat_jamaah" class="form-control" required><?php echo htmlspecialchars($data['alamat_jamaah']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Kecamatan:</label>
                    <select name="kecamatan_jamaah" id="kecamatan_jamaah" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kecamatan ---</option>
                        <option value="Aluh-Aluh" <?php if ($data['kecamatan_jamaah'] == 'Aluh-Aluh') echo 'selected'; ?>>Aluh-Aluh</option>
                        <option value="Aranio" <?php if ($data['kecamatan_jamaah'] == 'Aranio') echo 'selected'; ?>>Aranio</option>
                        <option value="Astambul" <?php if ($data['kecamatan_jamaah'] == 'Astambul') echo 'selected'; ?>>Astambul</option>
                        <option value="Beruntung Baru" <?php if ($data['kecamatan_jamaah'] == 'Beruntung Baru') echo 'selected'; ?>>Beruntung Baru</option>
                        <option value="Cintapuri Darussalam" <?php if ($data['kecamatan_jamaah'] == 'Cintapuri Darussalam') echo 'selected'; ?>>Cintapuri Darussalam</option>
                        <option value="Gambut" <?php if ($data['kecamatan_jamaah'] == 'Gambut') echo 'selected'; ?>>Gambut</option>
                        <option value="Karang Intan" <?php if ($data['kecamatan_jamaah'] == 'Karang Intan') echo 'selected'; ?>>Karang Intan</option>
                        <option value="Kertak Hanyar" <?php if ($data['kecamatan_jamaah'] == 'Kertak Hanyar') echo 'selected'; ?>>Kertak Hanyar</option>
                        <option value="Mataraman" <?php if ($data['kecamatan_jamaah'] == 'Mataraman') echo 'selected'; ?>>Mataraman</option>
                        <option value="Martapura" <?php if ($data['kecamatan_jamaah'] == 'Martapura') echo 'selected'; ?>>Martapura</option>
                        <option value="Martapura Barat" <?php if ($data['kecamatan_jamaah'] == 'Martapura Barat') echo 'selected'; ?>>Martapura Barat</option>
                        <option value="Martapura Timur" <?php if ($data['kecamatan_jamaah'] == 'Martapura Timur') echo 'selected'; ?>>Martapura Timur</option>
                        <option value="Paramasan" <?php if ($data['kecamatan_jamaah'] == 'Paramasan') echo 'selected'; ?>>Paramasan</option>
                        <option value="Pengaron" <?php if ($data['kecamatan_jamaah'] == 'Pengaron') echo 'selected'; ?>>Pengaron</option>
                        <option value="Sambung Makmur" <?php if ($data['kecamatan_jamaah'] == 'Sambung Makmur') echo 'selected'; ?>>Sambung Makmur</option>
                        <option value="Simpang Empat" <?php if ($data['kecamatan_jamaah'] == 'Simpang Empat') echo 'selected'; ?>>Simpang Empat</option>
                        <option value="Sungai Pinang" <?php if ($data['kecamatan_jamaah'] == 'Sungai Pinang') echo 'selected'; ?>>Sungai Pinang</option>
                        <option value="Sungai Tabuk" <?php if ($data['kecamatan_jamaah'] == 'Sungai Tabuk') echo 'selected'; ?>>Sungai Tabuk</option>
                        <option value="Tatah Makmur" <?php if ($data['kecamatan_jamaah'] == 'Tatah Makmur') echo 'selected'; ?>>Tatah Makmur</option>
                        <option value="Telaga Bauntung" <?php if ($data['kecamatan_jamaah'] == 'Telaga Bauntung') echo 'selected'; ?>>Telaga Bauntung</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelurahan:</label>
                    <select name="kelurahan_jamaah" id="kelurahan_jamaah" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kelurahan ---</option>
                    </select>

                    <script>
                        // Data kelurahan_ahliwaris berdasarkan kecamatan_jamaah
                        const kelurahan_jamaahData = {
                            "Aluh-Aluh": ["Aluh-Aluh Besar", "Aluh-Aluh Kecil", "Aluh-Aluh Kecil Muara", "Bakambat", "Balimau", "Bunipah", "Handil Baru", "Handil Bujur", "Kuin Besar", "Kuin Kecil", "Labat Muara", "Pemurus", "Podok", "Pulantan", "Simpang Warga", "Simpang Warga Dalam", "Sungai Musang", "Tanipah", "Terapu"],
                            "Aranio": ["Apuai", "Aranio", "Artain", "Belangian", "Benua Riam", "Kalaan", "Paau", "Rantau Balai", "Rantau Bujur", "Tiwingan Baru", "Tiwingan Lama"],
                            "Astambul": ["Astambul Kota", "Astambul Seberang", "Banua Anyar DS", "Banua Anyar ST", "Danau Salak", "Jati", "Kalampaian Tengah", "Kalampaian Ilir", "Kalampaian Ulu", "Kaliukan", "Limamar", "Lok Gabang", "Munggu Raya", "Pasar Jati", "Pematang Hambawang", "Pingaran Ilir", "Pingaran Ulu", "Sungai Alat", "Sungai Tuan Ulu", "Sungai Tuan Ilir", "Tambak Danau", "Tambangan"],
                            "Beruntung Baru": ["Babirik", "Handil Purai", "Haur Kuning", "Jambu Burung", "Jambu Raya", "Kampung Baru", "Lawahan", "Muara Halayung", "Pindahan Baru", "Rumpiang", "Selat Makmur", "Tambak Padi"],
                            "Cintapuri Darussalam": ["Alalak Padang", "Benua Anyar", "Cintapuri", "Garis Hanyar", "Karya Makmur", "Keramat Mina", "Makmur Karya", "Simpang Lima", "Sindang Jaya", "Sumber Sari", "Surian Hanyar"],
                            "Gambut": ["Gambut", "Gambut Barat", "Banyu Hirang", "Guntung Papuyu", "Guntung Ujung", "Kayu Bawang", "Keladan Baru", "Makmur", "Malintang", "Malintang Baru", "Sungai Kupang", "Tambak Sirang Baru", "Tambak Sirang Darat", "Tambak Sirang Laut"],
                            "Karang Intan": ["Abirau", "Awang Bangkal Barat", "Awang Bangkal Timur", "Balau", "Bi'ih", "Jingah Habang Ilir", "Jingah Habang Ulu", "Karang Intan", "Kiram", "Lihung", "Lok Tangga", "Mali-Mali", "Mandi Angin Barat", "Mandi Angin Timur", "Mandi Kapau Barat", "Mandi Kapau Timur", "Padang Panjang", "Pandak Daun", "Pasar Lama", "Penyambaran", "Pulau Nyiur", "Sungai Alang", "Sungai Arfat", "Sungai Asam", "Sungai Besar", "Sungai Landas"],
                            "Kertak Hanyar": ["Kertak Hanyar I", "Manarap Lama", "Mandar Sari", "Banua Hanyar", "Kertak Hanyar II", "Manarap Baru", "Manarap Tengah", "Mekar Raya", "Pasar Kemis", "Simpang Empat", "Sungai Lakum", "Tatah Belayung Baru", "Tatah Pemangkih Laut"],
                            "Mataraman": ["Baru", "Bawahan Pasar", "Bawahan Seberang", "Bawahan Selan", "Gunung Ulin", "Lok Tamu", "Mangkalawat", "Mataraman", "Pasiraman", "Pematang Danau", "Simpang Tiga", "Sungai Jati", "Surian", "Takuti", "Tanah Abang"],
                            "Martapura": ["Jawa", "Keraton", "Murung Keraton", "Pasayangan", "Sekumpul", "Sungai Paring", "Tanjung Rema Darat", "Bincau", "Bincau Muara", "Cindai Alus", "Indra Sari", "Jawa Laut", "Labuan Tabu", "Murung Kenanga", "Pasayangan Barat", "Pasayangan Selatan", "Pasayangan Utara", "Sungai Sipai", "Tambak Baru", "Tambak Baru Ilir", "Tambak Baru Ulu", "Tanjung Rema", "Tunggul Irang", "Tunggul Irang Ilir", "Tunggul Irang Ulu", "Tungkaran"],
                            "Martapura Barat": ["Antasan Sutun", "Keliling Benteng Tengah", "Keliling Benteng Ulu", "Penggalaman", "Sungai Batang", "Sungai Batang Ilir", "Sungai Rangas", "Sungai Rangas Hambuku", "Sungai Rangas Tengah", "Sungai Rangas Ulu", "Tangkas", "Teluk Selong", "Teluk Selong Ulu"],
                            "Martapura Timur": ["Akar Bagantung", "Akar Baru", "Antasan Senor", "Antasan Senor Ilir", "Dalam Pagar", "Dalam Pagar Ulu", "Keramat", "Keramat Baru", "Mekar", "Melayu Ilir", "Melayu Tengah", "Melayu Ulu", "Pekauman", "Pekauman Dalam", "Pekauman Ulu", "Pematang Baru", "Sungai Kitano", "Tambak Anyar", "Tambak Anyar Ilir", "Tambak Anyar Ulu"],
                            "Paramasan": ["Angkipih", "Paramasan Atas", "Paramasan Bawah", "Remo"],
                            "Pengaron": ["Alimukim", "Antaraku", "Ati'im", "Benteng", "Kertak Empat", "Lobang Baru", "Lok Tunggul", "Lumpangi", "Mangkauk", "Maniapun", "Panyiuran", "Pengaron"],
                            "Sambung Makmur": ["Baliangin", "Batang Banyu", "Batu Tanam", "Gunung Batu", "Madurejo", "Pasar Baru", "Sungai Lurus"],
                            "Simpang Empat": ["Batu Balian", "Berkat Mulia", "Cabi", "Lawiran", "Lok Cantung", "Paku", "Paring Tali", "Pasar Lama", "Simpang Empat", "Sungai Langsat", "Sungai Raya", "Sungai Tabuk", "Sungkai", "Sungkai Baru", "Tanah Intan"],
                            "Sungai Pinang": ["Belimbing Baru", "Belimbing Lama", "Hakim Makmur", "Kahelaan", "Kupang Rejo", "Pakutik", "Rantau Bakula", "Rantau Nangka", "Sumber Baru", "Sumber Harapan", "Sungai Pinang"],
                            "Sungai Tabuk": ["Sungai Lulut", "Abumbun Jaya", "Gudang Hirang", "Gudang Tengah", "Keliling Benteng Hilir", "Lok Baintan", "Lok Baintan Dalam", "Lok Buntar", "Paku Alam", "Pejambuan", "Pemakuan", "Pematang Panjang", "Pembantanan", "Sungai Bakung", "Sungai Bangkal", "Sungai Pinang Baru", "Sungai Pinang Lama", "Sungai Tabuk Keramat", "Sungai Tabuk Kota", "Sungai Tandipah", "Tajau Landung"],
                            "Tatah Makmur": ["Jaruju Laut", "Layap Baru", "Mekar Sari", "Pandan Sari", "Pemangkih Baru", "Taibah Raya", "Tampang Awang", "Tatah Bangkal", "Tatah Bangkal Tengah", "Tatah Jaruju", "Tatah Layap", "Tatah Pemangkih Darat", "Tatah Pemangkih Tengah"],
                            "Telaga Bauntung": ["Lok Tanah", "Rampah", "Rantau Bujur", "Telaga Baru"]
                        };

                        // Mendapatkan elemen select
                        const kecamatan_jamaahSelect = document.getElementById('kecamatan_jamaah');
                        const kelurahan_jamaahSelect = document.getElementById('kelurahan_jamaah');

                        // Fungsi untuk memuat kelurahan_ahliwaris berdasarkan kecamatan_jamaah yang dipilih
                        function loadKelurahan_jamaah(selectedKecamatan_jamaah) {
                            // Mengosongkan dropdown kelurahan_ahliwaris
                            kelurahan_jamaahSelect.innerHTML = '<option value="" disabled selected>--- Pilih Kelurahan ---</option>';

                            // Jika ada kecamatan_jamaah yang dipilih
                            if (selectedKecamatan_jamaah) {
                                // Mengambil data kelurahan_ahliwaris untuk kecamatan_jamaah yang dipilih
                                const kelurahan_jamaahList = kelurahan_jamaahData[selectedKecamatan_jamaah];

                                // Menambahkan opsi kelurahan_ahliwaris ke dropdown
                                kelurahan_jamaahList.forEach(kelurahan_jamaah => {
                                    const option = document.createElement('option');
                                    option.value = kelurahan_jamaah;
                                    option.textContent = kelurahan_jamaah;
                                    kelurahan_jamaahSelect.appendChild(option);
                                });
                            }
                        }

                        // Event listener untuk perubahan pada select kecamatan_jamaah
                        kecamatan_jamaahSelect.addEventListener('change', function() {
                            const selectedKecamatan_jamaah = this.value;
                            loadKelurahan_jamaah(selectedKecamatan_jamaah);
                        });

                        // Mengatur nilai default jika ada data sebelumnya (misalnya dari database)
                        const selectedKecamatan_jamaah = "<?php echo htmlspecialchars($data['kecamatan_jamaah']); ?>";
                        if (selectedKecamatan_jamaah) {
                            kecamatan_jamaahSelect.value = selectedKecamatan_jamaah;
                            loadKelurahan_jamaah(selectedKecamatan_jamaah);
                        }

                        // Mengatur nilai default kelurahan_ahliwaris berdasarkan data yang ada
                        const selectedKelurahan_jamaah = "<?php echo htmlspecialchars($data['kelurahan_jamaah']); ?>";
                        if (selectedKelurahan_jamaah) {
                            kelurahan_jamaahSelect.value = selectedKelurahan_jamaah;
                        }
                    </script>
                </div>
                <div class="form-group">
                    <label>Kode Pos:</label>
                    <input type="text" name="kode_pos_jamaah" class="form-control" value="<?php echo htmlspecialchars($data['kode_pos_jamaah']); ?>" required>
                </div>
            </div>
            <p>KETERANGAN SURAT</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal SPTJM</label>
                    <input type="date" name="tanggal_sptjm" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_sptjm']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Rekomendasi</label>
                    <input type="date" name="tanggal_rekomendasi" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_rekomendasi']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Surat:</label>
                    <input type="date" name="tanggal_masuk_surat" class="form-control" value="<?php echo htmlspecialchars($data['tanggal_masuk_surat']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nomor Surat:</label>
                    <input type="text" name="nomor_surat" class="form-control" value="<?php echo htmlspecialchars($data['nomor_surat']); ?>" required>
                </div>
            </div>
            
            <!-- tombol edit data -->
            <div>
                <button type="submit" name="update" class="simpan">Simpan Data</button>
            </div>
        </form>
    </div>
</body>

</html>