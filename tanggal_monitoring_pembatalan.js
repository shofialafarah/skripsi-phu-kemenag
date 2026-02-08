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

  const parts = str.trim().split(" ");
  const dateParts = parts[0].split("-");

  if (dateParts.length !== 3) return null;

  // Default time = 00:00
  let hours = 0, minutes = 0;
  if (parts.length === 2) {
    const timeParts = parts[1].split(":");
    if (timeParts.length === 2) {
      hours = parseInt(timeParts[0]);
      minutes = parseInt(timeParts[1]);
    }
  }

  const d = new Date(
    parseInt(dateParts[2]),
    parseInt(dateParts[1]) - 1,
    parseInt(dateParts[0]),
    hours,
    minutes
  );

  return isNaN(d.getTime()) ? null : d;
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
  const TGL_COL = 3; // kolom tanggal validasi

  $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
  if (settings.nTable.id !== "tabelStaf") return true;

  const startDate = $("#filter-start").val();
  const endDate = $("#filter-end").val();

  const rowDate = parseDateTimeDMYHM(data[TGL_COL]);

  console.log("Row date string:", data[TGL_COL]);
  console.log("Parsed row date:", rowDate);

  if (!startDate && !endDate) return true;
  if (!rowDate) return false;

  const minDate = startDate ? new Date(startDate + "T00:00:00") : null;
  const maxDate = endDate ? new Date(endDate + "T23:59:59") : null;

  if (minDate && rowDate < minDate) return false;
  if (maxDate && rowDate > maxDate) return false;

  return true;
});

  const table = $("#tabelStaf").DataTable({
    responsive: true,
    language: {
        url: 'assets/lang/id.json' // path lokal, pastikan file ada
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
