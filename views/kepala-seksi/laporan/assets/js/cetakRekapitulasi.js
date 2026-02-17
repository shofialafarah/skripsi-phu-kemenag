$(document).ready(function () {
  const table = $("#tabelRekapitulasi").DataTable({
    responsive: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const cetakLaporanBtn = document.getElementById("cetak-laporan-btn");

  if (cetakLaporanBtn) {
    cetakLaporanBtn.addEventListener("click", function () {
      // Langsung buka halaman cetak
      window.open("cetak/cetak_laporan_rekapitulasi.php", "_blank");
    });
  }
});
