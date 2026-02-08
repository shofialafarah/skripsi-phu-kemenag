<!-- =========================================================footer admin================================================= -->
    <script>
        function updateDate() {
            const now = new Date();
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            const namaHari = hari[now.getDay()];
            const tanggal = now.getDate();
            const namaBulan = bulan[now.getMonth()];
            const tahun = now.getFullYear();

            document.getElementById('currentDate').textContent = `${namaHari}, ${tanggal} ${namaBulan} ${tahun}`;
        }

        updateDate();
    </script>

    <script>
        function updateTime() {
            const now = new Date();

            // Ambil jam dan menit
            let hours = now.getHours();
            let minutes = now.getMinutes();

            // Tambahkan 0 di depan jika hanya 1 digit
            if (hours < 10) hours = '0' + hours;
            if (minutes < 10) minutes = '0' + minutes;

            // Format jam:menit
            const timeString = `${hours}:${minutes}`;

            // Masukkan ke elemen
            document.getElementById('currentTime').textContent = timeString;
        }

        // Panggil pertama kali
        updateTime();

        // Update setiap 30 detik (atau 60*1000 jika mau tiap menit saja)
        setInterval(updateTime, 30000);
    </script>