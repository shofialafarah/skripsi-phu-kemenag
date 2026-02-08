$(document).ready(function () {
    const table = $('#tabelPembatalan').DataTable({
    responsive: true,
    language: {
        url: 'assets/lang/id.json' // path lokal, pastikan file ada
    }
});

    function parseDate(dateStr) {
        const [year, month, day] = dateStr.split("-");
        return new Date(`${year}-${month}-${day}`);
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const startDate = $('#filter-start').val();
        const endDate = $('#filter-end').val();

        const tanggalPengajuanStr = data[8]; // ← sesuaikan ini dengan hasil console.log
        if (!tanggalPengajuanStr) return false;

        const [day, month, year] = tanggalPengajuanStr.split("-");
        const tanggalPengajuan = new Date(`${year}-${month}-${day}`);

        const start = startDate ? parseDate(startDate) : null;
        const end = endDate ? parseDate(endDate) : null;

        if ((!start || tanggalPengajuan >= start) && (!end || tanggalPengajuan <= end)) {
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
