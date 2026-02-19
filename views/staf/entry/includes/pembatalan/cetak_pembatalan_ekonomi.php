<?php

/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
session_start();
include_once __DIR__ . '/../../../../../includes/koneksi.php';

if (isset($_GET['id'])) {
    $id_batal_ekonomi = $_GET['id'];

    $query = "SELECT * FROM pembatalan_ekonomi WHERE id_batal_ekonomi = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_batal_ekonomi);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    echo "ID pembatalan tidak ditemukan.";
    exit();
}
?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../../includes/sidebar_staf.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../../../includes/header_staf.php'; ?>
        <link rel="stylesheet" href="../../assets/css/pembatalan.css">
        <main class="pembatalan-wrapper">
            <div class="cetak-pembatalan">
                <h1 class="judul-cetak">Cetak Pembatalan Haji - Keperluan Ekonomi</h1>

                <p class="subjudul-cetak">Silakan pilih salah satu format surat untuk dicetak:</p>

                <div class="btn-group">
                    <!-- Tombol untuk mencetak SPTJM -->
                    <a href="/phu-kemenag-banjar-copy/assets/laporan/pembatalan/pembatalan-ekonomi/sptjm_ekonomi.php?id=<?php echo $id_batal_ekonomi; ?>" class="btn-cetak" target="_blank">Cetak SPTJM</a>

                    <!-- Tombol untuk mencetak Permohonan -->
                    <a href="/phu-kemenag-banjar-copy/assets/laporan/pembatalan/pembatalan-ekonomi/surat_permohonan_ekonomi.php?id=<?php echo $id_batal_ekonomi; ?>" class="btn-cetak" target="_blank">Cetak Surat Permohonan</a>

                    <!-- Tombol untuk mencetak Surat Permohonan Pembatalan -->
                    <a href="/phu-kemenag-banjar-copy/assets/laporan/pembatalan/pembatalan-ekonomi/surat_pembatalan_ekonomi.php?id=<?php echo $id_batal_ekonomi; ?>" class="btn-cetak" target="_blank">Cetak Surat Pembatalan</a>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="../../../assets/js/sidebar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.btn');

        buttons.forEach(button => {
            button.addEventListener('mouseover', (e) => {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');

                const x = e.clientX - e.target.offsetLeft;
                const y = e.clientY - e.target.offsetTop;

                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                button.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 1000);
            });
        });
    });
    document.addEventListener('DOMContentLoaded', () => {
        function createBackgroundShapes() {
            const container = document.body;
            const shapesCount = 5;

            for (let i = 0; i < shapesCount; i++) {
                const shape = document.createElement('div');
                shape.classList.add('background-shape');

                const size = Math.random() * 100 + 50;
                shape.style.width = `${size}px`;
                shape.style.height = `${size}px`;

                shape.style.top = `${Math.random() * 100}%`;
                shape.style.left = `${Math.random() * 100}%`;

                shape.style.animationDelay = `${Math.random() * 3}s`;

                container.appendChild(shape);
            }
        }

        createBackgroundShapes();
    });
</script>
</body>

</html>