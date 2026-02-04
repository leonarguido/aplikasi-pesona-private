<?php
session_start();
require 'config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Inisialisasi Keranjang
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// ==========================================
// LOGIKA 1: HAPUS ITEM (Dari Session)
// ==========================================
if (isset($_GET['hapus'])) {
    $index = $_GET['hapus']; // Ambil index array
    unset($_SESSION['keranjang'][$index]); // Hapus item
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']); // Urutkan ulang index (re-index)
    echo "<script>alert('Item dihapus dari keranjang.'); window.location='keranjang.php';</script>";
}

// ==========================================
// LOGIKA 2: UPDATE JUMLAH (Edit Cart)
// ==========================================
if (isset($_POST['update_cart'])) {
    $index    = $_POST['index_array'];
    $jml_baru = $_POST['jumlah_baru'];
    $stok_max = $_POST['stok_max'];

    // Validasi Stok
    if ($jml_baru > $stok_max) {
        echo "<script>alert('Gagal! Jumlah melebihi stok tersedia ($stok_max).'); window.location='keranjang.php';</script>";
    } elseif ($jml_baru < 1) {
        echo "<script>alert('Jumlah minimal 1!'); window.location='keranjang.php';</script>";
    } else {
        // Update Session
        $_SESSION['keranjang'][$index]['jumlah'] = $jml_baru;
        echo "<script>alert('Jumlah berhasil diperbarui!'); window.location='keranjang.php';</script>";
    }
}

// ==========================================
// LOGIKA 3: PROSES CHECKOUT (Simpan ke DB)
// ==========================================
if (isset($_POST['ajukan'])) {
    $user_id = $_SESSION['user_id'];
    $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
    $tanggal = date('Y-m-d');

    if (count($_SESSION['keranjang']) == 0) {
        echo "<script>alert('Keranjang kosong!'); window.location='daftar_barang.php';</script>";
    } else {
        // 1. Simpan ke tb_permintaan
        $q_head = "INSERT INTO tb_permintaan (user_id, tanggal_permintaan, status, keperluan) 
                   VALUES ('$user_id', '$tanggal', 'menunggu', '$keperluan')";
        
        if (mysqli_query($koneksi, $q_head)) {
            $id_permintaan = mysqli_insert_id($koneksi); // Ambil ID terakhir

            // 2. Simpan detail ke tb_detail_permintaan
            foreach ($_SESSION['keranjang'] as $item) {
                $barang_id = $item['id'];
                $jumlah    = $item['jumlah'];
                $satuan    = $item['satuan'];

                mysqli_query($koneksi, "INSERT INTO tb_detail_permintaan (permintaan_id, barang_id, jumlah, satuan) 
                                        VALUES ('$id_permintaan', '$barang_id', '$jumlah', '$satuan')");
            }

            // 3. Kosongkan Keranjang & Redirect
            unset($_SESSION['keranjang']);
            echo "<script>alert('Permintaan berhasil diajukan! Menunggu persetujuan admin.'); window.location='permintaan_saya.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// =============================================================
// SET JUDUL KE TOPBAR (Agar muncul di kotak merah)
// =============================================================
$judul_halaman = "Keranjang Permintaan";
$deskripsi_halaman = "Periksa kembali barang sebelum mengajukan permintaan.";

require 'layout/topbar.php'; 
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 border-bottom-primary">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-cart"></i> Item yang dipilih</h6>
                </div>
                <div class="card-body">
                    
                    <?php if (empty($_SESSION['keranjang'])): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-basket fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Keranjang Anda masih kosong.</p>
                            <a href="daftar_barang.php" class="btn btn-primary btn-sm">Belanja Sekarang</a>
                        </div>
                    <?php else: ?>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Barang</th>
                                    <th>Merek</th>
                                    <th width="15%">Jumlah</th>
                                    <th>Satuan</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($_SESSION['keranjang'] as $key => $item): 
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="font-weight-bold text-primary"><?= $item['nama']; ?></td>
                                    <td><?= !empty($item['merek']) ? $item['merek'] : '-'; ?></td>
                                    <td class="font-weight-bold text-center"><?= $item['jumlah']; ?></td>
                                    <td><?= $item['satuan']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?= $key; ?>" title="Edit Jumlah">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <a href="keranjang.php?hapus=<?= $key; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus item ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEdit<?= $key; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title">Edit Jumlah Barang</h5>
                                                <button class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nama Barang</label>
                                                        <input type="text" class="form-control" value="<?= $item['nama']; ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Jumlah Permintaan (Max: <?= $item['stok_max']; ?>)</label>
                                                        <input type="number" name="jumlah_baru" class="form-control" value="<?= $item['jumlah']; ?>" min="1" max="<?= $item['stok_max']; ?>" required>
                                                    </div>
                                                    <input type="hidden" name="index_array" value="<?= $key; ?>">
                                                    <input type="hidden" name="stok_max" value="<?= $item['stok_max']; ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="update_cart" class="btn btn-warning">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <a href="daftar_barang.php" class="btn btn-secondary btn-sm shadow-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Barang
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-left-success h-100">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Konfirmasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold small text-uppercase">Keperluan / Alasan</label>
                            <textarea name="keperluan" class="form-control" rows="4" placeholder="Contoh: Kebutuhan ATK Bulanan Divisi IT..." required></textarea>
                            <small class="text-muted">Wajib diisi agar admin dapat memproses.</small>
                        </div>
                        
                        <hr>
                        
                        <?php if (!empty($_SESSION['keranjang'])): ?>
                            <button type="submit" name="ajukan" class="btn btn-primary btn-block shadow-sm py-2">
                                <i class="fas fa-paper-plane mr-2"></i> Ajukan Permintaan
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