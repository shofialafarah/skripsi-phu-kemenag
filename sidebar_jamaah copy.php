<aside class="sidebar">
    <button class="toggle" type="button" onclick="toggleOpen()">
        <span class="material-symbols-outlined">chevron_right</span>
    </button>
    <div class="inner">
        <div class="header">
            <img src="logo_kemenag.png" class="logo" />
            <h1>PHU KEMENAG</h1>
        </div>
        <div class="search">
            <span class="material-symbols-outlined">search</span>
            <input type="text" placeholder="Search" />
        </div>

        <!-- Kontainer isi utama sidebar (menu + tombol logout) -->
        <div class="content-wrapper">
            <nav class="menu">
                <a href="dashboard_coba.php" type="button">
                    <span class="material-symbols-outlined">dashboard</span>
                    <p>Dashboard</p>
                </a>
                <a type="button" class="dropdown-toggle" onclick="toggleDropdown('monitoringSubmenu', this)">
                    <span class="material-symbols-outlined">monitoring</span>
                    <p>Monitoring</p>
                    <span class="material-symbols-outlined arrow">expand_more</span>
                </a>
                <div class="submenu" id="monitoringSubmenu">
                    <a href="monitoring_pendaftaran.php" type="button">Pendaftaran</a>
                    <a href="monitoring_pembatalan.php" type="button">Pembatalan</a>
                    <a href="monitoring_pelimpahan.php" type="button">Pelimpahan</a>
                </div>
                <a type="button" class="dropdown-toggle" onclick="toggleDropdown('entrySubmenu', this)">
                    <span class="material-symbols-outlined">analytics</span>
                    <p>Entry</p>
                    <span class="material-symbols-outlined arrow">expand_more</span>
                </a>
                <div class="submenu" id="entrySubmenu">
                    <a href="entry_pendaftaran.php" type="button">Form A</a>
                    <a href="entry_pembatalan.php" type="button">Form B</a>
                </div>
                <a href="pembatalan_haji.php" type="button">
                    <span class="material-symbols-outlined">message</span>
                    <p>Pembatalan Haji</p>
                </a>
                <a href="pelimpahan_haji.php" type="button">
                    <span class="material-symbols-outlined">settings</span>
                    <p>Pelimpahan Haji</p>
                </a>
            </nav>

            <!-- Tombol keluar -->
            <button class="logout-btn" onclick="window.location.href='login.php'">
                <span class="material-symbols-outlined">logout</span>
            </button>
        </div>
    </div>
</aside>

<script src="main.js"></script>
