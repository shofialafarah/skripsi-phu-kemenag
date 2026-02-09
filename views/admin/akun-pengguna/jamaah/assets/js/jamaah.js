function previewGambar(input) {
            const file = input.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                const maxSize = 10 * 1024 * 1024; // 10MB

                // Validasi tipe file
                if (!allowedTypes.includes(file.type)) {
                    alert("Format file harus JPG atau PNG!");
                    input.value = "";
                    return;
                }

                // Validasi ukuran file
                if (file.size > maxSize) {
                    alert("Ukuran gambar maksimal 10MB!");
                    input.value = "";
                    return;
                }

                // Preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        function hapusFoto() {
            // Reset preview ke foto default
            document.getElementById('previewFoto').src = 'assets/img/profil.jpg';

            // Clear file input
            document.getElementById('foto').value = '';

            // Optional: Tampilkan konfirmasi
            alert('Foto telah dihapus. Sekarang menggunakan foto default.');
        }