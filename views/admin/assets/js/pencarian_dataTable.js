$(document).ready(function () {
  console.log("pencarian_dataTable.js loaded");

  // Inisialisasi DataTable dengan fitur pencarian
  const table = $("#tabelStaf").DataTable({
    responsive: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });

  // Search input bawaan DataTable tetap aktif
});
