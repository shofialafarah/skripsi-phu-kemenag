<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include_once __DIR__ . '/../../../includes/koneksi.php';

$query = "
    SELECT 'Jamaah' AS role, id_jamaah AS id, nama AS nama_lengkap, username, email, nomor_telepon AS no_telepon, created_at, updated_at, status_pengguna
    FROM jamaah
    UNION ALL
    SELECT 'Staf' AS role, id_staf AS id, nama_staf AS nama_lengkap, username, email, no_telepon, created_at, updated_at, status_pengguna
    FROM staf
    UNION ALL
    SELECT 'Kepala Seksi' AS role, id_kepala AS id, nama_kepala AS nama_lengkap, username, email, no_telepon, created_at, updated_at, status_pengguna
    FROM kepala_seksi
    ORDER BY created_at DESC
";

$result = $koneksi->query($query);
$data_pengguna = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data_pengguna[] = $row;
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
<link rel="stylesheet" href="assets/css/laporan.css">
<?php include '../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../includes/header_admin.php'; ?>

        <main class="laporan-wrapper">
            <div class="laporan">
                <div class="laporan-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Laporan Akun Pengguna Sistem
                </div>
                <div class="laporan-body">
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
                            <button id="edit-user-btn" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="fas fa-edit me-1"></i> Edit Status
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
                                <th class="text-center">Email</th>
                                <th class="text-center">Nomor Telepon</th>
                                <th class="text-center">Tanggal Akun Dibuat</th>
                                <th class="text-center">Tanggal Akun Diubah</th>
                                <th class="text-center">Status Pengguna</th>
                                <!-- <th class="text-center">Aksi</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if (!empty($data_pengguna)) {
                                foreach ($data_pengguna as $row) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $no++ . "</td>";

                                    //Role
                                    echo "<td class='text-center'>
                                            <span class='badge bg-primary'>" . htmlspecialchars($row['role']) . "</span>
                                        </td>";

                                    //Nama
                                    echo "<td class='text-center'>" . htmlspecialchars($row['nama_lengkap']) . "</td>";

                                    //Username
                                    echo "<td class='text-center'>" . htmlspecialchars($row['username']) . "</td>";

                                    //Email
                                    echo "<td class='text-center'>
                                            <a href='mailto:" . htmlspecialchars($row['email']) . "' style='color: #0d6efd; text-decoration: underline;'>" . htmlspecialchars($row['email']) . "</a>
                                        </td>";

                                    //Nomor Telepon
                                    $no_wa = preg_replace('/[^0-9]/', '', $row['no_telepon']);
                                    $link_wa = "https://wa.me/" . $no_wa;
                                    echo "<td class='text-center'>
                                            <a href='$link_wa' target='_blank' class='text-decoration-none'>
                                                <span class='badge bg-success'><i class='fas fa-phone-alt me-1'></i>" . htmlspecialchars($row['no_telepon']) . "</span>
                                            </a>
                                        </td>";

                                    // Tanggal Dibuatnya Akun Pengguna
                                    echo "<td class='text-center'>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";

                                    // Tanggal Diubahnya Akun Pengguna
                                    echo "<td class='text-center'>" . date('d-m-Y', strtotime($row['updated_at'])) . "</td>";

                                    //Status Pengguna
                                    $status_raw = strtolower(trim($row['status_pengguna'] ?? ''));

                                    switch ($status_raw) {
                                        case 'aktif':
                                            $status_tampil = "<span class='badge bg-success'>Aktif</span>";
                                            break;
                                        case 'nonaktif':
                                            $status_tampil = "<span class='badge bg-secondary'>Nonaktif</span>";
                                            break;
                                        case 'banned':
                                            $status_tampil = "<span class='badge bg-danger'>Diblokir</span>";
                                            break;
                                        default:
                                            $status_tampil = "<span class='badge bg-warning text-dark'>" . htmlspecialchars(ucfirst($status_raw)) . "</span>";
                                            break;
                                    }

                                    echo "<td class='text-center'>{$status_tampil}</td>";

                                    // Tombol Edit per baris
                                    // echo "<td class='text-center'>
                                    //     <button class='btn btn-warning btn-sm btn-edit' data-role='" . $row['role'] . "' data-id='" . $row['id'] . "'>
                                    //         <i class='fas fa-edit'></i> Edit
                                    //     </button>
                                    // </td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10' class='text-center'>Tidak ada data berkas pembatalan jamaah haji yang ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php include_once __DIR__ . '/../includes/footer_admin.php'; ?>
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
<script src="../assets/js/sidebar.js"></script>
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

<script src="assets/js/cetak_data_pengguna.js"></script>
<script src="assets/js/tanggal_cetak_pengguna.js"></script>
<script>
$(document).ready(function() {
    // Fungsi ini berjalan saat pilihan Role berubah
    $('#roleSelect').on('change', function() {
        var role = $(this).val(); // Mengambil nilai role yang dipilih (jamaah/staf/kepala_seksi)
        var usernameSelect = $('#usernameSelect');

        // Tampilkan pesan loading sementara
        usernameSelect.html('<option value="" disabled selected>-- Memuat... --</option>');

        if (role) {
            $.ajax({
                // SESUAIKAN PATH INI: pastikan url-nya benar mengarah ke file ambil_username_by_role.php
                url: 'proses/ambil_username_by_role.php', 
                type: 'POST',
                data: { role: role },
                success: function(response) {
                    // Masukkan hasil echo dari PHP tadi ke dalam dropdown username
                    usernameSelect.html(response);
                },
                error: function() {
                    alert('Gagal mengambil data username. Pastikan file ambil_username_by_role.php sudah benar.');
                    usernameSelect.html('<option value="" disabled selected>-- Pilih Username --</option>');
                }
            });
        }
    });
});
</script>
</body>

</html>