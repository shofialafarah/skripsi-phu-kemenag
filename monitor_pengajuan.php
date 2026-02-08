<?php
session_start();
include 'koneksi.php';
include 'header_staf.php'; // Header untuk staf

// Query untuk mengambil data pengajuan dari tabel pembatalan
$query_pembatalan = "SELECT * FROM pembatalan";
$result_pembatalan = $koneksi->query($query_pembatalan);

// Query untuk mengambil data pengajuan dari tabel pelimpahan
$query_pelimpahan = "SELECT * FROM pelimpahan";
$result_pelimpahan = $koneksi->query($query_pelimpahan);
?>

<div class="container">
    <h2>Monitor Pengajuan Jamaah</h2>

    <!-- Tabel Status Permohonan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Jamaah</th>
                <th>Kategori</th>
                <th>Alasan</th>
                <th>Dokumen</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Tampilkan data dari tabel pembatalan
            while ($row = $result_pembatalan->fetch_assoc()) {
                // Dapatkan nama jamaah berdasarkan ID
                $id_jamaah = $row['id_jamaah'];
                $query_jamaah = "SELECT nama FROM jamaah WHERE id_jamaah = ?";
                $stmt = $koneksi->prepare($query_jamaah);
                $stmt->bind_param("i", $id_jamaah);
                $stmt->execute();
                $jamaah_result = $stmt->get_result();
                $jamaah_data = $jamaah_result->fetch_assoc();

                echo "<tr>
                    <td>{$no}</td>
                    <td>{$jamaah_data['nama']}</td>
                    <td>{$row['kategori']}</td>
                    <td>{$row['alasan']}</td>
                    <td><a href='uploads/{$row['dokumen']}' target='_blank'>Lihat Dokumen</a></td>
                    <td class='status'>{$row['status']}</td>
                    <td>{$row['tanggal_pengajuan']}</td>
                    <td>
                        <a href='#' class='btn btn-success btn-aksi' data-id='{$row['id_pembatalan']}' data-status='Disetujui'>Setujui</a>
                        <a href='#' class='btn btn-danger btn-aksi' data-id='{$row['id_pembatalan']}' data-status='Ditolak'>Tolak</a>
                    </td>
                </tr>";
                $no++;
            }

            // Tampilkan data dari tabel pelimpahan
            while ($row = $result_pelimpahan->fetch_assoc()) {
                // Dapatkan nama jamaah berdasarkan ID
                $id_jamaah = $row['id_jamaah'];
                $query_jamaah = "SELECT nama FROM jamaah WHERE id_jamaah = ?";
                $stmt = $koneksi->prepare($query_jamaah);
                $stmt->bind_param("i", $id_jamaah);
                $stmt->execute();
                $jamaah_result = $stmt->get_result();
                $jamaah_data = $jamaah_result->fetch_assoc();

                echo "<tr>
                    <td>{$no}</td>
                    <td>{$jamaah_data['nama']}</td>
                    <td>{$row['kategori']}</td>
                    <td>{$row['alasan']}</td>
                    <td><a href='uploads/{$row['dokumen']}' target='_blank'>Lihat Dokumen</a></td>
                    <td class='status'>{$row['status']}</td>
                    <td>{$row['tanggal_pengajuan']}</td>
                    <td>
                        <a href='#' class='btn btn-success btn-aksi' data-id='{$row['id_pelimpahan']}' data-status='Disetujui'>Setujui</a>
                        <a href='#' class='btn btn-danger btn-aksi' data-id='{$row['id_pelimpahan']}' data-status='Ditolak'>Tolak</a>
                    </td>
                </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Script untuk AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        // Ketika tombol aksi diklik
        $('.btn-aksi').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var status = $(this).data('status');
            var row = $(this).closest('tr'); // Ambil baris yang berisi tombol

            // Kirim permintaan AJAX untuk memperbarui status
            $.ajax({
                url: 'proses_pengajuan.php',
                method: 'GET',
                data: { id: id, status: status },
                success: function(response){
                    // Update status pada kolom yang sesuai
                    row.find('.status').text(status);
                    // Anda bisa menambahkan feedback sukses atau error di sini
                },
                error: function(){
                    alert('Gagal memperbarui status');
                }
            });
        });
    });
</script>

<!-- CSS Styling -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2, h3 {
        color: #333;
    }

    .form-container {
        margin-bottom: 30px;
    }

    .form-label {
        font-weight: bold;
    }

    .btn {
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
    }
/* 
    .btn:hover {
        background-color: #0056b3;
    } */

    .table {
        width: 100%;
        margin-top: 20px;
    }

    .table th, .table td {
        padding: 12px;
        text-align: left;
        color: #333;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table a {
        color: #007bff;
        text-decoration: none;
    }

    .table a:hover {
        text-decoration: underline;
    }
</style>

</body>
</html>
