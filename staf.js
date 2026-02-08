// ====== DATE & TIME ======
function updateStafDateTime() {
  // Renamed to avoid conflict
  const now = new Date();
  const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
  const months = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];
  const day = days[now.getDay()];
  const date = now.getDate();
  const month = months[now.getMonth()];
  const year = now.getFullYear();
  const hh = String(now.getHours()).padStart(2, "0");
  const mm = String(now.getMinutes()).padStart(2, "0");
  const ss = String(now.getSeconds()).padStart(2, "0");

  // Check if elements exist before updating
  const dateElement = document.getElementById("currentDate");
  const timeElement = document.getElementById("currentTime");

  if (dateElement) {
    dateElement.textContent = `${day}, ${date} ${month} ${year}`;
  }
  if (timeElement) {
    timeElement.textContent = `${hh}:${mm}:${ss}`;
  }
}

// ====== DATE FILTER PARSER & EXT SEARCH ======
function parseDateTimeDMYHM(str) {
  if (!str) return null;
  // Format dari tabel adalah 'dd-mm-yyyy HH:MM'
  const parts = str.split(" ");
  if (parts.length !== 2) return null;

  const dateParts = parts[0].split("-");
  const timeParts = parts[1].split(":");

  if (dateParts.length !== 3 || timeParts.length !== 2) return null;

  return new Date(
    parseInt(dateParts[2]), // tahun
    parseInt(dateParts[1]) - 1, // bulan (0-11)
    parseInt(dateParts[0]), // hari
    parseInt(timeParts[0]), // jam
    parseInt(timeParts[1]) // menit
  );
}

$(document).ready(function () {
  console.log("staf.js loaded");

  // ====== SIDEBAR HANDLING ======
  const sidebar = document.querySelector(".sidebar");
  const layout = document.querySelector(".layout");

  const toggleOpen = () => {
    sidebar.classList.toggle("open");
    layout.classList.toggle("sidebar-open");

    if (!sidebar.classList.contains("open")) closeAllDropdowns();

    // After sidebar animation, adjust DataTables
    setTimeout(() => {
      const table = $("#tabelStaf").DataTable();
      table.columns.adjust().responsive.recalc();
    }, 300);
  };

  function closeAllDropdowns() {
    document
      .querySelectorAll(".submenu")
      .forEach((sm) => (sm.style.maxHeight = null));
    document
      .querySelectorAll(".dropdown-toggle")
      .forEach((btn) => btn.classList.remove("open"));
  }

  function toggleDropdown(submenuId, toggleBtn) {
    if (!sidebar.classList.contains("open")) return;
    const submenu = document.getElementById(submenuId);
    if (!submenu) return;

    // Close others
    document.querySelectorAll(".submenu").forEach((menu) => {
      if (menu.id !== submenuId) {
        menu.style.maxHeight = null;
        const btn = document.querySelector(`[onclick*="'${menu.id}'"]`);
        if (btn) btn.classList.remove("open");
      }
    });

    // Toggle current one
    if (submenu.style.maxHeight) {
      submenu.style.maxHeight = null;
      toggleBtn.classList.remove("open");
    } else {
      submenu.style.maxHeight = submenu.scrollHeight + "px";
      toggleBtn.classList.add("open");
    }
  }

  // ====== MENU ACTIVE TRACKING ======
  function setupMenuTracking() {
    const nav = document.querySelector(".sidebar nav");
    const menuButtons = document.querySelectorAll(".sidebar .menu > a");
    if (menuButtons.length) menuButtons[0].classList.add("active");

    menuButtons.forEach((btn, idx) => {
      if (!btn.classList.contains("dropdown-toggle")) {
        btn.addEventListener("click", () => {
          menuButtons.forEach((b) => b.classList.remove("active"));
          btn.classList.add("active");
          nav.style.setProperty("--top", `${idx * 56}px`);
        });
      }
    });

    const submenuBtns = document.querySelectorAll(".sidebar .submenu a");
    submenuBtns.forEach((sub) => {
      sub.addEventListener("click", () => {
        menuButtons.forEach((b) => b.classList.remove("active"));
        submenuBtns.forEach((s) => s.classList.remove("active"));
        sub.classList.add("active");

        const all = Array.from(
          document.querySelectorAll(".sidebar .menu > a, .sidebar .submenu a")
        );
        const index = all.indexOf(sub);
        nav.style.setProperty("--top", `${index * 56}px`);
      });
    });
  }

  // ====== INITIALIZE DATA TABLE ======
  const TGL_COL = 4; // kolom tanggal validasi

  $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
    if (settings.nTable.id !== "tabelStaf") return true;

    const startDate = $("#filter-start").val();
    const endDate = $("#filter-end").val();

    if (!startDate && !endDate) return true;

    const rowDate = parseDateTimeDMYHM(data[TGL_COL]);
    if (!rowDate) return false;

    const minDate = startDate ? new Date(startDate + "T00:00:00") : null;
    const maxDate = endDate ? new Date(endDate + "T23:59:59") : null;

    if (minDate && rowDate < minDate) return false;
    if (maxDate && rowDate > maxDate) return false;

    return true;
  });

  const table = $("#tabelStaf").DataTable({
    responsive: true,
    dom: `<"d-flex justify-content-between align-items-center mb-3"
          <"btn-export"B>
          <"#tanggal-filter-container">
          f
        >rtip`,
    columnDefs: [
      {
        targets: 5, // Ganti 5 dengan indeks kolom "Dokumen" kamu (mulai dari 0)
        visible: true,
        className: "text-center",
        responsivePriority: 1, // Berikan prioritas tinggi agar tidak disembunyikan
      },
    ],
    buttons: [
      {
        extend: "pdfHtml5",
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: "btn btn-danger btn-sm",
        filename: "Laporan_Pendaftaran_Haji",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
        customize: (doc) => {
          doc.styles.tableHeader = {
            fillColor: "#1b5e20",
            color: "white",
            alignment: "center",
            bold: true,
            fontSize: 12,
          };
          doc.styles.title = {
            fontSize: 16,
            bold: true,
            alignment: "center",
            color: "#1b5e20",
          };
          doc.defaultStyle.fontSize = 10;
        },
      },
      {
        extend: "excelHtml5",
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: "btn btn-success btn-sm",
        filename: "Laporan_Pendaftaran_Haji",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
      {
        extend: "print",
        text: '<i class="fas fa-print"></i> Print',
        className: "btn btn-secondary btn-sm",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] }
      },
    ],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/id.json",
    },
  });

  // Filter buttons
  $("#filter-btn").on("click", function () {
    table.draw();
    console.log("Filter button clicked");
  });

  $("#reset-btn").on("click", function () {
    $("#filter-start").val("");
    $("#filter-end").val("");
    table.draw();
    console.log("Reset button clicked");
  });

  // Sidebar & menu setup
  closeAllDropdowns();
  setupMenuTracking();

  // Date & time update every second - use renamed function
  updateStafDateTime();
  setInterval(updateStafDateTime, 1000);

  // Expose functions globally if needed
  window.toggleOpen = toggleOpen;
  window.toggleDropdown = toggleDropdown;
});
