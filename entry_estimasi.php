<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login sebagai staf
if (!isset($_SESSION['id_staf']) || $_SESSION['role'] != 'staf') {
    header("Location: login.php");
    exit();
}

// Ambil ID staf dari session
$id_staf = $_SESSION['id_staf'];

// Ambil total estimasi (misalnya untuk statistik)
$query_total = "SELECT COUNT(*) AS total FROM estimasi";
$result_total = $koneksi->query($query_total);
$row_total = $result_total->fetch_assoc();
$total_estimasi = $row_total['total'] ?? 0;

// Ambil seluruh data estimasi dengan JOIN ke tabel pendaftaran
$data_estimasi = [];
$query_estimasi = "
    SELECT e.*, p.nama_jamaah, p.nomor_porsi, p.nama_ayah, p.jenis_kelamin, p.tanggal_lahir, p.status_pergi_haji
    FROM estimasi e 
    JOIN pendaftaran p ON e.id_pendaftaran = p.id_pendaftaran
    ORDER BY e.id_estimasi DESC
";

$result_estimasi = $koneksi->query($query_estimasi);

if ($result_estimasi && $result_estimasi->num_rows > 0) {
    while ($row = $result_estimasi->fetch_assoc()) {
        $data_estimasi[] = $row;
    }
}

// Ambil jamaah yang belum punya estimasi dengan data lengkap
$dropdown_jamaah = [];
$query_jamaah = "
    SELECT p.id_pendaftaran, p.nama_jamaah, p.nama_ayah, p.jenis_kelamin, p.tanggal_lahir, status_pergi_haji
    FROM pendaftaran p 
    LEFT JOIN estimasi e ON p.id_pendaftaran = e.id_pendaftaran 
    WHERE e.id_pendaftaran IS NULL
    ORDER BY p.nama_jamaah ASC
";

$result_jamaah = $koneksi->query($query_jamaah);
$jamaah_tersedia = 0;

if ($result_jamaah) {
    $jamaah_tersedia = $result_jamaah->num_rows;
    while ($row = $result_jamaah->fetch_assoc()) {
        $dropdown_jamaah[] = $row;
    }
}

// Statistik untuk debug
$query_total_pendaftaran = "SELECT COUNT(*) as total FROM pendaftaran";
$result_total_pendaftaran = $koneksi->query($query_total_pendaftaran);
$total_pendaftaran = $result_total_pendaftaran->fetch_assoc()['total'];

$query_total_estimasi = "SELECT COUNT(*) as total FROM estimasi";
$result_total_estimasi = $koneksi->query($query_total_estimasi);
$total_estimasi_db = $result_total_estimasi->fetch_assoc()['total'];

// Ambil semua nomor porsi yang sudah ada untuk validasi
$nomor_porsi_exists = [];
$query_porsi = "SELECT DISTINCT nomor_porsi FROM pendaftaran WHERE nomor_porsi IS NOT NULL AND nomor_porsi != ''";
$result_porsi = $koneksi->query($query_porsi);
if ($result_porsi) {
    while ($row = $result_porsi->fetch_assoc()) {
        $nomor_porsi_exists[] = $row['nomor_porsi'];
    }
}

