<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Peminjaman Saya"; ?>
    <?php $id_staf_login = $_SESSION['user_id']; ?>
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

                        <?php
                        $cek_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_peminjaman WHERE user_id='$id_staf_login' AND status='menunggu_persetujuan' AND deleted_at IS NULL");
                        $count = mysqli_fetch_assoc($cek_pending);
                        if ($count['total'] > 0):
                        ?>
                            <div class="alert alert-warning border-left-warning alert-dismissible fade show" role="alert">
                                <strong>Perhatian!</strong> Ada <?= $count['total']; ?> pengajuan barang dari Admin Gudang yang menunggu persetujuan Anda.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Daftar Aset yang Dibebankan kepada Saya</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Detail Barang</th>
                                                <th>Jadwal Peminjaman</th>
                                                <th>Penyerah (Admin)</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center" width="20%">Aksi / Respon</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $query = mysqli_query($koneksi, "
                                            SELECT p.*, u.nama AS nama_admin, u.nip AS nip_admin
                                            FROM tb_peminjaman p
                                            LEFT JOIN tb_user u ON p.admin_id = u.id
                                            WHERE p.user_id = '$id_staf_login' AND p.deleted_at IS NULL
                                            ORDER BY p.id DESC
                                        ");

                                            while ($row = mysqli_fetch_assoc($query)):
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td>
                                                        <b><?= $row['nama_barang']; ?></b> <br>
                                                        <span class="badge badge-secondary"><?= $row['merek']; ?></span>
                                                        <small class="text-muted d-block mt-1">
                                                            Kode: <?= $row['kode_barang']; ?><br>
                                                            NUP: <?= $row['nup']; ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small>Mulai: <b><?= date('d M Y', strtotime($row['tgl_serah_terima'])); ?></b></small><br>
                                                        <?php if ($row['tgl_kembali'] == NULL): ?>
                                                            <span class="badge badge-light text-primary border border-primary small mt-1">Jangka Panjang</span>
                                                        <?php else: ?>
                                                            <small class="text-danger">Kembali: <b><?= date('d M Y', strtotime($row['tgl_kembali'])); ?></b></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= $row['nama_admin']; ?><br>
                                                        <small>NIP. <?= $row['nip_admin']; ?></small>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($row['status'] == 'menunggu_persetujuan'): ?>
                                                            <span class="badge badge-warning p-2">Menunggu<br>Respon Anda</span>
                                                        <?php elseif ($row['status'] == 'disetujui'): ?>
                                                            <span class="badge badge-primary p-2">Disetujui<br>(Proses TTD)</span>
                                                        <?php elseif ($row['status'] == 'selesai'): ?>
                                                            <span class="badge badge-success p-2">Selesai / Aktif</span>
                                                        <?php elseif ($row['status'] == 'dikembalikan'): ?>
                                                            <span class="badge badge-secondary p-2">Sudah Dikembalikan</span>
                                                        <?php elseif ($row['status'] == 'ditolak'): ?>
                                                            <span class="badge badge-danger p-2">Ditolak</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($row['status'] == 'menunggu_persetujuan'): ?>
                                                            <button class="btn btn-success btn-sm btn-block mb-2" data-toggle="modal" data-target="#modalSetuju<?= $row['id']; ?>">
                                                                <i class="fas fa-check"></i> Setuju & Terima
                                                            </button>
                                                            <button class="btn btn-danger btn-sm btn-block" data-toggle="modal" data-target="#modalTolak<?= $row['id']; ?>">
                                                                <i class="fas fa-times"></i> Tolak
                                                            </button>

                                                        <?php elseif ($row['status'] == 'disetujui'): ?>
                                                            <a href="<?= BASE_URL ?>cetak_berita_acara&id=<?= $row['id']; ?>" target="_blank" class="btn btn-primary btn-sm btn-block" title="Cetak Draft untuk TTD">
                                                                <i class="fas fa-print"></i> Cetak Draft Berita Acara
                                                            </a>
                                                            <small class="text-muted mt-2 d-block text-xs">
                                                                *Cetak, TTD, lalu serahkan ke Admin.
                                                            </small>

                                                        <?php elseif ($row['status'] == 'selesai'): ?>
                                                            <?php if (!empty($row['file_ba_signed'])): ?>
                                                                <a href="<?= FILE_URL ?>file.php?type=arsip&file=<?= urlencode($row['file_ba_signed']); ?>" target="_blank" class="btn btn-success btn-sm btn-block" title="Lihat Dokumen Asli">
                                                                    <i class="fas fa-file-pdf"></i> Berita Acara Peminjaman
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="badge badge-light text-muted border">Menunggu Upload Admin</span>
                                                            <?php endif; ?>

                                                        <?php elseif ($row['status'] == 'dikembalikan'): ?>

                                                            <?php if (!empty($row['file_ba_signed'])): ?>
                                                                <a href="<?= FILE_URL ?>file.php?type=arsip&file=<?= urlencode($row['file_ba_signed']); ?>" target="_blank" class="btn btn-outline-success btn-sm btn-block mb-2">
                                                                    <i class="fas fa-file-pdf"></i> Berita Acara Pinjam
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php if (!empty($row['file_ba_kembali'])): ?>
                                                                <a href="<?= FILE_URL ?>file.php?type=arsip&file=<?= urlencode($row['file_ba_kembali']); ?>" target="_blank" class="btn btn-outline-info btn-sm btn-block">
                                                                    <i class="fas fa-file-pdf"></i> Berita Acara Kembali
                                                                </a>
                                                            <?php endif; ?>

                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalSetuju<?= $row['id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title">Konfirmasi Penerimaan Barang</h5>
                                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" action="<?= BASE_URL ?>aksi_setuju" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                    <p>Apakah Anda yakin data barang berikut sudah sesuai dan Anda bersedia menerimanya?</p>

                                                                    <ul class="list-group mb-3">
                                                                        <li class="list-group-item"><strong>Barang:</strong> <?= $row['nama_barang']; ?></li>
                                                                        <li class="list-group-item"><strong>Kondisi:</strong> Baik / Layak Pakai</li>
                                                                        <li class="list-group-item"><strong>Tanggal Serah:</strong> <?= date('d F Y', strtotime($row['tgl_serah_terima'])); ?></li>

                                                                        <li class="list-group-item">
                                                                            <strong>Jadwal Kembali:</strong>
                                                                            <?php if ($row['tgl_kembali'] == NULL): ?>
                                                                                <span class="text-primary font-weight-bold">Jangka Panjang / Tidak Ditentukan</span>
                                                                            <?php else: ?>
                                                                                <span class="text-danger font-weight-bold"><?= date('d F Y', strtotime($row['tgl_kembali'])); ?></span>
                                                                            <?php endif; ?>
                                                                        </li>
                                                                    </ul>

                                                                    <div class="alert alert-warning small mt-3">
                                                                        Dengan klik <b>"Ya, Saya Setuju"</b>, sistem akan membuatkan Draft Berita Acara untuk Anda cetak.
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="aksi_setuju" class="btn btn-success">Ya, Saya Setuju</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="modalTolak<?= $row['id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">Tolak Pengajuan</h5>
                                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" action="<?= BASE_URL ?>aksi_tolak" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                    <p>Anda akan menolak penerimaan barang <strong><?= $row['nama_barang']; ?></strong>.</p>
                                                                    <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="aksi_tolak" class="btn btn-danger">Tolak Pengajuan</button>
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
                <?php require __DIR__ . '/../layout/footer.php'; ?>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>