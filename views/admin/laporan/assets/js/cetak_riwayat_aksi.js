// Script untuk tombol cetak - PERBAIKAN UTAMA
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