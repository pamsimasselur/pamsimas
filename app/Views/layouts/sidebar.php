<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/" class="logo-link">
            <h3>WLC Pamsimas</h3>
        </a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-header">MENU UTAMA</li>
            <li class="nav-item">
                <a href="/">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-header">ANALISIS & LOG</li>
            <li class="nav-item"> 
                <a href="/logs/sensors" class="<?= isActive('/logs/sensors') ?>">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span>Log Sensor</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/logs/pumps" class="<?= isActive('/logs/pumps') ?>">
                    <i class="fas fa-history nav-icon"></i>
                    <span>Riwayat Pompa</span>
                </a>
            </li>

            <li class="nav-header">PENGATURAN</li>
            <li class="nav-item">
                <a href="/detect">
                    <i class="fas fa-search nav-icon"></i>
                    <span>Deteksi Perangkat</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/controllers">
                    <i class="fas fa-cogs nav-icon"></i>
                    <span>Pengaturan Perangkat</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/settings/tanks">
                    <i class="fas fa-database nav-icon"></i>
                    <span>Data Tangki</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/settings/pumps">
                    <i class="fas fa-water nav-icon"></i>
                    <span>Data Pompa</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/settings/sensors">
                    <i class="fas fa-microchip nav-icon"></i>
                    <span>Data Sensor</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/settings/display" class="<?= isActive('/settings/display') ?>">
                    <i class="fas fa-palette nav-icon"></i>
                    <span>Pengaturan Tampilan</span>
                </a>
            </li>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Administrator'): ?>
            <li class="nav-header">ADMINISTRASI</li>
            <li class="nav-item">
                <a href="/users">
                    <i class="fas fa-users-cog nav-icon"></i>
                    <span>Manajemen Pengguna</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-header">AKUN</li>
            <li class="nav-item">
                <a href="/logout">
                    <i class="fas fa-sign-out-alt nav-icon"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>