<?php session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php"); // Redirect ke login jika belum login atau session tidak ada
    exit();
}

// Ambil ID staf dari session
$id_kepala = $_SESSION['id_kepala'];

$query = "SELECT 
    p.nama_jamaah,
    p.nomor_porsi,
    p.tanggal_lahir,
    e.tgl_pendaftaran,
    e.telah_menunggu,
    e.estimasi_berangkat,
    e.umur,
    e.sisa_menunggu,
    e.masa_menunggu,
    j.email
FROM 
    pendaftaran p
JOIN 
    estimasi e ON e.id_pendaftaran = p.id_pendaftaran
JOIN 
    jamaah j ON p.id_jamaah = j.id_jamaah
";

$result = $koneksi->query($query);
$data_estimasi = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_estimasi[] = $row;
    }
}

function clean_input($data)
{
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Kepala Seksi</title>
    <link rel="icon" href="logo_kemenag.png">
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_kasi.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_kasi.php'; ?>

            <main class="pPendaftaran-wrapper">
                <div class="pPendaftaran">
                    <div class="pPendaftaran-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Laporan Estimasi Jamaah Haji
                    </div>
                    <div class="pPendaftaran-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div id="tanggal-filter" class="d-flex gap-2 flex-wrap align-items-end">
                                <div>
                                    <label for="filter-start" class="form-label">Tanggal Mulai</label>
                                    <input type="date" id="filter-start" class="form-control form-control-sm border border-secondary">
                                </div>
                                <div>
                                    <label for="filter-end" class="form-label">Tanggal Akhir</label>
                                    <input type="date" id="filter-end" class="form-control form-control-sm border border-secondary">
                                </div>
                                <div class="d-flex gap-2 align-items-end">
                                    <button id="filter-btn" class="btn btn-sm btn-primary">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <button id="reset-btn" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="ms-auto"> <button id="print-report-btn" class="btn btn-info btn-sm">
                                    <i class="fas fa-print me-1"></i> Cetak Laporan
                                </button>
                            </div>

                            <!-- Tabel untuk tampilan normal -->
                            <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Nomor Porsi</th>
                                        <th class="text-center">Nama Jamaah</th>
                                        <th class="text-center">Umur</th>
                                        <th class="text-center">Tanggal Pendaftaran</th>
                                        <th class="text-center">Telah Menunggu</th>
                                        <th class="text-center">Estimasi Berangkat</th>
                                        <th class="text-center">Sisa Menunggu</th>
                                        <th class="text-center">Masa Menunggu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if (!empty($data_estimasi)) {
                                        foreach ($data_estimasi as $row) {
                                            echo "<tr>";
                                            echo "<td class='text-center'>" . $no++ . "</td>";
                                            $nomor_porsi = htmlspecialchars($row['nomor_porsi']);
                                            if (!empty($nomor_porsi)) {
                                                echo "<td class='text-center'><span class='badge bg-primary'>$nomor_porsi</span></td>";
                                            } else {
                                                echo "<td class='text-center'><span class='badge bg-danger'>❌ Belum ada</span></td>";
                                            }

                                            echo "<td class='text-center'>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['umur']) . "</td>";
                                            echo "<td class='text-center'>" . date('d-m-Y', strtotime($row['tgl_pendaftaran'])) . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['telah_menunggu']) . "</td>";
                                            $tanggal = strtotime($row['estimasi_berangkat']);
                                            $hari_ini = strtotime(date('Y-m-d'));

                                            $warna_badge = ($tanggal > $hari_ini) ? 'badge-warning' : 'badge-success';

                                            echo "<td class='text-center'><span class='badge $warna_badge'>" . date('d-m-Y', $tanggal) . "</span></td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['sisa_menunggu']) . "</td>";
                                            echo "<td class='text-center'>" . htmlspecialchars($row['masa_menunggu']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='10' class='text-center'>Tidak ada data berkas pendaftaran jamaah haji yang ditemukan.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="footer" style="color: white; text-align: center;">
                        <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>

    <!-- PDF Generation Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Custom Scripts -->
    <script src="tanggal_cetak_estimasi.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mendapatkan referensi ke elemen input tanggal
            const filterStartInput = document.getElementById('filter-start');
            const filterEndInput = document.getElementById('filter-end');

            // Mendapatkan referensi ke tombol "Cetak Laporan"
            const cetakLaporanBtn = document.getElementById('cetak-laporan-btn'); // Pastikan ID ini sesuai

            // Tambahkan event listener ke tombol cetak
            if (cetakLaporanBtn) {
                cetakLaporanBtn.addEventListener('click', function() {
                    const startDate = filterStartInput.value; // Nilai sudah dalam YYYY-MM-DD dari input type="date"
                    const endDate = filterEndInput.value; // Nilai sudah dalam YYYY-MM-DD dari input type="date"

                    // Validasi sederhana (opsional tapi disarankan)
                    if (!startDate || !endDate) {
                        alert('Mohon lengkapi Tanggal Mulai dan Tanggal Akhir untuk mencetak laporan.');
                        return; // Hentikan proses jika tanggal belum diisi
                    }

                    // Bangun URL dengan parameter tanggal yang benar
                    const printUrl = `cetak_laporan_estimasi.php?start_date=${startDate}&end_date=${endDate}`;

                    // Buka tab baru untuk mencetak
                    window.open(printUrl, '_blank');
                });
            }
        });
    </script>
</body>

</html>