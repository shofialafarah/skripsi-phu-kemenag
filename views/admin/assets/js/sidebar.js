const sidebar = document.querySelector(".sidebar");
const layout = document.querySelector(".layout"); // Tambahkan ini

// Toggle sidebar open/closed
const toggleOpen = () => {
  sidebar.classList.toggle("open");
  layout.classList.toggle("sidebar-open"); // Tambahkan ini

  // If closing sidebar, close all dropdowns
  if (!sidebar.classList.contains("open")) {
    closeAllDropdowns();
  }
};

// Close all dropdown menus
function closeAllDropdowns() {
  document.querySelectorAll('.submenu').forEach(submenu => {
    submenu.style.maxHeight = null;
  });

  document.querySelectorAll('.dropdown-toggle').forEach(btn => {
    btn.classList.remove('open');
  });
}

// Untuk tracking item menu aktif
const nav = document.querySelector(".sidebar nav");
const menuButtons = document.querySelectorAll(".sidebar .menu > a");



// Handle klik pada menu utama
menuButtons.forEach((button, index) => {
  if (!button.classList.contains("dropdown-toggle")) {
    button.addEventListener("click", () => {
      // Set state aktif
      menuButtons.forEach(b => b.classList.remove("active"));
      button.classList.add("active");

      // Update posisi indikator aktif
      nav.style.setProperty("--top", `${index * 56}px`);
    });
  }
});

// Handle dropdown toggle
function toggleDropdown(submenuId, toggleBtn) {
  // Kalau sidebar belum dibuka, jangan lakukan apa-apa
  if (!sidebar.classList.contains("open")) return;

  const submenu = document.getElementById(submenuId);
  if (!submenu) return;

  // Tutup semua submenu lain
  document.querySelectorAll('.submenu').forEach(menu => {
    if (menu.id !== submenuId) {
      menu.style.maxHeight = null;
      const btn = document.querySelector(`[onclick*="'${menu.id}'"]`);
      if (btn) btn.classList.remove('open');
    }
  });

  // Toggle submenu yang dimaksud
  if (submenu.style.maxHeight) {
    submenu.style.maxHeight = null;
    toggleBtn.classList.remove('open');
  } else {
    submenu.style.maxHeight = submenu.scrollHeight + 'px';
    toggleBtn.classList.add('open');
  }
}

// Inisialisasi - pastikan semua submenu tertutup saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
  closeAllDropdowns();
});

const submenuButtons = document.querySelectorAll(".sidebar .submenu a");

submenuButtons.forEach(subBtn => {
  subBtn.addEventListener("click", () => {
    menuButtons.forEach(b => b.classList.remove("active"));
    submenuButtons.forEach(sb => sb.classList.remove("active"));

    subBtn.classList.add("active");

    // Hitung posisi relatif terhadap semua tombol
    const allMenuItems = Array.from(document.querySelectorAll(".sidebar .menu > a, .sidebar .submenu a"));
    const index = allMenuItems.indexOf(subBtn);
    nav.style.setProperty("--top", `${index * 56}px`);
  });
});
