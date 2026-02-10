// Script untuk tombol cetak - PERBAIKAN UTAMA
document
  .getElementById("print-report-btn")
  .addEventListener("click", function (e) {
    e.preventDefault(); // Mencegah form submit atau behavior default

    const startDate = document.getElementById("filter-start")
      ? document.getElementById("filter-start").value
      : "";
    const endDate = document.getElementById("filter-end")
      ? document.getElementById("filter-end").value
      : "";

    let url = "cetak/cetak_laporan_pengguna.php";

    // Periksa kedua tanggal; jika tidak lengkap, tunjukkan SweetAlert2
    if (!startDate || !endDate) {
      Swal.fire({
        icon: "warning",
        title: "Tanggal belum lengkap",
        text: "Silakan pilih tanggal mulai dan tanggal akhir terlebih dahulu sebelum mencetak laporan.",
      });
      return;
    }

    // Jika kedua tanggal filter diisi, tambahkan sebagai parameter URL
    url += `?start_date=${startDate}&end_date=${endDate}`;

    console.log("Opening URL:", url); // Debug log

    // Buka halaman print di tab baru
    window.open(url, "_blank");
  });

// Script AJAX untuk modal edit
$(document).ready(function () {
  // Reset isi dropdown saat modal dibuka
  $("#editUserModal").on("show.bs.modal", function () {
    $("#roleSelect").val("");
    $("#usernameSelect").html(
      '<option value="" disabled selected>-- Pilih Username --</option>',
    );
    $("#statusSelect").val("");
  });

  // Ambil username berdasarkan role
  $("#roleSelect").change(function () {
    const role = $(this).val();
    if (role !== "") {
      $.ajax({
        url: "/phu-kemenag-banjar-copy/views/admin/laporan/proses/ambil_username_by_role.php",
        method: "POST",
        data: {
          role: role,
        },
        success: function (response) {
          $("#usernameSelect").html(response);
          console.log("Usernames loaded:", response);
        },
        error: function (xhr, status, error) {
          console.log("Error loading usernames:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Gagal memuat daftar username. Silakan coba lagi.",
          });
        },
      });
    } else {
      $("#usernameSelect").html(
        '<option value="" disabled selected>-- Pilih Username --</option>',
      );
    }
  });

  // Tombol edit per baris
  $(document).on("click", ".btn-edit", function () {
    const role = $(this).data("role");
    const id = $(this).data("id");

    $("#editUserModal").modal("show");
    $("#roleSelect").val(role.toLowerCase()).trigger("change");

    setTimeout(() => {
      $("#usernameSelect").val(id);
    }, 500);
  });

  // Submit form edit
  $("#formEditUser").submit(function (e) {
    e.preventDefault();

    // Validasi form
    const role = $("#roleSelect").val();
    const idPengguna = $("#usernameSelect").val();
    const status = $("#statusSelect").val();

    if (!role || !idPengguna || !status) {
      Swal.fire({
        icon: "warning",
        title: "Form tidak lengkap",
        text: "Silakan isi semua field sebelum menyimpan.",
      });
      return;
    }

    $.ajax({
      url: "/phu-kemenag-banjar-copy/views/admin/laporan/proses/edit_status_pengguna.php",
      method: "POST",
      data: $(this).serialize(),
      success: function (response) {
        $("#editUserModal").modal("hide");

        Swal.fire({
          icon: response.trim() === "sukses" ? "success" : "error",
          title: response.trim() === "sukses" ? "Berhasil" : "Gagal",
          text:
            response.trim() === "sukses"
              ? "Status berhasil diperbarui."
              : "Gagal memperbarui status.",
          position: "top",
          timer: 2000,
          showConfirmButton: false,
        }).then(() => {
          if (response.trim() === "sukses") {
            location.reload();
          }
        });
      },
      error: function (xhr, status, error) {
        console.log("Error updating user status:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Gagal menyimpan perubahan. Silakan coba lagi.",
        });
      },
    });
  });
});
