<?php
session_start();
require 'config/koneksi.php';

// 1. Cek Akses
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role'] == 'staff' || $_SESSION['role'] == 'user') {
    echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
    exit;
}

// ==========================================
// LOGIKA PROSES (BACKEND)
// ==========================================

// A. PROSES PERSETUJUAN (Approve)
if (isset($_POST['setuju'])) {
    $id_permintaan   = $_POST['id_permintaan'];
    $catatan_admin   = mysqli_real_escape_string($koneksi, $_POST['catatan_admin']);
    $admin_id        = $_SESSION['user_id'];
    $tanggal_acc     = date('Y-m-d');
    $qty_approved_array = $_POST['qty_approved']; 
    
    $error_msg = "";
    $stok_aman = true;

    // TAHAP 1: VALIDASI
    foreach ($qty_approved_array as $id_detail => $jml_acc) {
        $q_check = mysqli_query($koneksi, "SELECT d.jumlah AS jml_minta, b.stok, b.nama_barang 
                                           FROM tb_detail_permintaan d 
                                           JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                           WHERE d.id = '$id_detail'");
        $data = mysqli_fetch_assoc($q_check);
        
        if ($jml_acc > $data['jml_minta']) {
            $error_msg = "Gagal! Jumlah disetujui untuk '{$data['nama_barang']}' melebihi permintaan.";
            $stok_aman = false; break;
        }
        if ($jml_acc > $data['stok']) {
            $error_msg = "Gagal! Stok '{$data['nama_barang']}' tidak cukup.";
            $stok_aman = false; break;
        }
        if ($jml_acc <= 0) {
            $error_msg = "Gagal! Jumlah minimal 1.";
            $stok_aman = false; break;
        }
    }

    // TAHAP 2: EKSEKUSI
    if (!$stok_aman) {
        echo "<script>alert('$error_msg');</script>";
    } else {
        foreach ($qty_approved_array as $id_detail => $jml_acc) {
            mysqli_query($koneksi, "UPDATE tb_detail_permintaan SET jumlah = '$jml_acc' WHERE id = '$id_detail'");
            $q_b = mysqli_query($koneksi, "SELECT barang_id FROM tb_detail_permintaan WHERE id = '$id_detail'");
            $d_b = mysqli_fetch_assoc($q_b);
            $id_barang = $d_b['barang_id'];
            mysqli_query($koneksi, "UPDATE tb_barang_bergerak SET stok = stok - $jml_acc WHERE id = '$id_barang'");
        }

        $query_update = "UPDATE tb_permintaan SET 
                         status = 'disetujui', 
                         tanggal_disetujui = '$tanggal_acc', 
                         admin_id = '$admin_id',
                         catatan = '$catatan_admin'
                         WHERE id = '$id_permintaan'";

        if (mysqli_query($koneksi, $query_update)) {
            echo "<script>alert('Permintaan berhasil DISETUJUI!'); window.location='persetujuan.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// B. PROSES PENOLAKAN
if (isset($_POST['tolak'])) {
    $id_permintaan = $_POST['id_permintaan'];
    $catatan       = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $admin_id      = $_SESSION['user_id'];
    $tanggal_acc   = date('Y-m-d');

    $query = "UPDATE tb_permintaan SET 
              status = 'ditolak', 
              tanggal_ditolak = '$tanggal_acc', 
              admin_id = '$admin_id',
              catatan = '$catatan' 
              WHERE id = '$id_permintaan'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Permintaan berhasil DITOLAK!'); window.location='persetujuan.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';
require 'layout/topbar.php'; 
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Konfirmasi Permintaan Masuk</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Antrian Permintaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pemohon</th>
                            <th>Rincian Barang (Qty)</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = "SELECT p.id AS id_permintaan, p.tanggal_permintaan, u.nama AS nama_pemohon 
                                  FROM tb_permintaan p 
                                  JOIN tb_user u ON p.user_id = u.id 
                                  WHERE p.status = 'menunggu' 
                                  ORDER BY p.tanggal_permintaan ASC";
                        
                        $result = mysqli_query($koneksi, $query);
                        
                        if(mysqli_num_rows($result) == 0) {
                            echo "<tr><td colspan='5' class='text-center text-muted py-4'>Tidak ada permintaan baru.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($result)): 
                            $id_req = $row['id_permintaan'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_permintaan'])); ?></td>
                            <td class="font-weight-bold"><?= $row['nama_pemohon']; ?></td>
                            
                            <td>
                                <ul class="pl-3 mb-0" style="font-size: 0.9rem;">
                                <?php 
                                    $q_d = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang, b.stok 
                                                                   FROM tb_detail_permintaan d 
                                                                   JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                   WHERE d.permintaan_id = '$id_req'");
                                    while($d = mysqli_fetch_assoc($q_d)){
                                        $status_stok = ($d['stok'] >= $d['jumlah']) 
                                            ? "<span class='badge badge-success badge-pill ml-1'>Aman ({$d['stok']})</span>" 
                                            : "<span class='badge badge-danger badge-pill ml-1'>Kurang ({$d['stok']})</span>";
                                        echo "<li class='mb-1'>{$d['nama_barang']} : <b>{$d['jumlah']} {$d['satuan']}</b> $status_stok</li>";
                                    }
                                ?>
                                </ul>
                            </td>

                            <td class="text-center">
                                <button class="btn btn-success btn-sm btn-circle" data-toggle="modal" data-target="#modalSetuju<?= $id_req; ?>" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-danger btn-sm btn-circle" data-toggle="modal" data-target="#modalTolak<?= $id_req; ?>" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="modal fade text-left" id="modalSetuju<?= $id_req; ?>">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Konfirmasi & Edit Jumlah</h5>
                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <p>Permintaan dari: <strong><?= $row['nama_pemohon']; ?></strong></p>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="thead-light">
                                                                <tr><th>Nama Barang</th><th>Stok</th><th width="30%">Disetujui</th></tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                $q_modal = mysqli_query($koneksi, "SELECT d.id AS id_detail, d.jumlah, d.satuan, b.nama_barang, b.stok 
                                                                                                   FROM tb_detail_permintaan d 
                                                                                                   JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                                                   WHERE d.permintaan_id = '$id_req'");
                                                                while($dm = mysqli_fetch_assoc($q_modal)):
                                                                ?>
                                                                <tr>
                                                                    <td><?= $dm['nama_barang']; ?></td>
                                                                    <td><?= $dm['stok']; ?> <?= $dm['satuan']; ?></td>
                                                                    <td>
                                                                        <div class="input-group input-group-sm">
                                                                            <input type="number" name="qty_approved[<?= $dm['id_detail']; ?>]" class="form-control font-weight-bold text-success" value="<?= $dm['jumlah']; ?>" max="<?= $dm['jumlah']; ?>" min="1" required>
                                                                            <div class="input-group-append"><span class="input-group-text"><?= $dm['satuan']; ?></span></div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label>Catatan Admin</label>
                                                        <textarea name="catatan_admin" class="form-control" rows="2"></textarea>
                                                    </div>
                                                    <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="setuju" class="btn btn-success">Proses</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade text-left" id="modalTolak<?= $id_req; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Tolak Permintaan?</h5>
                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Alasan Penolakan:</label>
                                                        <textarea name="catatan" class="form-control" required></textarea>
                                                    </div>
                                                    <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="tolak" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require 'layout/footer.php'; ?>