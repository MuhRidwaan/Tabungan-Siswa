<?php
$user = function_exists('auth') ? auth()->user() : null;
$namaUser = $user ? ($user->username ?? 'User') : (session()->get('nama_lengkap') ?? 'Pengguna');
$role = 'admin';
if ($user && method_exists($user, 'inGroup') && $user->inGroup('guru')) {
    $role = 'guru';
} elseif (session()->get('role')) {
    $role = session()->get('role');
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= base_url('dashboard') ?>" class="brand-link">
        <img src=" <?= base_url('dist/img/AdminLTELogo.png') ?>" alt="Logo" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">Tabungan Siswa</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= base_url('dist/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= esc($namaUser) ?> (<?= ucfirst($role) ?>)</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt text-primary"></i>
                        <p>Dashboard Kelas</p>
                    </a>
                </li>

                <li class="nav-header">KELOLA TABUNGAN KELAS</li>
                
                <li class="nav-item">
                    <a href="<?= base_url('siswa') ?>" class="nav-link">
                        <i class="nav-icon fas fa-users text-info"></i>
                        <p>Data Siswa</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('transaksi/kolektif') ?>" class="nav-link">
                        <i class="nav-icon fas fa-layer-group text-success"></i>
                        <p>Setor / Tarik Kolektif</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('transaksi/multi-tanggal') ?>" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt text-teal"></i>
                        <p>Setor Multi-Tanggal</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('transaksi') ?>" class="nav-link">
                        <i class="nav-icon fas fa-history text-warning"></i>
                        <p>Riwayat Transaksi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('laporan') ?>" class="nav-link">
                        <i class="nav-icon fas fa-print text-danger"></i>
                        <p>Laporan & Cetak</p>
                    </a>
                </li>

                <li class="nav-header">AKUN</li>
                <li class="nav-item">
                    <a href="<?= base_url('logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-muted"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>