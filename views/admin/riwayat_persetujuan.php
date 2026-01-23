<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Stok Barang"; ?>
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
                        <div class="card-header py-3 border-bottom-primary">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history"></i> Log Riwayat Permintaan</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">Tanggal</th>
                                            <th>Pemohon</th>
                                            <th>Rincian Barang (Final)</th>
                                            <th class="text-center" width="10%">Status</th>
                                            <th>Admin Eksekutor</th>
                                            <th class="text-center" width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Ambil semua riwayat (Disetujui/Ditolak)
                                        $query_hist = "SELECT p.*, u.nama AS nama_pemohon, a.nama AS nama_admin
                                       FROM tb_permintaan p 
                                       JOIN tb_user u ON p.user_id = u.id 
                                       LEFT JOIN tb_user a ON p.admin_id = a.id
                                       WHERE p.status != 'menunggu' 
                                       ORDER BY p.id DESC";

                                        $res_hist = mysqli_query($koneksi, $query_hist);

                                        while ($hist = mysqli_fetch_assoc($res_hist)):
                                            $id_hist = $hist['id'];

                                            // Tentukan Tanggal (Disetujui atau Ditolak)
                                            $tgl_aksi = ($hist['status'] == 'disetujui') ? $hist['tanggal_disetujui'] : $hist['tanggal_ditolak'];
                                        ?>
                                            <tr>
                                                <td>
                                                    <i class="far fa-calendar-alt text-gray-400"></i> <?= date('d-m-Y', strtotime($tgl_aksi)); ?>
                                                </td>
                                                <td class="font-weight-bold text-primary"><?= $hist['nama_pemohon']; ?></td>

                                                <td>
                                                    <ul class="pl-3 mb-0" style="font-size: 0.9rem;">
                                                        <?php
                                                        $q_detail_hist = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang 
                                                                             FROM tb_detail_permintaan d 
                                                                             JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                                             WHERE d.permintaan_id = '$id_hist'");

                                                        while ($dh = mysqli_fetch_assoc($q_detail_hist)) {
                                                            echo "<li class='mb-1'>{$dh['nama_barang']} : <b>{$dh['jumlah']} {$dh['satuan']}</b></li>";
                                                        }
                                                        ?>
                                                    </ul>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($hist['status'] == 'disetujui'): ?>
                                                        <span class="badge badge-success px-2 py-1">Disetujui</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                                        <div class="small text-danger mt-1 font-italic">"<?= $hist['catatan']; ?>"</div>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="small text-muted">
                                                    <i class="fas fa-user-shield"></i> <?= $hist['nama_admin']; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($hist['status'] == 'disetujui'): ?>
                                                        <a href="<?= BASE_URL ?>cetak_surat&id=<?= $hist['id']; ?>" target="_blank" class="btn btn-info btn-sm shadow-sm" title="Cetak Surat Jalan">
                                                            <i class="fas fa-print"></i> Cetak
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-ban"></i></button>
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
            if (!$.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable({
                    "language": {
                        "search": "Cari Riwayat:",
                        "lengthMenu": "Tampilkan _MENU_ data",
                        "zeroRecords": "Tidak ada data riwayat",
                        "info": "Halaman _PAGE_ dari _PAGES_",
                        "infoEmpty": "Tidak ada data",
                        "infoFiltered": "(difilter dari _MAX_ total data)",
                        "paginate": {
                            "first": "Awal",
                            "last": "Akhir",
                            "next": "Lanjut",
                            "previous": "Kembali"
                        }
                    },
                    "ordering": false // Matikan sorting otomatis agar urutan tanggal DESC tetap terjaga
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