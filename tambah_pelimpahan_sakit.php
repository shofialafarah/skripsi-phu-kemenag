<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama_ahliwaris = $_POST['nama_ahliwaris'];
    $nama_ayah = $_POST['nama_ayah'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $kecamatan = $_POST['kecamatan'];
    $kelurahan = $_POST['kelurahan'];
    $provinsi = $_POST['provinsi'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $pekerjaan = $_POST['pekerjaan'];
    $no_telepon = $_POST['no_telepon'];
    $status_ahliwaris = $_POST['status_ahliwaris'];
    $nama_jamaah = $_POST['nama_jamaah'];
    $bin_binti = $_POST['bin_binti'];
    $tempat_lahir_jamaah = $_POST['tempat_lahir_jamaah'];
    $tanggal_lahir_jamaah = $_POST['tanggal_lahir_jamaah'];
    $alamat_jamaah = $_POST['alamat_jamaah'];
    $kecamatan_jamaah = $_POST['kecamatan_jamaah'];
    $kelurahan_jamaah = $_POST['kelurahan_jamaah'];
    $provinsi_jamaah = $_POST['provinsi_jamaah'];
    $bps = $_POST['bps'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $nomor_porsi = $_POST['nomor_porsi'];
    $spph_validasi = $_POST['spph_validasi'];
    $alasan = 'Sakit Permanen'; // Set alasan otomatis menjadi Sakit Permanen
    $tanggal_sptjm = $_POST['tanggal_sptjm'];
    $tanggal_rekomendasi = $_POST['tanggal_rekomendasi'];
    $tanggal_masuk_surat = $_POST['tanggal_masuk_surat'];
    $tanggal_register = $_POST['tanggal_register'];
    $nomor_surat = $_POST['nomor_surat'];
    $nominal_setoran = $_POST['nominal_setoran'];
    $no_rekening_ahli_waris = $_POST['no_rekening_ahli_waris'];
    $bank_ahli_waris = $_POST['bank_ahli_waris'];
    $nama_kuasa_waris = $_POST['nama_kuasa_waris'];
    $tahun_sakit = $_POST['tahun_sakit'];
    $tanggal_sakit = $_POST['tanggal_sakit'];

    // Insert data ke tabel pelimpahan_sakit
    $query_pelimpahan = "
        INSERT INTO pelimpahan_sakit (
            nama_ahliwaris, nama_ayah, tempat_lahir, tanggal_lahir, alamat, kecamatan, kelurahan, provinsi,
            jenis_kelamin, pekerjaan, no_telepon, status_ahliwaris, nama_jamaah, bin_binti, tempat_lahir_jamaah, tanggal_lahir_jamaah, alamat_jamaah,
            kecamatan_jamaah, kelurahan_jamaah, provinsi_jamaah, bps, nomor_rekening, nomor_porsi, spph_validasi, alasan,
            tanggal_sptjm, tanggal_rekomendasi, tanggal_masuk_surat, tanggal_register, nomor_surat,
            nominal_setoran, no_rekening_ahli_waris, bank_ahli_waris, nama_kuasa_waris, tahun_sakit, tanggal_sakit
        )
        VALUES (
            '$nama_ahliwaris', '$nama_ayah', '$tempat_lahir', '$tanggal_lahir', '$alamat', '$kecamatan', '$kelurahan', '$provinsi',
            '$jenis_kelamin', '$pekerjaan', '$no_telepon', '$status_ahliwaris', '$nama_jamaah', '$bin_binti', '$tempat_lahir_jamaah', '$tanggal_lahir_jamaah', '$alamat_jamaah',
            '$kecamatan_jamaah', '$kelurahan_jamaah', '$provinsi_jamaah', '$bps', '$nomor_rekening', '$nomor_porsi', '$spph_validasi', '$alasan',
            '$tanggal_sptjm', '$tanggal_rekomendasi', '$tanggal_masuk_surat', '$tanggal_register', '$nomor_surat',
            '$nominal_setoran', '$no_rekening_ahli_waris', '$bank_ahli_waris', '$nama_kuasa_waris', '$tahun_sakit', '$tanggal_sakit'
        )";

    if (mysqli_query($koneksi, $query_pelimpahan)) {
        // Redirect ke halaman lain atau tampilkan pesan sukses
        header("Location: pelimpahan_haji.php?message=Data berhasil disimpan&status=success");
    } else {
        // Tampilkan error jika gagal
        echo "Error: " . mysqli_error($koneksi);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="tambah_pelimpahan.css">
    <link rel="icon" href="logo_kemenag.png">
    <title>Halaman Staf</title>
</head>

<body>
    <div class="background-atas">
        <div class="header">
            <h1>Tambah Pelimpahan - Sakit Permanen</h1>
            <div class="button-group">
                <button type="reset" form="form-data" class="btn btn-reset"><i class="fas fa-rotate-left"></i></button>
                <button type="button" class="btn btn-back" onclick="window.location.href='pelimpahan_haji.php'">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
        </div>
        <form action="tambah_pelimpahan_sakit.php" method="POST">
            <P>DATA AHLI WARIS</P>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_ahliwaris" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nama Ayah:</label>
                    <input type="text" name="nama_ayah" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon:</label>
                    <input type="number" name="no_telepon" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tempat Lahir:</label>
                    <input type="text" name="tempat_lahir" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir:</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Alamat:</label>
                    <textarea name="alamat" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label>Kecamatan:</label>
                    <select name="kecamatan" id="kecamatan" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kecamatan ---</option>
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
                <div class="form-group">
                    <label>Kelurahan:</label>
                    <select name="kelurahan" id="kelurahan" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kelurahan ---</option>
                    </select>

                    <script>
                        // Data kelurahan berdasarkan kecamatan
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
                        const kecamatanSelect = document.getElementById('kecamatan');
                        const kelurahanSelect = document.getElementById('kelurahan');

                        // Event listener untuk perubahan pada select kecamatan
                        kecamatanSelect.addEventListener('change', function() {
                            // Mengambil nilai kecamatan yang dipilih
                            const selectedKecamatan = this.value;

                            // Mengosongkan dropdown kelurahan
                            kelurahanSelect.innerHTML = '<option value="" disabled selected>--- Pilih Kelurahan ---</option>';

                            // Jika ada kecamatan yang dipilih
                            if (selectedKecamatan) {
                                // Mengambil data kelurahan untuk kecamatan yang dipilih
                                const kelurahanList = kelurahanData[selectedKecamatan];

                                // Menambahkan opsi kelurahan ke dropdown
                                kelurahanList.forEach(kelurahan => {
                                    const option = document.createElement('option');
                                    option.value = kelurahan;
                                    option.textContent = kelurahan;
                                    kelurahanSelect.appendChild(option);
                                });
                            }
                        });
                    </script>
                </div>
                <div class="form-group">
                    <label>Provinsi:</label>
                    <input type="text" name="provinsi" value="Kalimantan Selatan" class="form-control" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Jenis Kelamin:</label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Jenis Kelamin ---</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pekerjaan:</label>
                    <select name="pekerjaan" id="pekerjaan" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Pekerjaan ---</option>
                        <option value="Pelajar/Mahasiswa">Pelajar/Mahasiswa</option>
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
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nama Bank:</label>
                    <input type="text" name="bank_ahli_waris" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Rekening:</label>
                    <input type="number" name="no_rekening_ahli_waris" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Kuasa Waris:</label>
                    <input type="text" name="nama_kuasa_waris" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Status dengan Jamaah:</label>
                    <select name="status_ahliwaris" id="status_ahliwaris" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Status ---</option>
                        <option value="Suami">Suami</option> 
                        <option value="Istri">Istri</option>
                        <option value="Orang Tua Kandung">Orang Tua Kandung</option>
                        <option value="Anak Kandung">Anak Kandung</option>
                        <option value="Saudara Kandung">Saudara Kandung</option>
                    </select>
                </div>
            </div>

            <p>DATA JAMAAH</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="nama_jamaah" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>BIN/BINTI:</label>
                    <input type="text" name="bin_binti" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Porsi:</label>
                    <input type="number" name="nomor_porsi" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tempat Lahir:</label>
                    <input type="text" name="tempat_lahir_jamaah" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir:</label>
                    <input type="date" name="tanggal_lahir_jamaah" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Alamat:</label>
                    <textarea name="alamat_jamaah" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label>Kecamatan:</label>
                    <select name="kecamatan_jamaah" id="kecamatan_jamaah" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kecamatan ---</option>
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
                <div class="form-group">
                    <label>Kelurahan:</label>
                    <select name="kelurahan_jamaah" id="kelurahan_jamaah" class="form-control" required>
                        <option value="" disabled selected>--- Pilih Kelurahan ---</option>
                    </select>

                    <script>
                        // Data kelurahan berdasarkan kecamatan
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

                        // Event listener untuk perubahan pada select kecamatan
                        kecamatan_jamaahSelect.addEventListener('change', function() {
                            // Mengambil nilai kecamatan yang dipilih
                            const selectedKecamatan_jamaah = this.value;

                            // Mengosongkan dropdown kelurahan
                            kelurahan_jamaahSelect.innerHTML = '<option value="" disabled selected>--- Pilih Kelurahan ---</option>';

                            // Jika ada kecamatan yang dipilih
                            if (selectedKecamatan_jamaah) {
                                // Mengambil data kelurahan untuk kecamatan yang dipilih
                                const kelurahan_jamaahList = kelurahan_jamaahData[selectedKecamatan_jamaah];

                                // Menambahkan opsi kelurahan ke dropdown
                                kelurahan_jamaahList.forEach(kelurahan_jamaah => {
                                    const option = document.createElement('option');
                                    option.value = kelurahan_jamaah;
                                    option.textContent = kelurahan_jamaah;
                                    kelurahan_jamaahSelect.appendChild(option);
                                });
                            }
                        });
                    </script>
                </div>
                <div class="form-group">
                    <label>Provinsi:</label>
                    <input type="text" name="provinsi_jamaah" value="Kalimantan Selatan" class="form-control" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>BPS:</label>
                    <input type="text" name="bps" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Rekening:</label>
                    <input type="number" name="nomor_rekening" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nominal Setoran:</label>
                    <input type="text" name="nominal_setoran" value="25.000.000" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>SPPH/Validasi:</label>
                    <input type="text" name="spph_validasi" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alasan:</label>
                    <input type="text" name="alasan" value="Sakit Permanen" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Tanggal Register:</label>
                    <input type="date" name="tanggal_register" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Tahun Sakit:</label>
                    <input type="number" name="tahun_sakit" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Sakit:</label>
                    <input type="date" name="tanggal_sakit" class="form-control" required>
                </div>
            </div>
            <!-- Keterangan Surat -->
            <p>KETERANGAN SURAT</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal SPTJM:</label>
                    <input type="date" name="tanggal_sptjm" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Rekomendasi:</label>
                    <input type="date" name="tanggal_rekomendasi" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Masuk Surat:</label>
                    <input type="date" name="tanggal_masuk_surat" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Nomor Surat:</label>
                    <input type="number" name="nomor_surat" class="form-control" required>
                </div>
            </div>
            <!-- Tombol Simpan -->
            <div>
                <button type="submit" class="simpan">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>