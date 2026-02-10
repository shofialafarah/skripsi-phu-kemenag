<?php
include_once __DIR__ . '/../../../../includes/koneksi.php';
session_start();

// Ambil semua data dari tabel staf
$query = "SELECT * FROM staf ORDER BY id_staf DESC";
$result = $koneksi->query($query);

// Cek jika query gagal
if (!$result) {
    die("Query error: " . $koneksi->error);
}
?>
<link rel="stylesheet" href="assets/css/staf.css">
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include '../../includes/sidebar_admin.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include '../../includes/header_admin.php'; ?>

        <main class="staf-wrapper">
            <div class="staf">
                <div class="staf-header" style="color: white;">
                    <i class="fas fa-table me-1"></i> Manajemen Akun Staf PHU
                </div>
                <div class="staf-body">
                    <div class="d-flex flex-wrap align-items-center mb-3">
                        <div class="ms-auto">
                            <a href="tambah_staf.php" class="btn btn-success">
                                <i class="fas fa-user-plus me-1"></i> Tambah Staf
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
                                    <th class="text-center">NIP</th>
                                    <th class="text-center">Posisi</th>
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
                                $query = "SELECT * FROM staf";
                                $result = mysqli_query($koneksi, $query);
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $no++ . "</td>";

                                    //Username
                                    echo "<td class='text-center'>
                                            <span class='badge bg-primary'>" . htmlspecialchars($row['username']) . "</span>
                                        </td>";

                                    //Nama Jamaah
                                    echo "<td class='text-center'>" . htmlspecialchars($row['nama_staf']) . "</td>";

                                    //Nomor NIP
                                    echo "<td class='text-center'>" . htmlspecialchars($row['nip']) . "</td>";

                                    //Posisi
                                    echo "<td class='text-center'>" . htmlspecialchars($row['posisi']) . "</td>";

                                    //Nomor Telepon
                                    echo "<td class='text-center'>
                                            <span class='badge bg-success'><i class='fas fa-phone'></i> " . htmlspecialchars($row['no_telepon']) . "</span>
                                        </td>";

                                    //Email
                                    echo "<td class='text-center'>
                                                <a href='mailto:" . htmlspecialchars($row['email']) . "' style='color: #0d6efd; text-decoration: underline;'>" . htmlspecialchars($row['email']) . " </a>
                                        </td>";

                                    //Password
                                    echo "<td class='text-center'>" . (!empty($row['password']) ? '*****' : '-') . "</td>";

                                    //Foto

                                    if (!empty($row['foto'])) {
                                        echo "<td class='text-center'>
                                                <img src='" . htmlspecialchars($row['foto']) . "' alt='Foto' 
                                                    style='width:60px; height:60px; object-fit:cover; border-radius:50%; border:2px solid #ccc;' />
                                            </td>";
                                    } else {
                                        echo "<td class='text-center text-muted'>Tidak ada</td>";
                                    }

                                    //AKSI
                                    echo "<td class='text-center'>";
                                    echo "<div class='d-flex justify-content-center gap-2'>";
                                    echo "<a href='edit_staf.php?id=" . $row['id_staf'] . "' class='btn btn-sm btn-warning me-1 mb-1' title='Edit'><i class='fas fa-edit'></i></a>";
                                    echo "<a href='hapus_staf.php?id=" . $row['id_staf'] . "' class='btn btn-sm btn-danger mb-1' onclick=\"return confirm('Yakin ingin menghapus data ini?')\" title='Hapus'><i class='fas fa-trash'></i></a>";
                                    echo "<a href='profil_staf.php?id=" . $row['id_staf'] . "' class='btn btn-sm btn-success me-1 mb-1' title='Cetak'><i class='fas fa-print'></i></a>";
                                    echo "</div>";
                                    echo "</td>";
                                    echo "</tr>";
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

<!-- Modal Verifikasi Detail -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Verifikasi Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="validate_document" value="1">
                    <input type="hidden" name="id_staf" id="modal_id_pendaftaran">

                    <div class="mb-3">
                        <label for="validation_status" class="form-label">Status Verifikasi</label>
                        <select class="form-select" name="validation_status" id="validation_status" required>
                            <option value="">Pilih Status</option>
                            <option value="Terverifikasi">Terverifikasi</option>
                            <option value="Revisi">Perlu Revisi</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" id="catatan" rows="3" placeholder="Berikan catatan verifikasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/js/sidebar_staf.js"></script>
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

<script>
    function openVerificationModal(idPendaftaran) {
        document.getElementById('modal_id_pendaftaran').value = idPendaftaran;
        var modal = new bootstrap.Modal(document.getElementById('verificationModal'));
        modal.show();
    }
</script>
</body>

</html>