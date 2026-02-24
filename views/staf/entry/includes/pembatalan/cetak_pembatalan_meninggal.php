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
    $id_batal_meninggal = $_GET['id'];

    // Query untuk mendapatkan data berdasarkan ID
    $query = "SELECT * FROM pembatalan_meninggal WHERE id_batal_meninggal = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_batal_meninggal);
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
<?php include '../../../includes/header_setup.php'; ?>
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
                <h1 class="judul-cetak">Cetak Pembatalan Haji - Meninggal Dunia</h1>

                <p class="subjudul-cetak">Silakan pilih salah satu format surat untuk dicetak:</p>

                <div class="btn-group">
                    <!-- Tombol untuk mencetak SPTJM -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pembatalan/dokumen-staf/pembatalan-meninggal/sptjm_meninggal.php?id=<?php echo $id_batal_meninggal; ?>" class="btn-cetak" target="_blank">Cetak SPTJM</a>

                    <!-- Tombol untuk mencetak Permohonan -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pembatalan/dokumen-staf/pembatalan-meninggal/surat_permohonan_meninggal.php?id=<?php echo $id_batal_meninggal; ?>" class="btn-cetak" target="_blank">Cetak Surat Permohonan</a>

                    <!-- Tombol untuk mencetak Surat Tanda Tangan Kasi -->
                    <a href="/phu-kemenag-banjar-copy/uploads/pembatalan/dokumen-staf/pembatalan-meninggal/surat_pembatalan_meninggal.php?id=<?php echo $id_batal_meninggal; ?>" class="btn-cetak" target="_blank">Cetak Surat Pembatalan</a>
                </div>


            </div>
        </main>
    </div>
</div>
<script src="../../../assets/js/sidebar.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Button hover animation
        const buttons = document.querySelectorAll('.btn-cetak');

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