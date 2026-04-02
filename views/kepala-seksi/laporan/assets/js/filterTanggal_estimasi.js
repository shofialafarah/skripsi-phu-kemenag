$(document).ready(function () {
  const table = $("#tabelEstimasi").DataTable({
    responsive: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });

  // Helper untuk mengubah string YYYY-MM-DD (dari input date) ke objek Date
  function parseInputDate(dateStr) {
    if (!dateStr) return null;
    return new Date(dateStr + "T00:00:00");
  }

  // Helper untuk mengubah string DD-MM-YYYY (dari kolom tabel) ke objek Date
  function parseTableDate(dateStr) {
    if (!dateStr || dateStr === "-") return null;
    const parts = dateStr.split("-");
    // parts[0] = tgl, parts[1] = bln, parts[2] = thn
    return new Date(parts[2], parts[1] - 1, parts[0]);
  }

  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    const startVal = $("#filter-start").val();
    const endVal = $("#filter-end").val();

    // Ambil data dari kolom ke-7 (index 6: Estimasi Berangkat)
    const tanggalBerangkatStr = data[6] ? data[6].trim() : "";
    const tanggalBerangkat = parseTableDate(tanggalBerangkatStr);

    if (!tanggalBerangkat) return false;

    const start = parseInputDate(startVal);
    const end = parseInputDate(endVal);

    // Logika perbandingan
    if (start && tanggalBerangkat < start) return false;
    if (end && tanggalBerangkat > end) return false;

    return true;
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
