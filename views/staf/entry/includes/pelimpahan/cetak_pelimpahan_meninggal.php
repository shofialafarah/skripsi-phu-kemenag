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
    $id_limpah_meninggal = $_GET['id'];

    $query = "SELECT * FROM pelimpahan_meninggal WHERE id_limpah_meninggal = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_limpah_meninggal);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        echo "Data tidak ditemukan.";
        exit();
    }
} else {
    echo "ID pelimpahan tidak ditemukan.";
    exit();
}
?>
<?php include '../../../includes/header_setup.php'; ?>
<div class="layout">
    <div class="layout-sidebar">
        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../../includes/sidebar_staf.php'; ?>
    </div>
    <!-- MAIN AREA -->
    <div class="layout-content">
        <?php include_once __DIR__ . '/../../../includes/header_staf.php'; ?>
        <link rel="stylesheet" href="../../assets/css/pelimpahan.css">
        <main class="pelimpahan-wrapper">
            <div class="cetak-pelimpahan">
                <h1 class="judul-cetak">Cetak Pelimpahan Haji - Meninggal Dunia</h1>

                <p class="subjudul-cetak">Silakan pilih salah satu format surat untuk dicetak:</p>

                <div class="btn-group">
                    <!-- Tombol untuk mencetak SURAT REKOMENDASI -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pelimpahan/dokumen-staf/surat_rekomendasi.php?id=<?php echo $id_limpah_meninggal; ?>" class="btn-cetak" target="_blank">Cetak Surat Rekomendasi</a>

                    <!-- Tombol untuk mencetak SURAT PENGANTAR -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pelimpahan/dokumen-staf/surat_pengantar.php?id=<?php echo $id_limpah_meninggal; ?>" class="btn-cetak" target="_blank">Cetak Surat Pengantar</a>

                    <!-- Tombol untuk mencetak SPTJM -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pelimpahan/dokumen-staf/sptjm_pelimpahan.php?id=<?php echo $id_limpah_meninggal; ?>" class="btn-cetak" target="_blank">Cetak SPTJM</a>

                    <!-- Tombol untuk mencetak Permohonan -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pelimpahan/dokumen-staf/surat_permohonan_pelimpahan.php?id=<?php echo $id_limpah_meninggal; ?>" class="btn-cetak" target="_blank">Cetak Surat Permohonan</a>

                    <!-- Tombol untuk mencetak Surat Tanda Tangan Kasi -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pelimpahan/dokumen-staf/surat_pelimpahan.php?id=<?php echo $id_limpah_meninggal; ?>" class="btn-cetak" target="_blank">Cetak Surat Pelimpahan</a>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="../../../assets/js/sidebar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Button hover animation
        const buttons = document.querySelectorAll('.btn');

        buttons.forEach(button => {
            // Add ripple effect on hover
            button.addEventListener('mouseover', (e) => {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');

                const x = e.clientX - e.target.offsetLeft;
                const y = e.clientY - e.target.offsetTop;

                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                button.appendChild(ripple);

                // Remove ripple after animation
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

                // Random size
                const size = Math.random() * 100 + 50;
                shape.style.width = `${size}px`;
                shape.style.height = `${size}px`;

                // Random position
                shape.style.top = `${Math.random() * 100}%`;
                shape.style.left = `${Math.random() * 100}%`;

                // Custom animation delay
                shape.style.animationDelay = `${Math.random() * 3}s`;

                container.appendChild(shape);
            }
        }

        createBackgroundShapes();
    });
</script>
</body>

</html>