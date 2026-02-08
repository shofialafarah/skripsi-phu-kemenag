// ====== DATE & TIME ======
function updateStafDateTime() {
  const now = new Date();
  const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
  const months = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
  ];
  const day = days[now.getDay()];
  const date = now.getDate();
  const month = months[now.getMonth()];
  const year = now.getFullYear();
  const hh = String(now.getHours()).padStart(2, "0");
  const mm = String(now.getMinutes()).padStart(2, "0");
  const ss = String(now.getSeconds()).padStart(2, "0");

  const dateElement = document.getElementById("currentDate");
  const timeElement = document.getElementById("currentTime");

  if (dateElement) {
    dateElement.textContent = `${day}, ${date} ${month} ${year}`;
  }
  if (timeElement) {
    timeElement.textContent = `${hh}:${mm}:${ss}`;
  }
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

    setTimeout(() => {
      const table = $("#tabelStaf").DataTable();
      table.columns.adjust().responsive.recalc();
    }, 300);
  };

  function closeAllDropdowns() {
    document.querySelectorAll(".submenu").forEach((sm) => sm.style.maxHeight = null);
    document.querySelectorAll(".dropdown-toggle").forEach((btn) => btn.classList.remove("open"));
  }

  function toggleDropdown(submenuId, toggleBtn) {
    if (!sidebar.classList.contains("open")) return;
    const submenu = document.getElementById(submenuId);
    if (!submenu) return;

    document.querySelectorAll(".submenu").forEach((menu) => {
      if (menu.id !== submenuId) {
        menu.style.maxHeight = null;
        const btn = document.querySelector(`[onclick*="'${menu.id}'"]`);
        if (btn) btn.classList.remove("open");
      }
    });

    if (submenu.style.maxHeight) {
      submenu.style.maxHeight = null;
      toggleBtn.classList.remove("open");
    } else {
      submenu.style.maxHeight = submenu.scrollHeight + "px";
      toggleBtn.classList.add("open");
    }
  }

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

        const all = Array.from(document.querySelectorAll(".sidebar .menu > a, .sidebar .submenu a"));
        const index = all.indexOf(sub);
        nav.style.setProperty("--top", `${index * 56}px`);
      });
    });
  }

  // ====== INIT DATATABLE ======
  const table = $("#tabelStaf").DataTable({
    responsive: true,
    language: {
      url: 'assets/lang/id.json'
    }
  });

  // ====== INIT FUNCTIONS ======
  closeAllDropdowns();
  setupMenuTracking();

  updateStafDateTime();
  setInterval(updateStafDateTime, 1000);

  // Expose globally
  window.toggleOpen = toggleOpen;
  window.toggleDropdown = toggleDropdown;
});
