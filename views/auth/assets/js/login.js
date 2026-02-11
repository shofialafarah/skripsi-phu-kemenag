
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

document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");
    const attempts = params.get("attempts");

    if (!error) return;

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
            text = attempts
                ? `Password salah! Percobaan ke-${attempts}`
                : "Password salah!";
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
        // bersihin URL biar alert ga muncul lagi pas refresh
        window.history.replaceState({}, document.title, "login.php");
    });
});
