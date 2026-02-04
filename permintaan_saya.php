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
// AMBIL DATA BARANG (Untuk Dropdown di Modal)
// ==============================================================
$list_barang = [];
$q_b = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE stok > 0 ORDER BY nama_barang ASC");
while ($b = mysqli_fetch_assoc($q_b)) {
    $list_barang[] = $b;
}

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
// LOGIKA 2: PROSES UPDATE & TAMBAH BARANG
// ==============================================================
if (isset($_POST['update_permintaan'])) {
    $id_permintaan = $_POST['id_permintaan'];

    // A. UPDATE BARANG YANG SUDAH ADA
    if (isset($_POST['id_detail'])) {
        $id_details = $_POST['id_detail']; 
        $jumlahs    = $_POST['jumlah'];    

        for ($i = 0; $i < count($id_details); $i++) {
            $curr_id  = $id_details[$i];
            $curr_jml = $jumlahs[$i];
            mysqli_query($koneksi, "UPDATE tb_detail_permintaan SET jumlah='$curr_jml' WHERE id='$curr_id'");
        }
    }

    // B. TAMBAH BARANG BARU (SUSULAN)
    if (isset($_POST['new_barang_id'])) {
        $new_ids  = $_POST['new_barang_id'];
        $new_jmls = $_POST['new_jumlah'];

        for ($i = 0; $i < count($new_ids); $i++) {
            $id_brg = $new_ids[$i];
            $jml    = $new_jmls[$i];

            if (!empty($id_brg) && $jml > 0) {
                // 1. Ambil info satuan barang
                $q_info = mysqli_query($koneksi, "SELECT satuan FROM tb_barang_bergerak WHERE id='$id_brg'");
                $d_info = mysqli_fetch_assoc($q_info);
                $satuan = $d_info['satuan'];

                // 2. Cek apakah barang ini SUDAH ADA di permintaan ini? (Supaya tidak duplikat baris)
                $cek_ada = mysqli_query($koneksi, "SELECT id, jumlah FROM tb_detail_permintaan WHERE permintaan_id='$id_permintaan' AND barang_id='$id_brg'");
                
                if (mysqli_num_rows($cek_ada) > 0) {
                    // Jika sudah ada, kita tambahkan jumlahnya ke yang lama
                    $row_ada = mysqli_fetch_assoc($cek_ada);
                    $jml_baru = $row_ada['jumlah'] + $jml;
                    $id_det_lama = $row_ada['id'];
                    mysqli_query($koneksi, "UPDATE tb_detail_permintaan SET jumlah='$jml_baru' WHERE id='$id_det_lama'");
                } else {
                    // Jika belum ada, insert baru
                    mysqli_query($koneksi, "INSERT INTO tb_detail_permintaan (permintaan_id, barang_id, jumlah, satuan) VALUES ('$id_permintaan', '$id_brg', '$jml', '$satuan')");
                }
            }
        }
    }

    echo "<script>alert('Perubahan berhasil disimpan!'); window.location='permintaan_saya.php';</script>";
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// Set Judul
$judul_halaman     = "Riwayat Permintaan Saya";
$deskripsi_halaman = "Daftar status permintaan barang yang pernah Anda ajukan.";

require 'layout/topbar.php'; 
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Penyesuaian Style Select2 agar cocok dengan Bootstrap */
    .select2-container .select2-selection--single {
        height: 31px !important; /* Sesuaikan tinggi dengan form-control-sm */
        border: 1px solid #d1d3e2;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 31px !important;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 31px !important;
    }
    /* Agar dropdown muncul di atas modal */
    .select2-container {
        z-index: 9999;
    }
</style>

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
                                    $q_detail = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang 
                                                                        FROM tb_detail_permintaan d 
                                                                        JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                        WHERE d.permintaan_id = '$id_req'");
                                    while($d = mysqli_fetch_assoc($q_detail)){
                                        echo "<li class='mb-1'>{$d['nama_barang']} (<b>{$d['jumlah']} {$d['satuan']}</b>)</li>";
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
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEdit<?= $id_req; ?>" title="Edit / Tambah Barang">
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
                                                    <h5 class="modal-title">Edit Permintaan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                                                        
                                                        <h6 class="font-weight-bold text-gray-800 mb-2">Barang yang Diajukan:</h6>
                                                        <table class="table table-sm table-bordered mb-4">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Nama Barang</th>
                                                                    <th width="150px">Jumlah</th>
                                                                    <th>Satuan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                $q_edit = mysqli_query($koneksi, "SELECT d.id AS id_detail, d.jumlah, d.satuan, b.nama_barang, b.stok AS stok_gudang 
                                                                                                  FROM tb_detail_permintaan d 
                                                                                                  JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                                                  WHERE d.permintaan_id = '$id_req'");
                                                                while($edit = mysqli_fetch_assoc($q_edit)):
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?= $edit['nama_barang']; ?>
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

                                                        <hr>

                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="font-weight-bold text-success mb-0"><i class="fas fa-plus-circle"></i> Tambah Barang Susulan</h6>
                                                            <button type="button" class="btn btn-sm btn-success shadow-sm" onclick="tambahBaris('tabelSusulan<?= $id_req; ?>', 'modalEdit<?= $id_req; ?>')">
                                                                <i class="fas fa-plus"></i> Baris
                                                            </button>
                                                        </div>
                                                        
                                                        <p class="small text-muted mb-2">Jika ada barang yang lupa dimasukkan, tambahkan di sini (bisa dicari).</p>

                                                        <table class="table table-sm table-bordered" id="tabelSusulan<?= $id_req; ?>">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Pilih Barang (Ketik untuk cari)</th>
                                                                    <th width="120px">Jumlah</th>
                                                                    <th width="50px"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                </tbody>
                                                        </table>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="update_permintaan" class="btn btn-primary">Simpan Perubahan</button>
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Membuat string option HTML dari PHP array
    var optionsBarang = '<option value="">-- Cari Barang --</option>';
    <?php foreach($list_barang as $brg): ?>
        optionsBarang += '<option value="<?= $brg['id']; ?>"><?= addslashes($brg['nama_barang']); ?> (Stok: <?= $brg['stok']; ?>)</option>';
    <?php endforeach; ?>

    // Fungsi Tambah Baris dengan SELECT2
    function tambahBaris(tableId, modalId) {
        var table = document.getElementById(tableId).getElementsByTagName('tbody')[0];
        var newRow = table.insertRow();
        
        // Generate ID unik untuk select ini agar bisa dipanggil jQuery
        var uniqueId = "sel_" + Date.now() + Math.floor(Math.random() * 1000);

        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);

        // Masukkan HTML Select dengan ID unik
        cell1.innerHTML = '<select id="' + uniqueId + '" name="new_barang_id[]" class="form-control form-control-sm" required style="width:100%">' + optionsBarang + '</select>';
        cell2.innerHTML = '<input type="number" name="new_jumlah[]" class="form-control form-control-sm" placeholder="Jml" min="1" required>';
        cell3.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)"><i class="fas fa-times"></i></button>';
        cell3.className = 'text-center';

        // AKTIFKAN SELECT2 PADA ELEMENT BARU
        // dropdownParent sangat penting agar search box berfungsi di dalam Modal Bootstrap
        $('#' + uniqueId).select2({
            dropdownParent: $('#' + modalId),
            placeholder: "Ketik nama barang...",
            allowClear: true,
            width: '100%' 
        });
    }

    // Fungsi Hapus Baris
    function hapusBaris(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "ordering": false,
                "language": {
                    "search": "Cari:",
                    "zeroRecords": "Data kosong"
                }
            });
        }
    });
</script>