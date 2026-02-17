document.addEventListener("DOMContentLoaded", function () {
  const filterStartInput = document.getElementById("filter-start");
  const filterEndInput = document.getElementById("filter-end");
  const cetakLaporanBtn = document.getElementById("print-report-btn");

  if (cetakLaporanBtn) {
    cetakLaporanBtn.addEventListener("click", function () {
      const startDate = filterStartInput.value;
      const endDate = filterEndInput.value;

      if (!startDate || !endDate) {
        Swal.fire({
          icon: "warning",
          title: "Peringatan!",
          text: "Mohon lengkapi Tanggal Mulai dan Tanggal Akhir untuk mencetak laporan.",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      const printUrl = `/phu-kemenag-banjar-copy/views/kepala-seksi/laporan/cetak/cetak_laporan_estimasi.php?start_date=${startDate}&end_date=${endDate}`;

      Swal.fire({
        title: "Cetak Laporan?",
        text: `Mencetak data dari ${startDate} sampai ${endDate}`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Cetak!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          window.open(printUrl, "_blank");
        }
      });
    });
  } else {
    console.error('Button dengan ID "print-report-btn" tidak ditemukan!');
  }
});
