<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
include 'header_setup.php';
?>
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($staf['nama_staf']) ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30</div>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="dropdown">
                        <button class="header-icon position-relative" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-outlined">mail</span><?php if ($jumlah_notif_validasi > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success badge-notif">
                                    <?= $jumlah_notif_validasi ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end scrollable-dropdown">
                            <?php if ($jumlah_notif_validasi > 0): ?>
                                <?php foreach ($notifikasi_validasi as $notif): ?>
                                    <li>
                                        <span class="dropdown-item">
                                            <?php
                                            $warnaBadge = '';
                                            switch ($notif['jenis_pelayanan']) {
                                                case 'Pendaftaran':
                                                    $warnaBadge = 'bg-success'; // hijau
                                                    break;
                                                case 'Pembatalan':
                                                    $warnaBadge = 'bg-danger'; // merah
                                                    break;
                                                case 'Pelimpahan':
                                                    $warnaBadge = 'bg-warning text-dark'; // kuning
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $warnaBadge ?>">
                                                <?= htmlspecialchars($notif['jenis_pelayanan']) ?>
                                            </span>
                                            Berkas atas nama <strong><?= htmlspecialchars($notif['nama_jamaah']) ?></strong> perlu divalidasi.
                                        </span>
                                    </li>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Tidak ada notifikasi baru</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="header-icon position-relative" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-outlined">notifications</span>
                            <?php if ($jumlah_notif_ditolak > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-notif">
                                    <?= $jumlah_notif_ditolak ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end scrollable-dropdown">
                            <?php if ($jumlah_notif_ditolak > 0): ?>
                                <?php foreach ($notifikasi_ditolak as $notif): ?>
                                    <li>
                                        <span class="dropdown-item">
                                            <?php
                                            $warnaBadge = '';
                                            switch ($notif['jenis_pelayanan']) {
                                                case 'Pendaftaran':
                                                    $warnaBadge = 'bg-success'; // hijau
                                                    break;
                                                case 'Pembatalan':
                                                    $warnaBadge = 'bg-danger'; // merah
                                                    break;
                                                case 'Pelimpahan':
                                                    $warnaBadge = 'bg-warning text-dark'; // kuning
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $warnaBadge ?>">
                                                <?= htmlspecialchars($notif['jenis_pelayanan']) ?>
                                            </span>
                                            Berkas atas nama <strong><?= htmlspecialchars($notif['nama_jamaah']) ?></strong> ditolak oleh Kepala Seksi.
                                        </span>
                                    </li>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Tidak ada berkas yang ditolak</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </header>
    </div>
    <script src="/phu-kemenag-banjar-copy/views/staf/assets/js/waktu_header.js"></script>