/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
document.addEventListener('DOMContentLoaded', function() {
        var params = new URLSearchParams(window.location.search);
        if (params.get('updated') === '1') {
            Swal.fire('Berhasil', 'Pengaturan berhasil diperbarui.', 'success');
        } else if (params.get('updated') === '0') {
            Swal.fire('Gagal', 'Gagal memperbarui pengaturan.', 'error');
        }

        if (params.get('reset') === 'success') {
            Swal.fire('Berhasil', 'Pengaturan berhasil dikembalikan ke default.', 'success');
        }

        // Remove known query params without reloading
        if (history.replaceState) {
            var url = window.location.href.replace(/\?(updated|reset)=[^&]*(&)?/g, '');
            url = url.replace(/&$/, '');
            history.replaceState(null, '', url);
        }
    });