// Fungsi untuk membersihkan input
function clean_input($data)
{
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

// Fungsi untuk menghitung selisih waktu dalam format yang lebih detail
function hitungSelisihWaktu($tanggal_awal, $tanggal_akhir)
{
    $awal = new DateTime($tanggal_awal);
    $akhir = new DateTime($tanggal_akhir);
    $selisih = $awal->diff($akhir);

    $hasil = [];

    if ($selisih->y > 0) {
        $hasil[] = $selisih->y . ' tahun';
    }
    if ($selisih->m > 0) {
        $hasil[] = $selisih->m . ' bulan';
    }
    if ($selisih->d > 0) {
        $hasil[] = $selisih->d . ' hari';
    }

    // Jika tidak ada selisih yang signifikan, tampilkan hari
    if (empty($hasil)) {
        $total_hari = $awal->diff($akhir)->days;
        if ($total_hari == 0) {
            return 'Hari ini';
        } else {
            return $total_hari . ' hari';
        }
    }

    return implode(', ', $hasil);
}

// Fungsi untuk menghitung dari hari ke format yang lebih readable
function formatWaktuDariHari($total_hari)
{
    $total_hari = (int) $total_hari;  // <-- tambahkan ini

    if ($total_hari == 0) {
        return 'Hari ini';
    }

    $tahun = floor($total_hari / 365);
    $sisa_hari = $total_hari % 365;
    $bulan = floor($sisa_hari / 30);
    $hari = $sisa_hari % 30;

    $hasil = [];

    if ($tahun > 0) {
        $hasil[] = $tahun . ' tahun';
    }
    if ($bulan > 0) {
        $hasil[] = $bulan . ' bulan';
    }
    if ($hari > 0) {
        $hasil[] = $hari . ' hari';
    }

    return empty($hasil) ? '0 hari' : implode(', ', $hasil);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Staf</title>
    <link rel="icon" href="logo_kemenag.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css" rel="stylesheet">
</head>


<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_staf.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_staf.php'; ?>

            <main class="entry-wrapper">
                <div class="entry">
                    <div class="entry-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Entry Dokumen Estimasi Keberangkatan Haji
                    </div>

                    <div class="entry-body">
                        <!-- Info Statistik -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 style="color: white;">Total Pendaftaran</h6>
                                        <h4 style="color: white;"><?= $total_pendaftaran ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 style="color: white;">Total Estimasi</h6>
                                        <h4 style="color: white;"><?= $total_estimasi_db ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 style="color: white;">Jamaah Tersedia</h6>
                                        <h4 style="color: white;"><?= $jamaah_tersedia ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 style="color: white;">Persentase</h6>
                                        <h4 style="color: white;"><?= $total_pendaftaran > 0 ? round(($total_estimasi_db / $total_pendaftaran) * 100, 1) : 0 ?>%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Tambah Data -->
                        <div class="mb-3">
                            <?php if ($jamaah_tersedia > 0): ?>
                                <button class="btn btn-success" onclick="showAddModal()">
                                    <i class="fas fa-plus"></i> Tambah Data (<?= $jamaah_tersedia ?> Tersedia)
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled title="Semua jamaah sudah memiliki estimasi">
                                    <i class="fas fa-plus"></i> Tambah Data (Tidak Ada yang Tersedia)
                                </button>
                                <div class="alert alert-info mt-2">
                                    <i class="fas fa-info-circle"></i>
                                    Semua jamaah yang terdaftar (<?= $total_pendaftaran ?> orang) sudah memiliki estimasi.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tabel Data -->
                        <div class="table-responsive">
                            <table id="dataEstimasi" class="table table-striped table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">NO</th>
                                        <th>NOMOR PORSI</th>
                                        <th>NAMA JAMAAH</th>
                                        <th>NAMA AYAH</th>
                                        <th>JENIS KELAMIN</th>
                                        <th>TANGGAL LAHIR</th>
                                        <th>UMUR</th>
                                        <th>TANGGAL PENDAFTARAN</th>
                                        <th>STATUS HAJI</th>
                                        <th>TELAH MENUNGGU</th>
                                        <th>ESTIMASI KEBERANGKATAN</th>
                                        <th>SISA MENUNGGU</th>
                                        <th>MASA MENUNGGU</th>
                                        <th class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if (count($data_estimasi) > 0) {
                                        foreach ($data_estimasi as $row) {
                                            echo "<tr>";
                                            echo "<td class='text-center'>" . $no++ . "</td>";
                                            echo "<td><span class='badge bg-primary'>" . htmlspecialchars($row['nomor_porsi'] ?? '') . "</span></td>";
                                            echo "<td>" . htmlspecialchars($row['nama_jamaah'] ?? '') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nama_ayah'] ?? '') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['jenis_kelamin'] ?? '') . "</td>";

                                            if (!empty($row['tanggal_lahir'])) {
                                                echo "<td>" . date('d-m-Y', strtotime($row['tanggal_lahir'])) . "</td>";

                                                $tanggal_lahir = new DateTime($row['tanggal_lahir']);
                                                $today = new DateTime();
                                                $umur = $today->diff($tanggal_lahir)->y;
                                                $badge = 'secondary';
                                                if ($umur < 17) {
                                                    $badge = 'warning';
                                                } elseif ($umur >= 60) {
                                                    $badge = 'danger';
                                                } else {
                                                    $badge = 'success';
                                                }
                                                echo "<td><span class='badge bg-$badge'>" . $umur . " Th</span></td>";
                                            } else {
                                                echo "<td>-</td>";
                                                echo "<td>-</td>";
                                            }

                                            if (!empty($row['tgl_pendaftaran'])) {
                                                echo "<td>" . date('d-m-Y', strtotime($row['tgl_pendaftaran'])) . "</td>";
                                            } else {
                                                echo "<td>-</td>";
                                            }

                                            echo "<td>" . htmlspecialchars($row['status_pergi_haji'] ?? '') . "</td>";

                                            // Format telah menunggu
                                            if (!empty($row['tgl_pendaftaran'])) {
                                                $tgl_pendaftaran = $row['tgl_pendaftaran'];
                                                $hari_ini = date('Y-m-d');
                                                echo "<td>" . hitungSelisihWaktu($tgl_pendaftaran, $hari_ini) . "</td>";
                                            } else {
                                                echo "<td>-</td>";
                                            }

                                            if (!empty($row['estimasi_berangkat'])) {
                                                $estimasi = new DateTime($row['estimasi_berangkat']);
                                                $hari_ini = new DateTime();
                                                $selisih_tahun = $hari_ini->diff($estimasi)->y;

                                                $badge_color = 'success';
                                                if ($selisih_tahun <= 1) {
                                                    $badge_color = 'danger';
                                                } elseif ($selisih_tahun <= 5) {
                                                    $badge_color = 'warning';
                                                }

                                                echo "<td><span class='badge bg-$badge_color'>" . $estimasi->format('d-m-Y') . "</span></td>";
                                            } else {
                                                echo "<td>-</td>";
                                            }

                                            // Format sisa menunggu
                                            $sisa_menunggu = $row['sisa_menunggu'] ?? 0;
                                            echo "<td>" . formatWaktuDariHari($sisa_menunggu) . "</td>";

                                            // Format masa menunggu
                                            $masa_menunggu = $row['masa_menunggu'] ?? 0;
                                            echo "<td>" . formatWaktuDariHari($masa_menunggu) . "</td>";

                                            // Encode data untuk modal edit
                                            $encodedRow = json_encode($row, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                                            echo "<td class='text-center' style='min-width: 150px;'>";
                                            // echo "<button class='btn btn-sm btn-warning me-1 mb-1' onclick='showEditModal($encodedRow)' title='Edit'><i class='fas fa-edit'></i></button>";
                                            echo "<button class='btn btn-sm btn-danger me-1 mb-1' onclick='hapusData(" . $row['id_estimasi'] . ")' title='Hapus'><i class='fas fa-trash'></i></button>";

                                            // Tombol Cetak
                                            echo "<a href='cetak_estimasi.php?id=" . $row['id_pendaftaran'] . "' target='_blank' class='btn btn-sm btn-success mb-1' title='Cetak'><i class='fas fa-print'></i></a>";

                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='14' class='text-center'>Tidak ada data estimasi tersedia.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="footer" style="color: white; text-align: center;">
                    <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Tambah Estimasi -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Data Estimasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="tambah_estimasi.php" method="post">
                    <div class="modal-body">

                        <!-- Status Info -->
                        <div class="alert alert-info">
                            <strong>Status:</strong>
                            <?php if ($jamaah_tersedia > 0): ?>
                                <span class="text-success">Ada <?= $jamaah_tersedia ?> jamaah yang belum memiliki estimasi</span>
                            <?php else: ?>
                                <span class="text-warning">Tidak ada jamaah yang tersedia untuk estimasi</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($jamaah_tersedia > 0): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="id_pendaftaran" class="form-label">
                                            <i class="fas fa-user"></i> Nama Jamaah
                                            <span class="badge bg-primary"><?= $jamaah_tersedia ?> tersedia</span>
                                        </label>
                                        <select name="id_pendaftaran" id="id_pendaftaran" class="form-control" required onchange="isiDataJamaah()">
                                            <option value="">-- Pilih Jamaah --</option>
                                            <?php foreach ($dropdown_jamaah as $row): ?>
                                                <option value="<?= $row['id_pendaftaran'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama_jamaah']) ?>"
                                                    data-ayah="<?= htmlspecialchars($row['nama_ayah']) ?>"
                                                    data-kelamin="<?= htmlspecialchars($row['jenis_kelamin']) ?>"
                                                    data-lahir="<?= $row['tanggal_lahir'] ?>"
                                                    data-status="<?= htmlspecialchars($row['status_pergi_haji']) ?>">
                                                    <?= htmlspecialchars($row['nama_jamaah']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            Data akan otomatis terisi setelah memilih jamaah
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nomor Porsi</label>
                                        <input type="text" class="form-control" name="nomor_porsi" id="nomor_porsi" required>
                                        <small class="form-text text-muted">Nomor porsi harus unik</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Ayah</label>
                                        <input type="text" class="form-control" name="nama_ayah" id="nama_ayah" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <input type="text" class="form-control" name="jenis_kelamin" id="jenis_kelamin" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal Pendaftaran</label>
                                        <input type="date" class="form-control" name="tgl_pendaftaran" id="tgl_pendaftaran" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status Haji</label>
                                        <input type="text" class="form-control" name="status_pergi_haji" id="status_pergi_haji" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>
                            </div>



                        <?php else: ?>
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> Tidak Ada Jamaah Tersedia</h5>
                                <p>Semua jamaah yang terdaftar sudah memiliki estimasi.</p>
                                <hr>
                                <p class="mb-0">
                                    <strong>Statistik:</strong><br>
                                    - Total Pendaftaran: <?= $total_pendaftaran ?> orang<br>
                                    - Total Estimasi: <?= $total_estimasi_db ?> orang<br>
                                    - Sisa yang belum estimasi: <?= $jamaah_tersedia ?> orang
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="modal-footer">
                        <?php if ($jamaah_tersedia > 0): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Tambah Estimasi
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary" disabled>
                                Tidak Dapat Menambah Data
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Estimasi -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Estimasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="edit_estimasi.php" method="post" id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_estimasi" id="edit_id_estimasi">
                        <input type="hidden" name="id_pendaftaran" id="edit_id_pendaftaran">
                        <input type="hidden" name="nomor_porsi_lama" id="edit_nomor_porsi_lama">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nomor_porsi" class="form-label">Nomor Porsi</label>
                                    <input type="text" class="form-control" name="nomor_porsi" id="edit_nomor_porsi" required>
                                    <small class="form-text text-muted">Nomor porsi harus unik</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nama_jamaah" class="form-label">Nama Jamaah</label>
                                    <input type="text" class="form-control" name="nama_jamaah" id="edit_nama_jamaah" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nama_ayah" class="form-label">Nama Ayah Kandung</label>
                                    <input type="text" class="form-control" name="nama_ayah" id="edit_nama_ayah" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                    <input type="text" class="form-control" name="jenis_kelamin" id="edit_jenis_kelamin" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" id="edit_tanggal_lahir" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tgl_pendaftaran" class="form-label">Tanggal Pendaftaran</label>
                                    <input type="date" class="form-control" name="tgl_pendaftaran" id="edit_tgl_pendaftaran" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status_haji" class="form-label">Status Haji</label>
                                    <input type="text" class="form-control" name="status_pergi_haji" id="edit_status_haji" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_telah_menunggu" class="form-label">Telah Menunggu (dalam hari)</label>
                                    <input type="number" class="form-control" name="telah_menunggu" id="edit_telah_menunggu">
                                    <small class="form-text text-muted">Masukkan dalam satuan hari</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_estimasi_berangkat" class="form-label">Estimasi Keberangkatan</label>
                                    <input type="date" class="form-control" name="estimasi_berangkat" id="edit_estimasi_berangkat">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_sisa_menunggu" class="form-label">Sisa Menunggu (dalam hari)</label>
                                    <input type="number" class="form-control" name="sisa_menunggu" id="edit_sisa_menunggu">
                                    <small class="form-text text-muted">Masukkan dalam satuan hari</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_masa_menunggu" class="form-label">Masa Menunggu (dalam hari)</label>
                                    <input type="number" class="form-control" name="masa_menunggu" id="edit_masa_menunggu">
                                    <small class="form-text text-muted">Masukkan dalam satuan hari</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
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
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>

    <script>
        // Fungsi untuk menampilkan modal tambah
        function showAddModal() {
            <?php if ($jamaah_tersedia > 0): ?>
                $('#addModal').modal('show');
            <?php else: ?>
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Jamaah Tersedia',
                    text: 'Semua jamaah yang terdaftar sudah memiliki estimasi.',
                    confirmButtonText: 'OK'
                });
            <?php endif; ?>
        }

        // Fungsi untuk mengisi data jamaah otomatis saat dropdown berubah
        function isiDataJamaah() {
            const selectElement = document.getElementById('id_pendaftaran');
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            if (selectedOption.value !== '') {
                // Ambil data dari atribut data-* pada option yang dipilih
                const namaAyah = selectedOption.getAttribute('data-ayah') || '';
                const jenisKelamin = selectedOption.getAttribute('data-kelamin') || '';
                const tanggalLahir = selectedOption.getAttribute('data-lahir') || '';
                const statusHaji = selectedOption.getAttribute('data-status') || '';

                // Isi field-field yang readonly
                document.getElementById('nama_ayah').value = namaAyah;
                document.getElementById('jenis_kelamin').value = jenisKelamin;
                document.getElementById('tanggal_lahir').value = tanggalLahir;
                document.getElementById('status_pergi_haji').value = statusHaji;

                // Debug log untuk memastikan data terambil
                console.log('Data jamaah yang dipilih:', {
                    namaAyah: namaAyah,
                    jenisKelamin: jenisKelamin,
                    tanggalLahir: tanggalLahir,
                    statusHaji: statusHaji
                });
            } else {
                // Kosongkan field jika tidak ada yang dipilih
                document.getElementById('nama_ayah').value = '';
                document.getElementById('jenis_kelamin').value = '';
                document.getElementById('tanggal_lahir').value = '';
                document.getElementById('status_pergi_haji').value = '';
            }
        }

        // Fungsi untuk menampilkan modal edit
        function showEditModal(data) {
            $('#id_estimasi').val(data.id_estimasi);
            $('#nomor_porsi').val(data.nomor_porsi);
            $('#nama_jamaah').val(data.nama_jamaah);
            $('#nama_ayah').val(data.nama_ayah);
            $('#jenis_kelamin').val(data.jenis_kelamin);
            $('#tanggal_lahir').val(data.tanggal_lahir);
            $('#status_pergi_haji').val(data.status_pergi_haji);
            $('#editModal').modal('show');
        }

        // Fungsi untuk menghapus data
        function hapusData(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_estimasi.php?id=' + id;
                }
            });
        }

        // Initialize DataTable
        $(document).ready(function() {
            $('#dataEstimasi').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json'
                },
            });
        });
    </script>

    <script src="entry_estimasi.js"></script>

</body>

</html>