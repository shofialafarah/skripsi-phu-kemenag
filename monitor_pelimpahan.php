<?php
session_start();
include 'koneksi.php';
include 'header_staf.php'; // Header untuk staf

// Query untuk mengambil data pengajuan dari tabel pelimpahan
$query_pelimpahan = "SELECT * FROM pelimpahan";
$result_pelimpahan = $koneksi->query($query_pelimpahan);
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<div class="container">
    <h2>Monitor Pengajuan Pelimpahan Jamaah</h2>

    <!-- Tabel Status Permohonan Pelimpahan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pengaju</th>
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
            while ($row = $result_pelimpahan->fetch_assoc()) {
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
                        <a href='#' class='btn btn-success btn-aksi' data-id='{$row['id_pelimpahan']}' data-status='Disetujui' data-type='pelimpahan'>
                        <i class='fas fa-check-circle'></i></a>
                        <a href='#' class='btn btn-danger btn-aksi' data-id='{$row['id_pelimpahan']}' data-status='Ditolak' data-type='pelimpahan'>
                        <i class='fas fa-times-circle'></i></a>
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
        $('.btn-aksi').on('click', function(e){
            e.preventDefault(); // Mencegah tombol melakukan tindakan default (termasuk redirect)

            var id = $(this).data('id');
            var status = $(this).data('status');
            var type = $(this).data('type');
            var row = $(this).closest('tr'); // Mendapatkan baris tabel yang terkait

            $.ajax({
                url: 'proses_pengajuan.php',
                method: 'GET',
                data: { id: id, status: status, type: type },
                success: function(response){
                    row.find('.status').text(status); // Update status di tabel
                },
                error: function(){
                    alert('Gagal memperbarui status');
                }
            });
        });
    });
</script>


<style>
    /* Global Styles */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f6f9;
    margin: 0;
    padding: 0;
}

/* Header */
header {
    background-color: #3a5a40; /* Hijau Kemenag */
    color: #fff;
    padding: 20px 0;
    text-align: center;
}

header h1 {
    font-size: 2.5rem;
    margin: 0;
    font-weight: bold;
}

/* Container */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    color: green;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #e0e0e0;
}

table th {
    background-color: #3a5a40; /* Hijau Kemenag */
    color: #fff;
    font-size: 1.1rem;
}

table td {
    background-color: #f9f9f9;
    color: #333; /* Ubah warna teks menjadi lebih gelap */
}

table tbody tr:hover {
    background-color: #f1f1f1;
}

table td a {
    color: #3a5a40;
    text-decoration: none;
}

table td a:hover {
    text-decoration: none;
}

/* Button Styles */
.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    text-align: center;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    position: relative; /* Tambahkan relative positioning */
    z-index: 10; /* Pastikan tombol tetap di atas elemen lain */
}

.btn-success {
    background-color: #4caf50; /* Hijau untuk Setujui */
    color: white;
}

.btn-success:hover {
    background-color: #45a049;
    transform: translateY(-2px);
}

.btn-danger {
    background-color: #f44336; /* Merah untuk Tolak */
    color: white;
}

.btn-danger:hover {
    background-color: #e53935;
    transform: translateY(-2px);
}

/* Status Styles */
.status {
    font-weight: bold;
    color: #3a5a40;
}

/* Responsive Design */
@media (max-width: 768px) {
    table th, table td {
        padding: 10px;
    }

    header h1 {
        font-size: 2rem;
    }
    
    .container {
        width: 95%;
    }
}

@media (max-width: 480px) {
    header h1 {
        font-size: 1.5rem;
    }

    .btn {
        font-size: 0.9rem;
        padding: 8px 15px;
    }
}

</style>

</body>
</html>
