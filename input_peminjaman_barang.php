<?php
session_start();
require 'config/koneksi.php';

// =======================================================
// CEK AKSES: HANYA ADMIN YANG BOLEH AKSES HALAMAN INI
// =======================================================
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'admin gudang' && $_SESSION['role'] != 'super_admin')) {
    header("Location: index.php");
    exit;
}

$id_admin_login = $_SESSION['user_id'];

// 1. AMBIL DATA STAF (Untuk Dropdown Peminjam)
$list_pegawai = [];
// Ambil user yang role-nya BUKAN admin
$q_pgw = mysqli_query($koneksi, "SELECT id, nip, nama FROM tb_user WHERE role != 'admin' AND role != 'super_admin' ORDER BY nama ASC");
while ($p = mysqli_fetch_assoc($q_pgw)) {
    $list_pegawai[] = $p;
}

// =======================================================
// LOGIKA BACKEND (CRUD)
// =======================================================

// A. TAMBAH PENGAJUAN BARU (Admin Input)
if (isset($_POST['ajukan_pinjam'])) {
    $nama_barang    = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $merek          = mysqli_real_escape_string($koneksi, $_POST['merek']);
    $kode_barang    = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nup            = mysqli_real_escape_string($koneksi, $_POST['nup']);
    $tahun_perolehan= mysqli_real_escape_string($koneksi, $_POST['tahun_perolehan']);
    
    $id_penerima    = $_POST['id_penerima']; // ID Staf
    $tgl_serah      = $_POST['tgl_serah_terima'];
    
    // LOGIKA TANGGAL KEMBALI
    if (isset($_POST['jangka_panjang'])) {
        $tgl_kembali_sql = "NULL"; 
    } else {
        $tgl_kembali_input = $_POST['tgl_kembali'];
        $tgl_kembali_sql = "'$tgl_kembali_input'";
    }

    if(empty($id_penerima) || empty($nama_barang)) {
        echo "<script>alert('Harap lengkapi nama barang dan penerima!');</script>";
    } else {
        $query = "INSERT INTO tb_peminjaman 
                  (admin_id, user_id, nama_barang, merek, kode_barang, nup, tahun_perolehan, tgl_serah_terima, tgl_kembali, status)
                  VALUES 
                  ('$id_admin_login', '$id_penerima', '$nama_barang', '$merek', '$kode_barang', '$nup', '$tahun_perolehan', '$tgl_serah', $tgl_kembali_sql, 'menunggu_persetujuan')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Pengajuan Berhasil Dibuat! Menunggu persetujuan staf.'); window.location='input_peminjaman_barang.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// B. EDIT PENGAJUAN (UPDATE DATA) - BARU DITAMBAHKAN
if (isset($_POST['update_pinjam'])) {
    $id_edit        = $_POST['id_edit'];
    $nama_barang    = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $merek          = mysqli_real_escape_string($koneksi, $_POST['merek']);
    $kode_barang    = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
    $nup            = mysqli_real_escape_string($koneksi, $_POST['nup']);
    $tahun_perolehan= mysqli_real_escape_string($koneksi, $_POST['tahun_perolehan']);
    
    $id_penerima    = $_POST['id_penerima']; 
    $tgl_serah      = $_POST['tgl_serah_terima'];
    
    // LOGIKA TANGGAL KEMBALI (SAMA SEPERTI INPUT)
    if (isset($_POST['jangka_panjang'])) {
        $tgl_kembali_sql = "NULL"; 
    } else {
        $tgl_kembali_input = $_POST['tgl_kembali'];
        $tgl_kembali_sql = "'$tgl_kembali_input'";
    }

    $query_update = "UPDATE tb_peminjaman SET 
                     user_id='$id_penerima', 
                     nama_barang='$nama_barang', 
                     merek='$merek', 
                     kode_barang='$kode_barang', 
                     nup='$nup', 
                     tahun_perolehan='$tahun_perolehan', 
                     tgl_serah_terima='$tgl_serah', 
                     tgl_kembali=$tgl_kembali_sql 
                     WHERE id='$id_edit'";

    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>alert('Data Pengajuan Berhasil Diupdate!'); window.location='input_peminjaman_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal Update: " . mysqli_error($koneksi) . "');</script>";
    }
}

