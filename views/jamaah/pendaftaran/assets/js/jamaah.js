$(document).ready(function () {
  function parseDateTimeDMYHM(str) {
    if (!str) return null;
    const parts = str.split(" ");
    if (parts.length !== 2) return null;

    const dateParts = parts[0].split("-");
    const timeParts = parts[1].split(":");

    if (dateParts.length !== 3 || timeParts.length !== 2) return null;

    return new Date(
      parseInt(dateParts[2]), // tahun
      parseInt(dateParts[1]) - 1, // bulan (0-11)
      parseInt(dateParts[0]), // tanggal
      parseInt(timeParts[0]), // jam
      parseInt(timeParts[1]) // menit
    );
  }

  const TGL_COL = 5; // Kolom ke-6 (index ke-5)

  $.fn.dataTable.ext.search.push(function (settings, data) {
    if (settings.nTable.id !== "tabelJamaah") return true;

    const startDate = $("#filter-start").val();
    const endDate = $("#filter-end").val();
    if (!startDate && !endDate) return true;

    const rowDate = parseDateTimeDMYHM(data[TGL_COL]);
    if (!rowDate) return false;

    const minDate = startDate ? new Date(startDate + "T00:00:00") : null;
    const maxDate = endDate ? new Date(endDate + "T23:59:59") : null;

    if (minDate && rowDate < minDate) return false;
    if (maxDate && rowDate > maxDate) return false;

    return true;
  });

  const table = $("#tabelJamaah").DataTable({
    responsive: true,
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });

  $("#filter-btn").on("click", function () {
    table.draw();
  });

  $("#reset-btn").on("click", function () {
    $("#filter-start").val("");
    $("#filter-end").val("");
    table.draw();
  });
});

function previewDocument(filePath, fileName) {
    const encodedPath = encodeURIComponent('../' + filePath); // asumsi file di luar folder web/
    const url = 'pdfjs/web/viewer.html?file=' + encodedPath;
    window.open(url, '_blank');
}