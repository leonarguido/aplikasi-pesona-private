<?php
session_start();
<<<<<<< HEAD
require 'config/koneksi.php';

// 1. Cek Login
=======
// Cek Login
>>>>>>> 01cdf0ab3db84c2185f41d76f0a621ae8a07aef3
if (!isset($_SESSION['user_id'])) {
    header("Location: /aplikasi-pesona-private/routes/web.php?method=login");
    exit;
}
<<<<<<< HEAD

require 'layout/header.php';
require 'layout/sidebar.php';

// =============================================================
// SET JUDUL HALAMAN
// =============================================================
$judul_halaman = "Dashboard";
require 'layout/topbar.php'; 

// Ambil Role & ID User
$role    = $_SESSION['role'];
$id_user = $_SESSION['user_id'];
=======
>>>>>>> 01cdf0ab3db84c2185f41d76f0a621ae8a07aef3
?>

<!DOCTYPE html>
<html lang="en">

<<<<<<< HEAD
    <?php if ($role == 'admin' || $role == 'super_admin'): ?>

        <?php
        // --- LOGIKA HITUNG DATA GUDANG (HANYA DIJALANKAN JIKA ADMIN) ---
        
        // 1. Total Jenis Barang
        $q_barang = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_barang_bergerak");
        $total_barang = mysqli_fetch_assoc($q_barang)['total'];

        // 2. Permintaan Menunggu (Global)
        $q_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan WHERE status='menunggu'");
        $total_pending = mysqli_fetch_assoc($q_pending)['total'];

        // 3. Disetujui Bulan Ini (Global)
        $bulan_ini = date('m');
        $q_acc = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan WHERE status='disetujui' AND MONTH(tanggal_disetujui) = '$bulan_ini'");
        $total_acc = mysqli_fetch_assoc($q_acc)['total'];

        // 4. Stok Menipis
        $ambang_batas = 10;
        $q_tipis = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_barang_bergerak WHERE stok <= '$ambang_batas'");
        $total_tipis = mysqli_fetch_assoc($q_tipis)['total'];
        ?>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jenis Barang</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_barang; ?> Item</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-boxes fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Permintaan Menunggu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pending; ?> Ajuan</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                        </div>
                        <?php if($total_pending > 0): ?>
                            <a href="persetujuan.php" class="btn btn-sm btn-warning mt-2 shadow-sm btn-block">Proses Sekarang <i class="fas fa-arrow-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Disetujui (Bulan Ini)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_acc; ?> Transaksi</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Menipis (≤ <?= $ambang_batas; ?>)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_tipis; ?> Barang</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
=======
<head>
    <?php require 'views/layout/header.php'; ?>
</head>

<body id="page-top">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="row">
            <div class="col-md-2">
                <?php require 'views/layout/sidebar.php'; ?>
            </div>
            <div class="col-md-10">
                <?php
                require 'views/layout/topbar.php';
                $judul_halaman = "Dashboard";
                ?>
                <div class="container-fluid mt-4">

                    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Selamat Datang, <?= $_SESSION['full_name']; ?></h6>
                                </div>
                                <div class="card-body">
                                    <p>Anda login sebagai: <strong><?= ucfirst($_SESSION['role']); ?></strong></p>
                                    <hr>
                                    <p class="mb-0">Silakan pilih menu di samping untuk memulai.</p>
                                </div>
                            </div>
>>>>>>> 01cdf0ab3db84c2185f41d76f0a621ae8a07aef3
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-bottom-danger">
                        <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-exclamation-triangle"></i> Peringatan Stok Menipis</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Satuan</th>
                                        <th class="text-center">Sisa Stok</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q_low = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE stok <= '$ambang_batas' ORDER BY stok ASC LIMIT 5");
                                    if(mysqli_num_rows($q_low) > 0):
                                        while($low = mysqli_fetch_assoc($q_low)):
                                    ?>
                                    <tr>
                                        <td><?= $low['nama_barang']; ?></td>
                                        <td><?= $low['satuan']; ?></td>
                                        <td class="text-center text-danger font-weight-bold"><?= $low['stok']; ?></td>
                                        <td class="text-center">
                                            <a href="data_barang.php" class="btn btn-primary btn-sm btn-circle" title="Tambah Stok"><i class="fas fa-plus"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-success">Stok aman.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 border-bottom-primary">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history"></i> Permintaan Terbaru</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pemohon</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q_recent = mysqli_query($koneksi, "SELECT p.*, u.nama FROM tb_permintaan p JOIN tb_user u ON p.user_id = u.id ORDER BY p.id DESC LIMIT 5");
                                    while($rec = mysqli_fetch_assoc($q_recent)):
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($rec['tanggal_permintaan'])); ?></td>
                                        <td><?= $rec['nama']; ?></td>
                                        <td class="text-center">
                                            <?php if($rec['status'] == 'menunggu'): ?>
                                                <span class="badge badge-warning">Menunggu</span>
                                            <?php elseif($rec['status'] == 'disetujui'): ?>
                                                <span class="badge badge-success">Disetujui</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <?php else: ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4 border-left-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Selamat Datang, <?= $_SESSION['full_name']; ?>!</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="font-weight-bold mb-3">Sistem Informasi Logistik & Permintaan Barang</h4>
                                <p>Anda login sebagai <strong><?= ucfirst($role); ?></strong>. Gunakan menu di samping untuk:</p>
                                <ul>
                                    <li>Melihat daftar barang yang tersedia di gudang.</li>
                                    <li>Mengajukan permintaan barang baru.</li>
                                    <li>Memantau status persetujuan permintaan Anda.</li>
                                </ul>
                                <a href="daftar_barang.php" class="btn btn-primary shadow-sm mt-3">
                                    <i class="fas fa-search"></i> Lihat Barang & Ajukan Permintaan
                                </a>
                            </div>
                            <div class="col-md-4 text-center d-none d-md-block">
                                <img src="assets/img/undraw_posting_photo.svg" alt="Welcome" class="img-fluid" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            // Hitung permintaan user sendiri
            $q_my_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan WHERE user_id='$id_user' AND status='menunggu'");
            $my_pending = mysqli_fetch_assoc($q_my_pending)['total'];

            $q_my_acc = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_permintaan WHERE user_id='$id_user' AND status='disetujui'");
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

    <?php endif; ?>
    </div>

<<<<<<< HEAD
<?php require 'layout/footer.php'; ?>
=======
    <?php require 'views/layout/footer.php'; ?>

    <script>
        if (window.innerHeight <= 700) {
            document.getElementById('accordionSidebar')
                .style.height = '100vh';
        }
    </script>
</body>

</html>
>>>>>>> 01cdf0ab3db84c2185f41d76f0a621ae8a07aef3
