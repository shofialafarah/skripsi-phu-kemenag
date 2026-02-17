$(document).ready(function () {
    const table = $('#tabelPembatalan').DataTable({
    responsive: true,
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    }
});

    function parseDate(dateStr) {
        const [year, month, day] = dateStr.split("-");
        return new Date(`${year}-${month}-${day}`);
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const startDate = $('#filter-start').val();
        const endDate = $('#filter-end').val();

        const tanggalVerifikasiStr = data[6]; 
        if (!tanggalVerifikasiStr) return false;

        const [day, month, year] = tanggalVerifikasiStr.split("-");
        const tanggalVerifikasi = new Date(`${year}-${month}-${day}`);

        const start = startDate ? parseDate(startDate) : null;
        const end = endDate ? parseDate(endDate) : null;

        if ((!start || tanggalVerifikasi >= start) && (!end || tanggalVerifikasi <= end)) {
            return true;
        }

        return false;
    });

    $('#filter-btn').on('click', function () {
        table.draw();
    });

    $('#reset-btn').on('click', function () {
        $('#filter-start').val('');
        $('#filter-end').val('');
        table.draw();
    });
});
