<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Data Jabatan"; ?>
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

                        <!-- <h1 class="h3 mb-2 text-gray-800">Kelola Jabatan</h1>
                        <p class="mb-4">Manajemen akun untuk Admin, Pimpinan, dan Staff.</p> -->

                        <div class="card shadow mb-4">
                            <div class="card-header py-3 border-bottom-primary">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                                    <i class="fas fa-plus"></i> Tambah Jabatan Baru
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Jabatan</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $query = mysqli_query($koneksi, 
                                                    "SELECT * FROM tb_jabatan ORDER BY nama_jabatan ASC");
                                            while ($row = mysqli_fetch_assoc($query)):
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td><?= $row['nama_jabatan']; ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#modalEdit<?= $row['id']; ?>" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="<?= BASE_URL ?>hapus_data_jabatan&hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="confirmHapus(event, this.href)">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalEdit<?= $row['id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Jabatan</h5>
                                                                <button class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>edit_data_jabatan">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id_jabatan" value="<?= $row['id']; ?>">
                                                                    <div class="form-group">
                                                                        <label>Nama Jabatan <span class="text-danger">*</span></label>
                                                                        <input type="text" name="jabatan" class="form-control" value="<?= $row['nama_jabatan']; ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="edit_jabatan" class="btn btn-warning">Update</button>
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
                                    <h5 class="modal-title">Tambah Jabatan Baru</h5>
                                    <button class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>tambah_data_jabatan">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nama Jabatan <span class="text-danger">*</span></label>
                                            <input type="text" name="jabatan" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" name="tambah_jabatan" class="btn btn-primary">Simpan Jabatan</button>
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
                        "search": "Cari Jabatan:",
                        "lengthMenu": "Tampilkan _MENU_ antrian",
                        "zeroRecords": "Tidak ada jabatan yang cocok",
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

        function confirmHapus(event, url) {
            event.preventDefault();

            Swal.fire({
                title: 'Yakin?',
                text: 'Data jabatan akan dihapus!',
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