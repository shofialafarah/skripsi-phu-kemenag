<?php include 'includes/header.php'; ?>
<!-- Carousel Start -->
<div class="container-fluid p-0 mb-0 wow fadeIn" data-wow-delay="0.1s">
    <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="assets/img/carousel-1.png" alt="Image" />
            </div>
            <div class="carousel-item">
                <img class="w-100" src="assets/img/carousel-2.png" alt="Image" />
            </div>
        </div>
        <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#header-carousel"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sebelumnya</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">selanjutnya</span>
        </button>
    </div>
</div>
<!-- Carousel End -->

<!-- Layanan Start -->
<div class="container-fluid my-0 py-5" style="background-color: #89ee69">
    <div class="layanan-container">
        <div class="section-header text-center mx-auto mb-2 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 1000px">
            <h1 class="display-5 mb-3">Layanan</h1>
            <p>
                Disini Anda bisa melihat persyaratan berkas yang harus diupload.
            </p>
        </div>
        <div class="row g-4">
            <!-- Pendaftaran Haji -->
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.4s">
                <div
                    class="bg-white text-center h-100 p-4 p-xl-5"
                    style="border-radius: 20px">
                    <img
                        class="img-fluid mb-4"
                        src="assets/img/icon-kakbah.png"
                        width="100"
                        height="auto"
                        alt="" />
                    <h4 class="mb-3">Pendaftaran Haji</h4>
                    <p class="mb-4">
                        Informasi dan panduan pendaftaran Haji reguler dan khusus.
                    </p>
                    <a
                        class="btn btn-outline-primary border-2 py-2 px-4 rounded-pill"
                        href="pendaftaran.php">Baca Selengkapnya</a>
                </div>
            </div>
            <!-- Estimasi Haji -->
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.4s">
                <div
                    class="bg-white text-center h-100 p-4 p-xl-5"
                    style="border-radius: 20px">
                    <img
                        class="img-fluid mb-4"
                        src="assets/img/icon-kakbah.png"
                        width="100"
                        height="auto"
                        alt="" />
                    <h4 class="mb-3">Estimasi Haji</h4>
                    <p class="mb-4">
                        Perkirakan tahun keberangkatan berdasarkan nomor porsi.
                    </p>
                    <a
                        class="btn btn-outline-primary border-2 py-2 px-4 rounded-pill"
                        href="estimasi.php">Baca Selengkapnya</a>
                </div>
            </div>

            <!-- First Row -->
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div
                    class="bg-white text-center h-100 p-4 p-xl-5"
                    style="border-radius: 20px">
                    <img
                        class="img-fluid mb-4"
                        src="assets/img/icon-kakbah.png"
                        width="100"
                        height="auto"
                        alt="" />
                    <h4 class="mb-3">Pembatalan Haji</h4>
                    <p class="mb-4">
                        Jika alasan jamaah Meninggal Dunia / Keperluan Ekonomi.
                    </p>
                    <a
                        class="btn btn-outline-primary border-2 py-2 px-4 rounded-pill"
                        href="pembatalan.php">Baca Selengkapnya</a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div
                    class="bg-white text-center h-100 p-4 p-xl-5"
                    style="border-radius: 20px">
                    <img
                        class="img-fluid mb-4"
                        src="assets/img/icon-kakbah.png"
                        width="100"
                        height="auto"
                        alt="" />
                    <h4 class="mb-3">Pelimpahan Haji</h4>
                    <p class="mb-4">
                        Jika alasan jamaah Sakit Permanen / Meninggal Dunia.
                    </p>
                    <a
                        class="btn btn-outline-primary border-2 py-2 px-4 rounded-pill"
                        href="pelimpahan.php">Baca Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Layanan End -->

