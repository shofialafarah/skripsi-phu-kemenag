/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
function previewGambar(input) {
  const file = input.files[0];
  if (file) {
    const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
    const maxSize = 10 * 1024 * 1024; // 10MB

    // Validasi tipe file
    if (!allowedTypes.includes(file.type)) {
      alert("Format file harus JPG atau PNG!");
      input.value = "";
      return;
    }

    // Validasi ukuran file
    if (file.size > maxSize) {
      alert("Ukuran gambar maksimal 10MB!");
      input.value = "";
      return;
    }

    // Preview gambar
    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById("previewFoto").src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function hapusFoto() {
  Swal.fire({
    title: "Hapus Foto Profil?",
    text: "Foto akan dikembalikan ke setelan default!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6e7d88",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      const params = new URLSearchParams(window.location.search);
      const id = params.get("id");

      // Ambil nama file saat ini (edit_staf.php atau tambah_staf.php)
      const currentPage = window.location.pathname.split("/").pop();

      if (id && currentPage === "edit_staf.php") {
        // Jika di halaman EDIT: Kirim perintah hapus ke database
        window.location.href = `edit_staf.php?id=${id}&remove_foto=true`;
      } else {
        // Jika di halaman TAMBAH: Cukup reset tampilan (karena belum simpan ke DB)
        // Pastikan ID 'previewFoto' sesuai dengan tag <img> kamu
        const preview = document.getElementById("previewFoto");
        if (preview)
          preview.src =
            "/phu-kemenag-banjar-copy/views/admin/akun-pengguna/staf/assets/img/profil.jpg";

        document.getElementById("foto").value = "";

        Swal.fire(
          "Terhapus!",
          "Foto dipilih telah dihapus dari form.",
          "success",
        );
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  var params = new URLSearchParams(window.location.search);
  if (params.get("foto_removed") === "1") {
    Swal.fire("Terhapus", "Foto profil telah dihapus.", "success");
    if (history.replaceState) {
      var url = window.location.href.replace(/\?foto_removed=1/, "");
      history.replaceState(null, "", url);
    }
  }
});

// =================================================================================
document.addEventListener("DOMContentLoaded", function () {
  // Intercept delete links
  document.querySelectorAll("a.delete-link").forEach(function (el) {
    el.addEventListener("click", function (e) {
      e.preventDefault();
      var href = this.getAttribute("href");
      var name = this.getAttribute("data-name") || "data ini";
      Swal.fire({
        title: "Hapus?",
        text: "Yakin ingin menghapus " + name + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = href;
        }
      });
    });
  });

  // Show success/error messages when redirected after actions
  var params = new URLSearchParams(window.location.search);
  if (params.get("deleted") === "1") {
    Swal.fire("Terhapus", "Data berhasil dihapus.", "success");
  } else if (params.get("deleted") === "0") {
    Swal.fire("Gagal", "Gagal menghapus data.", "error");
  }

  if (params.get("created") === "1") {
    if (params.get("mail") === "0") {
      Swal.fire(
        "Berhasil",
        "Akun berhasil dibuat, tetapi email gagal dikirim.",
        "warning",
      );
    } else {
      Swal.fire(
        "Berhasil",
        "Akun berhasil dibuat dan email dikirim.",
        "success",
      );
    }
  } else if (params.get("created") === "0") {
    Swal.fire("Gagal", "Gagal menambahkan data staf.", "error");
  }

  if (params.get("updated") === "1") {
    Swal.fire("Berhasil", "Data staf berhasil diperbarui.", "success");
  } else if (params.get("updated") === "0") {
    Swal.fire("Gagal", "Gagal memperbarui data staf.", "error");
  }

  // remove known query params without reload
  if (history.replaceState) {
    var url = window.location.href.replace(
      /\?(deleted|created|mail|updated)=[^&]*(&)?/g,
      "",
    );
    url = url.replace(/&$/, "");
    history.replaceState(null, "", url);
  }
});
