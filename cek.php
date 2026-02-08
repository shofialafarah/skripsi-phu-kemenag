<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floating Placeholder</title>
    <style>
        :root {
            --primary-blue: #007BFF;
            --light-grey: #f7f7f7;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
            width: 300px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 10px;
            font-size: 16px;
            background-color: var(--light-grey);
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group label {
            position: absolute;
            top: 50%;
            left: 10px;
            font-size: 16px;
            color: #999;
            transform: translateY(-50%);
            pointer-events: none;
            transition: all 0.3s ease;
            background: #fff;
            padding: 0 5px;
        }

        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label {
            top: -5px;
            left: 10px;
            font-size: 12px;
            color: var(--primary-blue);
        }
    </style>
</head>
<body>
    <div class="form-group">
        <input type="text" id="name" placeholder=" " required />
        <label for="name">Nama Lengkap</label>
    </div>


<div>
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

    <label>Kelurahan:</label>
    <select name="kelurahan" id="kelurahan" class="form-control" required>
        <option value="" disabled selected>--- Pilih Kelurahan ---</option>
    </select>

    <script>
        // Data kelurahan berdasarkan kecamatan
        const kelurahanData = {
            "Aluh-Aluh": ["Aluh-Aluh Besar", "Aluh-Aluh Kecil", "Bakambat", "Handil Baru", "Handil Bujur", "Karya Baru", "Kuin Besar", "Labat Muara", "Pulantan", "Simpang Warga", "Sungai Musang", "Tanipah", "Tanjung Harapan"],
            "Aranio": ["Artain", "Benua Riam", "Kalaan", "Manunggal", "Rantau Balai", "Rantau Bujur", "Tiwingan Baru", "Tiwingan Lama"],
            "Astambul": ["Astambul Kota", "Astambul Seberang", "Kalampayan", "Kelampaian", "Limamar", "Lok Gabang", "Tambak Danau", "Tambangan"],
            "Beruntung Baru": ["Beruntung Baru", "Handil Bakti", "Indrasari", "Malintang", "Tanjung", "Teluk Mati"],
            "Cintapuri Darussalam": ["Cintapuri Darussalam", "Cintapuri Seberang", "Kabun Sari", "Sungai Raja"],
            "Gambut": ["Gambut", "Kalampaian Ulu", "Malintang", "Tambak Sirang Baru", "Tambak Sirang Darat", "Tambak Sirang Laut"],
            "Karang Intan": ["Belangian", "Jingah Habang", "Karang Intan", "Mandikapau", "Mandi Kapau Timur", "Sungai Alang", "Sungai Landas"],
            "Kertak Hanyar": ["Kertak Hanyar I", "Kertak Hanyar II", "Tatah Pemangkih Laut", "Tatah Belayung Baru", "Tatah Belayung"],
            "Mataraman": ["Mataraman", "Mataraman Barat", "Mataraman Timur", "Pemakuan", "Sungai Batang"],
            "Martapura": ["Indrasari", "Jawa", "Keraton", "Murung Keraton", "Pasayangan Barat", "Pasayangan Selatan", "Pasayangan Utara", "Pesayangan Timur", "Sekumpul", "Tanjung Rema", "Tanjung Rema Darat"],
            "Martapura Barat": ["Antasan Senor", "Bincau", "Sungai Kitano", "Sungai Rangas Hambuku", "Sungai Rangas Ulu"],
            "Martapura Timur": ["Antasan Sutum", "Dalam Pagar", "Sungai Sipai", "Sungai Tabuk Kota", "Tungkaran"],
            "Paramasan": ["Angkipih", "Paramasan Atas", "Paramasan Bawah"],
            "Pengaron": ["Bengkuang", "Pengaron", "Sungai Batang", "Sungai Pinang", "Telaga Biru"],
            "Sambung Makmur": ["Madurejo", "Sambung Makmur", "Sungai Kupang", "Sungai Raya"],
            "Simpang Empat": ["Banjarbaru Utara", "Cindai Alus", "Empat", "Lianganggang", "Sungai Besar", "Sungai Ulin"],
            "Sungai Pinang": ["Aranio", "Lok Buntar", "Rantau Kujang", "Sungai Pinang Lama"],
            "Sungai Tabuk": ["Gudang Hirang", "Lok Baintan", "Paku Alam", "Pemurus", "Sungai Lulut", "Sungai Tabuk Keramat"],
            "Tatah Makmur": ["Tatah Makmur", "Tatah Pemangkih Darat"],
            "Telaga Bauntung": ["Bauntung", "Mundar", "Pageran", "Telaga"]
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

</body>
</html>
