function showEditModal(data) {
    $('#id_estimasi').val(data.id_estimasi);
    $('#nomor_porsi').val(data.nomor_porsi);
    $('#nama_jamaah').val(data.nama_jamaah);
    $('#nama_ayah').val(data.nama_ayah);
    $('#jenis_kelamin').val(data.jenis_kelamin);
    $('#tanggal_lahir').val(data.tanggal_lahir);
    $('#tgl_pendaftaran').val(data.tgl_pendaftaran);
    $('#status_haji').val(data.status_haji);
    $('#editModal').modal('show');
}

function hapusData(id) {
Swal.fire({
title: 'Yakin ingin menghapus?',
text: "Data ini akan dihapus permanen!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#3085d6',
confirmButtonText: 'Ya, hapus!',
cancelButtonText: 'Batal'
}).then((result) => {
if (result.isConfirmed) {
    window.location.href = "hapus_estimasi.php?id=" + id;
}
});
}

function showAddModal() {
    const modal = new bootstrap.Modal(document.getElementById('addModal'));
    modal.show();
}
