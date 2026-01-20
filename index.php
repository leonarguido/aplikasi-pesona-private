<?php
session_start();

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Panggil File Layout
require 'layout/header.php';
require 'layout/sidebar.php';

// =============================================================
// SET JUDUL KE TOPBAR (Agar muncul di bagian atas/kotak merah)
// =============================================================
$judul_halaman = "Dashboard";

require 'layout/topbar.php';
?>

<div class="container-fluid">

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
        </div>
    </div>

</div>

<?php
require 'layout/footer.php';
?>