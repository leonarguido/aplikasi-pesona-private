$admin_aset_bmn =
$admin_habis_pakai =


<div class="row">
                                <div class="col-lg-12">
                                    <div class="card shadow mb-4 border-left-primary">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Selamat Datang, <?= $_SESSION['full_name']; ?>!</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h4 class="font-weight-bold mb-3">(PESONA) Penata Usahaan Stok Opname</h4>
                                                    <p>Anda login sebagai <strong>Admin Barang Habis Pakai</strong>. Gunakan menu di samping untuk:</p>
                                                    <ul>
                                                        <li>Memantau stok data barang yang tersedia di gudang.</li>
                                                        <li>Menyetujui permintaan barang baru.</li>
                                                        <li>Melihat laporan terkini terkait stok barang.</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4 text-center d-none d-md-block">
                                                    <img src="<?= ASSETS_URL ?>img/undraw_posting_photo.svg" alt="Welcome" class="img-fluid" style="max-height: 200px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            // Hitung permintaan user sendiri
                            $q_my_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan WHERE status='menunggu' AND deleted_at IS NULL");
                            $my_pending = mysqli_fetch_assoc($q_my_pending)['total'];

                            $q_my_acc = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan WHERE status='disetujui' AND deleted_at IS NULL");
                            $my_acc = mysqli_fetch_assoc($q_my_acc)['total'];
                            ?>

                            <div class="row">

                                <div class="col-xl-6 col-md-6 mb-4">
                                    <div class="card border-left-warning shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Permintaan Menunggu</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $my_pending; ?> Ajuan</div>
                                                </div>
                                                <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                                                <?php if ($my_pending > 0): ?>
                                                    <a href="<?= BASE_URL ?>persetujuan" class="btn btn-sm btn-warning mt-2 shadow-sm btn-block">Proses Sekarang <i class="fas fa-arrow-right"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Permintaan Disetujui (Total)</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $my_acc; ?> Ajuan</div>
                                                </div>
                                                <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>