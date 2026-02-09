<?php include 'includes/header.php'; ?>

    <!-- Custom CSS for Popup -->
    <style>
        /* Popup Modal Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            animation: popupSlideIn 0.3s ease-out;
        }

        @keyframes popupSlideIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .popup-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .popup-icon.error {
            color: #dc3545;
        }

        .popup-icon.success {
            color: #28a745;
        }

        .popup-icon.warning {
            color: #ffc107;
        }

        .popup-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .popup-message {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .popup-close-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .popup-close-btn:hover {
            background: #0056b3;
        }

        .popup-close-btn.error {
            background: #dc3545;
        }

        .popup-close-btn.error:hover {
            background: #c82333;
        }

        .popup-close-btn.success {
            background: #28a745;
        }

        .popup-close-btn.success:hover {
            background: #218838;
        }

        .popup-close-btn.warning {
            background: #ffc107;
            color: #212529;
        }

        .popup-close-btn.warning:hover {
            background: #e0a800;
        }

        /* Form validation styles */
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>

    <!-- Popup Modal -->
    <div id="popupOverlay" class="popup-overlay">
        <div class="popup-content">
            <div id="popupIcon" class="popup-icon"></div>
            <div id="popupTitle" class="popup-title"></div>
            <div id="popupMessage" class="popup-message"></div>
            <button id="popupCloseBtn" class="popup-close-btn">OK</button>
        </div>
    </div>

    <!-- Page Header Start -->
    <div class="container-fluid page-header wow fadeIn" data-wow-delay="0.1s">
        <div class="container">
            <h1 class="display-3 mb-3 animated slideInDown">Hubungi Kami</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-body" href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item text-dark active" aria-current="page">Hubungi Kami</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Contact Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="section-header text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                <h1 class="display-5 mb-3">Hubungi Kami</h1>
            </div>
            <div class="row g-5 justify-content-center">
                <div class="col-lg-5 col-md-12 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-primary text-white d-flex flex-column justify-content-center h-100 p-5">
                        <h5 class="text-white">Hubungi Melalui Telepon Kami</h5>
                        <p class="mb-5"><i class="fa fa-phone-alt me-3"></i>(0511) 4721249</p>
                        <h5 class="text-white">Hubungi Melalui Email Kami</h5>
                        <p class="mb-5"><i class="fa fa-envelope me-3"></i>phukemenagbanjar@gmail.com</p>
                        <h5 class="text-white">Alamat Kantor</h5>
                        <p class="mb-5"><i class="fa fa-map-marker-alt me-3"></i>Jl. Sekumpul No. 72-73 Kelurahan Jawa Martapura Banjar 70614</p>
                        <h5 class="text-white">Ikuti Kami</h5>
                        <div class="d-flex pt-2">
                            <a class="btn btn-square btn-outline-light rounded-circle me-1" href="https://x.com/kemenagkalsel/"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-square btn-outline-light rounded-circle me-1" href="https://www.facebook.com/kemenagbanjar/"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-square btn-outline-light rounded-circle me-1" href="www.youtube.com/@kementerianagamakabbanjar"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
                    <p class="mb-4">Silakan hubungi kami melalui kolom informasi berikut untuk pertanyaan atau bantuan lebih lanjut.</p>
                    <form id="contact-form" method="POST" action="kontak.php" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="nama_anda" placeholder="Nama Anda" required>
                                    <label for="name">Nama Anda</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email_anda" placeholder="Email Anda" required>
                                    <label for="email">Email Anda</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="subject" name="subjek" placeholder="Subjek" required>
                                    <label for="subject">Subjek</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Leave a message here" id="message" name="pesan" style="height: 200px" required></textarea>
                                    <label for="message">Pesan</label>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary rounded-pill py-3 px-5" type="submit">Kirim Pesan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

    <!-- Google Map Start -->
    <div class="container-xxl px-0 wow fadeIn" data-wow-delay="0.1s" style="margin-bottom: -6px;">
        <iframe class="w-100" style="height: 450px;"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3982.6959899671638!2d114.84959327406287!3d-3.424023641697639!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de681b23f10f8b7%3A0x1a5b58027a21327c!2sKANTOR%20KEMENTERIAN%20AGAMA%20KABUPATEN%20BANJAR!5e0!3m2!1sen!2sid!4v1736727053962!5m2!1sen!2sid"
            frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
    <!-- Google Map End -->
