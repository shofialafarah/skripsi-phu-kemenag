/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
const signUpButton = document.getElementById('signUp');
const container = document.getElementById('container');

// Aktifkan daftar secara default
container.classList.add('right-panel-active');

// Tambahkan pengecekan jika tombol ditemukan
if (signUpButton && container) {
    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });
} else {
    console.error("Tombol atau container tidak ditemukan!");
}

// Ambil elemen input password dan ikon mata
const passwordInput = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');

// Ketika ikon mata diklik, ganti tipe input password
togglePassword.addEventListener('click', function() {
    // Cek apakah tipe input saat ini password atau text
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type; // Ganti tipe input password
    // Ubah ikon mata berdasarkan tipe input
    this.classList.toggle('fa-eye-slash');
});
