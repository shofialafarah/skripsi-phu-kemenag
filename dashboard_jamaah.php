<?php 
session_start();
include 'koneksi.php';

// Cek apakah user sudah login sebagai jamaah
if (!isset($_SESSION['id_jamaah'])) {
    header("Location: login.php");
    exit();
}

// Ambil keyword pencarian dari URL (kalau ada)
$keyword = $_GET['search'] ?? '';
$keyword = $koneksi->real_escape_string($keyword);

// Ambil daftar jamaah (filtered atau tidak)
if ($keyword !== '') {
    $sql = "SELECT nama FROM jamaah WHERE nama LIKE '%$keyword%'";
} else {
    $sql = "SELECT nama FROM jamaah";
}
$jamaahResult = $koneksi->query($sql);

// PERBAIKAN: Ambil id_jamaah dari session, bukan hardcode
$id_jamaah = $_SESSION['id_jamaah']; // Ambil dari session login

// Query untuk mengambil data jamaah yang sedang login
$sql = "SELECT jamaah.nama, pendaftaran.nomor_porsi, jamaah.foto, jamaah.id_jamaah
        FROM jamaah
        LEFT JOIN pendaftaran ON pendaftaran.id_jamaah = jamaah.id_jamaah
        WHERE jamaah.id_jamaah = ?";
        
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_jamaah);
$stmt->execute();
$result = $stmt->get_result();

// Inisialisasi variabel default
$nama = "Nama tidak ditemukan";
$nomor_porsi = "Belum terdaftar";
$foto = 'profil.jpg';

