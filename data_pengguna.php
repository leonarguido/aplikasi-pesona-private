<?php
session_start();
require 'config/koneksi.php';

// CEK AKSES: Hanya Super Admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek Role Super Admin
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'super admin')) {
    echo "<script>alert('Akses Ditolak! Anda bukan Super Admin.'); window.location='index.php';</script>";
    exit;
}

// =======================================================
// LOGIKA BACKEND (CRUD)
// =======================================================

// A. TAMBAH USER BARU
if (isset($_POST['tambah_user'])) {
    // Baris debug dihapus agar data bisa tersimpan
    
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role']; // Sekarang value-nya akan 'staff', 'admin gudang', dll

    // Upload Tanda Tangan
    $paraf_name = null;
    if (!empty($_FILES['paraf']['name'])) {
        $filename   = $_FILES['paraf']['name'];
        $filesize   = $_FILES['paraf']['size'];
        $ext        = pathinfo($filename, PATHINFO_EXTENSION);
        $allowed    = ['png', 'jpg', 'jpeg'];

        if(!in_array(strtolower($ext), $allowed)){
            echo "<script>alert('Format TTD harus PNG, JPG, atau JPEG!'); window.location='data_pengguna.php';</script>";
            exit;
        }
        if($filesize > 2000000){
            echo "<script>alert('Ukuran file TTD terlalu besar (Max 2MB)!'); window.location='data_pengguna.php';</script>";
            exit;
        }

        $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['paraf']['tmp_name'], 'assets/img/ttd/' . $paraf_name);
    }

    $q = "INSERT INTO tb_user (nama, nip, username, password, role, paraf) 
          VALUES ('$nama', '$nip', '$username', '$password', '$role', '$paraf_name')";
    
    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('User Berhasil Ditambahkan!'); window.location='data_pengguna.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah user: " . mysqli_error($koneksi) . "');</script>";
    }
}

// B. EDIT USER
if (isset($_POST['edit_user'])) {
    $id       = $_POST['id_user'];
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role     = $_POST['role'];

    // Password Logic
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $q_pass = ", password='$password'";
    } else {
        $q_pass = "";
    }

    // TTD Logic
    $q_ttd = "";
    if (!empty($_FILES['paraf']['name'])) {
        $filename   = $_FILES['paraf']['name'];
        $ext        = pathinfo($filename, PATHINFO_EXTENSION);
        $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['paraf']['tmp_name'], 'assets/img/ttd/' . $paraf_name);
        $q_ttd = ", paraf='$paraf_name'";
    }

    $q = "UPDATE tb_user SET 
          nama='$nama', nip='$nip', username='$username', role='$role' $q_pass $q_ttd
          WHERE id='$id'";

    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('Data User Diupdate!'); window.location='data_pengguna.php';</script>";
    } else {
        echo "<script>alert('Gagal update!');</script>";
    }
}

// C. HAPUS USER
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q_img = mysqli_query($koneksi, "SELECT paraf FROM tb_user WHERE id='$id'");
    $row_img = mysqli_fetch_assoc($q_img);
    if($row_img['paraf'] != null && file_exists('assets/img/ttd/'.$row_img['paraf'])){
        unlink('assets/img/ttd/'.$row_img['paraf']);
    }

    $q = "DELETE FROM tb_user WHERE id='$id'";
    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('User Dihapus!'); window.location='data_pengguna.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus!');</script>";
    }
}
?>

<?php 
require 'layout/header.php';
require 'layout/sidebar.php';

// Set Judul Halaman di Topbar
$judul_halaman = "Kelola Pengguna";
require 'layout/topbar.php'; 
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Pengguna Sistem</h1>
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambahUser">
            <i class="fas fa-user-plus fa-sm text-white-50"></i> Tambah User Baru
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 border-bottom-primary">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna (User & Admin)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th class="text-center">Tanda Tangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM tb_user ORDER BY role ASC, nama ASC");
                        while ($row = mysqli_fetch_assoc($query)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= !empty($row['nip']) ? $row['nip'] : '-'; ?></td>
                            <td><?= $row['username']; ?></td>
                            <td>
                                <?php if($row['role'] == 'super_admin' || $row['role'] == 'super admin'): ?>
                                    <span class="badge badge-danger">Super Admin</span>
                                <?php elseif($row['role'] == 'admin gudang' || $row['role'] == 'admin'): ?>
                                    <span class="badge badge-primary">Admin Gudang</span>
                                <?php elseif($row['role'] == 'pimpinan' || $row['role'] == 'Pimpinan'): ?>
                                    <span class="badge badge-warning">Pimpinan</span>
                                <?php else: ?>
                                    <span class="badge badge-success">User / Staff</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if(!empty($row['paraf'])): ?>
                                    <img src="assets/img/ttd/<?= $row['paraf']; ?>" alt="TTD" style="height: 40px;">
                                <?php else: ?>
                                    <span class="text-muted"><small>Belum ada</small></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#modalEditUser<?= $row['id']; ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="data_pengguna.php?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Hapus User <?= $row['nama']; ?>?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditUser<?= $row['id']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning text-white">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_user" value="<?= $row['id']; ?>">
                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>NIP (Opsional)</label>
                                                <input type="text" name="nip" class="form-control" value="<?= $row['nip']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control" value="<?= $row['username']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengganti)</small></label>
                                                <input type="password" name="password" class="form-control" placeholder="***">
                                            </div>
                                            <div class="form-group">
                                                <label>Role / Jabatan</label>
                                                <select name="role" class="form-control" required>
                                                    <option value="staff" <?= ($row['role']=='staff' || $row['role']=='user') ? 'selected':''; ?>>User / Staff</option>
                                                    
                                                    <option value="admin gudang" <?= ($row['role']=='admin'||$row['role']=='admin gudang')?'selected':''; ?>>Admin Gudang</option>
                                                    
                                                    <option value="pimpinan" <?= ($row['role']=='pimpinan'||$row['role']=='Pimpinan')?'selected':''; ?>>Pimpinan</option>
                                                    <option value="super admin" <?= ($row['role']=='super_admin'||$row['role']=='super admin')?'selected':''; ?>>Super Admin</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Update Tanda Tangan <small>(Kosongkan jika tidak ubah)</small></label><br>
                                                <input type="file" name="paraf" accept="image/*" class="form-control-file">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit_user" class="btn btn-warning">Update</button>
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

<div class="modal fade" id="modalTambahUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>NIP (Opsional)</label>
                        <input type="text" name="nip" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role / Jabatan</label>
                        <select name="role" class="form-control" required>
                            <option value="staff">User / Staff</option>
                            
                            <option value="admin gudang">Admin Gudang</option>
                            <option value="pimpinan">Pimpinan</option>
                            <option value="super admin">Super Admin</option>
                        </select>
                    </div>
                    
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Upload File Tanda Tangan (Paraf)</label>
                        <input type="file" name="paraf" accept="image/*" class="form-control-file">
                        <small class="text-muted">Format: JPG/PNG, Transparan lebih baik.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_user" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require 'layout/footer.php'; ?>