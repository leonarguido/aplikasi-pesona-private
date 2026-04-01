<?php
session_start();
require 'config/koneksi.php';

// CEK AKSES: HANYA ADMIN
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'admin gudang' && $_SESSION['role'] != 'super_admin')) {
    header("Location: index.php");
    exit;
}

// =======================================================
// LOGIKA BACKEND PENGEMBALIAN
// =======================================================

// A. SIMPAN DATA KONDISI & TANGGAL KEMBALI (Tahap 1)
if (isset($_POST['simpan_kondisi'])) {
    $id_pinjam = $_POST['id'];
    $tgl_kembali_aktual = $_POST['tgl_dikembalikan'];
    $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi_kembali']);

    // Update data kondisi, tapi status tetap 'selesai' sementara waktu sampai arsip diupload
    $query = "UPDATE tb_peminjaman SET tgl_dikembalikan='$tgl_kembali_aktual', kondisi_kembali='$kondisi' WHERE id='$id_pinjam'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data kondisi tersimpan! Silakan cetak BA Pengembalian lalu upload arsipnya.'); window.location='pengembalian_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}

// B. UPLOAD / EDIT ARSIP PENGEMBALIAN & FOTO BUKTI (Tahap 2 - FINALISASI) - DIPERBARUI
if (isset($_POST['upload_arsip_kembali'])) {
    $id_pinjam = $_POST['id'];
    
    // Ambil data file lama untuk dihapus jika di-replace
    $q_old = mysqli_query($koneksi, "SELECT file_ba_kembali, foto_bukti_kembali FROM tb_peminjaman WHERE id='$id_pinjam'");
    $d_old = mysqli_fetch_assoc($q_old);
    
    $ba_name = $d_old['file_ba_kembali'];
    $foto_name = $d_old['foto_bukti_kembali'];

    // 1. Upload Berita Acara (PDF)
    if (!empty($_FILES['file_ba_kembali']['name'])) {
        // Hapus file lama jika ada
        if ($ba_name && file_exists("assets/arsip/" . $ba_name)) { unlink("assets/arsip/" . $ba_name); }
        
        $ba_tmp = $_FILES['file_ba_kembali']['tmp_name'];
        $ba_name = time() . "_BA_KEMBALI_" . $_FILES['file_ba_kembali']['name'];
        
        if (!is_dir("assets/arsip/")) { mkdir("assets/arsip/", 0777, true); }
        move_uploaded_file($ba_tmp, "assets/arsip/" . $ba_name);
    }

    // 2. Upload Foto Bukti Pengembalian (JPG/PNG)
    if (!empty($_FILES['foto_bukti_kembali']['name'])) {
        // Hapus file lama jika ada
        if ($foto_name && file_exists("assets/arsip/" . $foto_name)) { unlink("assets/arsip/" . $foto_name); }
        
        $foto_tmp = $_FILES['foto_bukti_kembali']['tmp_name'];
        $foto_name = time() . "_FOTO_KEMBALI_" . $_FILES['foto_bukti_kembali']['name'];
        
        if (!is_dir("assets/arsip/")) { mkdir("assets/arsip/", 0777, true); }
        move_uploaded_file($foto_tmp, "assets/arsip/" . $foto_name);
    }
    
    // Update DB: Simpan file (baik baru maupun lama yang tidak diubah) dan pastikan status jadi 'dikembalikan'
    $q_update = "UPDATE tb_peminjaman SET file_ba_kembali='$ba_name', foto_bukti_kembali='$foto_name', status='dikembalikan' WHERE id='$id_pinjam'";
    
    if (mysqli_query($koneksi, $q_update)) {
        echo "<script>alert('Arsip Pengembalian Berhasil Disimpan/Diperbarui!'); window.location='pengembalian_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}

// C. HAPUS DATA (BARU DITAMBAHKAN)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Hapus juga file fisik arsip (peminjaman & pengembalian) sebelum menghapus row data
    $q_del = mysqli_query($koneksi, "SELECT file_ba_signed, foto_bukti, file_ba_kembali, foto_bukti_kembali FROM tb_peminjaman WHERE id='$id'");
    $d_del = mysqli_fetch_assoc($q_del);
    
    // Hapus arsip peminjaman
    if($d_del['file_ba_signed'] && file_exists("assets/arsip/" . $d_del['file_ba_signed'])) { unlink("assets/arsip/" . $d_del['file_ba_signed']); }
    if($d_del['foto_bukti'] && file_exists("assets/arsip/" . $d_del['foto_bukti'])) { unlink("assets/arsip/" . $d_del['foto_bukti']); }
    
    // Hapus arsip pengembalian
    if($d_del['file_ba_kembali'] && file_exists("assets/arsip/" . $d_del['file_ba_kembali'])) { unlink("assets/arsip/" . $d_del['file_ba_kembali']); }
    if($d_del['foto_bukti_kembali'] && file_exists("assets/arsip/" . $d_del['foto_bukti_kembali'])) { unlink("assets/arsip/" . $d_del['foto_bukti_kembali']); }

    mysqli_query($koneksi, "DELETE FROM tb_peminjaman WHERE id='$id'");
    echo "<script>alert('Data Transaksi beserta seluruh arsip berhasil dihapus secara permanen!'); window.location='pengembalian_barang.php';</script>";
}

?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';
$judul_halaman = "Pengembalian Barang";
require 'layout/topbar.php'; 
?>

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Barang yang Sedang Dipinjam & Dikembalikan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Peminjam</th>
                            <th>Target Kembali</th>
                            <th>Status Pengembalian</th>
                            <th>Arsip</th> <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // HANYA AMBIL YANG STATUSNYA SELESAI (Sedang Dipinjam) ATAU DIKEMBALIKAN
                        $query = mysqli_query($koneksi, "
                            SELECT p.*, u.nama AS nama_peminjam, u.nip AS nip_peminjam
                            FROM tb_peminjaman p
                            JOIN tb_user u ON p.user_id = u.id
                            WHERE p.status IN ('selesai', 'dikembalikan')
                            ORDER BY p.id DESC
                        ");
                        
                        while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <b><?= $row['nama_barang']; ?></b> <br>
                                <small class="text-muted"><?= $row['merek']; ?> - NUP: <?= $row['nup']; ?></small>
                            </td>
                            <td>
                                <?= $row['nama_peminjam']; ?> <br>
                                <small>NIP: <?= $row['nip_peminjam']; ?></small>
                            </td>
                            <td>
                                <span class="<?= (strtotime($row['tgl_kembali']) < time() && $row['status'] == 'selesai') ? 'text-danger font-weight-bold' : ''; ?>">
                                    <?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 'selesai' && empty($row['kondisi_kembali'])): ?>
                                    <span class="badge badge-warning">Sedang Dipinjam</span>
                                
                                <?php elseif($row['status'] == 'selesai' && !empty($row['kondisi_kembali'])): ?>
                                    <span class="badge badge-info">Proses Pengembalian<br>(Belum Upload BA)</span>
                                
                                <?php elseif($row['status'] == 'dikembalikan'): ?>
                                    <span class="badge badge-success">Sudah Dikembalikan</span><br>
                                    <small>Kondisi: <?= $row['kondisi_kembali']; ?></small>
                                <?php endif; ?>
                            </td>

                            <td class="text-center" style="white-space: nowrap;">
                                <?php if($row['status'] == 'dikembalikan'): ?>
                                    
                                    <?php if($row['file_ba_kembali']): ?>
                                        <a href="assets/arsip/<?= $row['file_ba_kembali']; ?>" target="_blank" class="btn btn-outline-success btn-sm" title="Lihat PDF Pengembalian">
                                            <i class="fas fa-file-pdf"></i> BA
                                        </a>
                                    <?php endif; ?>

                                    <?php if(!empty($row['foto_bukti_kembali'])): ?>
                                        <a href="assets/arsip/<?= $row['foto_bukti_kembali']; ?>" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat Foto Bukti">
                                            <i class="fas fa-image"></i> Foto
                                        </a>
                                    <?php endif; ?>

                                    <?php if(!$row['file_ba_kembali'] && empty($row['foto_bukti_kembali'])): ?>
                                        -
                                    <?php endif; ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center" style="white-space: nowrap;">
                                
                                <?php if($row['status'] == 'selesai' && empty($row['kondisi_kembali'])): ?>
                                    <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalKembali<?= $row['id']; ?>">
                                        <i class="fas fa-undo"></i> Proses Kembali
                                    </button>

                                <?php elseif($row['status'] == 'selesai' && !empty($row['kondisi_kembali'])): ?>
                                    <a href="cetak_ba_kembali.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-warning btn-sm shadow-sm" title="Cetak BA Pengembalian">
                                        <i class="fas fa-print"></i> Cetak BA
                                    </a>
                                    <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalUploadKembali<?= $row['id']; ?>" title="Upload Arsip">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                
                                <?php elseif($row['status'] == 'dikembalikan'): ?>
                                    
                                    <button class="btn btn-warning btn-sm shadow-sm" data-toggle="modal" data-target="#modalUploadKembali<?= $row['id']; ?>" title="Edit / Upload Ulang Arsip">
                                        <i class="fas fa-pencil-alt"></i> Edit Arsip
                                    </button>

                                    <a href="cetak_ba_kembali.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm" title="Cetak BA Pengembalian">
                                        <i class="fas fa-print"></i> BA
                                    </a>

                                <?php endif; ?>
                                
                                <a href="pengembalian_barang.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen? Seluruh arsip juga akan terhapus.')" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </a>

                            </td>
                        </tr>

                        <div class="modal fade" id="modalKembali<?= $row['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">Cek Fisik Pengembalian Barang</h5>
                                        <button class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <p>Barang: <b><?= $row['nama_barang']; ?></b><br>Peminjam: <b><?= $row['nama_peminjam']; ?></b></p>
                                            <hr>
                                            <div class="form-group">
                                                <label>Tanggal Dikembalikan</label>
                                                <input type="date" name="tgl_dikembalikan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Kondisi Barang Saat Dikembalikan</label>
                                                <select name="kondisi_kembali" class="form-control" required>
                                                    <option value="">-- Pilih Kondisi --</option>
                                                    <option value="Baik dan Lengkap">Baik dan Lengkap</option>
                                                    <option value="Rusak Ringan">Rusak Ringan (Bisa Diperbaiki)</option>
                                                    <option value="Rusak Berat">Rusak Berat</option>
                                                    <option value="Hilang / Tidak Lengkap">Hilang / Tidak Lengkap</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="simpan_kondisi" class="btn btn-primary">Simpan Data & Lanjut Cetak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalUploadKembali<?= $row['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header <?= ($row['status'] == 'dikembalikan') ? 'bg-warning' : 'bg-success'; ?> text-white">
                                        <h5 class="modal-title"><?= ($row['status'] == 'dikembalikan') ? 'Edit Arsip Pengembalian' : 'Finalisasi Pengembalian'; ?></h5>
                                        <button class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            
                                            <div class="alert alert-info small">
                                                Pastikan Berita Acara Pengembalian sudah dicetak dan ditandatangani oleh Admin Gudang dan Staf. <br>
                                                <b>Catatan:</b> Kosongkan form input file di bawah ini jika tidak ingin mengubah file yang sudah ada sebelumnya.
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>
                                                    Upload BA Pengembalian (PDF)
                                                    <?= $row['file_ba_kembali'] ? '<span class="text-success small font-weight-bold"><i class="fas fa-check-circle"></i> Sudah diupload</span>' : ''; ?>
                                                </label>
                                                <input type="file" name="file_ba_kembali" class="form-control-file" accept=".pdf">
                                            </div>

                                            <div class="form-group">
                                                <label>
                                                    Foto Bukti Pengembalian (JPG/PNG)
                                                    <?= $row['foto_bukti_kembali'] ? '<span class="text-success small font-weight-bold"><i class="fas fa-check-circle"></i> Sudah diupload</span>' : ''; ?>
                                                </label>
                                                <input type="file" name="foto_bukti_kembali" class="form-control-file" accept="image/*">
                                                <small class="text-muted">Foto barang saat dikembalikan sebagai bukti kondisi fisik.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="upload_arsip_kembali" class="btn <?= ($row['status'] == 'dikembalikan') ? 'btn-warning' : 'btn-success'; ?>">
                                                <?= ($row['status'] == 'dikembalikan') ? 'Simpan Perubahan' : 'Selesaikan Pengembalian'; ?>
                                            </button>
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
        $('#dataTable').DataTable();
    });
</script>