    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar"
        style="height:auto; min-height:100%;">

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

        <?php if ($_SESSION['role'] == 'super_admin' || $_SESSION['role'] == 'super admin'): ?>
            <div class="sidebar-heading">Admin Core</div>

            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>data_pengguna">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Kelola Pengguna</span></a>
            </li>
        <?php endif; ?>


        <?php if ($_SESSION['role'] == 'admin gudang' || $_SESSION['role'] == 'admin'): ?>
            <div class="sidebar-heading">Inventaris</div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataBarang"
                    aria-expanded="true" aria-controls="collapseDataBarang">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Data Barang</span>
                </a>
                <div id="collapseDataBarang" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilih Kategori:</h6>
                        <a class="collapse-item" href="<?= BASE_URL ?>data_barang">Barang Bergerak</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>data_barang_tg">Brg Tidak Bergerak</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>persetujuan">
                    <i class="fas fa-fw fa-check-circle"></i>
                    <span>Persetujuan (Pending)</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>riwayat_persetujuan">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Riwayat Persetujuan</span></a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Laporan</div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataLaporan"
                    aria-expanded="true" aria-controls="collapseDataLaporan">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseDataLaporan" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilih Kategori:</h6>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan_stok">Laporan Stok Barang</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan_stock_opname">Laporan Stock Opname</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan_permintaan">Laporan Permintaan</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan">Laporan Transaksi Berhasil</a>
                    </div>
                </div>
            </li>
        <?php endif; ?>


        <?php if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'staff'): ?>
            <div class="sidebar-heading">Permintaan</div>

            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>daftar_barang">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Daftar Barang</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>permintaan_saya">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Riwayat Permintaan</span></a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Aset Saya</div>

            <li class="nav-item">
                <a class="nav-link" href="<?= BASE_URL ?>aset_saya">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Aset Saya</span></a>
            </li>
        <?php endif; ?>


        <?php if ($_SESSION['role'] == 'pimpinan'): ?>
            <div class="sidebar-heading">Laporan</div>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataLaporan"
                    aria-expanded="true" aria-controls="collapseDataLaporan">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseDataLaporan" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilih Kategori:</h6>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan_stok">Laporan Stok Barang</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan_stock_opname">Laporan Stock Opname</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan_permintaan">Laporan Permintaan</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>laporan">Laporan Transaksi Berhasil</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataBarang"
                    aria-expanded="true" aria-controls="collapseDataBarang">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Log Barang</span>
                </a>
                <div id="collapseDataBarang" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Pilih Kategori:</h6>
                        <a class="collapse-item" href="<?= BASE_URL ?>log_barang">Log Barang Bergerak</a>
                        <a class="collapse-item" href="<?= BASE_URL ?>log_barang_tg">Log Brg Tidak Bergerak</a>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <hr class="sidebar-divider d-none d-md-block">
        <!-- 
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div> -->

    </ul>
    <!-- End of Sidebar -->