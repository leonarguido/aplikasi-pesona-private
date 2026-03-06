<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Data Pengguna"; ?>

    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d1d3e2;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <?php require __DIR__ . '/../layout/sidebar.php'; ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="row">
                    <div class="col-md-12">
                        <?php require __DIR__ . '/../layout/topbar.php'; ?>
                    </div>
                </div>
                <div class="row">
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
                                                <th>Jabatan</th>
                                                <th class="text-center">Tanda Tangan</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $query = mysqli_query(
                                                $koneksi,
                                                "SELECT u.*, j.nama_jabatan 
                                                    FROM tb_user u
                                                    LEFT JOIN tb_jabatan j ON u.jabatan_id = j.id
                                                    ORDER BY u.role ASC, u.nama ASC"
                                            );
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
                                                        <?php elseif ($row['role'] == 'admin bmn') : ?>
                                                            <span class="badge badge-primary">Admin Aset BMN</span>
                                                        <?php elseif ($row['role'] == 'admin bhp') : ?>
                                                            <span class="badge badge-primary">Admin Barang Habis Pakai</span>
                                                        <?php elseif ($row['role'] == 'pimpinan' || $row['role'] == 'Pimpinan'): ?>
                                                            <span class="badge badge-warning">Pimpinan</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success">User / Staff</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $row['nama_jabatan']; ?></td>
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
                                                        <a href="<?= BASE_URL ?>hapus_data_pengguna&hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="confirmHapus(event, this.href)">
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
                                                                        <label>Nama Lengkap <span class="text-danger">*</span></label>
                                                                        <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>NIP <span class="text-danger">*</span></label>
                                                                        <input type="text" name="nip" class="form-control" value="<?= $row['nip']; ?>" required minlength="18" pattern="^\d{18}$" title="NIP harus terdiri dari 18 digit angka">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Username <span class="text-danger">*</span></label>
                                                                        <input type="text" name="username" class="form-control" value="<?= $row['username']; ?>" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengganti)</small></label>
                                                                        <input type="password" name="password" class="form-control" placeholder="***">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Role <span class="text-danger">*</span></label>
                                                                        <select name="role" class="form-control" required>
                                                                            <option value="staff" <?= $row['role'] == 'user' ? 'selected' : ''; ?>>User / Staff</option>
                                                                            <option value="admin bmn" <?= ($row['role'] == 'admin bmn') ? 'selected' : ''; ?>>Admin Aset BMN</option>
                                                                            <option value="admin bhp" <?= ($row['role'] == 'admin bhp') ? 'selected' : ''; ?>>Admin Barang Habis Pakai</option>
                                                                            <option value="pimpinan" <?= ($row['role'] == 'pimpinan' || $row['role'] == 'Pimpinan') ? 'selected' : ''; ?>>Pimpinan</option>
                                                                            <option value="super admin" <?= ($row['role'] == 'super_admin' || $row['role'] == 'super admin') ? 'selected' : ''; ?>>Super Admin</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Jabatan <span class="text-danger">*</span></label>
                                                                        <select name="jabatan" id="selectEditJabatan" class="form-control select2-edit" style="width:100%" required>
                                                                            <option value="">-- Pilih Jabatan --</option>
                                                                            <?php foreach ($list_jabatan as $jbtn): ?>
                                                                                <option value="<?= $jbtn['id']; ?>" <?= ($jbtn['id'] == $row['jabatan_id']) ? 'selected' : ''; ?>>
                                                                                    <?= $jbtn['nama_jabatan']; ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
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
                                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="nama" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>NIP <span class="text-danger">*</span></label>
                                            <input type="text" name="nip" class="form-control" required minlength="18" pattern="^\d{18}$" title="NIP harus terdiri dari 18 digit angka">
                                        </div>
                                        <div class="form-group">
                                            <label>Username <span class="text-danger">*</span></label>
                                            <input type="text" name="username" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Role<span class="text-danger">*</span></label>
                                            <select name="role" class="form-control" required>
                                                <option value="staff">User / Staff</option>
                                                <option value="admin bmn">Admin Aset BMN</option>
                                                <option value="admin bhp">Admin Barang Habis Pakai</option>
                                                <option value="pimpinan">Pimpinan</option>
                                                <option value="super admin">Super Admin</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Jabatan <span class="text-danger">*</span></label>
                                            <select name="jabatan" id="selectTambahJabatan" class="form-control select2-tambah" style="width:100%" required>
                                                <option value="">-- Pilih Jabatan --</option>
                                                <?php foreach ($list_jabatan as $jbtn): ?>
                                                    <option value="<?= $jbtn['id']; ?>">
                                                        <?= $jbtn['nama_jabatan']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
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

                <?php require __DIR__ . '/../layout/footer.php'; ?>
            </div>
        </div>
    </div>

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

        $('.modal').on('shown.bs.modal', function() {
            $(this).find('.select2-tambah').select2({
                dropdownParent: $(this),
                placeholder: "Pilih Jabatan...",
                allowClear: true
            });
        });

        $('.modal').on('shown.bs.modal', function() {
            $(this).find('.select2-edit').select2({
                dropdownParent: $(this),
                placeholder: "Pilih Jabatan...",
                allowClear: true
            });
        });

        function confirmHapus(event, url) {
            event.preventDefault();

            Swal.fire({
                title: 'Yakin?',
                text: 'Data pengguna akan dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
</body>

</html>