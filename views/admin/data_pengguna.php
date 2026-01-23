<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Data Pengguna"; ?>
</head>

<body id="page-top">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="row">
            <div class="col-md-2">
                <?php require __DIR__ . '/../layout/sidebar.php'; ?>
            </div>
            <div class="col-md-10">
                <?php require __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid mt-4">

                    <!-- <h1 class="h3 mb-2 text-gray-800">Kelola Pengguna</h1>
                    <p class="mb-4">Manajemen akun untuk Admin, Pimpinan, dan Staff.</p> -->

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 border-bottom-primary">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                                <i class="fas fa-plus"></i> Tambah User Baru
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
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
                                                    <?php if ($row['role'] == 'super_admin' || $row['role'] == 'super admin'): ?>
                                                        <span class="badge badge-danger">Super Admin</span>
                                                    <?php elseif ($row['role'] == 'admin gudang' || $row['role'] == 'admin'): ?>
                                                        <span class="badge badge-primary">Admin Gudang</span>
                                                    <?php elseif ($row['role'] == 'pimpinan' || $row['role'] == 'Pimpinan'): ?>
                                                        <span class="badge badge-warning">Pimpinan</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">User / Staff</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (!empty($row['paraf'])): ?>
                                                        <img src="<?= ASSETS_URL ?>img/ttd/<?= $row['paraf']; ?>" alt="TTD" style="height: 40px;">
                                                    <?php else: ?>
                                                        <span class="text-muted"><small>Belum ada</small></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#modalEdit<?= $row['id']; ?>" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= BASE_URL ?>hapus_data_pengguna&hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Hapus User <?= $row['nama']; ?>?')" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="modalEdit<?= $row['id']; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit User</h5>
                                                            <button class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>edit_data_pengguna">
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
                                                                        <option value="staff" <?= $row['role'] == 'user' ? 'selected' : ''; ?>>User / Staff</option>

                                                                        <option value="admin gudang" <?= ($row['role'] == 'admin' || $row['role'] == 'admin gudang') ? 'selected' : ''; ?>>Admin Gudang</option>

                                                                        <option value="pimpinan" <?= ($row['role'] == 'pimpinan' || $row['role'] == 'Pimpinan') ? 'selected' : ''; ?>>Pimpinan</option>
                                                                        <option value="super admin" <?= ($row['role'] == 'super_admin' || $row['role'] == 'super admin') ? 'selected' : ''; ?>>Super Admin</option>
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
                <div class="modal fade" id="modalTambah">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah User Baru</h5>
                                <button class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>tambah_data_pengguna">
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
            </div>
        </div>
    </div>

    <?php require '../views/layout/footer.php'; ?>

    <script>
        $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "search": "Cari Pengguna:",
                    "lengthMenu": "Tampilkan _MENU_ antrian",
                    "zeroRecords": "Tidak ada pengguna yang cocok",
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
        if (window.innerHeight <= 700) {
            document.getElementById('accordionSidebar')
                .style.height = '100vh';
        }
    </script>
</body>

</html>