<?php include 'includes/footer.php'; ?>

    <script>
        // Popup Functions
        function showPopup(type, title, message) {
            const popup = document.getElementById('popupOverlay');
            const icon = document.getElementById('popupIcon');
            const titleEl = document.getElementById('popupTitle');
            const messageEl = document.getElementById('popupMessage');
            const closeBtn = document.getElementById('popupCloseBtn');

            // Set icon based on type
            let iconClass = '';
            switch(type) {
                case 'error':
                    iconClass = 'fas fa-times-circle error';
                    break;
                case 'success':
                    iconClass = 'fas fa-check-circle success';
                    break;
                case 'warning':
                    iconClass = 'fas fa-exclamation-triangle warning';
                    break;
                default:
                    iconClass = 'fas fa-info-circle';
            }

            icon.className = `popup-icon ${iconClass}`;
            titleEl.textContent = title;
            messageEl.textContent = message;
            closeBtn.className = `popup-close-btn ${type}`;

            popup.style.display = 'flex';
        }

        function hidePopup() {
            document.getElementById('popupOverlay').style.display = 'none';
        }

        // Close popup when clicking close button or overlay
        document.getElementById('popupCloseBtn').addEventListener('click', hidePopup);
        document.getElementById('popupOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                hidePopup();
            }
        });

        // Form validation functions
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validateField(field, value, fieldName) {
            const feedback = field.parentElement.querySelector('.invalid-feedback');
            
            // Reset validation state
            field.classList.remove('is-invalid');
            feedback.textContent = '';

            // Check if field is empty
            if (!value.trim()) {
                field.classList.add('is-invalid');
                feedback.textContent = `${fieldName} harus diisi.`;
                return false;
            }

            // Special validation for email
            if (field.type === 'email' && !validateEmail(value)) {
                field.classList.add('is-invalid');
                feedback.textContent = 'Format email tidak valid.';
                return false;
            }

            // Check minimum length for certain fields
            if (fieldName === 'Nama' && value.trim().length < 2) {
                field.classList.add('is-invalid');
                feedback.textContent = 'Nama minimal 2 karakter.';
                return false;
            }

            if (fieldName === 'Subjek' && value.trim().length < 3) {
                field.classList.add('is-invalid');
                feedback.textContent = 'Subjek minimal 3 karakter.';
                return false;
            }

            if (fieldName === 'Pesan' && value.trim().length < 10) {
                field.classList.add('is-invalid');
                feedback.textContent = 'Pesan minimal 10 karakter.';
                return false;
            }

            return true;
        }

        // Real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contact-form');
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const subjectField = document.getElementById('subject');
            const messageField = document.getElementById('message');

            // Add real-time validation on blur
            nameField.addEventListener('blur', function() {
                validateField(this, this.value, 'Nama');
            });

            emailField.addEventListener('blur', function() {
                validateField(this, this.value, 'Email');
            });

            subjectField.addEventListener('blur', function() {
                validateField(this, this.value, 'Subjek');
            });

            messageField.addEventListener('blur', function() {
                validateField(this, this.value, 'Pesan');
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const name = nameField.value;
                const email = emailField.value;
                const subject = subjectField.value;
                const message = messageField.value;

                // Validate all fields
                const isNameValid = validateField(nameField, name, 'Nama');
                const isEmailValid = validateField(emailField, email, 'Email');
                const isSubjectValid = validateField(subjectField, subject, 'Subjek');
                const isMessageValid = validateField(messageField, message, 'Pesan');

                // Check if all fields are valid
                if (!isNameValid || !isEmailValid || !isSubjectValid || !isMessageValid) {
                    showPopup('error', 'Form Tidak Lengkap', 'Mohon lengkapi semua kolom dengan benar sebelum mengirim pesan.');
                    return;
                }

                // If all validation passes, submit the form
                showPopup('success', 'Validasi Berhasil', 'Semua data telah terisi dengan benar. Mengirim pesan...');
                
                // Simulate form submission after 2 seconds
                setTimeout(() => {
                    hidePopup();
                    form.submit();
                }, 2000);
            });

            // Handle URL parameters for server response
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'success') {
                showPopup('success', 'Pesan Terkirim', 'Pesan Anda berhasil terkirim. Terima kasih!');
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (status === 'invalid_input') {
                showPopup('warning', 'Input Tidak Valid', 'Mohon lengkapi semua kolom dan pastikan format email benar.');
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (status === 'error') {
                showPopup('error', 'Terjadi Kesalahan', 'Terjadi kesalahan saat mengirim pesan. Mohon coba lagi nanti.');
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