// C. HAPUS DATA
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM tb_peminjaman WHERE id='$id'");
    echo "<script>alert('Data Peminjaman Dihapus!'); window.location='input_peminjaman_barang.php';</script>";
}

// D. UPLOAD ARSIP (FINALISASI)
if (isset($_POST['upload_arsip'])) {
    $id_pinjam = $_POST['id'];
    
    // Upload Berita Acara (PDF)
    $ba_name = null;
    if (!empty($_FILES['file_ba']['name'])) {
        $ba_tmp = $_FILES['file_ba']['tmp_name'];
        $ba_name = time() . "_BA_" . $_FILES['file_ba']['name'];
        if (!is_dir("assets/arsip/")) { mkdir("assets/arsip/", 0777, true); }
        move_uploaded_file($ba_tmp, "assets/arsip/" . $ba_name);
    }
    
    // Upload Foto Bukti (JPG)
    $foto_name = null;
    if (!empty($_FILES['foto_bukti']['name'])) {
        $foto_tmp = $_FILES['foto_bukti']['tmp_name'];
        $foto_name = time() . "_FOTO_" . $_FILES['foto_bukti']['name'];
        move_uploaded_file($foto_tmp, "assets/arsip/" . $foto_name);
    }
    
    // Update DB -> Status Selesai
    $q_update = "UPDATE tb_peminjaman SET file_ba_signed='$ba_name', foto_bukti='$foto_name', status='selesai' WHERE id='$id_pinjam'";
    
    if (mysqli_query($koneksi, $q_update)) {
        echo "<script>alert('Arsip Berhasil Diupload! Transaksi Selesai.'); window.location='input_peminjaman_barang.php';</script>";
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';
$judul_halaman = "Input Peminjaman Barang";
require 'layout/topbar.php'; 
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #d1d3e2; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
    .nowrap { white-space: nowrap; }
</style>

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi Peminjaman</h6>
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalAjukan">
                <i class="fas fa-plus fa-sm text-white-50"></i> Buat Pengajuan Baru
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Peminjam (Staf)</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                            <th>Arsip</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "
                            SELECT p.*, u.nama AS nama_peminjam, u.nip AS nip_peminjam
                            FROM tb_peminjaman p
                            JOIN tb_user u ON p.user_id = u.id
                            ORDER BY p.id DESC
                        ");
                        
                        while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <b><?= $row['nama_barang']; ?></b> <br>
                                <small class="text-muted"><?= $row['merek']; ?> - <?= $row['nup']; ?></small>
                            </td>
                            <td>
                                <?= $row['nama_peminjam']; ?> <br>
                                <small>NIP: <?= $row['nip_peminjam']; ?></small>
                            </td>
                            <td>
                                <small>Serah: <?= date('d/m/Y', strtotime($row['tgl_serah_terima'])); ?></small><br>
                                
                                <?php if($row['tgl_kembali'] == NULL): ?>
                                    <span class="badge badge-light text-primary border border-primary small">Jangka Panjang</span>
                                <?php else: ?>
                                    <small class="text-danger">Kembali: <?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 'menunggu_persetujuan'): ?>
                                    <span class="badge badge-warning">Menunggu Staf</span>
                                <?php elseif($row['status'] == 'disetujui'): ?>
                                    <span class="badge badge-info">Disetujui (Belum Tanda Tangan)</span>
                                <?php elseif($row['status'] == 'selesai'): ?>
                                    <span class="badge badge-success">Selesai / Aktif</span>
                                <?php elseif($row['status'] == 'dikembalikan'): ?>
                                    <span class="badge badge-secondary">Sudah Dikembalikan</span>
                                <?php elseif($row['status'] == 'ditolak'): ?>
                                    <span class="badge badge-danger">Ditolak Staf</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 'selesai' || $row['status'] == 'dikembalikan'): ?>
                                    <?php if($row['file_ba_signed']): ?>
                                        <a href="assets/arsip/<?= $row['file_ba_signed']; ?>" target="_blank" class="btn btn-xs btn-primary" title="Lihat Berita Acara"><i class="fas fa-file-pdf"></i></a>
                                    <?php endif; ?>
                                    <?php if($row['foto_bukti']): ?>
                                        <a href="assets/arsip/<?= $row['foto_bukti']; ?>" target="_blank" class="btn btn-xs btn-success" title="Lihat Foto"><i class="fas fa-image"></i></a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center nowrap">
                                <?php if($row['status'] == 'menunggu_persetujuan'): ?>
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEdit<?= $row['id']; ?>" title="Edit Pengajuan">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <a href="input_peminjaman_barang.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Batalkan pengajuan ini?')" title="Batalkan">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                
                                <?php elseif($row['status'] == 'disetujui'): ?>
                                    
                                    <a href="cetak_berita_acara.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-warning btn-sm shadow-sm" title="Cetak Berita Acara">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalUpload<?= $row['id']; ?>" title="Upload Arsip">
                                        <i class="fas fa-upload"></i>
                                    </button>

                                <?php endif; ?>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEdit<?= $row['id']; ?>">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Pengajuan Barang</h5>
                                        <button class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_edit" value="<?= $row['id']; ?>">
                                            <div class="row">
                                                <div class="col-md-6 border-right">
                                                    <h6 class="font-weight-bold text-primary mb-3">Data Barang</h6>
                                                    <div class="form-group">
                                                        <label>Nama Barang</label>
                                                        <input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Merek</label>
                                                        <input type="text" name="merek" class="form-control" value="<?= $row['merek']; ?>">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>Kode Barang</label>
                                                                <input type="text" name="kode_barang" class="form-control" value="<?= $row['kode_barang']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>NUP</label>
                                                                <input type="text" name="nup" class="form-control" value="<?= $row['nup']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tahun Perolehan</label>
                                                        <input type="number" name="tahun_perolehan" class="form-control" value="<?= $row['tahun_perolehan']; ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <h6 class="font-weight-bold text-primary mb-3">Data Peminjaman</h6>
                                                    
                                                    <div class="form-group">
                                                        <label>Yang Menerima (Staf)</label>
                                                        <select name="id_penerima" class="form-control select2-modal-edit" style="width: 100%" required>
                                                            <option value="">-- Pilih Staf --</option>
                                                            <?php foreach($list_pegawai as $pgw): ?>
                                                                <option value="<?= $pgw['id']; ?>" <?= ($pgw['id'] == $row['user_id']) ? 'selected' : ''; ?>>
                                                                    <?= $pgw['nama']; ?> (NIP: <?= $pgw['nip']; ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Tanggal Serah Terima</label>
                                                        <input type="date" name="tgl_serah_terima" class="form-control" value="<?= $row['tgl_serah_terima']; ?>" required>
                                                    </div>

                                                    <div class="form-group bg-light p-2 rounded border">
                                                        <label>Tanggal Kembali</label>
                                                        <div class="custom-control custom-checkbox mb-2">
                                                            <input type="checkbox" class="custom-control-input" id="checkEdit<?= $row['id']; ?>" name="jangka_panjang" value="1" <?= ($row['tgl_kembali'] == NULL) ? 'checked' : ''; ?>>
                                                            <label class="custom-control-label small text-primary font-weight-bold" for="checkEdit<?= $row['id']; ?>">
                                                                Peminjaman Jangka Panjang
                                                            </label>
                                                        </div>
                                                        <input type="date" name="tgl_kembali" id="inputTglEdit<?= $row['id']; ?>" class="form-control" value="<?= $row['tgl_kembali']; ?>" <?= ($row['tgl_kembali'] == NULL) ? 'disabled' : ''; ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="update_pinjam" class="btn btn-warning">Update Perubahan</button>
                                        </div>
                                    </form>
                                    
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const check = document.getElementById('checkEdit<?= $row['id']; ?>');
                                            const input = document.getElementById('inputTglEdit<?= $row['id']; ?>');
                                            
                                            if(check && input) {
                                                check.addEventListener('change', function() {
                                                    if(this.checked) {
                                                        input.value = '';
                                                        input.disabled = true;
                                                        input.removeAttribute('required');
                                                    } else {
                                                        input.disabled = false;
                                                        input.setAttribute('required', '');
                                                    }
                                                });
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalUpload<?= $row['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">Finalisasi Peminjaman</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <div class="alert alert-info small">
                                                Pastikan Berita Acara sudah ditandatangani basah dan distempel sebelum diupload.
                                            </div>
                                            <div class="form-group">
                                                <label>Scan Berita Acara (PDF)</label>
                                                <input type="file" name="file_ba" class="form-control-file" accept=".pdf" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Foto Bukti Penyerahan (JPG/PNG)</label>
                                                <input type="file" name="foto_bukti" class="form-control-file" accept="image/*" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="upload_arsip" class="btn btn-success">Simpan & Selesai</button>
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

<div class="modal fade" id="modalAjukan">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Form Peminjaman Barang (Admin)</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 border-right">
                            <h6 class="font-weight-bold text-primary mb-3">Data Barang</h6>
                            <div class="form-group">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Laptop / Proyektor" required>
                            </div>
                            <div class="form-group">
                                <label>Merek</label>
                                <input type="text" name="merek" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Kode Barang</label>
                                        <input type="text" name="kode_barang" class="form-control">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>NUP</label>
                                        <input type="text" name="nup" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Tahun Perolehan</label>
                                <input type="number" name="tahun_perolehan" class="form-control" placeholder="YYYY">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-primary mb-3">Data Peminjaman</h6>
                            
                            <div class="form-group">
                                <label>Yang Menyerahkan (Admin)</label>
                                <input type="text" class="form-control" value="Saya (Admin Gudang)" readonly>
                            </div>

                            <div class="form-group">
                                <label>Yang Menerima (Staf)</label>
                                <select name="id_penerima" class="form-control select2-modal" style="width: 100%" required>
                                    <option value="">-- Pilih Staf --</option>
                                    <?php foreach($list_pegawai as $pgw): ?>
                                        <option value="<?= $pgw['id']; ?>">
                                            <?= $pgw['nama']; ?> (NIP: <?= $pgw['nip']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Serah Terima</label>
                                <input type="date" name="tgl_serah_terima" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                            </div>

                            <div class="form-group bg-light p-2 rounded border">
                                <label>Tanggal Kembali</label>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="checkJangkaPanjang" name="jangka_panjang" value="1">
                                    <label class="custom-control-label small text-primary font-weight-bold" for="checkJangkaPanjang">
                                        Peminjaman Jangka Panjang (Laptop/Kendaraan)
                                    </label>
                                </div>
                                <input type="date" name="tgl_kembali" id="inputTglKembali" class="form-control" required>
                                <small class="text-muted" id="noteTgl">Wajib diisi untuk peminjaman sementara.</small>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="ajukan_pinjam" class="btn btn-primary">Ajukan ke Staf</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
        
        // Aktifkan Select2 di Modal Baru
        $('.select2-modal').select2({
            dropdownParent: $('#modalAjukan'),
            placeholder: "Cari Nama Staf...",
            allowClear: true
        });

        // Script Checkbox Modal Baru
        $('#checkJangkaPanjang').change(function() {
            if(this.checked) {
                $('#inputTglKembali').val('').prop('disabled', true).removeAttr('required');
                $('#noteTgl').text('Barang digunakan dalam jangka panjang.').addClass('text-primary font-weight-bold');
            } else {
                $('#inputTglKembali').prop('disabled', false).attr('required', true);
                $('#noteTgl').text('Wajib diisi untuk peminjaman sementara.').removeClass('text-primary font-weight-bold');
            }
        });
    });
</script>