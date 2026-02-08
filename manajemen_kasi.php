<?php session_start();
include 'koneksi.php'; // pastikan file koneksi benar

// Ambil semua data dari tabel kasi
$query = "SELECT * FROM kepala_seksi ORDER BY id_kepala DESC";
$result = $koneksi->query($query);

// Cek jika query gagal
if (!$result) {
    die("Query error: " . $koneksi->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Administrator</title>
    <link rel="icon" href="logo_kemenag.png">

<body>
    <div class="layout">
        <div class="layout-sidebar">
            <!-- SIDEBAR -->
            <?php include 'sidebar_admin.php'; ?>
        </div>
        <!-- MAIN AREA -->
        <div class="layout-content">
            <?php include 'header_admin.php'; ?>

            <main class="pPendaftaran-wrapper">
                <div class="pPendaftaran">
                    <div class="pPendaftaran-header" style="color: white;">
                        <i class="fas fa-table me-1"></i> Manajemen Akun Kepala Seksi
                    </div>
                    <div class="pPendaftaran-body">
                        <div class="d-flex flex-wrap align-items-center mb-3">
                        </div>
                        <table id="tabelStaf" class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">Username</th>
                                    <th class="text-center">Nama Lengkap</th>
                                    <th class="text-center">NIP</th>
                                    <th class="text-center">Jabatan</th>
                                    <th class="text-center">Nomor Telepon</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Password</th>
                                    <th class="text-center">Foto</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'koneksi.php';
                                $query = "SELECT * FROM kepala_seksi";
                                $result = mysqli_query($koneksi, $query);
                                $no = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        //Username
                                        echo "<td class='text-center'>
                                            <span class='badge bg-primary'>" . htmlspecialchars($row['username']) . "</span>
                                        </td>";

                                        //Nama Kasi
                                        echo "<td class='text-center'>" . htmlspecialchars($row['nama_kepala']) . "</td>";

                                        //Nomor NIP
                                        echo "<td class='text-center'>" . htmlspecialchars($row['nip']) . "</td>";

                                        //Posisi
                                        echo "<td class='text-center'>" . htmlspecialchars($row['jabatan']) . "</td>";

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
                                        echo "<a href='edit_kasi.php?id=" . $row['id_kepala'] . "' class='btn btn-sm btn-warning me-1 mb-1' title='Edit'><i class='fas fa-edit'></i></a>";
                                        echo "<a href='profil_kasi.php?id=" . $row['id_kepala'] . "' class='btn btn-sm btn-success me-1 mb-1' title='Edit'><i class='fas fa-print'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
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
</body>

</html>