document.addEventListener("DOMContentLoaded", function () {
  const uploadButtons = document.querySelectorAll(".upload-doc-btn");
  uploadButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const id_pendaftaran = this.getAttribute("data-id");
      document.getElementById("id_pendaftaran").value = id_pendaftaran;
    });
  });

  const deleteButtons = document.querySelectorAll(".delete-doc-btn");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const id_pendaftaran = this.getAttribute("data-id");

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
          window.location.href = `hapus_ePendaftaran.php?id_pendaftaran=${id_pendaftaran}`;
        }
      });
    });
  });
});
