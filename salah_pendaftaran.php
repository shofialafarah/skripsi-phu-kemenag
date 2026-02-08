<?php
$conn = new mysqli("localhost", "root", "", "aplikasi_haji");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// Ambil data staf (pastikan nama kolom di tabel 'staf' adalah 'nama' — cek pakai DESCRIBE staf di phpMyAdmin)
$staffQuery = $conn->query("SELECT nama_staf FROM staf WHERE id_staf = 1");
$staff = $staffQuery->fetch_assoc();

// Ambil keyword pencarian dari URL (kalau ada)
$keyword = $_GET['search'] ?? '';
$keyword = $conn->real_escape_string($keyword);
// Ambil data pembatalan jamaah
$searchCondition = $keyword !== '' ? "WHERE jamaah.nama LIKE '%$keyword%'" : '';

$sql = "
    SELECT 
        jamaah.nama, 


        pendaftaran.dokumen, 
        pendaftaran.tanggal_pengajuan, 
        pendaftaran.tanggal_validasi 
    FROM pendaftaran
    JOIN jamaah ON pendaftaran.id_jamaah = jamaah.id_jamaah
    $searchCondition
";

$pendaftaranResult = $conn->query($sql);

// Fungsi untuk membersihkan input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Proses pencarian jika form search disubmit
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = clean_input($_GET['search']);
}

// Proses filter date jika ada
$start_date = "";
$end_date = "";
if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
    $start_date = clean_input($_GET['start_date']);
}
if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = clean_input($_GET['end_date']);
}

// Proses validasi (setuju/tolak) jika ada
if (isset($_POST['action']) && isset($_POST['pendaftaran_id'])) {
    $action = clean_input($_POST['action']);
    $pendaftaran_id = clean_input($_POST['pendaftaran_id']);
    $tanggal_validasi = date("Y-m-d H:i:s");
    
    if ($action == 'setuju') {
        $status = 'Disetujui';
    } else if ($action == 'tolak') {
        $status = 'Ditolak';
    }
    
    $update_query = "UPDATE pendaftaran SET status = '$status', tanggal_validasi = '$tanggal_validasi' WHERE id = '$pendaftaran_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success_message'] = "Status pendaftaran berhasil diperbarui";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui status: " . mysqli_error($conn);
    }
    
    // Redirect untuk menghindari pengiriman ulang form saat refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Pendaftaran</title>
    <link rel="stylesheet" href="monitoring_pendaftaran.css">
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0"/>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
</head>
<body>
    <!-- ====================================== sidebar ========================================== -->
    <?php include 'sidebar_jamaah.php'; ?>
