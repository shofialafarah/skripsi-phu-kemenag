<?php
// Data dummy untuk demonstrasi
$pendaftar = [
    'id' => 'HR2025001',
    'nama' => 'Ahmad Maulana Hasbi',
    'nik' => '6371012345678901',
    'tempat_lahir' => 'Banjarmasin',
    'tanggal_lahir' => '15 Januari 1975',
    'jenis_kelamin' => 'Laki-laki',
    'alamat' => 'Jl. Sultan Adam No. 123, Banjarmasin Selatan',
    'telepon' => '081234567890',
    'email' => 'ahmad.maulana@email.com',
    'pekerjaan' => 'Pegawai Negeri Sipil',
    'pendidikan' => 'S1',
    'status_pernikahan' => 'Menikah',
    'mahram' => 'Istri - Siti Khadijah',
    'porsi' => 'Reguler 2025',
    'kloter' => 'BDJ-001',
    'embarkasi' => 'Banjarmasin',
    'tanggal_daftar' => '15 Februari 2025',
    'status_pembayaran' => 'Lunas',
    'setoran_awal' => 'Rp 10.000.000',
    'total_setoran' => 'Rp 35.000.000',
    'sisa_pembayaran' => 'Rp 0',
    'status_dokumen' => 'Lengkap',
    'status_kesehatan' => 'Sehat',
    'status_verifikasi' => 'Terverifikasi'
];

