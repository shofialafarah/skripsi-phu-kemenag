$(document).ready(function () {
    const table = $('#tabelPembatalan').DataTable({
        responsive: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
        },
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }],
        "order": [[5, 'desc']] 
    });

    table.on('order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    function parseDate(dateStr) {
        const [year, month, day] = dateStr.split("-");
        return new Date(`${year}-${month}-${day}`);
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const startDate = $('#filter-start').val();
        const endDate = $('#filter-end').val();
        const tanggalValidasiStr = data[4]; 

        if (!tanggalValidasiStr) return false;

        const [day, month, year] = tanggalValidasiStr.split("-");
        const tanggalValidasi = new Date(`${year}-${month}-${day}`);
        const start = startDate ? parseDate(startDate) : null;
        const end = endDate ? parseDate(endDate) : null;

        if ((!start || tanggalValidasi >= start) && (!end || tanggalValidasi <= end)) {
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