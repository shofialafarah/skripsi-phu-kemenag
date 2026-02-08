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
