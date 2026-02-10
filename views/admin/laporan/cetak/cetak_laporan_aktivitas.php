<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';

// Pastikan parameter ini diteruskan dari laporan_riwayat_aksi.php
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$base_union = "
    SELECT 'Jamaah' AS role, id_jamaah AS id, nama AS nama_lengkap, username, 
       last_login_at, tipe_aktivitas, deskripsi_aktivitas, waktu_aktivitas
FROM jamaah
UNION ALL
SELECT 'Staf' AS role, id_staf AS id, nama_staf AS nama_lengkap, username, last_login_at, tipe_aktivitas, deskripsi_aktivitas, waktu_aktivitas
FROM staf
UNION ALL
SELECT 'Kepala Seksi' AS role, id_kepala AS id, nama_kepala AS nama_lengkap, username, last_login_at, tipe_aktivitas, deskripsi_aktivitas, waktu_aktivitas
FROM kepala_seksi
ORDER BY waktu_aktivitas DESC

";

$params = [];
$types = '';

if ($start_date && $end_date) {
    // Apply date filter on the combined result's last column
    $query = "SELECT * FROM (" . $base_union . ") AS t WHERE t.waktu_aktivitas BETWEEN ? AND ? ORDER BY t.waktu_aktivitas DESC";
    $params[] = $start_date;
    $params[] = $end_date;
    $types = 'ss';
} else {
    $query = "SELECT * FROM (" . $base_union . ") AS t ORDER BY t.waktu_aktivitas DESC";
}

// Prepare and execute
if (!empty($params)) {
    $stmt = $koneksi->prepare($query);
    if ($stmt) {
        // bind_param requires variables passed by reference; create variables dynamically
        $bind_names[] = $types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "Error preparing statement: " . $koneksi->error;
        $result = false;
    }
} else {
    $result = $koneksi->query($query);
}


$data_pengguna = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pengguna[] = $row;
    }
}

$tanggal = date('d');
$bulan_angka = date('m');
$tahun = date('Y');
$bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];
$format_tanggal = $tanggal . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Aktivitas</title>
    <link rel="icon" href="../assets/img/logo_kemenag.png">
    <link rel="stylesheet" href="../assets/css/cetak.css">
</head>

<body>
    <div class="cetak-wrapper">
        <div class="kop-surat">
            <img align="left" src="/phu-kemenag-banjar-copy/assets/logo_kemenag.png" alt="Logo Kemenag">
            <h2>KEMENTERIAN AGAMA REPUBLIK INDONESIA <br>
                KANTOR KEMENTERIAN AGAMA KABUPATEN BANJAR</h2>
            <p>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura, Banjar 70614 <br>
                Telp/Fax: (0511) 4721442 | Email: <a href="mailto:phukemenagbanjar@gmail.com">phukemenagbanjar@gmail.com</a>
            </p>
        </div>

        <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN DATA AKTIVITAS PENGGUNA</h3>

        <?php
        // Format dan tampilkan periode tanggal
        if ($start_date && $end_date) {
            $start_formatted = date('d-m-Y', strtotime($start_date));
            $end_formatted = date('d-m-Y', strtotime($end_date));
            echo "<p style=\"text-align: center; margin: 5px 0 20px 0; font-style: italic;\">Periode: <strong>" . htmlspecialchars($start_formatted) . "</strong> sampai <strong>" . htmlspecialchars($end_formatted) . "</strong></p>";
        }
        ?>

        <table class="tabel-cetak">
            <thead>
                <tr>
                    <th class="kepala-cetak">No</th>
                    <th class="kepala-cetak">Role</th>
                    <th class="kepala-cetak">Nama Lengkap</th>
                    <th class="kepala-cetak">Username</th>
                    <th class="kepala-cetak">Terakhir Login</th>
                    <th class="kepala-cetak">Tipe Aktivitas</th>
                    <th class="kepala-cetak">Deskripsi</th>
                    <th class="kepala-cetak">Waktu Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (!empty($data_pengguna)) {
                    foreach ($data_pengguna as $row) {
                        // Hilangkan tag HTML dari badge, hanya ambil teksnya untuk dicetak
                        echo "<tr>";
                        echo "<td class='badan-cetak'>" . $no++ . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                        echo "<td class='badan-cetak'>" . htmlspecialchars($row['username']) . "</td>";

                        // Tanggal Terakhir Login Pengguna
                        $lastLogin = $row['last_login_at'] ? date('d-m-Y H:i', strtotime($row['last_login_at'])) : '-';
                        echo "<td class='badan-cetak''>" . $lastLogin . "</td>";
                        echo "<td class='badan-cetak'>" . (!empty($row['tipe_aktivitas']) ? htmlspecialchars($row['tipe_aktivitas']) : "-") . "</td>";
                        echo "<td class='badan-cetak'>" . (!empty($row['deskripsi_aktivitas']) ? htmlspecialchars($row['deskripsi_aktivitas']) : "-") . "</td>";
                        echo "<td class='badan-cetak'>" . (!empty($row['waktu_aktivitas']) ? date('d-m-Y H:i:s', strtotime($row['waktu_aktivitas'])) : "-") . "</td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='badan-cetak'>Tidak ada data aktivitas pengguna yang ditemukan.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
            <table style="border: none; text-align: left; width: auto;">
                <tr>
                    <td style="border: none; padding: 10px; line-height: 1.2;">
                        <p style="margin: 0;">Martapura, <?php echo $format_tanggal; ?></p>
                        <p style="margin: 0;">An. Kepala Kantor Kementerian Agama</p>
                        <p style="margin: 0;">Kabupaten Banjar,</p>
                        <p style="margin: 0;">Kepala Seksi Peny. Haji dan Umrah</p>
                        <div style="margin: 20px 0; text-align: center;">
                            <img src="../assets/img/ttd_kasi.jpg" alt="Tanda Tangan Kepala Seksi" style="width: 80px; height: auto;">
                            <!-- <br><br><br> -->
                        </div>
                        <strong>
                            <p style="margin: 0;"><u>Erfan Maulana</u></p>
                            <p style="margin: 0;">NIP.198411042003121004</p>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>

        <script>
            // Otomatis memicu dialog cetak saat halaman dimuat
            window.onload = function() {
                window.print();
                // Opsional: Tutup tab setelah mencetak. Beberapa browser mungkin memblokir ini.
                // window.onafterprint = function() { window.close(); };
            };
        </script>
    </div>
</body>

</html>