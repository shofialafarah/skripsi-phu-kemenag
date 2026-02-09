<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>PHU KEMENAG BANJAR</title>
    <link rel="icon" href="assets/img/logo_kemenag.png" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="keywords" />
    <meta content="" name="description" />

    <!-- Favicon -->
    <link href="assets/img/favicon.icon" rel="icon" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Lora:wght@600;700&display=swap"
      rel="stylesheet"
    />

    <!-- Icon Font Stylesheet -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css"
      rel="stylesheet"
    />

    <!-- Libraries Stylesheet -->
    <link href="assets/lib/animate/animate.min.css" rel="stylesheet" />
    <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet" />
  </head>

  <body>
    <!-- Spinner Start -->
    <div
      id="spinner"
      class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center"
    >
      <div class="spinner-border text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- Navbar Start -->
    <div
      class="container-fluid fixed-top px-0 wow fadeIn"
      data-wow-delay="0.1s"
    >
      <div class="top-bar row gx-0 align-items-center d-none d-lg-flex">
        <div class="col-lg-6 px-5 text-start">
          <small
            ><i class="fa fa-map-marker-alt me-2"></i>Jl. Sekumpul No. 72-73
            Kelurahan Jawa
          </small>
          <small class="ms-4"
            ><i class="fa fa-envelope me-2"></i
            >phukemenagbanjar@gmail.com</small
          >
        </div>
        <div class="col-lg-6 px-5 text-end">
          <small>Ikuti Kami:</small>
          <a
            class="text-body ms-3"
            href="https://www.facebook.com/kemenagbanjar/"
            ><i class="fab fa-facebook-f"></i
          ></a>
          <a class="text-body ms-3" href="https://x.com/kemenagkalsel/"
            ><i class="fab fa-twitter"></i
          ></a>
          <a class="text-body ms-3" href=""
            ><i class="fab fa-linkedin-in"></i
          ></a>
          <a
            class="text-body ms-3"
            href="https://instagram.com/kemenag_kab_banjar/"
            ><i class="fab fa-instagram"></i
          ></a>
        </div>
      </div>

      <nav
        class="navbar navbar-expand-lg navbar-light py-lg-0 px-lg-5 wow fadeIn"
        data-wow-delay="0.1s"
      >
        <a href="index.php" class="navbar-brand ms-4 ms-lg-0">
          <h1 class="fw-bold text-black m-0">
            Kementerian<span class="text-primary-green"> Agama</span>
          </h1>
        </a>
        <button
          type="button"
          class="navbar-toggler me-4"
          data-bs-toggle="collapse"
          data-bs-target="#navbarCollapse"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.php" class="nav-item nav-link active">Beranda</a>
            <a href="tentang-phu.php" class="nav-item nav-link">Tentang PHU</a>
            <div class="nav-item dropdown">
              <a
                href="#"
                class="nav-link dropdown-toggle"
                data-bs-toggle="dropdown"
                >Layanan</a
              >

              <div class="dropdown-menu m-0">
                <a href="pendaftaran.php" class="dropdown-item"
                  >Pendaftaran Haji</a
                >
                <a href="estimasi.php" class="dropdown-item"
                  >Estimasi Keberangkatan</a
                >
                <a href="pembatalan.php" class="dropdown-item"
                  >Pembatalan Haji</a
                >
                <a href="pelimpahan.php" class="dropdown-item"
                  >Pelimpahan Haji</a
                >
              </div>
            </div>
            <a href="kontak.php" class="nav-item nav-link">Hubungi Kami</a>
          </div>
          <div class="d-none d-lg-flex ms-2">
            <!-- Tombol Login -->
            <a
              class="btn-sm-square bg-white rounded-circle ms-3"
              href="../auth/login.php"
            >
              <small class="fa fa-user text-body"></small>
            </a>

            <!-- Tombol Register -->
            <a
              class="btn-sm-square bg-white rounded-circle ms-3"
              href="../auth/register.php"
            >
              <small class="fa fa-user-plus text-body"></small>
            </a>
          </div>
        </div>
      </nav>
    </div>
    <!-- Navbar End -->
