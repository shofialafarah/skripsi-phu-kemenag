/** =============================================================================
 * Nama Aplikasi: Sistem Informasi Pelayanan Ibadah Haji Berbasis Web pada Kementerian Agama Kabupaten Banjar
 * Author: SHOFIA NABILA ELFA RAHMA - 2110010113
 * Copyright (c) 2025. All Rights Reserved.
 * Dibuat untuk keperluan Skripsi di Universitas Islam Kalimantan Muhammad Arsyad Al Banjari Banjarmasin
 * ==============================================================================
 */
$(document).ready(function () {
  console.log("pencarian_dataTable.js loaded");

  // Inisialisasi DataTable dengan fitur pencarian
  const table = $("#tabelStaf").DataTable({
    responsive: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });

  // Search input bawaan DataTable tetap aktif
});