// Countdown timer (contoh untuk keberangkatan)
$target_date = '2025-08-15'; // Tanggal keberangkatan estimasi
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaftaran Haji Reguler - Kemenag</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f8f0 0%, #e8f5e8 100%);
            min-height: 100vh;
            color: #2d5016;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-section {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 50%, #2d5016 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(45, 80, 22, 0.3);
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="mosque" patternUnits="userSpaceOnUse" width="50" height="50"><path d="M25 10 L30 20 L20 20 Z M25 20 L25 40 M15 40 L35 40" stroke="rgba(255,255,255,0.1)" stroke-width="1" fill="none"/></pattern></defs><rect width="100" height="100" fill="url(%23mosque)"/></svg>') repeat;
            opacity: 0.1;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .countdown-section {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        .countdown-title {
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .countdown-timer {
            display: flex;
            justify-content: space-around;
            gap: 15px;
        }

        .countdown-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            min-width: 80px;
        }

        .countdown-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }

        .countdown-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(45, 80, 22, 0.1);
            border: 1px solid rgba(45, 80, 22, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2d5016, #4a7c59, #5d8a66);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(45, 80, 22, 0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f8f0;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4a7c59, #2d5016);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d5016;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f9f5;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #2d5016;
            min-width: 140px;
            font-size: 0.95rem;
        }

        .info-value {
            color: #4a7c59;
            font-weight: 500;
            flex: 1;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-verified {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-completed {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .status-paid {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .status-healthy {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .progress-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(45, 80, 22, 0.1);
        }

        .progress-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d5016;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 25px;
            right: 25px;
            height: 3px;
            background: #e5e7eb;
            z-index: 1;
        }

        .progress-steps::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 25px;
            width: 80%;
            height: 3px;
            background: linear-gradient(90deg, #10b981, #059669);
            z-index: 2;
        }

        .progress-step {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 3;
            color: white;
            font-size: 1.1rem;
        }

        .progress-step.completed {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .progress-step.current {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .progress-step.pending {
            background: #e5e7eb;
            color: #9ca3af;
        }

        .step-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .step-label {
            text-align: center;
            font-size: 0.9rem;
            color: #4a7c59;
            font-weight: 500;
            max-width: 120px;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .document-item {
            background: #f8fdf8;
            border: 2px solid #e8f5e8;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .document-item:hover {
            border-color: #4a7c59;
            background: #f0f8f0;
        }

        .document-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4a7c59, #2d5016);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 1.5rem;
        }

        .document-name {
            font-weight: 600;
            color: #2d5016;
            margin-bottom: 8px;
        }

        .document-status {
            font-size: 0.9rem;
            color: #10b981;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2d5016, #4a7c59);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(45, 80, 22, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #2d5016;
            border: 2px solid #2d5016;
        }

        .btn-secondary:hover {
            background: #2d5016;
            color: white;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 15px;
            }
            
            .header-title {
                font-size: 2rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .countdown-timer {
                flex-wrap: wrap;
            }
            
            .progress-steps {
                flex-direction: column;
                gap: 20px;
            }
            
            .progress-steps::before,
            .progress-steps::after {
                display: none;
            }
            
            .step-labels {
                flex-direction: column;
                gap: 20px;
            }
        }

        .floating-help {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .floating-help:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="header-content">
                <h1 class="header-title">
                    <i class="fas fa-kaaba"></i>
                    Data Pendaftaran Haji Reguler
                </h1>
                <p class="header-subtitle">
                    Sistem Informasi Penyelenggaraan Ibadah Haji - Kementerian Agama RI
                </p>
                
                <div class="countdown-section">
                    <div class="countdown-title">
                        <i class="fas fa-clock"></i>
                        Estimasi Keberangkatan Haji 1446 H / 2025 M
                    </div>
                    <div class="countdown-timer" id="countdown">
                        <div class="countdown-item">
                            <span class="countdown-number" id="days">--</span>
                            <span class="countdown-label">Hari</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="hours">--</span>
                            <span class="countdown-label">Jam</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="minutes">--</span>
                            <span class="countdown-label">Menit</span>
                        </div>
                        <div class="countdown-item">
                            <span class="countdown-number" id="seconds">--</span>
                            <span class="countdown-label">Detik</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="progress-section">
            <div class="progress-title">
                <i class="fas fa-tasks"></i>
                Progress Pendaftaran Haji
            </div>
            <div class="progress-steps">
                <div class="progress-step completed">
                    <i class="fas fa-check"></i>
                </div>
                <div class="progress-step completed">
                    <i class="fas fa-check"></i>
                </div>
                <div class="progress-step completed">
                    <i class="fas fa-check"></i>
                </div>
                <div class="progress-step current">
                    <i class="fas fa-plane"></i>
                </div>
                <div class="progress-step pending">5</div>
            </div>
            <div class="step-labels">
                <div class="step-label">Pendaftaran</div>
                <div class="step-label">Pelunasan</div>
                <div class="step-label">Verifikasi</div>
                <div class="step-label">Persiapan Keberangkatan</div>
                <div class="step-label">Keberangkatan</div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <!-- Data Pribadi -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="card-title">Data Pribadi</div>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Pendaftar:</span>
                    <span class="info-value"><?= $pendaftar['id'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Lengkap:</span>
                    <span class="info-value"><?= $pendaftar['nama'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">NIK:</span>
                    <span class="info-value"><?= $pendaftar['nik'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tempat Lahir:</span>
                    <span class="info-value"><?= $pendaftar['tempat_lahir'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Lahir:</span>
                    <span class="info-value"><?= $pendaftar['tanggal_lahir'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jenis Kelamin:</span>
                    <span class="info-value"><?= $pendaftar['jenis_kelamin'] ?></span>
                </div>
            </div>

            <!-- Kontak & Alamat -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="card-title">Kontak & Alamat</div>
                </div>
                <div class="info-item">
                    <span class="info-label">Alamat:</span>
                    <span class="info-value"><?= $pendaftar['alamat'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value"><?= $pendaftar['telepon'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= $pendaftar['email'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pekerjaan:</span>
                    <span class="info-value"><?= $pendaftar['pekerjaan'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pendidikan:</span>
                    <span class="info-value"><?= $pendaftar['pendidikan'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><?= $pendaftar['status_pernikahan'] ?></span>
                </div>
            </div>

            <!-- Data Haji -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-kaaba"></i>
                    </div>
                    <div class="card-title">Data Haji</div>
                </div>
                <div class="info-item">
                    <span class="info-label">Mahram:</span>
                    <span class="info-value"><?= $pendaftar['mahram'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jenis Porsi:</span>
                    <span class="info-value"><?= $pendaftar['porsi'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kloter:</span>
                    <span class="info-value"><?= $pendaftar['kloter'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Embarkasi:</span>
                    <span class="info-value"><?= $pendaftar['embarkasi'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tgl Daftar:</span>
                    <span class="info-value"><?= $pendaftar['tanggal_daftar'] ?></span>
                </div>
            </div>

            <!-- Status & Pembayaran -->
            <div class="info-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="card-title">Status & Pembayaran</div>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Bayar:</span>
                    <span class="info-value">
                        <span class="status-badge status-paid">
                            <i class="fas fa-check-circle"></i>
                            <?= $pendaftar['status_pembayaran'] ?>
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Setoran Awal:</span>
                    <span class="info-value"><?= $pendaftar['setoran_awal'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Setoran:</span>
                    <span class="info-value"><?= $pendaftar['total_setoran'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Sisa:</span>
                    <span class="info-value"><?= $pendaftar['sisa_pembayaran'] ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Dokumen:</span>
                    <span class="info-value">
                        <span class="status-badge status-completed">
                            <i class="fas fa-folder-check"></i>
                            <?= $pendaftar['status_dokumen'] ?>
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Kesehatan:</span>
                    <span class="info-value">
                        <span class="status-badge status-healthy">
                            <i class="fas fa-heartbeat"></i>
                            <?= $pendaftar['status_kesehatan'] ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="progress-section">
            <div class="progress-title">
                <i class="fas fa-file-alt"></i>
                Status Dokumen Persyaratan
            </div>
            <div class="documents-grid">
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="document-name">KTP</div>
                    <div class="document-status">✓ Lengkap</div>
                </div>
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <div class="document-name">Paspor</div>
                    <div class="document-status">✓ Lengkap</div>
                </div>
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="document-name">Akta Kelahiran</div>
                    <div class="document-status">✓ Lengkap</div>
                </div>
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-ring"></i>
                    </div>
                    <div class="document-name">Buku Nikah</div>
                    <div class="document-status">✓ Lengkap</div>
                </div>
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <div class="document-name">Sertifikat Vaksin</div>
                    <div class="document-status">✓ Lengkap</div>
                </div>
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <div class="document-name">Surat Sehat</div>
                    <div class="document-status">✓ Lengkap</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="#" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Cetak Data
            </a>
            <a href="#" class="btn btn-secondary">
                <i class="fas fa-edit"></i>
                Edit Data
            </a>
            <a href="#" class="btn btn-primary">
                <i class="fas fa-download"></i>
                Download PDF
            </a>
            <a href="#" class="btn btn-secondary">
                <i class="fas fa-envelope"></i>
                Kirim Email
            </a>
        </div>
    </div>

    <!-- Floating Help Button -->
    <div class="floating-help" onclick="alert('Hubungi Call Center Haji: 14045 atau WhatsApp: 0812-3456-7890')">
        <i class="fas fa-question"></i>
    </div>

    <script>
        // Countdown Timer
        function updateCountdown() {
            const targetDate = new Date('<?= $target_date ?>').getTime();
            const now = new Date().getTime();
            const difference = targetDate - now;

            if (difference > 0) {
                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((difference % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = days.toString().padStart(2, '0');
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            } else {
                document.getElementById('days').textContent = '00';                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
            }
        }

        // Jalankan countdown setiap detik
        setInterval(updateCountdown, 1000);
        // Jalankan pertama kali agar langsung tampil tanpa menunggu interval
        updateCountdown();
    </script>
</body>
