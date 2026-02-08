$(document).ready(function () {
  $("#filter-btn").on("click", function () {
    let start = $("#filter-start").val();
    let end = $("#filter-end").val();

    if (!start && !end) {
      alert("Isi salah satu atau kedua tanggal untuk memfilter.");
      return;
    }

    // Convert ke timestamp utk dibandingkan
    let startDate = start ? new Date(start).getTime() : null;
    let endDate = end ? new Date(end).getTime() : null;

    $("#tabelStaf tbody tr").each(function () {
      let tanggalText = $(this).find("td:eq(4)").text(); // kolom ke-6 (0-indexed)
      let tanggal = new Date(
        tanggalText.split("-").reverse().join("-")
      ).getTime(); // format dari 'dd-mm-YYYY'

      let show = true;

      if (startDate && tanggal < startDate) show = false;
      if (endDate && tanggal > endDate) show = false;

      $(this).toggle(show);
    });
  });

  $("#reset-btn").on("click", function () {
    $("#filter-start").val("");
    $("#filter-end").val("");
    $("#tabelStaf tbody tr").show();
  });
});
