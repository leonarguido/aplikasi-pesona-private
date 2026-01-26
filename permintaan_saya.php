<?php
session_start();
require 'config/koneksi.php';

// 1. Cek Login (Hanya User/Staff)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];

// ==============================================================
// LOGIKA 1: PROSES BATALKAN PERMINTAAN
// ==============================================================
if (isset($_GET['batal_id'])) {
    $id_batal = $_GET['batal_id'];
    
    // Cek keamanan
    $cek = mysqli_query($koneksi, "SELECT status FROM tb_permintaan WHERE id='$id_batal' AND user_id='$id_user'");
    $d = mysqli_fetch_assoc($cek);

    if ($d && $d['status'] == 'menunggu') {
        mysqli_query($koneksi, "DELETE FROM tb_detail_permintaan WHERE permintaan_id='$id_batal'");
        mysqli_query($koneksi, "DELETE FROM tb_permintaan WHERE id='$id_batal'");
        echo "<script>alert('Permintaan berhasil dibatalkan.'); window.location='permintaan_saya.php';</script>";
    } else {
        echo "<script>alert('Gagal! Permintaan tidak bisa dibatalkan.'); window.location='permintaan_saya.php';</script>";
    }
}

// ==============================================================
// LOGIKA 2: PROSES UPDATE JUMLAH
// ==============================================================
if (isset($_POST['update_permintaan'])) {
    $id_details = $_POST['id_detail']; // Array ID Detail
    $jumlahs    = $_POST['jumlah'];    // Array Jumlah Baru

    for ($i = 0; $i < count($id_details); $i++) {
        $curr_id  = $id_details[$i];
        $curr_jml = $jumlahs[$i];
        mysqli_query($koneksi, "UPDATE tb_detail_permintaan SET jumlah='$curr_jml' WHERE id='$curr_id'");
    }

    echo "<script>alert('Perubahan jumlah berhasil disimpan!'); window.location='permintaan_saya.php';</script>";
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// ==========================================================
// SET JUDUL KE TOPBAR
// ==========================================================
$judul_halaman     = "Riwayat Permintaan Saya";
$deskripsi_halaman = "Daftar status permintaan barang yang pernah Anda ajukan.";

require 'layout/topbar.php'; 
?>

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3 border-bottom-primary">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt"></i> Log Permintaan Anda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th>Detail Barang</th>
                            <th>Catatan Admin</th>
                            <th class="text-center" width="10%">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = "SELECT p.* FROM tb_permintaan p 
                                  WHERE p.user_id = '$id_user' 
                                  ORDER BY p.id DESC";
                        
                        $result = mysqli_query($koneksi, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)): 
                            $id_req = $row['id'];
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <i class="far fa-calendar-alt text-gray-400"></i> <?= date('d-m-Y', strtotime($row['tanggal_permintaan'])); ?>
                            </td>
                            
                            <td>
                                <ul class="pl-3 mb-0" style="font-size: 0.9rem;">
                                <?php 
                                    $q_detail = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang, b.merk_barang
                                                                        FROM tb_detail_permintaan d 
                                                                        JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                        WHERE d.permintaan_id = '$id_req'");
                                    while($d = mysqli_fetch_assoc($q_detail)){
                                        echo "<li class='mb-1'>{$d['nama_barang']} ({$d['merk_barang']}) (<b>{$d['jumlah']} {$d['satuan']}</b>)</li>";
                                    }
                                ?>
                                </ul>
                            </td>

                            <td class="align-middle">
                                <?php if(!empty($row['catatan'])): ?>
                                    <span class="text-dark small font-weight-bold"><?= $row['catatan']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center align-middle">
                                <?php if($row['status'] == 'menunggu'): ?>
                                    <span class="badge badge-warning px-2 py-1">Menunggu</span>
                                <?php elseif($row['status'] == 'disetujui'): ?>
                                    <span class="badge badge-success px-2 py-1">Disetujui</span>
                                    <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                        <?= date('d-m-Y', strtotime($row['tanggal_disetujui'])); ?>
                                    </div>
                                <?php elseif($row['status'] == 'ditolak'): ?>
                                    <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-center align-middle">
                                <?php if($row['status'] == 'menunggu'): ?>
                                    
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEdit<?= $id_req; ?>" title="Edit Jumlah">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="permintaan_saya.php?batal_id=<?= $id_req; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin membatalkan?')" title="Batalkan">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>

                                    <div class="modal fade text-left" id="modalEdit<?= $id_req; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">Edit Jumlah Permintaan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                                                        <p class="small text-muted">Silakan ubah jumlah barang jika diperlukan:</p>
                                                        
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Nama Barang</th>
                                                                    <th width="120px">Jumlah</th>
                                                                    <th>Satuan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                $q_edit = mysqli_query($koneksi, "SELECT d.id AS id_detail, d.jumlah, d.satuan, b.nama_barang, b.merk_barang, b.stok AS stok_gudang 
                                                                                                  FROM tb_detail_permintaan d 
                                                                                                  JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                                                  WHERE d.permintaan_id = '$id_req'");
                                                                while($edit = mysqli_fetch_assoc($q_edit)):
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?= $edit['nama_barang']; ?> (<?= $edit['merk_barang']; ?>)
                                                                        <br><small class="text-success">Sisa Stok Gudang: <?= $edit['stok_gudang']; ?></small>
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" name="id_detail[]" value="<?= $edit['id_detail']; ?>">
                                                                        <input type="number" name="jumlah[]" class="form-control form-control-sm" value="<?= $edit['jumlah']; ?>" min="1" max="<?= $edit['stok_gudang']; ?>" required>
                                                                    </td>
                                                                    <td class="align-middle"><?= $edit['satuan']; ?></td>
                                                                </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="update_permintaan" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php elseif($row['status'] == 'disetujui'): ?>
                                    <a href="cetak_surat.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm">
                                        <i class="fas fa-print"></i> Cetak
                                    </a>

                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-ban"></i></button>
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
<?php require 'layout/footer.php'; ?>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "search": "Cari Riwayat:",
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
                },
                "ordering": false 
            });
        }
    });
</script>