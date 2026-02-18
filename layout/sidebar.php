<div id="wrapper">

    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="sidebar-brand-text mx-3">APLIKASI PESONA</div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item active">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <hr class="sidebar-divider">

        <?php if($_SESSION['role'] == 'super_admin' || $_SESSION['role'] == 'super admin'): ?>
        <div class="sidebar-heading">Superadmin</div>
        
        <li class="nav-item">
            <a class="nav-link" href="data_pengguna.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Kelola Pengguna</span></a>
        </li>
        <?php endif; ?>


        <?php 
        if($_SESSION['role'] == 'admin gudang' || $_SESSION['role'] == 'admin' || $_SESSION['role'] == 'super_admin' || $_SESSION['role'] == 'super admin'): 
        ?>
        <div class="sidebar-heading">Aset BMN</div>

        <li class="nav-item">
            <a class="nav-link" href="input_peminjaman_barang.php">
                <i class="fas fa-fw fa-file-alt"></i> <span>Input Peminjaman Barang</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="pengembalian_barang.php">
                <i class="fas fa-fw fa-undo"></i>
                <span>Pengembalian Barang</span></a>
        </li>
        
        <div class="sidebar-heading">Barang Habis Pakai</div>

        <li class="nav-item">
            <a class="nav-link" href="data_barang.php">
                <i class="fas fa-fw fa-dolly"></i> <span>Data Barang Bergerak</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="persetujuan.php">
                <i class="fas fa-fw fa-check-circle"></i>
                <span>Persetujuan (Pending)</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="riwayat_persetujuan.php">
                <i class="fas fa-fw fa-history"></i>
                <span>Riwayat Persetujuan</span></a>
        </li>

        <hr class="sidebar-divider">
        
        <div class="sidebar-heading">Laporan (Admin)</div>
        
        <li class="nav-item">
            <a class="nav-link" href="laporan.php">
                <i class="fas fa-fw fa-file-pdf"></i>
                <span>Laporan Transaksi</span></a>
        </li>
        <?php endif; ?>


        <?php 
        if($_SESSION['role'] == 'user' || $_SESSION['role'] == 'staff' || $_SESSION['role'] == 'super_admin' || $_SESSION['role'] == 'super admin'): 
        ?>
        
        <div class="sidebar-heading">Barang BMN</div>

        <li class="nav-item">
            <a class="nav-link" href="peminjaman_saya.php">
                <i class="fas fa-fw fa-cube"></i>
                <span>Peminjaman Saya</span></a>
        </li>
        
        <hr class="sidebar-divider my-2">

        <div class="sidebar-heading">Permintaan Barang Habis Pakai</div>
        
        <li class="nav-item">
            <a class="nav-link" href="daftar_barang.php">
                <i class="fas fa-fw fa-search"></i>
                <span>Daftar Barang Bergerak</span></a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="permintaan_saya.php">
                <i class="fas fa-fw fa-history"></i>
                <span>Riwayat Permintaan</span></a>
        </li>
        <?php endif; ?>


        <?php 
        if($_SESSION['role'] == 'pimpinan'): 
        ?>
        
        <div class="sidebar-heading">Data Inventaris</div>

        <li class="nav-item">
            <a class="nav-link" href="input_peminjaman_barang.php">
                <i class="fas fa-fw fa-building"></i>
                <span>Input Peminjaman Barang</span></a>
        </li>

        <hr class="sidebar-divider my-2">

        <div class="sidebar-heading">Laporan</div>
        
        <li class="nav-item">
            <a class="nav-link" href="laporan_stok.php">
                <i class="fas fa-fw fa-boxes"></i>
                <span>Laporan Stok Saat Ini</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="laporan.php">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Laporan Transaksi</span></a>
        </li>
        <?php endif; ?>

        <hr class="sidebar-divider d-none d-md-block">

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>