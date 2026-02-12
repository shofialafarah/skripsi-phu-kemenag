/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
document
  .getElementById("print-report-btn")
  .addEventListener("click", function (e) {
    e.preventDefault(); // Mencegah form submit atau behavior default

    const startDate = document.getElementById("filter-start")
      ? document.getElementById("filter-start").value
      : "";
    const endDate = document.getElementById("filter-end")
      ? document.getElementById("filter-end").value
      : "";

    let url = "cetak/cetak_laporan_aktivitas.php";

    // Periksa kedua tanggal; jika tidak lengkap, tunjukkan SweetAlert2
    if (!startDate || !endDate) {
      Swal.fire({
        icon: "warning",
        title: "Tanggal belum lengkap",
        text: "Silakan pilih tanggal mulai dan tanggal akhir terlebih dahulu sebelum mencetak laporan.",
      });
      return;
    }

    // Jika kedua tanggal filter diisi, tambahkan sebagai parameter URL
    url += `?start_date=${startDate}&end_date=${endDate}`;

    console.log("Opening URL:", url); // Debug log

    // Buka halaman print di tab baru
    window.open(url, "_blank");
  });