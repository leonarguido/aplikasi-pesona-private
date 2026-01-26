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
        $q_check = mysqli_query($koneksi, "SELECT d.jumlah AS jml_minta, b.stok, b.nama_barang, b.merk_barang
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

$judul_halaman = "Konfirmasi Permintaan Masuk";
require 'layout/topbar.php'; 
?>

<div class="container-fluid">
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 border-bottom-primary">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt"></i> Daftar Antrian Permintaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th>Pemohon</th>
                            <th>Rincian Barang (Qty)</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Query Utama
                        $query = "SELECT p.id AS id_permintaan, p.tanggal_permintaan, u.nama AS nama_pemohon, u.role 
                                  FROM tb_permintaan p 
                                  JOIN tb_user u ON p.user_id = u.id 
                                  WHERE p.status = 'menunggu' 
                                  ORDER BY p.tanggal_permintaan ASC";
                        
                        $result = mysqli_query($koneksi, $query);
                        
                        // LOOPING 1: HANYA UNTUK MENAMPILKAN TABEL
                        while ($row = mysqli_fetch_assoc($result)): 
                            $id_req = $row['id_permintaan'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <i class="far fa-calendar-alt text-gray-400"></i> <?= date('d-m-Y', strtotime($row['tanggal_permintaan'])); ?>
                            </td>
                            <td>
                                <div class="font-weight-bold text-primary"><?= $row['nama_pemohon']; ?></div>
                                <small class="text-muted text-capitalize">(<?= $row['role']; ?>)</small>
                            </td>
                            <td>
                                <ul class="pl-3 mb-0" style="font-size: 0.9rem;">
                                <?php 
                                    $q_d = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang, b.merk_barang, b.stok 
                                                                   FROM tb_detail_permintaan d 
                                                                   JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                   WHERE d.permintaan_id = '$id_req'");
                                    while($d = mysqli_fetch_assoc($q_d)){
                                        $status_stok = ($d['stok'] >= $d['jumlah']) 
                                            ? "<span class='badge badge-success badge-pill ml-1' style='font-size:0.7rem'>Stok: {$d['stok']}</span>" 
                                            : "<span class='badge badge-danger badge-pill ml-1' style='font-size:0.7rem'>Stok Kritis: {$d['stok']}</span>";
                                        
                                        echo "<li class='mb-2'>{$d['nama_barang']} ({$d['merk_barang']}) : <b>{$d['jumlah']} {$d['satuan']}</b> $status_stok</li>";
                                    }
                                ?>
                                </ul>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalSetuju<?= $id_req; ?>" title="Setujui">
                                        <i class="fas fa-check"></i> Acc
                                    </button>
                                    <button class="btn btn-danger btn-sm shadow-sm" data-toggle="modal" data-target="#modalTolak<?= $id_req; ?>" title="Tolak">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
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

<?php 
mysqli_data_seek($result, 0); 
while ($row = mysqli_fetch_assoc($result)): 
    $id_req = $row['id_permintaan'];
?>

<div class="modal fade" id="modalSetuju<?= $id_req; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                <button class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="alert alert-light border-left-success" role="alert">
                        Permintaan dari: <strong><?= $row['nama_pemohon']; ?></strong><br>
                        <small>Pastikan stok fisik di gudang mencukupi sebelum menyetujui.</small>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr><th>Nama Barang</th><th width="20%">Stok Gudang</th><th width="30%">Jml Disetujui</th></tr>
                            </thead>
                            <tbody>
                                <?php 
                                $q_modal = mysqli_query($koneksi, "SELECT d.id AS id_detail, d.jumlah, d.satuan, b.nama_barang, b.merk_barang, b.stok 
                                                                   FROM tb_detail_permintaan d 
                                                                   JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                   WHERE d.permintaan_id = '$id_req'");
                                while($dm = mysqli_fetch_assoc($q_modal)):
                                ?>
                                <tr>
                                    <td class="align-middle"><?= $dm['nama_barang']; ?> (<?= $dm['merk_barang']; ?>)</td>
                                    <td class="align-middle text-center <?= ($dm['stok'] < $dm['jumlah']) ? 'text-danger font-weight-bold':'text-success'; ?>">
                                        <?= $dm['stok']; ?> <?= $dm['satuan']; ?>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="qty_approved[<?= $dm['id_detail']; ?>]" class="form-control font-weight-bold text-success" value="<?= $dm['jumlah']; ?>" max="<?= $dm['jumlah']; ?>" min="1" required>
                                            <div class="input-group-append"><span class="input-group-text"><?= $dm['satuan']; ?></span></div>
                                        </div>
                                        <small class="text-muted">Permintaan Awal: <?= $dm['jumlah']; ?></small>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Catatan Admin (Opsional)</label>
                        <textarea name="catatan_admin" class="form-control" rows="2" placeholder="Cth: Barang diambil jam 2 siang"></textarea>
                    </div>
                    <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="setuju" class="btn btn-success"><i class="fas fa-check"></i> Proses Setuju</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak<?= $id_req; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tolak Permintaan?</h5>
                <button class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <p>Anda yakin ingin menolak permintaan dari <strong><?= $row['nama_pemohon']; ?></strong>?</p>
                    <div class="form-group">
                        <label>Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="3" required placeholder="Cth: Stok habis, atau permintaan tidak valid."></textarea>
                    </div>
                    <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tolak" class="btn btn-danger"><i class="fas fa-times"></i> Tolak Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endwhile; ?>

<?php require 'layout/footer.php'; ?>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "search": "Cari Permintaan:",
                    "lengthMenu": "Tampilkan _MENU_ antrian",
                    "zeroRecords": "Tidak ada permintaan yang cocok",
                    "info": "Menampilkan _PAGE_ dari _PAGES_",
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
