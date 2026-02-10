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

    let url = "cetak_laporan_aktivitas.php";

    // Jika kedua tanggal filter diisi, tambahkan sebagai parameter URL
    if (startDate && endDate) {
      url += `?start_date=${startDate}&end_date=${endDate}`;
    }

    console.log("Opening URL:", url); // Debug log

    // Buka halaman print di tab baru
    window.open(url, "_blank");
  });
