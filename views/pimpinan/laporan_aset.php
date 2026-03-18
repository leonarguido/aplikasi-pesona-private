<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Aset"; ?>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d1d3e2;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .nowrap {
            white-space: nowrap;
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

                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi Peminjaman</h6>
                                <a href="<?= BASE_URL ?>cetak_laporan_aset" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    <i class="fas fa-print fa-sm text-white-50"></i> Cetak / Simpan PDF
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Barang</th>
                                                <th>Peminjam (Staf)</th>
                                                <th>Tgl Pinjam</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $query = mysqli_query($koneksi, "
                            SELECT p.*, u.nama AS nama_peminjam, u.nip AS nip_peminjam
                            FROM tb_peminjaman p
                            JOIN tb_user u ON p.user_id = u.id
                            ORDER BY p.id DESC
                        ");

                                            while ($row = mysqli_fetch_assoc($query)):
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td>
                                                        <b><?= $row['nama_barang']; ?></b> <br>
                                                        <small class="text-muted"><?= $row['merek']; ?> - <?= $row['nup']; ?></small>
                                                    </td>
                                                    <td>
                                                        <?= $row['nama_peminjam']; ?> <br>
                                                        <small>NIP: <?= $row['nip_peminjam']; ?></small>
                                                    </td>
                                                    <td>
                                                        <small>Serah: <?= date('d/m/Y', strtotime($row['tgl_serah_terima'])); ?></small><br>

                                                        <?php if ($row['tgl_kembali'] == NULL): ?>
                                                            <span class="badge badge-light text-primary border border-primary small">Jangka Panjang</span>
                                                        <?php else: ?>
                                                            <small class="text-danger">Kembali: <?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($row['status'] == 'menunggu_persetujuan'): ?>
                                                            <span class="badge badge-warning">Menunggu Staf</span>
                                                        <?php elseif ($row['status'] == 'disetujui'): ?>
                                                            <span class="badge badge-info">Disetujui (Belum Tanda Tangan)</span>
                                                        <?php elseif ($row['status'] == 'selesai'): ?>
                                                            <span class="badge badge-success">Selesai / Aktif</span>
                                                        <?php elseif ($row['status'] == 'dikembalikan'): ?>
                                                            <span class="badge badge-secondary">Sudah Dikembalikan</span>
                                                        <?php elseif ($row['status'] == 'ditolak'): ?>
                                                            <span class="badge badge-danger">Ditolak Staf</span>
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
                <?php require __DIR__ . '/../layout/footer.php'; ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                if (!$.fn.DataTable.isDataTable('#dataTable')) {
                    $('#dataTable').DataTable({
                        "language": {
                            "search": "Cari Permintaan:",
                            "lengthMenu": "Tampilkan _MENU_ antrian",
                            "zeroRecords": "Tidak ada permintaan yang cocok",
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
        });
    </script>

</body>

</html>