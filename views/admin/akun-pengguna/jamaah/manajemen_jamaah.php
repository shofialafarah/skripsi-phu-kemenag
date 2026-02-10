<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';
session_start();

// Ambil semua data dari tabel jamaah
$query = "SELECT * FROM jamaah ORDER BY id_jamaah DESC";
$result = mysqli_query($koneksi, $query);

// Cek jika query gagal
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>
<link rel="stylesheet" href="assets/css/jamaah.css">
<div class="layout">
    <div class="layout-sidebar">
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>

        <main class="jamaah-wrapper">
            <div class="jamaah">
                <div class="jamaah-header">
                    <i class="fas fa-table me-1"></i> Manajemen Akun Jamaah Haji
                </div>
                <div class="jamaah-body">
                    <div class="d-flex flex-wrap align-items-center mb-3">
                        <div class="ms-auto">
                            <a href="tambah_jamaah.php" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i> Tambah Jamaah
                            </a>
                        </div>
                    </div>
                    <div style="overflow-x: auto; border-radius: 5px;">
                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Username</th>
                                    <th class="text-center">Nama Lengkap</th>
                                    <th class="text-center">Nomor Validasi</th>
                                    <th class="text-center">Nomor Telepon</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Password</th>
                                    <th class="text-center">Foto</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include_once __DIR__ . '/../../../../includes/koneksi.php';
                                $query = "SELECT * FROM jamaah";
                                $result = mysqli_query($koneksi, $query);
                                $no = 1;

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $no++ . "</td>";
                                    echo "<td class='text-center'><span class='badge bg-primary'>" . htmlspecialchars($row['username']) . "</span></td>";
                                    echo "<td class='text-center'>" . htmlspecialchars($row['nama']) . "</td>";
                                    echo "<td class='text-center'>" . htmlspecialchars($row['validasi_bank']) . "</td>";
                                    //Nomor Telepon
                                    echo "<td class='text-center'>
                                            <span class='badge bg-success'><i class='fas fa-phone'></i> " . htmlspecialchars($row['nomor_telepon']) . "</span>
                                        </td>";

                                    //Email
                                    echo "<td class='text-center'>
                                                <a href='mailto:" . htmlspecialchars($row['email']) . "' style='color: #0d6efd; text-decoration: underline;'>" . htmlspecialchars($row['email']) . " </a>
                                        </td>";

                                    echo "<td class='text-center'>" . (!empty($row['password']) ? '*****' : '-') . "</td>";
                                    echo "<td class='text-center'>";
                                    if (!empty($row['foto'])) {
                                        echo "<img src='" . htmlspecialchars($row['foto']) . "' style='width:60px; height:60px; border-radius:50%; object-fit:cover;' />";
                                    } else {
                                        echo "Tidak ada";
                                    }
                                    echo "</td>";
                                    echo "<td class='text-center'>";
                                    echo "<div class='d-flex justify-content-center gap-2'>";
                                    echo "<a href='edit_jamaah.php?id=" . $row['id_jamaah'] . "' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></a>";
                                    echo "<a href='hapus_jamaah.php?id=" . $row['id_jamaah'] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin ingin menghapus data ini?')\"><i class='fas fa-trash'></i></a>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
            </div>
        </main>
    </div>
</div>

<!-- JS -->
<script src="../../assets/js/sidebar_staf.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
</body>

</html>