// Script untuk mengisi ID pendaftaran ke modal saat tombol upload diklik
document.addEventListener("DOMContentLoaded", function () {
  // Menangani klik pada tombol upload
  const uploadButtons = document.querySelectorAll(".upload-doc-btn");
  uploadButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const id_pembatalan = this.getAttribute("data-id");
      document.getElementById("id_pembatalan").value = id_pembatalan;
    });
  });

  // Menangani klik pada tombol delete jika ada
  const deleteButtons = document.querySelectorAll(".delete-doc-btn");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const id_pembatalan = this.getAttribute("data-id");

      Swal.fire({
        title: "Konfirmasi",
        text: "Anda yakin ingin menghapus dokumen ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect ke halaman hapus dokumen
          window.location.href = `hapus_ePembatalan.php?id_pembatalan=${id_pembatalan}`;
        }
      });
    });
  });
});
