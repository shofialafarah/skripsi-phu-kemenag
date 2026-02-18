$(document).ready(function () {
    // 1. Inisialisasi DataTable
    const table = $('#tabelPendaftaran').DataTable({
        responsive: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
        },
        // Matikan pengurutan otomatis di kolom pertama (index 0)
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }],
        // Urutan default (misal berdasarkan tanggal pengajuan di kolom index 4)
        "order": [[4, 'desc']] 
    });

    // 2. Logika Nomor Urut Otomatis (PENTING)
    // Fungsi ini akan dijalankan setiap kali tabel di-sort atau di-filter
    table.on('order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();

    // --- Sisa kode filter tanggal kamu (tetap sama) ---
    function parseDate(dateStr) {
        const [year, month, day] = dateStr.split("-");
        return new Date(`${year}-${month}-${day}`);
    }

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const startDate = $('#filter-start').val();
        const endDate = $('#filter-end').val();
        const tanggalPengajuanStr = data[4]; 

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