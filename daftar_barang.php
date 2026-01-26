<?php
session_start();
require 'config/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Inisialisasi Keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// ==========================================
// LOGIKA: TAMBAH KE KERANJANG
// ==========================================
if (isset($_POST['tambah_keranjang'])) {
    $id_barang   = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $merk_barang = $_POST['merk_barang'];
    $jumlah      = $_POST['jumlah'];
    $satuan      = $_POST['satuan'];
    $stok_max    = $_POST['stok_max'];

    // Cek apakah barang sudah ada?
    $sudah_ada = false;
    foreach ($_SESSION['keranjang'] as $key => $item) {
        if ($item['id'] == $id_barang) {
            $_SESSION['keranjang'][$key]['jumlah'] += $jumlah;
            // Validasi max stok
            if ($_SESSION['keranjang'][$key]['jumlah'] > $stok_max) {
                $_SESSION['keranjang'][$key]['jumlah'] = $stok_max;
            }
            $sudah_ada = true;
            break;
        }
    }

    // Jika baru, masukkan
    if (!$sudah_ada) {
        $_SESSION['keranjang'][] = [
            'id' => $id_barang,
            'nama' => $nama_barang,
            'merk' => $merk_barang,
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            'stok_max' => $stok_max
        ];
    }

    echo "<script>alert('Barang masuk keranjang!'); window.location='daftar_barang.php';</script>";
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// =============================================================
// SET JUDUL KE TOPBAR
// =============================================================
$judul_halaman = "Daftar Barang";
$deskripsi_halaman = "Pilih barang yang ingin Anda ajukan.";

require 'layout/topbar.php'; 

// Hitung jumlah keranjang untuk badge
$jml_item_keranjang = count($_SESSION['keranjang']);
?>

<div class="container-fluid">

    <div class="card shadow mb-4">
        
        <div class="card-header py-3 border-bottom-primary d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-boxes"></i> Daftar Barang Tersedia</h6>
            
            <a href="keranjang.php" class="btn btn-success btn-sm shadow-sm">
                <i class="fas fa-shopping-cart fa-sm"></i> Lihat Keranjang 
                <span class="badge badge-light text-danger ml-1 font-weight-bold"><?= $jml_item_keranjang; ?></span>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Merk</th>
                            <th>Nama Barang</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = "SELECT * FROM tb_barang_bergerak WHERE is_deleted = 0 ORDER BY nama_barang ASC";
                        $result = mysqli_query($koneksi, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['kode_barang']; ?></td>
                            <td><?= $row['merk_barang']; ?></td>
                            <td class="font-weight-bold text-primary"><?= $row['nama_barang']; ?></td>
                            <td class="<?= $row['stok'] == 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold'; ?>">
                                <?= $row['stok']; ?>
                            </td>
                            <td><?= $row['satuan']; ?></td>
                            <td class="text-center">
                                <?php if($row['stok'] > 0): ?>
                                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAdd<?= $row['id']; ?>">
                                        <i class="fas fa-plus"></i> Add
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Habis</button>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <div class="modal fade text-left" id="modalAdd<?= $row['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Tambah ke Keranjang</h5>
                                        <button class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Barang</label>
                                                <input type="text" class="form-control" value="<?= $row['nama_barang']; ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Jumlah (Max: <?= $row['stok']; ?>)</label>
                                                <input type="number" name="jumlah" class="form-control" min="1" max="<?= $row['stok']; ?>" required>
                                            </div>
                                            
                                            <input type="hidden" name="id_barang" value="<?= $row['id']; ?>">
                                            <input type="hidden" name="nama_barang" value="<?= $row['nama_barang']; ?>">
                                            <input type="hidden" name="merk_barang" value="<?= $row['merk_barang']; ?>">
                                            <input type="hidden" name="satuan" value="<?= $row['satuan']; ?>">
                                            <input type="hidden" name="stok_max" value="<?= $row['stok']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="tambah_keranjang" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "search": "Cari Barang:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Tidak ada data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Awal",
                        "last": "Akhir",
                        "next": "Lanjut",
                        "previous": "Kembali"
                    }
                }
            });
        }
    });
</script>