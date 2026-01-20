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
                                <table class="table table-bordered table-hover" id="myTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Tanggal Ajuan</th>
                                            <th>Detail Barang</th>
                                            <th class="text-center">Status & Aksi</th>
                                            <th>Catatan Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        // Query Header Permintaan milik User ini
                                        $query = "SELECT p.* FROM tb_permintaan p 
                                  WHERE p.user_id = '$id_user' 
                                  ORDER BY p.tanggal_permintaan DESC";

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
                                                    <?php
                                                    if ($row['status'] == 'menunggu') {
                                                        echo '<span class="badge badge-warning">Menunggu Konfirmasi</span>';
                                                    } elseif ($row['status'] == 'disetujui') {
                                                        echo '<span class="badge badge-success"><i class="fas fa-check"></i> Disetujui</span>';
                                                        echo '<br><small class="text-muted">' . date('d-m-Y', strtotime($row['tanggal_disetujui'])) . '</small>';

                                                        echo '<div class="mt-2">';
                                                        echo '<a href="' . BASE_URL . 'cetak_surat&id=' . $row['id'] . '" target="_blank" class="btn btn-primary btn-sm shadow-sm">';
                                                        echo '<i class="fas fa-print fa-sm text-white-50"></i> Cetak Surat';
                                                        echo '</a>';
                                                        echo '</div>';
                                                    } elseif ($row['status'] == 'ditolak') {
                                                        echo '<span class="badge badge-danger"><i class="fas fa-times"></i> Ditolak</span>';
                                                    }
                                                    ?>
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