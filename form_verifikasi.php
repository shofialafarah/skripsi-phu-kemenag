<?php 
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['id_kepala']) || $_SESSION['role'] != 'kepala_seksi') {
    header("Location: login.php");
    exit();
}

// Ambil ID dari parameter GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ID pendaftaran tidak valid.";
    header("Location: verifikasi_pendaftaran.php");
    exit();
}

$id_pendaftaran = (int)$_GET['id'];

// Ambil data pendaftaran berdasarkan ID
$query = "SELECT * FROM pendaftaran WHERE id_pendaftaran = $id_pendaftaran";
$result = $koneksi->query($query);

if (!$result || $result->num_rows == 0) {
    $_SESSION['error_message'] = "Data pendaftaran tidak ditemukan.";
    header("Location: verifikasi_pendaftaran.php");
    exit();
}

$data_pendaftaran = $result->fetch_assoc();

function clean_input($data)
{
    global $koneksi;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($koneksi, $data);
}

// Proses form verifikasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_verifikasi = clean_input($_POST['status_verifikasi']);
    $catatan = clean_input($_POST['catatan']);
    $id_kepala = $_SESSION['id_kepala'];

    // Update data verifikasi
    $update_query = "UPDATE pendaftaran 
                     SET status_verifikasi = '$status_verifikasi', 
                         catatan_verifikasi = '$catatan',
                         tanggal_verifikasi = NOW(),
                         id_verifikator = '$id_kepala'
                     WHERE id_pendaftaran = $id_pendaftaran";

    if (mysqli_query($koneksi, $update_query)) {
        $_SESSION['success_message'] = "Verifikasi berhasil disimpan.";
        header("Location: verifikasi_pendaftaran.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal menyimpan verifikasi: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Verifikasi - Sistem Pendaftaran Haji</title>
    <link rel="icon" href="logo_kemenag.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .layout {
            display: flex;
            min-height: 100vh;
        }
        
        .layout-sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
        }
        
        .layout-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .entry-wrapper {
            flex: 1;
            padding: 20px;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        
        .info-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #212529;
            margin-bottom: 15px;
        }
        
        .document-preview {
            max-width: 100%;
            height: 500px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        .btn-back {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }
    </style>
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
            
            <main class="entry-wrapper">
                <div class="container-fluid">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="dashboard_kasi.php">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="verifikasi_pendaftaran.php">Verifikasi Pendaftaran</a>
                            </li>
                            <li class="breadcrumb-item active">Form Verifikasi</li>
                        </ol>
                    </nav>

                    <!-- Alert Messages -->
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Data Jamaah -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user"></i> Data Jamaah
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-section">
                                        <div class="info-label">Nama Jamaah:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data_pendaftaran['nama_jamaah']); ?></div>
                                        
                                        <div class="info-label">Nomor Porsi:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data_pendaftaran['nomor_porsi']); ?></div>
                                        
                                        <div class="info-label">NIK:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data_pendaftaran['nik'] ?? '-'); ?></div>
                                        
                                        <div class="info-label">Tanggal Lahir:</div>
                                        <div class="info-value">
                                            <?php 
                                            if (!empty($data_pendaftaran['tanggal_lahir'])) {
                                                echo date('d-m-Y', strtotime($data_pendaftaran['tanggal_lahir']));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </div>
                                        
                                        <div class="info-label">Alamat:</div>
                                        <div class="info-value">
                                            <?php 
                                            echo htmlspecialchars($data_pendaftaran['alamat']) . "<br>";
                                            echo htmlspecialchars($data_pendaftaran['kelurahan']) . ", ";
                                            echo htmlspecialchars($data_pendaftaran['kecamatan']) . "<br>";
                                            echo "Kode Pos: " . htmlspecialchars($data_pendaftaran['kode_pos']);
                                            ?>
                                        </div>
                                        
                                        <div class="info-label">Nomor Telepon:</div>
                                        <div class="info-value"><?php echo htmlspecialchars($data_pendaftaran['no_telepon']); ?></div>
                                        
                                        <div class="info-label">Status Saat Ini:</div>
                                        <div class="info-value">
                                            <?php 
                                            $isVerified = !empty($data_pendaftaran['tanggal_verifikasi']);
                                            if ($isVerified) {
                                                $badgeClass = 'bg-success';
                                                $statusText = htmlspecialchars($data_pendaftaran['status_verifikasi']);
                                            } else {
                                                $badgeClass = 'bg-warning';
                                                $statusText = 'Belum Diverifikasi';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> status-badge"><?php echo $statusText; ?></span>
                                        </div>
                                        
                                        <?php if ($isVerified): ?>
                                        <div class="info-label">Tanggal Verifikasi:</div>
                                        <div class="info-value">
                                            <?php echo date('d-m-Y H:i', strtotime($data_pendaftaran['tanggal_verifikasi'])); ?>
                                        </div>
                                        
                                        <?php if (!empty($data_pendaftaran['catatan_verifikasi'])): ?>
                                        <div class="info-label">Catatan Sebelumnya:</div>
                                        <div class="info-value">
                                            <div class="alert alert-info">
                                                <?php echo nl2br(htmlspecialchars($data_pendaftaran['catatan_verifikasi'])); ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen dan Form Verifikasi -->
                        <div class="col-lg-6">
                            <!-- Dokumen -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-pdf"></i> Dokumen
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($data_pendaftaran['upload_doc'])): ?>
                                        <div class="text-center mb-3">
                                            <a href="<?php echo htmlspecialchars($data_pendaftaran['upload_doc']); ?>" 
                                               target="_blank" class="btn btn-danger">
                                                <i class="fas fa-file-pdf"></i> Buka Dokumen PDF
                                            </a>
                                        </div>
                                        
                                        <!-- Embed PDF -->
                                        <iframe src="<?php echo htmlspecialchars($data_pendaftaran['upload_doc']); ?>" 
                                                class="document-preview" 
                                                frameborder="0">
                                            Browser Anda tidak mendukung preview PDF. 
                                            <a href="<?php echo htmlspecialchars($data_pendaftaran['upload_doc']); ?>" target="_blank">
                                                Klik di sini untuk membuka dokumen
                                            </a>
                                        </iframe>
                                    <?php else: ?>
                                        <div class="alert alert-warning text-center">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Tidak ada dokumen yang diupload
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Form Verifikasi -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle"></i> Form Verifikasi
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" id="verificationForm">
                                        <div class="mb-3">
                                            <label for="status_verifikasi" class="form-label">
                                                <strong>Status Verifikasi</strong>
                                            </label>
                                            <select class="form-select" name="status_verifikasi" id="status_verifikasi" required>
                                                <option value="">-- Pilih Status --</option>
                                                <option value="Terverifikasi">✅ Terverifikasi</option>
                                                <option value="Perlu Revisi">⚠️ Perlu Revisi</option>
                                                <option value="Ditolak">❌ Ditolak</option>
                                                <option value="Pending">⏳ Pending</option>
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="catatan" class="form-label">
                                                <strong>Catatan Verifikasi</strong>
                                            </label>
                                            <textarea class="form-control" name="catatan" id="catatan" 
                                                      rows="6" placeholder="Berikan catatan detail mengenai hasil verifikasi..."></textarea>
                                            <div class="form-text">
                                                Berikan penjelasan yang jelas jika status adalah "Perlu Revisi" atau "Ditolak"
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="verifikasi_pendaftaran.php" class="btn btn-secondary btn-back">
                                                <i class="fas fa-arrow-left"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="fas fa-save"></i> Simpan Verifikasi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.0/dist/sweetalert2.min.js"></script>

    <script>
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const status = document.getElementById('status_verifikasi').value;
            const catatan = document.getElementById('catatan').value;
            
            if (!status) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih status verifikasi!'
                });
                return;
            }
            
            // Validasi catatan untuk status tertentu
            if ((status === 'Perlu Revisi' || status === 'Ditolak') && catatan.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Diperlukan',
                    text: 'Harap berikan catatan untuk status "' + status + '"!'
                });
                return;
            }
            
            Swal.fire({
                title: 'Konfirmasi Verifikasi',
                text: 'Apakah Anda yakin ingin menyimpan hasil verifikasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Disable button to prevent double submission
                    document.getElementById('submitBtn').disabled = true;
                    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                    
                    // Submit form
                    this.submit();
                }
            });
        });

        // Auto-resize textarea
        document.getElementById('catatan').addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Change alert color based on status selection
        document.getElementById('status_verifikasi').addEventListener('change', function() {
            const catatan = document.getElementById('catatan');
            const status = this.value;
            
            // Reset classes
            catatan.classList.remove('border-success', 'border-warning', 'border-danger');
            
            // Add appropriate border color
            switch(status) {
                case 'Terverifikasi':
                    catatan.classList.add('border-success');
                    break;
                case 'Perlu Revisi':
                    catatan.classList.add('border-warning');
                    break;
                case 'Ditolak':
                    catatan.classList.add('border-danger');
                    break;
            }
        });
    </script>
</body>
</html>