<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
?>

    <!-- ====================================== header =========================================== -->
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($kasi['nama_kepala']) ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30</div>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="header-icon"><span class="material-symbols-outlined">mail</span></button>
                    <button class="header-icon"><span class="material-symbols-outlined">notifications</span></button>
                </div>
            </div>
        </header>
    </div>
    <!-- ========================================================================================================== -->
    <script src="/phu-kemenag-banjar-copy/views/kepala-seksi/assets/js/waktu_header.js"></script>