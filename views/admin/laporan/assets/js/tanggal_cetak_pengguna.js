/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
// ====== DATE FILTER PARSER & EXT SEARCH ======
function parseDateTimeDMYHM(str) {
  if (!str) return null;

  const parts = str.trim().split(" ");
  const dateParts = parts[0].split("-");

  if (dateParts.length !== 3) return null;

  // Default time = 00:00
  let hours = 0, minutes = 0;
  if (parts.length === 2) {
    const timeParts = parts[1].split(":");
    if (timeParts.length >= 2) {
      hours = parseInt(timeParts[0]);
      minutes = parseInt(timeParts[1]);
    }
  }

  const d = new Date(
    parseInt(dateParts[2]),
    parseInt(dateParts[1]) - 1,
    parseInt(dateParts[0]),
    hours,
    minutes
  );

  return isNaN(d.getTime()) ? null : d;
}


$(document).ready(function () {
  console.log("tanggal_cetak_pengguna.js loaded");

  // ====== INITIALIZE DATA TABLE ======
  const TGL_COL = 6; // kolom tanggal validasi

  $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
  if (settings.nTable.id !== "tabelStaf") return true;

  const startDate = $("#filter-start").val();
  const endDate = $("#filter-end").val();

  const rowDate = parseDateTimeDMYHM(data[TGL_COL]);

  console.log("Row date string:", data[TGL_COL]);
  console.log("Parsed row date:", rowDate);

  if (!startDate && !endDate) return true;
  if (!rowDate) return false;

  const minDate = startDate ? new Date(startDate + "T00:00:00") : null;
  const maxDate = endDate ? new Date(endDate + "T23:59:59") : null;

  if (minDate && rowDate < minDate) return false;
  if (maxDate && rowDate > maxDate) return false;

  return true;
});

  const table = $("#tabelStaf").DataTable({
    responsive: true,
    dom: `<"d-flex justify-content-between align-items-center mb-3"
        <"#tanggal-filter-container">
        <"d-flex align-items-center justify-content-between w-100"
            <"length-menu-container"l>
            f
        >
      >rtip`,

    columnDefs: [
      {
        targets: 5,
        visible: true,
        className: "text-center",
        responsivePriority: 1,
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });

  // Filter buttons
  $("#filter-btn").on("click", function () {
    table.draw();
    console.log("Filter button clicked");
  });

  $("#reset-btn").on("click", function () {
    $("#filter-start").val("");
    $("#filter-end").val("");
    table.draw();
    console.log("Reset button clicked");
  });

  // Sidebar & menu setup
  closeAllDropdowns();
  setupMenuTracking();

  // Date & time update every second - use renamed function
  updateStafDateTime();
  setInterval(updateStafDateTime, 1000);

  // Expose functions globally if needed
  window.toggleOpen = toggleOpen;
  window.toggleDropdown = toggleDropdown;
});