<!-- Testimonial Start -->
<div class="container-fluid bg-white bg-icon py-5 mb-5">
    <div class="container">
        <div
            class="section-header text-center mx-auto mb-2 wow fadeInUp"
            data-wow-delay="0.1s"
            style="max-width: 1000px">
            <h1 class="display-5 mb-3">Umpan Balik Pengguna</h1>
            <p>Disini kita bisa melihat tanggapan dari pengguna website.</p>
        </div>
        <div
            class="owl-carousel testimonial-carousel wow fadeInUp"
            data-wow-delay="0.1s">
            <div
                class="testimonial-item position-relative bg-light p-5 mt-4"
                style="border-radius: 20px">
                <i
                    class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                <p class="mb-4">
                    Alhamdulillah.. Baru di update versi terbaru.. Semoga sukses terus
                    bisa makin memudahkan. Aamiin
                </p>
                <div class="d-flex align-items-center">
                    <img
                        class="flex-shrink-0 rounded-circle"
                        src="assets/img/testimonial-1.jpg"
                        alt="" />
                    <div class="ms-3">
                        <h5 class="mb-1">Nikmah El Humra</h5>
                        <span>Guru Madrasah Ibtidaiyah</span>
                    </div>
                </div>
            </div>
            <div
                class="testimonial-item position-relative bg-light p-5 mt-4"
                style="border-radius: 20px">
                <i
                    class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                <p class="mb-4">
                    Alhamdulillah.. Websitenya informatif dan mudah dipahami oleh
                    pengguna baru seperti saya😊
                </p>
                <div class="d-flex align-items-center">
                    <img
                        class="flex-shrink-0 rounded-circle"
                        src="assets/img/testimonial-2.jpg"
                        alt="" />
                    <div class="ms-3">
                        <h5 class="mb-1">Arwan Sutriawan</h5>
                        <span>Petugas Haji</span>
                    </div>
                </div>
            </div>
            <div
                class="testimonial-item position-relative bg-light p-5 mt-4"
                style="border-radius: 20px">
                <i
                    class="fa fa-quote-left fa-3x position-absolute top-0 start-0 mt-n4 ms-5"></i>
                <p class="mb-4">
                    Alhamdulillah sangat mudah digunakan. Mudah-mudahan pada berbagai
                    layanan lainnya bisa menerapkan digitalisasi yang sama bahkan
                    lebih unggul
                </p>
                <div class="d-flex align-items-center">
                    <img
                        class="flex-shrink-0 rounded-circle"
                        src="assets/img/testimonial-3.jpg"
                        alt="" />
                    <div class="ms-3">
                        <h5 class="mb-1">Imam Abdillah</h5>
                        <span>Petugas Haji</span>
                    </div>
                </div>
            </div>
            <div
                class="testimonial-item position-relative bg-light p-5 mt-4"
                style="border-radius: 20px">
                <i
                    class="fa fa-quote-left fa-3x text-primary position-absolute top-0 start-0 mt-n4 ms-5"></i>
                <p class="mb-4">
                    Sangat mudah digunakan hasilnya pun langsuung tau. Sehingga bisa
                    transparan dan akuntabel.
                </p>
                <div class="d-flex align-items-center">
                    <img
                        class="flex-shrink-0 rounded-circle"
                        src="assets/img/testimonial-4.jpg"
                        alt="" />
                    <div class="ms-3">
                        <h5 class="mb-1">Rusdiannor Iyan</h5>
                        <span>Guru Madrasah Ibtidaiyah</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testimonial End -->

<!-- Blog Start -->
<div class="container-fluid py-5 mb-0" style="background-color: #89ee69">
    <div class="container">
        <div
            class="section-header text-center mx-auto mb-2 wow fadeInUp"
            data-wow-delay="0.1s"
            style="max-width: 1000px">
            <h1 class="display-5 mb-3">Artikel Pilihan</h1>
            <div class="keterangan">
                Disini berisi seputar informasi berita terkini di Kementrian Agama
                Kabupaten Banjar Provinsi Kalimantan Selatan
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <img class="img-fluid" src="assets/img/blog-1.png" alt="gambar blog 1" />
                <div class="bg-light p-4">
                    <a
                        class="d-block h5 lh-base mb-4"
                        href="https://kalsel.kemenag.go.id/berita/542663/Kemenag-Banjar-Berkomitmen-Tingkatkan-Pelayanan-di-PTSP">Kemenag Banjar Berkomitmen Tingkatkan Pelayanan di PTSP</a>
                    <div class="text-muted border-top pt-4">
                        <small class="me-3"><i class="fa fa-user text-primary me-2"></i>Admin</small>
                        <small class="me-3"><i class="fa fa-calendar text-primary me-2"></i>02 Februari,
                            2021</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <img
                    class="img-fluid"
                    src="assets/img/blog-2.png"
                    alt="gambar blog 2" />
                <div class="bg-light p-4">
                    <a class="d-block h5 lh-base mb-4" href="https://www.facebook.com/100063789843059/photos/1085838930219092/?_rdr">Penyelenggara Haji dan Umrah</a>
                    <div class="text-muted border-top pt-4">
                        <small class="me-3"><i class="fa fa-user text-primary me-2"></i>Admin</small>
                        <small class="me-3"><i class="fa fa-calendar text-primary me-2"></i>01 Jan,
                            2045</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <img
                    class="img-fluid"
                    src="assets/img/blog-3.png"
                    alt="gambar blog 3" />
                <div class="bg-light p-4">
                    <a class="d-block h5 lh-base mb-4" href="https://www.facebook.com/kemenagbanjar/photos/d41d8cd9/1280725874063729/">Kegiatan Apel Pagi KanKemenag Kab. Banjar </a>
                    <div class="text-muted border-top pt-4">
                        <small class="me-3"><i class="fa fa-user text-primary me-2"></i>Admin</small>
                        <small class="me-3"><i class="fa fa-calendar text-primary me-2"></i>28 Juni,
                            2019</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Blog End -->

<?php include 'includes/footer.php'; ?>