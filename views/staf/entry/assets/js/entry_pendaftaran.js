document.addEventListener("DOMContentLoaded", function () {
  const flashSuccess = document.getElementById("flash-success")?.innerText;
  const flashError = document.getElementById("flash-error")?.innerText;

  if (flashSuccess) {
    Swal.fire({
      icon: "success",
      title: "Berhasil!",
      text: flashSuccess,
      timer: 3000,
      showConfirmButton: false,
    });
  }

  if (flashError) {
    Swal.fire({
      icon: "error",
      title: "Gagal!",
      text: flashError,
    });
  }

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
          window.location.href = `includes/hapus_ePendaftaran.php?id_pendaftaran=${id_pendaftaran}`;
        }
      });
    });
  });
});
