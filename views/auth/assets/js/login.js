/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
document.addEventListener("DOMContentLoaded", function () {
    // 1. Deklarasi elemen cukup sekali saja di sini
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    // 2. Fungsi Toggle Mata (Password Visibility)
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.classList.toggle('fa-eye-slash');
        });
    }

    // 3. Fungsi Enter dari Username pindah ke Password
    if (usernameInput && passwordInput) {
        usernameInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                passwordInput.focus();
            }
        });
    }

    // 4. Logika SweetAlert Error Login
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");
    const attempts = params.get("attempts");

    if (error) {
        let title = "Login Gagal";
        let text = "";
        let icon = "error";

        switch (error) {
            case "banned":
                title = "Akun Dibanned";
                text = "Akun Anda telah dibanned. Hubungi admin.";
                break;
            case "nonaktif":
                title = "Akun Nonaktif";
                icon = "warning";
                text = "Akun Anda nonaktif karena tidak aktif lebih dari seminggu.";
                break;
            case "password":
                text = attempts ? `Password salah! Percobaan ke-${attempts}` : "Password salah!";
                break;
            case "username":
                text = "Username tidak ditemukan!";
                break;
        }

        Swal.fire({
            icon: icon,
            title: title,
            text: text
        }).then(() => {
            window.history.replaceState({}, document.title, "login.php");
        });
    }
});