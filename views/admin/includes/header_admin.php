<?php
/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
?>
<div class="header-wrapper">
    <header>
        <div class="header">
            <div class="header-welcome">
                <h1 class="ucapan">Selamat Datang, <?= htmlspecialchars($adminstrator['nama_admin'] ?? 'Admin') ?></h1>
                <div class="date-time-section">
                    <div class="current-date" id="currentDate">Sabtu, 21 Februari 2026</div>
                    <div class="current-time" id="currentTime">07:30</div>
                </div>
            </div>
        </div>
    </header>
</div>
<script src="/phu-kemenag-banjar-copy/views/admin/assets/js/waktu_header.js"></script>