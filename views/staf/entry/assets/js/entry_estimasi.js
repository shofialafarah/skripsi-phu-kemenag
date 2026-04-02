function isiDataJamaah() {
  const selectElement = document.getElementById("id_pendaftaran");
  const selectedOption = selectElement.options[selectElement.selectedIndex];

  if (selectedOption.value !== "") {
    // Ambil data dari atribut data-* pada option yang dipilih
    const namaAyah = selectedOption.getAttribute("data-ayah") || "";
    const jenisKelamin = selectedOption.getAttribute("data-kelamin") || "";
    const tanggalLahir = selectedOption.getAttribute("data-lahir") || "";
    const statusHaji = selectedOption.getAttribute("data-status") || "";

    // Isi field-field yang readonly
    document.getElementById("nama_ayah").value = namaAyah;
    document.getElementById("jenis_kelamin").value = jenisKelamin;
    document.getElementById("tanggal_lahir").value = tanggalLahir;
    document.getElementById("status_pergi_haji").value = statusHaji;

    // Debug log untuk memastikan data terambil
    console.log("Data jamaah yang dipilih:", {
      namaAyah: namaAyah,
      jenisKelamin: jenisKelamin,
      tanggalLahir: tanggalLahir,
      statusHaji: statusHaji,
    });
  } else {
    // Kosongkan field jika tidak ada yang dipilih
    document.getElementById("nama_ayah").value = "";
    document.getElementById("jenis_kelamin").value = "";
    document.getElementById("tanggal_lahir").value = "";
    document.getElementById("status_pergi_haji").value = "";
  }
}

function editData(data) {
  // 1. Isi Hidden ID (Penting untuk WHERE di SQL)
  document.getElementById("edit_id_estimasi").value = data.id_estimasi;
  document.getElementById("edit_id_pendaftaran").value = data.id_pendaftaran;

  // 2. Isi Input yang boleh diubah
  document.getElementById("edit_nomor_porsi").value = data.nomor_porsi;
  document.getElementById("edit_tgl_pendaftaran").value = data.tgl_pendaftaran;

  // 3. Isi Label/Input Readonly sebagai referensi saja
  document.getElementById("edit_nama_jamaah").value = data.nama_jamaah;

  // Debugging di console
  console.log("Data diedit:", data);
}

// Urutan: id, nomorPorsi, nama
function hapusData(id, nomorPorsi, nama) {
  // Debugging: Munculkan di console F12 untuk cek apakah data masuk atau tidak
  console.log("ID:", id, "Porsi:", nomorPorsi, "Nama:", nama);

  Swal.fire({
    title: "Apakah Anda yakin?",
    html: `Anda akan menghapus data Estimasi:<br>
           <strong class="text-danger">${nomorPorsi}</strong> - <strong>${nama}</strong><br>
           <small>Data yang dihapus tidak dapat dikembalikan!</small>`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      // Pastikan parameter id_estimasi sudah benar
      window.location.href = "includes/hapus_estimasi.php?id_estimasi=" + id;
    }
  });
}

// Initialize DataTable
$(document).ready(function () {
  $("#dataEstimasi").DataTable({
    responsive: false,
    scrollX: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });
});
