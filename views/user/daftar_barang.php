<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Daftar Barang"; ?>
</head>

<body id="page-top">
    <?php $jml_item_keranjang = count($_SESSION['keranjang']); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="row">
                <div class="col-md-2">
                    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
                </div>
                <div class="col-md-10">
                    <?php require __DIR__ . '/../layout/topbar.php'; ?>
                    <div class="container-fluid mt-4">

                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Daftar Barang Tersedia</h6>

                                <a href="<?= BASE_URL ?>keranjang" class="btn btn-success btn-sm shadow-sm">
                                    <i class="fas fa-shopping-cart fa-sm"></i> Lihat Keranjang
                                    <span class="badge badge-light text-danger ml-1 font-weight-bold"><?= $jml_item_keranjang; ?></span>
                                </a>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Kode</th>
                                                <th>Merk Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Stok</th>
                                                <th>Satuan</th>
                                                <th class="text-center" width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $query = "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 ORDER BY nama_barang ASC";
                                            $result = mysqli_query($koneksi, $query);

                                            while ($row = mysqli_fetch_assoc($result)):
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td><?= $row['kode_barang']; ?></td>
                                                    <td><?= $row['merk_barang']; ?></td>
                                                    <td class="font-weight-bold"><?= $row['nama_barang']; ?></td>
                                                    <td class="<?= $row['stok'] == 0 ? 'text-danger' : 'text-success'; ?>">
                                                        <?= $row['stok']; ?>
                                                    </td>
                                                    <td><?= $row['satuan']; ?></td>
                                                    <td class="text-center">
                                                        <?php if ($row['stok'] > 0): ?>
                                                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAdd<?= $row['id']; ?>">
                                                                <i class="fas fa-plus"></i> Add
                                                            </button>
                                                        <?php else: ?>
                                                            <button class="btn btn-secondary btn-sm" disabled>Habis</button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <div class="modal fade text-left" id="modalAdd<?= $row['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title">Tambah ke Keranjang</h5>
                                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" action="<?= BASE_URL ?>tambah_keranjang_item">
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Nama Barang</label>
                                                                        <input type="text" class="form-control" value="<?= $row['nama_barang']; ?>" readonly>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Jumlah (Max: <?= $row['stok']; ?>)</label>
                                                                        <input type="number" name="jumlah" class="form-control" min="1" max="<?= $row['stok']; ?>" required>
                                                                    </div>

                                                                    <input type="hidden" name="id_barang" value="<?= $row['id']; ?>">
                                                                    <input type="hidden" name="nama_barang" value="<?= $row['nama_barang']; ?>">
                                                                    <input type="hidden" name="merk_barang" value="<?= $row['merk_barang']; ?>">
                                                                    <input type="hidden" name="satuan" value="<?= $row['satuan']; ?>">
                                                                    <input type="hidden" name="stok_max" value="<?= $row['stok']; ?>">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="tambah_keranjang" class="btn btn-primary">Simpan</button>
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
    </div>

    <?php require __DIR__ . '/../layout/footer.php'; ?>

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

         
    </script>
</body>

</html>