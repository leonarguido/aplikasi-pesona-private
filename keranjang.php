<?php
session_start();
require 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Inisialisasi
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// 1. HAPUS ITEM DARI KERANJANG
if (isset($_GET['hapus'])) {
    $key = $_GET['hapus'];
    unset($_SESSION['keranjang'][$key]);
    // Reset urutan array agar rapi
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
    echo "<script>window.location='keranjang.php';</script>";
}

// 2. PROSES CHECKOUT (AJUKAN PERMINTAAN)
if (isset($_POST['checkout'])) {
    $user_id   = $_SESSION['user_id'];
    $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
    $tanggal   = date('Y-m-d');
    
    // Validasi Keranjang Kosong
    if (empty($_SESSION['keranjang'])) {
        echo "<script>alert('Keranjang kosong!'); window.location='daftar_barang.php';</script>";
        exit;
    }

    // A. INSERT HEADER (tb_permintaan) - HANYA SEKALI!
    $query_header = "INSERT INTO tb_permintaan (user_id, tanggal_permintaan, status, keperluan) 
                     VALUES ('$user_id', '$tanggal', 'menunggu', '$keperluan')";
    
    if (mysqli_query($koneksi, $query_header)) {
        // Ambil ID Permintaan yang baru saja dibuat
        $id_permintaan_baru = mysqli_insert_id($koneksi);

        // B. INSERT DETAIL (Looping Keranjang)
        $berhasil_detail = true;
        foreach ($_SESSION['keranjang'] as $item) {
            $id_barang = $item['id'];
            $jumlah    = $item['jumlah'];
            $satuan    = $item['satuan'];
            
            // Masukkan ke tb_detail_permintaan dengan ID Header yang SAMA
            $q_detail = "INSERT INTO tb_detail_permintaan (permintaan_id, barang_id, jumlah, satuan) 
                         VALUES ('$id_permintaan_baru', '$id_barang', '$jumlah', '$satuan')";
            
            if (!mysqli_query($koneksi, $q_detail)) {
                $berhasil_detail = false;
            }
        }

        if ($berhasil_detail) {
            // Kosongkan Keranjang
            unset($_SESSION['keranjang']);
            echo "<script>alert('Permintaan berhasil diajukan! Satu surat untuk semua barang.'); window.location='permintaan_saya.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan detail barang.');</script>";
        }

    } else {
        echo "<script>alert('Gagal membuat permintaan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';
require 'layout/topbar.php'; 
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Keranjang Permintaan</h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Item yang dipilih</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($_SESSION['keranjang'])): ?>
                                    <tr><td colspan="5" class="text-center">Keranjang masih kosong. Silakan pilih barang di katalog.</td></tr>
                                <?php else: ?>
                                    <?php foreach($_SESSION['keranjang'] as $key => $item): ?>
                                    <tr>
                                        <td><?= $key + 1; ?></td>
                                        <td><?= $item['nama']; ?></td>
                                        <td><?= $item['jumlah']; ?></td>
                                        <td><?= $item['satuan']; ?></td>
                                        <td>
                                            <a href="keranjang.php?hapus=<?= $key; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus item ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="daftar_barang.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Barang</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Konfirmasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold">Keperluan / Alasan</label>
                            <textarea name="keperluan" class="form-control" rows="4" placeholder="Contoh: Kebutuhan ATK Bulanan Divisi IT..." required></textarea>
                            <small class="text-muted">Keperluan ini berlaku untuk semua barang di keranjang.</small>
                        </div>
                        
                        <hr>
                        
                        <?php if(!empty($_SESSION['keranjang'])): ?>
                            <button type="submit" name="checkout" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-block" disabled>Keranjang Kosong</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>