<!-- ====================================== header =========================================== -->
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="welcome-section">
                <h1>Selamat Datang, <?= htmlspecialchars($staff['nama_staf']) ?></h1>
                <div class="date-time-section">
                    <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                    <div class="current-time" id="currentTime">07:30:00</div>
                </div>
            </div>
            <div class="header-actions">
                <form method="GET" class="search-container">
                    <span class="material-symbols-outlined search-icon">search</span>
                    <input type="text" name="search" id="searchInput" class="search-input" placeholder="Cari Jamaah..." value="<?= htmlspecialchars($keyword) ?>" />
                </form>
                <button class="icon-button"><span class="material-symbols-outlined">mail</span></button>
                <button class="icon-button"><span class="material-symbols-outlined">notifications</span></button>
            </div>
        </div>
    <!-- =================================================================================== -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Monitoring Pendaftaran Jamaah Haji</h1>
                </div>
                
                
                <!-- Filter dan Pencarian -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-filter me-1"></i> Filter dan Pencarian
                    </div>
                    <div class="card-body">
                        <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari berdasarkan nama</label>
                                <input type="text" class="form-control" id="search" name="search" value="<?php echo $search_query; ?>" placeholder="Masukkan nama...">
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>                

                <!-- Tabel Data Pendaftaran -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-table me-1"></i> Data Pendaftaran
                    </div>
                    <div class="card-body">
                        <table id="dataPendaftaran" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>No. Telepon</th>
                                    <th>Email</th>
                                    <th class="text-center">Dokumen</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Tanggal Validasi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query untuk mengambil data pendaftaran
                                $query = "SELECT * FROM pendaftaran WHERE 1=1";
                                
                                // Tambahkan filter pencarian jika ada
                                if (!empty($search_query)) {
                                    $query .= " AND nama_jamaah LIKE '%$search_query%'";
                                }
                                
                                // Tambahkan filter tanggal jika ada
                                if (!empty($start_date) && !empty($end_date)) {
                                    $query .= " AND tanggal_pengajuan BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
                                } else if (!empty($start_date)) {
                                    $query .= " AND tanggal_pengajuan >= '$start_date 00:00:00'";
                                } else if (!empty($end_date)) {
                                    $query .= " AND tanggal_pengajuan <= '$end_date 23:59:59'";
                                }
                                
                                $query .= " ORDER BY tanggal_pengajuan DESC";
                                $result = mysqli_query($conn, $query);
                                
                                $no = 1;
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_jamaah']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nik']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td class='text-center'><a href='lihat_dokumen.php?id_pendaftaran=" . $row['id_pendaftaran'] . "' class='btn btn-sm btn-info' target='_blank'><i class='fas fa-file-pdf'></i> Lihat</a></td>";
                                        echo "<td>" . date('d-m-Y H:i', strtotime($row['tanggal_pengajuan'])) . "</td>";
                                        echo "<td>" . (!empty($row['tanggal_validasi']) ? date('d-m-Y H:i', strtotime($row['tanggal_validasi'])) : '-') . "</td>";
                                        
                                        // Status dengan label warna
                                        $status_class = '';
                                        switch($row['status']) {
                                            case 'Disetujui':
                                                $status_class = 'success';
                                                break;
                                            case 'Ditolak': 
                                                $status_class = 'danger';
                                                break;
                                            default:
                                                $status_class = 'warning';
                                                break;
                                        }
                                        echo "<td class='text-center'><span class='badge bg-" . $status_class . "'>" . (empty($row['status']) ? 'Menunggu' : htmlspecialchars($row['status'])) . "</span></td>";
                                        
                                        // Tombol aksi hanya ditampilkan jika belum divalidasi
                                        echo "<td class='text-center'>";
                                        if (empty($row['status']) || $row['status'] == 'Menunggu') {
                                            echo "<div class='btn-group' role='group'>";
                                            echo "<form method='post' class='d-inline me-1'>";
                                            echo "<input type='hidden' name='pendaftaran_id' value='" . $row['id_pendaftaran'] . "'>";
                                            echo "<input type='hidden' name='action' value='setuju'>";
                                            echo "<button type='submit' class='btn btn-sm btn-success' onclick=\"return confirm('Apakah Anda yakin ingin menyetujui pendaftaran ini?');\"><i class='fas fa-check'></i></button>";
                                            echo "</form>";
                                            
                                            echo "<form method='post' class='d-inline'>";
                                            echo "<input type='hidden' name='pendaftaran_id' value='" . $row['id_pendaftaran'] . "'>";
                                            echo "<input type='hidden' name='action' value='tolak'>";
                                            echo "<button type='submit' class='btn btn-sm btn-danger' onclick=\"return confirm('Apakah Anda yakin ingin menolak pendaftaran ini?');\"><i class='fas fa-times'></i></button>";
                                            echo "</form>";
                                            echo "</div>";
                                        } else {
                                            echo "<span class='text-muted'>-</span>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data pendaftaran yang ditemukan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#dataPendaftaran').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    titleAttr: 'Export ke PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 6, 7, 8]
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 11;
                        doc.content[1].table.widths = ['5%', '15%', '10%', '10%', '15%', '15%', '15%', '15%'];
                        doc.content.splice(0, 1, {
                            text: 'Data Monitoring Pendaftaran',
                            fontSize: 14,
                            alignment: 'center',
                            margin: [0, 0, 0, 12]
                        });
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    titleAttr: 'Export ke Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 6, 7, 8]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    titleAttr: 'Print',
                    className: 'btn btn-primary btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 6, 7, 8]
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/id.json',
            }
        });
    });
    </script>
    <script src="main.js"></script> <!-- Sidebar script -->
    <script src="monitoring_pembatalan.js"></script>
    <script>
    const updateDateTime = () => {
        const now = new Date();
        const days = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
        const months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        const day = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        document.getElementById("currentDate").textContent = `${day}, ${date} ${month} ${year}`;
        document.getElementById("currentTime").textContent = `${hours}:${minutes}:${seconds}`;
    };
    setInterval(updateDateTime, 1000);
    updateDateTime(); // pertama kali load
</script>

</body>
</html>