<?php include 'header_setup.php'; ?>
    <div class="header-wrapper">
        <header>
            <div class="header">
                <div class="header-welcome">
                    <h1>Selamat Datang, <?= htmlspecialchars($jamaah['nama'] ?? 'Jamaah') ?></h1>
                    <div class="date-time-section">
                        <div class="current-date" id="currentDate">Kamis, 1 Mei 2025</div>
                        <div class="current-time" id="currentTime">07:30</div>
                    </div>
                </div>
                <div class="header-actions">

                    <div class="dropdown">
                        <button class="header-icon position-relative" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-outlined">mail</span>
                            <?php if ($jumlah_notif_mail > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success badge-notif">
                                    <?= $jumlah_notif_mail ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end scrollable-dropdown">
                            <?php if ($jumlah_notif_mail > 0): ?>
                                <?php foreach ($notif_mail as $notif): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= htmlspecialchars($notif['link']) ?>" target="_blank">
                                            <?= $notif['pesan'] ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Tidak ada pesan baru</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="header-icon position-relative" data-bs-toggle="dropdown">
                            <span class="material-symbols-outlined">notifications</span>
                            <?php if ($jumlah_notif_bell > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger badge-notif">
                                    <?= $jumlah_notif_bell ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end scrollable-dropdown">
                            <?php if ($jumlah_notif_bell > 0): ?>
                                <?php foreach ($notif_bell as $notif): ?>
                                    <li><span class="dropdown-item"><?= $notif ?></span></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Tidak ada notifikasi baru</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </header>
    </div>

    <script src="/phu-kemenag-banjar-copy/views/jamaah/assets/js/waktu_header.js"></script>