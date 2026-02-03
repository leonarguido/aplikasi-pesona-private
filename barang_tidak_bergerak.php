<?php
session_start();
require 'config/koneksi.php';

// Cek Login & Role
// KITA TAMBAHKAN 'pimpinan' DI SINI AGAR BISA AKSES
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'admin gudang' && $_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'pimpinan')) {
    header("Location: index.php");
    exit;
}

// =======================================================
// 1. AMBIL DATA PEGAWAI (UNTUK DROPDOWN)
// =======================================================
$list_pegawai = [];
$q_pgw = mysqli_query($koneksi, "SELECT nip, nama FROM tb_user WHERE nip IS NOT NULL AND nip != '' ORDER BY nama ASC");
while ($p = mysqli_fetch_assoc($q_pgw)) {
    $list_pegawai[] = $p;
}

// =======================================================
// LOGIKA BACKEND (CRUD) - HANYA JALAN KALAU BUKAN PIMPINAN
// =======================================================
if ($_SESSION['role'] != 'pimpinan') {

    // A. TAMBAH DATA
    if (isset($_POST['tambah'])) {
        $nip    = mysqli_real_escape_string($koneksi, $_POST['nip']);
        $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
        $kode   = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
        $merk   = mysqli_real_escape_string($koneksi, $_POST['merk_barang']);
        $satuan = $_POST['satuan'];
        $jumlah = $_POST['jumlah'];
        $ket    = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
        
        $nama_file = null;
        if (!empty($_FILES['berkas']['name'])) {
            $file_tmp  = $_FILES['berkas']['tmp_name'];
            $file_name = time() . "_" . $_FILES['berkas']['name'];
            if (!is_dir("assets/img/berkas/")) { mkdir("assets/img/berkas/", 0777, true); }
            move_uploaded_file($file_tmp, "assets/img/berkas/" . $file_name);
            $nama_file = $file_name;
        }

        $q = "INSERT INTO tb_barang_tidak_bergerak (nip, nama_barang, kode_barang, merk_barang, satuan, jumlah, keterangan, berkas) 
            VALUES ('$nip', '$nama', '$kode', '$merk', '$satuan', '$jumlah', '$ket', '$nama_file')";

        if (mysqli_query($koneksi, $q)) {
            echo "<script>alert('Data Berhasil Ditambahkan!'); window.location='barang_tidak_bergerak.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
        }
    }

    // B. EDIT DATA
    if (isset($_POST['edit'])) {
        $id     = $_POST['id'];
        $nip    = mysqli_real_escape_string($koneksi, $_POST['nip']);
        $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
        $kode   = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
        $merk   = mysqli_real_escape_string($koneksi, $_POST['merk_barang']);
        $satuan = $_POST['satuan'];
        $jumlah = $_POST['jumlah'];
        $ket    = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

        $query_file = "";
        if (!empty($_FILES['berkas']['name'])) {
            $file_tmp  = $_FILES['berkas']['tmp_name'];
            $file_name = time() . "_" . $_FILES['berkas']['name'];
            move_uploaded_file($file_tmp, "assets/img/berkas/" . $file_name);
            $query_file = ", berkas='$file_name'";
        }

        $q = "UPDATE tb_barang_tidak_bergerak SET 
            nip='$nip', nama_barang='$nama', kode_barang='$kode', merk_barang='$merk', 
            satuan='$satuan', jumlah='$jumlah', keterangan='$ket' $query_file 
            WHERE id='$id'";

        if (mysqli_query($koneksi, $q)) {
            echo "<script>alert('Data Berhasil Diupdate!'); window.location='barang_tidak_bergerak.php';</script>";
        }
    }

    // C. HAPUS DATA
    if (isset($_GET['hapus'])) {
        $id = $_GET['hapus'];
        $q_cek = mysqli_query($koneksi, "SELECT berkas FROM tb_barang_tidak_bergerak WHERE id='$id'");
        $d_cek = mysqli_fetch_assoc($q_cek);
        if($d_cek['berkas'] && file_exists("assets/img/berkas/".$d_cek['berkas'])){
            unlink("assets/img/berkas/".$d_cek['berkas']);
        }
        mysqli_query($koneksi, "DELETE FROM tb_barang_tidak_bergerak WHERE id='$id'");
        echo "<script>alert('Data Dihapus!'); window.location='barang_tidak_bergerak.php';</script>";
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// Judul Halaman
$judul_halaman = "Data Barang Tidak Bergerak";
require 'layout/topbar.php'; 
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #d1d3e2; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
    .select2-container { z-index: 99999; }
</style>

<div class="container-fluid">

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Inventaris Aset Tetap</h6>
            
            <?php if($_SESSION['role'] != 'pimpinan'): ?>
                <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Aset
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Penanggung Jawab</th>
                            <th>Kode & Nama Barang</th>
                            <th>Merk</th>
                            <th>Jumlah</th>
                            <th>Berita Acara</th>
                            <?php if($_SESSION['role'] != 'pimpinan'): ?>
                                <th class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "
                            SELECT b.*, u.nama AS nama_pegawai 
                            FROM tb_barang_tidak_bergerak b 
                            LEFT JOIN tb_user u ON b.nip = u.nip 
                            ORDER BY b.nama_barang ASC
                        ");
                        
                        while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <b><?= !empty($row['nama_pegawai']) ? $row['nama_pegawai'] : 'Nama Tidak Ditemukan'; ?></b>
                                <br>
                                <small class="text-muted">NIP: <?= !empty($row['nip']) ? $row['nip'] : '-'; ?></small>
                            </td>
                            <td>
                                <b><?= $row['nama_barang']; ?></b><br>
                                <small class="text-muted"><?= $row['kode_barang'] ? $row['kode_barang'] : 'Tanpa Kode'; ?></small>
                            </td>
                            <td><?= $row['merk_barang'] ? $row['merk_barang'] : '-'; ?></td>
                            <td><?= $row['jumlah']; ?> <?= $row['satuan']; ?></td>
                            <td class="text-center">
                                <?php if($row['berkas']): ?>
                                    <a href="assets/img/berkas/<?= $row['berkas']; ?>" target="_blank" class="btn btn-sm btn-success shadow-sm" title="Lihat Berkas">
                                        <i class="fas fa-file-alt"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if($_SESSION['role'] != 'pimpinan'): ?>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#modalEdit<?= $row['id']; ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="barang_tidak_bergerak.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>

                        <?php if($_SESSION['role'] != 'pimpinan'): ?>
                        <div class="modal fade" id="modalEdit<?= $row['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Aset</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            
                                            <div class="form-group">
                                                <label>Penanggung Jawab (NIP)</label>
                                                <select name="nip" class="form-control select2-edit" style="width:100%" required>
                                                    <option value="">-- Pilih Pegawai --</option>
                                                    <?php foreach($list_pegawai as $pgw): ?>
                                                        <option value="<?= $pgw['nip']; ?>" <?= ($pgw['nip'] == $row['nip']) ? 'selected' : ''; ?>>
                                                            <?= $pgw['nip']; ?> (<?= $pgw['nama']; ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group"><label>Kode Barang</label><input type="text" name="kode_barang" class="form-control" value="<?= $row['kode_barang']; ?>"></div>
                                            <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" required></div>
                                            <div class="form-group"><label>Merk</label><input type="text" name="merk_barang" class="form-control" value="<?= $row['merk_barang']; ?>"></div>
                                            <div class="row">
                                                <div class="col-md-6"><div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" value="<?= $row['jumlah']; ?>" required></div></div>
                                                <div class="col-md-6"><div class="form-group"><label>Satuan</label><input type="text" name="satuan" class="form-control" value="<?= $row['satuan']; ?>" required></div></div>
                                            </div>
                                            <div class="form-group"><label>Keterangan / Lokasi</label><textarea name="keterangan" class="form-control"><?= $row['keterangan']; ?></textarea></div>
                                            <div class="form-group"><label>Update Berita Acara</label><br><input type="file" name="berkas" class="form-control-file"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn btn-warning">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if($_SESSION['role'] != 'pimpinan'): ?>
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Aset Baru</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Penanggung Jawab (NIP)</label>
                        <select name="nip" id="selectNipTambah" class="form-control" style="width:100%" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach($list_pegawai as $pgw): ?>
                                <option value="<?= $pgw['nip']; ?>">
                                    <?= $pgw['nip']; ?> (<?= $pgw['nama']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Kode Barang (Opsional)</label><input type="text" name="kode_barang" class="form-control" placeholder="Contoh: INV-LAP-01"></div>
                    <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" class="form-control" required placeholder="Contoh: Laptop"></div>
                    <div class="form-group"><label>Merk</label><input type="text" name="merk_barang" class="form-control" placeholder="Contoh: Asus / Lenovo"></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" required></div></div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Satuan</label>
                            <select name="satuan" class="form-control">
                                <option value="Unit">Unit</option><option value="Pcs">Pcs</option><option value="Set">Set</option><option value="Buah">Buah</option>
                            </select></div>
                        </div>
                    </div>
                    <div class="form-group"><label>Keterangan / Lokasi</label><textarea name="keterangan" class="form-control" placeholder="Contoh: Di Ruang Rapat"></textarea></div>
                    <div class="form-group"><label>Upload Berita Acara</label><input type="file" name="berkas" class="form-control-file"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require 'layout/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        // Aktifkan Select2 HANYA jika elemennya ada (untuk admin)
        if ($('#selectNipTambah').length) {
            $('#selectNipTambah').select2({
                dropdownParent: $('#modalTambah'),
                placeholder: "Ketik NIP atau Nama Pegawai...",
                allowClear: true
            });
        }
        
        if ($('.select2-edit').length) {
            $('.select2-edit').each(function() {
                $(this).select2({
                    dropdownParent: $(this).closest('.modal'),
                    placeholder: "Pilih Pegawai...",
                    allowClear: true
                });
            });
        }
    });
</script>