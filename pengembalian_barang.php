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

// B. UPLOAD ARSIP PENGEMBALIAN & FOTO BUKTI (Tahap 2 - FINALISASI)
if (isset($_POST['upload_arsip_kembali'])) {
    $id_pinjam = $_POST['id'];
    
    // 1. Upload Berita Acara (PDF)
    $ba_name = null;
    if (!empty($_FILES['file_ba_kembali']['name'])) {
        $ba_tmp = $_FILES['file_ba_kembali']['tmp_name'];
        $ba_name = time() . "_BA_KEMBALI_" . $_FILES['file_ba_kembali']['name'];
        
        if (!is_dir("assets/arsip/")) { mkdir("assets/arsip/", 0777, true); }
        move_uploaded_file($ba_tmp, "assets/arsip/" . $ba_name);
    }

    // 2. Upload Foto Bukti Pengembalian (JPG/PNG) - BARU DITAMBAHKAN
    $foto_name = null;
    if (!empty($_FILES['foto_bukti_kembali']['name'])) {
        $foto_tmp = $_FILES['foto_bukti_kembali']['tmp_name'];
        $foto_name = time() . "_FOTO_KEMBALI_" . $_FILES['foto_bukti_kembali']['name'];
        
        if (!is_dir("assets/arsip/")) { mkdir("assets/arsip/", 0777, true); }
        move_uploaded_file($foto_tmp, "assets/arsip/" . $foto_name);
    }
    
    // Update DB: Simpan kedua file dan ubah status jadi 'dikembalikan'
    $q_update = "UPDATE tb_peminjaman SET file_ba_kembali='$ba_name', foto_bukti_kembali='$foto_name', status='dikembalikan' WHERE id='$id_pinjam'";
    
    if (mysqli_query($koneksi, $q_update)) {
        echo "<script>alert('Pengembalian Selesai! Arsip dan Foto Bukti berhasil disimpan.'); window.location='pengembalian_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
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
                            <th>Aksi</th>
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
                                    
                                    <?php if($row['file_ba_kembali']): ?>
                                        <a href="assets/arsip/<?= $row['file_ba_kembali']; ?>" target="_blank" class="btn btn-outline-success btn-sm" title="Lihat BA Pengembalian">
                                            <i class="fas fa-file-pdf"></i> BA
                                        </a>
                                    <?php endif; ?>

                                    <?php if(!empty($row['foto_bukti_kembali'])): ?>
                                        <a href="assets/arsip/<?= $row['foto_bukti_kembali']; ?>" target="_blank" class="btn btn-outline-info btn-sm" title="Lihat Foto Bukti">
                                            <i class="fas fa-image"></i> Foto
                                        </a>
                                    <?php endif; ?>

                                <?php endif; ?>

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
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Finalisasi Pengembalian</h5>
                                        <button class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <div class="alert alert-info small">
                                                Pastikan Berita Acara Pengembalian sudah dicetak dan ditandatangani oleh Admin Gudang dan Staf.
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Upload BA Pengembalian (PDF)</label>
                                                <input type="file" name="file_ba_kembali" class="form-control-file" accept=".pdf" required>
                                            </div>

                                            <div class="form-group">
                                                <label>Foto Bukti Pengembalian (JPG/PNG)</label>
                                                <input type="file" name="foto_bukti_kembali" class="form-control-file" accept="image/*" required>
                                                <small class="text-muted">Foto barang saat dikembalikan sebagai bukti kondisi fisik.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="upload_arsip_kembali" class="btn btn-success">Selesaikan Pengembalian</button>
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