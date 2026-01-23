<?php
session_start();
require 'config/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'pimpinan') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.location='index.php';</script>";
    exit;
}

// =======================================================
// LOGIKA PHP (CRUD & IMPORT) - (TETAP SAMA)
// =======================================================

// A. LOGIKA IMPORT CSV
if (isset($_POST['import_excel'])) {
    if (isset($_FILES['file_excel']['name']) && $_FILES['file_excel']['name'] != "") {
        $filename = $_FILES['file_excel']['tmp_name'];
        $ext = pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION);
        if ($ext != 'csv') {
            echo "<script>alert('Format file harus .CSV!');</script>";
        } else {
            $file = fopen($filename, "r");
            $count = 0;
            fgetcsv($file); 
            while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                $kode   = mysqli_real_escape_string($koneksi, $data[0]);
                $nama   = mysqli_real_escape_string($koneksi, $data[1]);
                $satuan = mysqli_real_escape_string($koneksi, $data[2]);
                $stok   = (int) $data[3];
                $desc   = mysqli_real_escape_string($koneksi, $data[4]);
                $cek = mysqli_query($koneksi, "SELECT kode_barang FROM tb_barang_bergerak WHERE kode_barang = '$kode'");
                if (mysqli_num_rows($cek) == 0 && !empty($kode)) {
                    $query = "INSERT INTO tb_barang_bergerak (kode_barang, nama_barang, satuan, stok, keterangan) VALUES ('$kode', '$nama', '$satuan', '$stok', '$desc')";
                    mysqli_query($koneksi, $query);
                    $count++;
                }
            }
            fclose($file);
            echo "<script>alert('Berhasil mengimpor $count data barang!'); window.location='data_barang.php';</script>";
        }
    } else {
        echo "<script>alert('Pilih file terlebih dahulu!');</script>";
    }
}

// B. Tambah Manual
if (isset($_POST['tambah'])) {
    $kode   = $_POST['kode_barang'];
    $nama   = $_POST['nama_barang'];
    $satuan = $_POST['satuan'];
    $desc   = $_POST['keterangan']; 
    $stok   = $_POST['stok'];
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE kode_barang = '$kode'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Kode Barang sudah ada!');</script>";
    } else {
        $query = "INSERT INTO tb_barang_bergerak (kode_barang, nama_barang, satuan, keterangan, stok) VALUES ('$kode', '$nama', '$satuan', '$desc', '$stok')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Barang berhasil ditambahkan!'); window.location='data_barang.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

// C. Edit Barang
if (isset($_POST['edit'])) {
    $id     = $_POST['id'];
    $nama   = $_POST['nama_barang'];
    $satuan = $_POST['satuan'];
    $desc   = $_POST['keterangan'];
    $stok   = $_POST['stok'];
    $query = "UPDATE tb_barang_bergerak SET nama_barang='$nama', satuan='$satuan', keterangan='$desc', stok='$stok' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='data_barang.php';</script>";
    }
}

// D. Hapus Barang
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM tb_barang_bergerak WHERE id = '$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Barang berhasil dihapus!'); window.location='data_barang.php';</script>";
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// SET JUDUL KE TOPBAR
$judul_halaman = "Data Barang Bergerak";
$deskripsi_halaman = "Kelola stok barang, tambah manual, atau import via Excel.";

require 'layout/topbar.php'; 
?>

<div class="container-fluid">

    <div class="card shadow mb-4">
        
        <div class="card-header py-3 border-bottom-primary d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-boxes"></i> Daftar Stok Barang</h6>
            
            <div>
                <button class="btn btn-primary btn-sm shadow-sm mr-1" data-toggle="modal" data-target="#tambahModal">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Manual
                </button>
                <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-file-excel fa-sm text-white-50"></i> Import
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th class="text-center">Stok</th>
                            <th>Keterangan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = "SELECT * FROM tb_barang_bergerak ORDER BY nama_barang ASC";
                        $data = mysqli_query($koneksi, $query);
                        
                        while ($row = mysqli_fetch_assoc($data)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['kode_barang']; ?></td>
                            <td class="font-weight-bold text-primary"><?= $row['nama_barang']; ?></td>
                            <td><?= $row['satuan']; ?></td>
                            
                            <td class="text-center">
                                <?php if($row['stok'] > 0): ?>
                                    <span class="badge badge-success p-2" style="min-width: 50px;">
                                        <?= $row['stok']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger p-2">Habis</span>
                                <?php endif; ?>
                            </td>
                            
                            <td><small class="text-muted"><?= $row['keterangan']; ?></small></td>
                            
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $row['id']; ?>" title="Edit">
                                    <i class="fas fa-edit text-white"></i>
                                </button>
                                <a href="data_barang.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus barang ini?');" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit Barang</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <div class="form-group">
                                                <label>Nama Barang</label>
                                                <input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Stok</label>
                                                        <input type="number" name="stok" class="form-control" value="<?= $row['stok']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Satuan</label>
                                                        <select name="satuan" class="form-control">
                                                            <option value="Unit" <?= ($row['satuan']=='Unit')?'selected':''; ?>>Unit</option>
                                                            <option value="Pcs" <?= ($row['satuan']=='Pcs')?'selected':''; ?>>Pcs</option>
                                                            <option value="Buah" <?= ($row['satuan']=='Buah')?'selected':''; ?>>Buah</option>
                                                            <option value="Rim" <?= ($row['satuan']=='Rim')?'selected':''; ?>>Rim</option>
                                                            <option value="Box" <?= ($row['satuan']=='Box')?'selected':''; ?>>Box</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Keterangan</label>
                                                <textarea name="keterangan" class="form-control"><?= $row['keterangan']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn btn-warning">Update</button>
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

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import Data Barang</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Cara Penggunaan:</strong>
                        <ol class="pl-3 mb-0 small">
                            <li>Download template Excel (CSV).</li>
                            <li>Isi data tanpa mengubah judul kolom.</li>
                            <li>Simpan sebagai <strong>.CSV</strong>.</li>
                        </ol>
                        <a href="template_barang.php" class="btn btn-sm btn-light mt-2 text-success font-weight-bold"><i class="fas fa-download"></i> Download Template</a>
                    </div>
                    
                    <div class="form-group">
                        <label>Pilih File CSV</label>
                        <input type="file" name="file_excel" class="form-control-file" required accept=".csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="import_excel" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Barang Baru</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Barang (Unik)</label>
                        <input type="text" name="kode_barang" class="form-control" placeholder="Cth: KTS-001" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Satuan</label>
                                <select name="satuan" class="form-control">
                                    <option value="Pcs">Pcs</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Rim">Rim</option>
                                    <option value="Box">Box</option>
                                    <option value="Buah">Buah</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stok Awal</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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