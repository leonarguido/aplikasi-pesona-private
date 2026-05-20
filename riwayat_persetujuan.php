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

// =============================================================
// AKSI MANUAL: TANDAI BARANG SUDAH DIAMBIL (STATUS -> SELESAI)
// =============================================================
if (isset($_GET['ambil_id'])) {
    $id_ambil = mysqli_real_escape_string($koneksi, $_GET['ambil_id']);
    mysqli_query($koneksi, "UPDATE tb_permintaan SET status = 'selesai' WHERE id = '$id_ambil'");
    
    // Refresh halaman agar bersih dari parameter URL
    echo "<script>window.location='riwayat_persetujuan.php';</script>";
    exit;
}

// =============================================================
// PENGECEKAN PASIF (LAZY EVALUATION) - BATAL OTOMATIS 7 HARI
// =============================================================
$query_kedaluwarsa = mysqli_query($koneksi, "
    SELECT id 
    FROM tb_permintaan 
    WHERE status = 'disetujui' 
    AND DATEDIFF(CURRENT_DATE(), tanggal_disetujui) >= 7
");

if (mysqli_num_rows($query_kedaluwarsa) > 0) {
    while ($data_kdl = mysqli_fetch_assoc($query_kedaluwarsa)) {
        $id_kdl = $data_kdl['id'];
        
        // 1. Ambil detail barang untuk dikembalikan stoknya
        $query_detail_kdl = mysqli_query($koneksi, "
            SELECT barang_id, jumlah 
            FROM tb_detail_permintaan 
            WHERE permintaan_id = '$id_kdl'
        ");
        
        while ($detail_kdl = mysqli_fetch_assoc($query_detail_kdl)) {
            $id_brg_kdl = $detail_kdl['barang_id'];
            $jumlah_kdl = $detail_kdl['jumlah'];
            
            // 2. Kembalikan stok ke tb_barang_bergerak
            mysqli_query($koneksi, "
                UPDATE tb_barang_bergerak 
                SET stok = stok + $jumlah_kdl 
                WHERE id = '$id_brg_kdl'
            ");
        }
        
        // 3. Ubah status menjadi batal_otomatis dan tambahkan catatan
        mysqli_query($koneksi, "
            UPDATE tb_permintaan 
            SET status = 'batal_otomatis', 
                catatan = CONCAT(IFNULL(catatan,''), ' [Dibatalkan otomatis: Melewati batas waktu 7 hari]') 
            WHERE id = '$id_kdl'
        ");
    }
}
// =============================================================

require 'layout/header.php';
require 'layout/sidebar.php';

// SET JUDUL KE TOPBAR
$judul_halaman = "Riwayat Persetujuan";
$deskripsi_halaman = "Log riwayat permintaan yang telah disetujui, selesai, ditolak, atau batal otomatis.";

require 'layout/topbar.php'; 
?>

<div class="container-fluid">
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 border-bottom-primary">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history"></i> Log Riwayat Permintaan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="15%">Tanggal</th>
                            <th>Pemohon</th>
                            <th>Rincian Barang (Final)</th>
                            <th class="text-center" width="12%">Status</th>
                            <th>Admin Eksekutor</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil semua riwayat selain 'menunggu'
                        $query_hist = "SELECT p.*, u.nama AS nama_pemohon, a.nama AS nama_admin
                                       FROM tb_permintaan p 
                                       JOIN tb_user u ON p.user_id = u.id 
                                       LEFT JOIN tb_user a ON p.admin_id = a.id
                                       WHERE p.status != 'menunggu' 
                                       ORDER BY p.tanggal_disetujui DESC, p.tanggal_ditolak DESC";
                        
                        $res_hist = mysqli_query($koneksi, $query_hist);
                        
                        while($hist = mysqli_fetch_assoc($res_hist)):
                            $id_hist = $hist['id'];
                            
                            // Tentukan Tanggal acuan (Jika ditolak pakai tgl_ditolak, sisanya pakai tgl_disetujui)
                            $tgl_aksi = ($hist['status'] == 'ditolak') ? $hist['tanggal_ditolak'] : $hist['tanggal_disetujui'];
                        ?>
                        <tr>
                            <td>
                                <i class="far fa-calendar-alt text-gray-400"></i> <?= date('d-m-Y', strtotime($tgl_aksi)); ?>
                            </td>
                            <td class="font-weight-bold text-primary"><?= $hist['nama_pemohon']; ?></td>
                            
                            <td>
                                <ul class="pl-3 mb-0" style="font-size: 0.9rem;">
                                <?php 
                                    $q_detail_hist = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang 
                                                                             FROM tb_detail_permintaan d 
                                                                             JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                             WHERE d.permintaan_id = '$id_hist'");
                                    
                                    while($dh = mysqli_fetch_assoc($q_detail_hist)){
                                        echo "<li class='mb-1'>{$dh['nama_barang']} : <b>{$dh['jumlah']} {$dh['satuan']}</b></li>";
                                    }
                                ?>
                                </ul>
                            </td>

                            <td class="text-center">
                                <?php if($hist['status'] == 'disetujui'): ?>
                                    <span class="badge badge-warning px-2 py-1 text-dark">Disetujui (Belum diambil)</span>
                                <?php elseif($hist['status'] == 'selesai'): ?>
                                    <span class="badge badge-success px-2 py-1">Selesai (Diambil)</span>
                                <?php elseif($hist['status'] == 'batal_otomatis'): ?>
                                    <span class="badge badge-secondary px-2 py-1">Batal Otomatis</span>
                                    <div class="small text-danger mt-1 font-italic">Hangus (Lewat 7 Hari)</div>
                                <?php else: ?>
                                    <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                    <div class="small text-danger mt-1 font-italic">"<?= $hist['catatan']; ?>"</div>
                                <?php endif; ?>
                            </td>
                            
                            <td class="small text-muted">
                                <i class="fas fa-user-shield"></i> <?= $hist['nama_admin']; ?>
                            </td>
                            
                            <td class="text-center">
                                <?php if($hist['status'] == 'disetujui'): ?>
                                    <a href="riwayat_persetujuan.php?ambil_id=<?= $hist['id']; ?>" class="btn btn-success btn-sm shadow-sm mb-1" onclick="return confirm('Konfirmasi: Apakah Staf sudah mengambil barang fisik secara langsung?');" title="Tandai Sudah Diambil">
                                        <i class="fas fa-check-double"></i> Diambil
                                    </a>
                                    <a href="cetak_surat.php?id=<?= $hist['id']; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm mb-1" title="Cetak Surat Jalan">
                                        <i class="fas fa-print"></i> Cetak
                                    </a>
                                <?php elseif($hist['status'] == 'selesai'): ?>
                                    <a href="cetak_surat.php?id=<?= $hist['id']; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm" title="Cetak Surat Jalan">
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
                    "zeroRecords": "Tidak ada data riwayat",
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
                "ordering": false // Matikan sorting otomatis agar urutan tanggal DESC tetap terjaga
            });
        }
    });
</script>