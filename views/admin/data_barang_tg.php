<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        .select2-container {
            z-index: 99999;
        }
    </style>

    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Data Barang Tidak Bergerak"; ?>

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

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Inventaris Aset Tetap</h6>

                            <?php if ($_SESSION['role'] != 'pimpinan'): ?>
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
                                            <?php if ($_SESSION['role'] != 'pimpinan'): ?>
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
                                            WHERE b.is_deleted=0
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
                                                    <?php if ($row['berkas']): ?>
                                                        <a href="<?= ASSETS_URL ?>img/berkas/<?= $row['berkas']; ?>" target="_blank" class="btn btn-sm btn-success shadow-sm" title="Lihat Berkas">
                                                            <i class="fas fa-file-alt"></i> Lihat
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>

                                                <?php if ($_SESSION['role'] != 'pimpinan'): ?>
                                                    <td class="text-center">
                                                        <button class="btn btn-warning btn-sm btn-circle" data-toggle="modal" data-target="#modalEdit<?= $row['id']; ?>" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="<?= BASE_URL ?>hapus_data_barang_tg&hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm btn-circle" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>

                                            <?php if ($_SESSION['role'] != 'pimpinan'): ?>
                                                <div class="modal fade" id="modalEdit<?= $row['id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning text-white">
                                                                <h5 class="modal-title">Edit Aset</h5>
                                                                <button class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>edit_data_barang_tg">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">

                                                                    <div class="form-group">
                                                                        <label>Penanggung Jawab (NIP)</label>
                                                                        <select name="nip" id="selectNipEdit" class="form-control select2-edit" style="width:100%" required>
                                                                            <option value="">-- Pilih Pegawai --</option>
                                                                            <?php foreach ($list_pegawai as $pgw): ?>
                                                                                <option value="<?= $pgw['nip']; ?>" <?= ($pgw['nip'] == $row['nip']) ? 'selected' : ''; ?> data-id="<?= $pgw['id'] ?>">
                                                                                    <?= $pgw['nip']; ?> (<?= $pgw['nama']; ?>)
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class=" form-group"><label>Kode Barang</label><input type="text" name="kode_barang" class="form-control" value="<?= $row['kode_barang']; ?>">
                                                                    </div>
                                                                    <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" required></div>
                                                                    <div class="form-group"><label>Merk</label><input type="text" name="merk_barang" class="form-control" value="<?= $row['merk_barang']; ?>"></div>
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" value="<?= $row['jumlah']; ?>" required></div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group"><label>Satuan</label><input type="text" name="satuan" class="form-control" value="<?= $row['satuan']; ?>" required></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group"><label>Keterangan / Lokasi</label><textarea name="keterangan" class="form-control"><?= $row['keterangan']; ?></textarea></div>
                                                                    <div class="form-group"><label>Update Berita Acara</label><br><input type="file" name="berkas" class="form-control-file"></div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" id="id_user" name="id_user" value="#">
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
            </div>
        </div>
    </div>

    <?php if ($_SESSION['role'] != 'pimpinan'): ?>
        <div class="modal fade" id="modalTambah">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Aset Baru</h5>
                        <button class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>tambah_data_barang_tg">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Penanggung Jawab (NIP)</label>
                                <select name="nip" id="selectNipTambah" class="form-control" style="width:100%" required>
                                    <option value="">-- Pilih Pegawai --</option>
                                    <?php foreach ($list_pegawai as $pgw): ?>
                                        <option value="<?= $pgw['nip']; ?>" data-id="<?= $pgw['id'] ?>">
                                            <?= $pgw['nip']; ?> (<?= $pgw['nama']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Kode Barang (Opsional)</label><input type="text" name="kode_barang" class="form-control" placeholder="Contoh: INV-LAP-01"></div>
                            <div class="form-group"><label>Nama Barang</label><input type="text" name="nama_barang" class="form-control" required placeholder="Contoh: Laptop"></div>
                            <div class="form-group"><label>Merk</label><input type="text" name="merk_barang" class="form-control" placeholder="Contoh: Asus / Lenovo"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group"><label>Jumlah</label><input type="number" name="jumlah" class="form-control" required></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"><label>Satuan</label>
                                        <select name="satuan" class="form-control">
                                            <option value="Unit">Unit</option>
                                            <option value="Pcs">Pcs</option>
                                            <option value="Set">Set</option>
                                            <option value="Buah">Buah</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"><label>Keterangan / Lokasi</label><textarea name="keterangan" class="form-control" placeholder="Contoh: Di Ruang Rapat"></textarea></div>
                            <div class="form-group"><label>Upload Berita Acara</label><input type="file" name="berkas" class="form-control-file"></div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="id_user" name="id_user" value="#">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/../layout/footer.php'; ?>

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

        $('#selectNipTambah').on('change', function() {
            let id = $(this).find(':selected').data('id');
            $('#id_user').val(id);
        });

        function confirmHapus(event, url) {
            event.preventDefault();

            Swal.fire({
                title: 'Yakin?',
                text: 'Barang akan dihapus permanen!',
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