if ($result && $row = $result->fetch_assoc()) {
    $nama = $row['nama'];
    $nomor_porsi = $row['nomor_porsi'] ?? 'Belum terdaftar';
    $foto_path = 'uploads/' . $row['foto'];
    if (!empty($row['foto']) && file_exists($foto_path)) {
        $foto = $row['foto'];
    } else {
        $foto = 'profil.jpg'; // fallback ke default
    }
} else {
    // Jika data tidak ditemukan, ambil data dasar dari tabel jamaah saja
    $sql_basic = "SELECT nama, foto FROM jamaah WHERE id_jamaah = ?";
    $stmt_basic = $koneksi->prepare($sql_basic);
    $stmt_basic->bind_param("i", $id_jamaah);
    $stmt_basic->execute();
    $result_basic = $stmt_basic->get_result();
    
    if ($result_basic && $row_basic = $result_basic->fetch_assoc()) {
        $nama = $row_basic['nama'];
        $nomor_porsi = 'Belum terdaftar';
        $foto_path = 'uploads/' . $row_basic['foto'];
        if (!empty($row_basic['foto']) && file_exists($foto_path)) {
            $foto = $row_basic['foto'];
        } else {
            $foto = 'profil.jpg';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Jamaah</title>
    <link rel="icon" href="logo_kemenag.png">
    <link rel="stylesheet" href="kumpulan-css/global_style.css">
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

            <main class="dashboardJamaah-wrapper">

                <div class="jamaah-grid">
                    <!-- Cuaca -->
                    <div class="card cuaca">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div class="temperature">24°C</div>
                                <div class="date-info" style="font-size: 1rem; color: gray;">Martapura, Kalimantan Selatan</div>
                            </div>
                            <span class="material-symbols-outlined" style="font-size: 48px; color: #f39c12;">wb_sunny</span>
                        </div>
                        <div class="weather-details" style="margin-top: 10px; display: flex; gap: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="background-color: #e0f7fa; border-radius: 6px; padding: 4px;">
                                    <span class="material-symbols-outlined" style="font-size: 20px; color: #0288d1;">water_drop</span>
                                </div>
                                <div>Humidity: 40%</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="background-color: #f3e5f5; border-radius: 6px; padding: 4px;">
                                    <span class="material-symbols-outlined" style="font-size: 20px; color: #8e24aa;">compress</span>
                                </div>
                                <div>Pressure: 20%</div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="background-color: #e8f5e9; border-radius: 6px; padding: 4px;">
                                    <span class="material-symbols-outlined" style="font-size: 20px; color: #388e3c;">air</span>
                                </div>
                                <div>Wind: 8 Km/h</div>
                            </div>
                        </div>
                        <div class="profil-jamaah">
                            <div class="jamaah-card">
                                <img src="uploads/<?= htmlspecialchars($foto) ?>" alt="Foto Jamaah" class="jamaah-foto" />
                                <div class="jamaah-info">
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div>
                                            <div class="nama-jamaah"><?= htmlspecialchars($nama) ?></div>
                                            <div class="nomor-porsi">No. Porsi <b><?= htmlspecialchars($nomor_porsi) ?></b></div>
                                        </div>
                                        <span class="material-symbols-outlined titik-tiga" onclick="togglePopup()">more_vert</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Popup Form -->
                            <div class="popup-form" id="popupForm">
                                <form method="POST" action="edit-jamaah.php" enctype="multipart/form-data">
                                    <input type="hidden" name="id_jamaah" value="<?= $id_jamaah ?>">
                                    <label>Nama:</label>
                                    <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>

                                    <label>Nomor Porsi:</label>
                                    <input type="text" name="nomor_porsi" value="<?= htmlspecialchars($nomor_porsi) ?>" required>

                                    <label>Foto Baru (opsional):</label>
                                    <input type="file" name="foto">

                                    <button type="submit">Simpan</button>
                                    <button type="button" onclick="togglePopup()">Batal</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Waktu Kerja -->
                    <div class="card waktu-kerja">
                        <span class="material-symbols-outlined icon-waktuKerja">more_horiz</span>
                        <div class="working-time-value">450 H</div>
                        <div class="working-time-label">Working Time</div>
                        <div class="farming-activities">
                            <div class="farming-activity">
                                <div class="working-bar-wrapper">
                                    <div class="working-bar bar-base"></div>
                                    <div class="working-bar bar-overlay height-medium"></div>
                                </div>
                                <div class="working-name">Plowing</div>
                            </div>
                            <div class="farming-activity">
                                <div class="working-bar-wrapper">
                                    <div class="working-bar bar-base"></div>
                                    <div class="working-bar bar-overlay height-low"></div>
                                </div>
                                <div class="working-name">Spraying</div>
                            </div>
                            <div class="farming-activity">
                                <div class="working-bar-wrapper">
                                    <div class="working-bar bar-base"></div>
                                    <div class="working-bar bar-overlay height-medium"></div>
                                </div>
                                <div class="working-name">Fertilization</div>
                            </div>
                            <div class="farming-activity">
                                <div class="working-bar-wrapper">
                                    <div class="working-bar bar-base"></div>
                                    <div class="working-bar bar-overlay height-high"></div>
                                </div>
                                <div class="working-name">Harvesting</div>
                            </div>
                        </div>
                    </div>

                    <!-- Kalender -->
                    <div class="card kalender-modern-fix">
                        <div class="kalender-header-fix">
                            <span>May 2025</span>
                        </div>
                        <div class="kalender-grid-fix">
                            <div class="kalender-day-fix">Sun</div>
                            <div class="kalender-day-fix">Mon</div>
                            <div class="kalender-day-fix">Tue</div>
                            <div class="kalender-day-fix">Wed</div>
                            <div class="kalender-day-fix">Thu</div>
                            <div class="kalender-day-fix">Fri</div>
                            <div class="kalender-day-fix">Sat</div>

                            <!-- contoh isi tanggal -->
                            <div class="kalender-date-fix">1</div>
                            <div class="kalender-date-fix">2</div>
                            <div class="kalender-date-fix today-fix">3</div>
                            <div class="kalender-date-fix">4</div>
                            <!-- lanjutkan sesuai kebutuhan -->
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="sidebar_jamaah.js"></script>
    <script>
        const grid = document.querySelector('.kalender-grid-fix');
        const header = document.querySelector('.kalender-header-fix span:first-child');

        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth(); // 0-indexed

        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        header.textContent = `${monthNames[month]} ${year}`;

        // Clear previous dates if needed
        grid.innerHTML = '';

        // Days of the week
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        for (const day of dayNames) {
            const dayEl = document.createElement('div');
            dayEl.className = 'kalender-day-fix';
            dayEl.textContent = day;
            grid.appendChild(dayEl);
        }

        // Find first day and number of days in month
        const firstDay = new Date(year, month, 1).getDay(); // 0 (Sun) to 6 (Sat)
        const lastDate = new Date(year, month + 1, 0).getDate(); // Last date of current month

        // Fill empty cells before 1st
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'kalender-date-fix empty';
            grid.appendChild(emptyCell);
        }

        // Fill the dates
        for (let i = 1; i <= lastDate; i++) {
            const dateCell = document.createElement('div');
            dateCell.className = 'kalender-date-fix';
            dateCell.textContent = i;

            if (
                i === today.getDate() &&
                month === today.getMonth() &&
                year === today.getFullYear()
            ) {
                dateCell.classList.add('today-fix');
            }

            grid.appendChild(dateCell);
        }
    </script>
    <script>
        function togglePopup() {
            const popup = document.getElementById("popupForm");
            popup.style.display = (popup.style.display === "block") ? "none" : "block";
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const apiKey = "10fc461d891b997b984bfd3e8114334b";
            const city = "Martapura,ID"; // Kota dan kode negara (ID untuk Indonesia)
            const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const temp = Math.round(data.main.temp);
                    const humidity = data.main.humidity;
                    const pressure = data.main.pressure;
                    const wind = data.wind.speed;
                    const icon = data.weather[0].icon;

                    document.querySelector(".temperature").textContent = `${temp}°C`;

                    // Menampilkan nama kota dan provinsi
                    document.querySelector(".date-info").textContent = "Martapura, Kalimantan Selatan";

                    // Update elemen lainnya (jika perlu)
                    // document.querySelectorAll(".weather-details div")[0].lastElementChild.textContent = `Humidity: ${humidity}%`;
                    // document.querySelectorAll(".weather-details div")[1].lastElementChild.textContent = `Pressure: ${pressure} hPa`;
                    // document.querySelectorAll(".weather-details div")[2].lastElementChild.textContent = `Wind: ${wind} Km/h`;
                })
                .catch(error => {
                    console.error("Gagal ambil data cuaca:", error);
                });
        });
    </script>

</body>

</html>