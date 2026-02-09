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

    function setDefaultFoto() {
        document.getElementById('previewFoto').src = 'default.png';
    }