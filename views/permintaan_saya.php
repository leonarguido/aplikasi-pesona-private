<!DOCTYPE html>
<html lang="en">

<head>
    <?php require 'layout/header.php'; ?>
</head>

<body id="page-top">
    <?php $id_user = $_SESSION['user_id']; ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="row">
            <div class="col-md-2">
                <?php require 'layout/sidebar.php'; ?>
            </div>
            <div class="col-md-10">
                <?php require 'layout/topbar.php'; ?>
                <div class="container-fluid mt-4">

                    <h1 class="h3 mb-2 text-gray-800">Riwayat Permintaan Saya</h1>
                    <p class="mb-4">Berikut adalah daftar status permintaan barang yang pernah Anda ajukan.</p>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Log Permintaan</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal Ajuan</th>
                                            <th>Detail Barang</th>
                                            <th class="text-center" width="20%">Status & Aksi</th>
                                            <th>Catatan Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = "SELECT p.* FROM tb_permintaan p 
                                  WHERE p.user_id = '$id_user' 
                                  ORDER BY p.id DESC";

                                        $result = mysqli_query($koneksi, $query);

                                        while ($row = mysqli_fetch_assoc($result)):
                                            $id_req = $row['id'];
                                        ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['tanggal_permintaan'])); ?></td>

                                                <td>
                                                    <ul class="pl-3 mb-0">
                                                        <?php
                                                        $q_detail = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang 
                                                                        FROM tb_detail_permintaan d 
                                                                        JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                        WHERE d.permintaan_id = '$id_req'");
                                                        while ($d = mysqli_fetch_assoc($q_detail)) {
                                                            echo "<li>{$d['nama_barang']} (<b>{$d['jumlah']} {$d['satuan']}</b>)</li>";
                                                        }
                                                        ?>
                                                    </ul>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($row['status'] == 'menunggu'): ?>

                                                        <span class="badge badge-warning mb-2">Menunggu Konfirmasi</span>
                                                        <br>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalEdit<?= $id_req; ?>">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </button>
                                                            <a href="<?= BASE_URL ?>batalkan_permintaan_saya&batal_id=<?= $id_req; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin membatalkan?')">
                                                                <i class="fas fa-trash"></i> Batal
                                                            </a>
                                                        </div>

                                                        <div class="modal fade text-left" id="modalEdit<?= $id_req; ?>" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-lg" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title">Edit Jumlah Permintaan</h5>
                                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <form method="POST" action="<?= BASE_URL ?>edit_permintaan_saya">
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="id_permintaan" value="<?= $id_req; ?>">
                                                                            <p class="small text-muted">Silakan ubah jumlah barang:</p>

                                                                            <table class="table table-sm table-bordered">
                                                                                <thead class="thead-light">
                                                                                    <tr>
                                                                                        <th>Nama Barang</th>
                                                                                        <th width="120px">Jumlah</th>
                                                                                        <th>Satuan</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <?php
                                                                                    // Query ulang detail untuk ditampilkan di dalam Modal
                                                                                    $q_edit = mysqli_query($koneksi, "SELECT d.id AS id_detail, d.jumlah, d.satuan, b.nama_barang, b.stok AS stok_gudang 
                                                                                                  FROM tb_detail_permintaan d 
                                                                                                  JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                                                  WHERE d.permintaan_id = '$id_req'");
                                                                                    while ($edit = mysqli_fetch_assoc($q_edit)):
                                                                                    ?>
                                                                                        <tr>
                                                                                            <td><?= $edit['nama_barang']; ?> <br><small class="text-success">Stok: <?= $edit['stok_gudang']; ?></small></td>
                                                                                            <td>
                                                                                                <input type="hidden" name="id_detail[]" value="<?= $edit['id_detail']; ?>">
                                                                                                <input type="number" name="jumlah[]" class="form-control form-control-sm" value="<?= $edit['jumlah']; ?>" min="1" max="<?= $edit['stok_gudang']; ?>" required>
                                                                                            </td>
                                                                                            <td><?= $edit['satuan']; ?></td>
                                                                                        </tr>
                                                                                    <?php endwhile; ?>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                            <button type="submit" name="update_permintaan" class="btn btn-primary">Simpan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php elseif ($row['status'] == 'disetujui'): ?>
                                                        <span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>
                                                        <br><small class="text-muted"><?= date('d-m-Y', strtotime($row['tanggal_disetujui'])); ?></small>
                                                        <div class="mt-2">
                                                            <a href="<?= BASE_URL ?>cetak_surat&id=<?= $row['id']; ?>" target="_blank" class="btn btn-primary btn-sm shadow-sm"><i class="fas fa-print fa-sm text-white-50"></i> Cetak Surat</a>
                                                        </div>

                                                    <?php elseif ($row['status'] == 'ditolak'): ?>
                                                        <span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php if (!empty($row['catatan'])): ?>
                                                        <div class="alert alert-danger py-1 px-2 m-0" style="font-size: 0.85rem;">
                                                            <strong>Info:</strong> <?= $row['catatan']; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted text-center d-block">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
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
    <?php require '../views/layout/footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
        if (window.innerHeight <= 700) {
            document.getElementById('accordionSidebar')
                .style.height = '100vh';
        }
    </script>
</body>

</html>