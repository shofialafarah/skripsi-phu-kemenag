<?php session_start();
include 'koneksi.php';

// Cek apakah user sudah login

$query = "
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

$result = $koneksi->query($query);
$data_aktivitas = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_aktivitas[] = $row;
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
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.css">
</head>

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_admin.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_admin.php'; ?>

            <main class="pPembatalan-wrapper">
                <div class="pPembatalan">
                    <div class="pPembatalan-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Laporan Aktivitas Pengguna
                    </div>
                    <div class="pPembatalan-body">
                        <div class="d-flex flex-wrap align-items-center mb-3">
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

                            <div class="ms-auto">
                                <button id="print-report-btn" class="btn btn-info btn-sm">
                                    <i class="fas fa-print me-1"></i> Cetak Laporan
                                </button>
                            </div>
                        </div>

                        <!-- Tabel untuk tampilan normal -->
                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">Nama Lengkap</th>
                                    <th class="text-center">Username</th>
                                    <th class="text-center">Terakhir Login</th>
                                    <th class="text-center">Tipe Aktivitas</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Waktu Aktivitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (!empty($data_aktivitas)) {
                                    foreach ($data_aktivitas as $row) {
                                        echo "<tr>";
                                        echo "<td class='text-center'>" . $no++ . "</td>";

                                        //Role
                                        $badgeColor = match ($row['role']) {
                                            'Jamaah' => 'bg-success',
                                            'Staf' => 'bg-warning text-dark',
                                            'Kepala Seksi' => 'bg-info text-dark',
                                            default => 'bg-secondary'
                                        };

                                        echo "<td class='text-center'>
    <span class='badge {$badgeColor}'>" . htmlspecialchars($row['role']) . "</span>
</td>";


                                        //Nama
                                        echo "<td class='text-center'>" . htmlspecialchars($row['nama_lengkap']) . "</td>";

                                        //Username
                                        echo "<td class='text-center'>" . htmlspecialchars($row['username']) . "</td>";

                                        // Tanggal Terakhir Login Pengguna
                                        $lastLogin = $row['last_login_at'] ? date('d-m-Y H:i', strtotime($row['last_login_at'])) : '-';
                                        echo "<td class='text-center'>" . $lastLogin . "</td>";

                                        //Tipe Aktivitas
                                        echo "<td class='text-center'>" . htmlspecialchars($row['tipe_aktivitas']) . "</td>";

                                        //Deskripsi
                                        echo "<td class='text-center'>" . htmlspecialchars($row['deskripsi_aktivitas']) . "</td>";

                                        //Waktu Aktivitas
                                        echo "<td class='text-center'>" . date('d-m-Y H:i:s', strtotime($row['waktu_aktivitas'])) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Tidak ada data berkas pembatalan jamaah haji yang ditemukan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="footer" style="color: white; text-align: center;">
                        <p style="margin: 0;">&copy; UNISKA_<?= date('Y'); ?> | Shofia Nabila Elfa Rahma. 2110010113.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Status Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditUser">
                        <div class="mb-3">
                            <label for="roleSelect" class="form-label">Pilih Role</label>
                            <select class="form-select" id="roleSelect" name="role">
                                <option value="" disabled selected>-- Pilih Role --</option>
                                <option value="jamaah">Jamaah</option>
                                <option value="staf">Staf</option>
                                <option value="kepala_seksi">Kepala Seksi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="usernameSelect" class="form-label">Pilih Username</label>
                            <select class="form-select" id="usernameSelect" name="id_pengguna">
                                <option value="" disabled selected>-- Pilih Username --</option>
                                <!-- Akan diisi dengan AJAX -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Status Pengguna</label>
                            <select class="form-select" id="statusSelect" name="status_pengguna">
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
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
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>

    <script>
        // Script untuk tombol cetak - PERBAIKAN UTAMA
        document.getElementById('print-report-btn').addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah form submit atau behavior default

            const startDate = document.getElementById('filter-start') ? document.getElementById('filter-start').value : '';
            const endDate = document.getElementById('filter-end') ? document.getElementById('filter-end').value : '';

            let url = 'cetak_laporan_aktivitas.php';

            // Jika kedua tanggal filter diisi, tambahkan sebagai parameter URL
            if (startDate && endDate) {
                url += `?start_date=${startDate}&end_date=${endDate}`;
            }

            console.log('Opening URL:', url); // Debug log

            // Buka halaman print di tab baru
            window.open(url, '_blank');
        });
    </script>
    <script src="tanggal_cetak_aktivitas.js"></script>
</body>

</html>