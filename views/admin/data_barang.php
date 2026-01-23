<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Data Barang"; ?>
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

                    <!-- <h1 class="h3 mb-2 text-gray-800">Data Barang Bergerak</h1>
                    <p class="mb-4">Kelola stok barang, tambah manual, atau import via Excel.</p> -->

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tambahModal">
                                    <i class="fas fa-plus"></i> Tambah Manual
                                </button>
                                <button class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#importModal">
                                    <i class="fas fa-file-excel"></i> Import Excel
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
                                            <th width="15%">Aksi</th>
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
                                                <td><span class="badge badge-secondary"><?= $row['kode_barang']; ?></span></td>
                                                <td class="font-weight-bold"><?= $row['nama_barang']; ?></td>
                                                <td><?= $row['satuan']; ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['stok'] > 0): ?>
                                                        <span class="badge badge-success" style="font-size: 1rem;"><?= $row['stok']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Habis</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><small><?= $row['keterangan']; ?></small></td>
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $row['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="<?= BASE_URL ?>hapus_data_barang&id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus barang ini?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <!-- <a href="data_barang.php?hapus=row[id]" class="btn btn-danger btn-sm" onclick="return confirm('Hapus barang ini?');"> -->
                                                    <!-- <i class="fas fa-trash"></i> -->
                                                    <!-- </a> -->
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Barang</h5>
                                                            <button class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form method="POST" action="<?= BASE_URL ?>edit_data_barang">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                <div class="form-group">
                                                                    <label>Nama Barang</label>
                                                                    <input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Satuan</label>
                                                                    <select name="satuan" class="form-control">
                                                                        <option value="Unit" <?= ($row['satuan'] == 'Unit') ? 'selected' : ''; ?>>Unit</option>
                                                                        <option value="Pcs" <?= ($row['satuan'] == 'Pcs') ? 'selected' : ''; ?>>Pcs</option>
                                                                        <option value="Buah" <?= ($row['satuan'] == 'Buah') ? 'selected' : ''; ?>>Buah</option>
                                                                        <option value="Rim" <?= ($row['satuan'] == 'Rim') ? 'selected' : ''; ?>>Rim</option>
                                                                        <option value="Box" <?= ($row['satuan'] == 'Box') ? 'selected' : ''; ?>>Box</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Stok</label>
                                                                    <input type="number" name="stok" class="form-control" value="<?= $row['stok']; ?>" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Keterangan</label>
                                                                    <textarea name="keterangan" class="form-control"><?= $row['keterangan']; ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
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
                <form method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>import_excel_data_barang">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Cara Penggunaan:</strong>
                            <ol class="pl-3 mb-0 small">
                                <li>Download template Excel (CSV).</li>
                                <li>Isi data tanpa mengubah judul kolom.</li>
                                <li>Simpan sebagai <strong>.CSV</strong>.</li>
                            </ol>
                            <a href="<?= BASE_URL ?>template_barang" class="btn btn-sm btn-light mt-2 text-success font-weight-bold"><i class="fas fa-download"></i> Download Template</a>
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
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Barang Baru</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="POST" action="<?= BASE_URL ?>tambah_data_barang">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Barang (Unik)</label>
                            <input type="text" name="kode_barang" class="form-control" placeholder="Cth: KTS-001" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
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
                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require '../views/layout/footer.php'; ?>

    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable({
                    "language": {
                        "search": "Cari Barang:",
                        "lengthMenu": "Tampilkan _MENU_ antrian",
                        "zeroRecords": "Tidak ada barang yang cocok",
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