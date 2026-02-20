
        <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow">

            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            
            <div class="d-none d-md-block mr-auto ml-md-3 my-2 my-md-0 mw-100">
                <?php if(isset($judul_halaman)): ?>
                    <h1 class="h5 mb-0 text-gray-800 font-weight-bold"><?= $judul_halaman; ?></h1>
                    <?php if(isset($deskripsi_halaman)): ?>
                        <small class="text-muted" style="font-size: 0.75rem;"><?= $deskripsi_halaman; ?></small>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <ul class="navbar-nav ml-auto">

                <div class="topbar-divider d-none d-sm-block"></div>

                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?php 
                                // Cek variasi nama session (jaga-jaga jika pakai 'nama' atau 'full_name')
                                if(isset($_SESSION['nama'])) {
                                    echo $_SESSION['nama'];
                                } elseif(isset($_SESSION['full_name'])) {
                                    echo $_SESSION['full_name'];
                                } else {
                                    echo 'User';
                                }
                            ?> 
                            
                            (<?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Guest'; ?>)
                        </span>
                        <img class="img-profile rounded-circle" src="<?= ASSETS_URL ?>img/undraw_profile.svg">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="<?= BASE_URL ?>profil_saya">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>
        <!-- End of Topbar -->