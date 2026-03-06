<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Pengembalian Barang"; ?>
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

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Daftar Barang yang Sedang Dipinjam & Dikembalikan</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Barang</th>
                                                <th>Peminjam</th>
                                                <th>Target Kembali</th>
                                                <th>Status Pengembalian</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            // HANYA AMBIL YANG STATUSNYA SELESAI (Sedang Dipinjam) ATAU DIKEMBALIKAN
                                            $query = mysqli_query($koneksi, "
                            SELECT p.*, u.nama AS nama_peminjam, u.nip AS nip_peminjam
                            FROM tb_peminjaman p
                            JOIN tb_user u ON p.user_id = u.id
                            WHERE p.status IN ('selesai', 'dikembalikan')
                            ORDER BY p.id DESC
                        ");

                                            while ($row = mysqli_fetch_assoc($query)):
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td>
                                                        <b><?= $row['nama_barang']; ?></b> <br>
                                                        <small class="text-muted"><?= $row['merek']; ?> - NUP: <?= $row['nup']; ?></small>
                                                    </td>
                                                    <td>
                                                        <?= $row['nama_peminjam']; ?> <br>
                                                        <small>NIP: <?= $row['nip_peminjam']; ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($row['tgl_kembali']) && $row['tgl_kembali'] != null) { ?>
                                                            <span class="<?= (strtotime($row['tgl_kembali']) < time() && $row['status'] == 'selesai') ? 'text-danger font-weight-bold' : ''; ?>">
                                                                <?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                                                            </span>
                                                        <?php } else {
                                                            echo '-';
                                                        } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($row['status'] == 'selesai' && empty($row['kondisi_kembali'])): ?>
                                                            <span class="badge badge-warning">Sedang Dipinjam</span>

                                                        <?php elseif ($row['status'] == 'selesai' && !empty($row['kondisi_kembali'])): ?>
                                                            <span class="badge badge-info">Proses Pengembalian<br>(Belum Upload BA)</span>

                                                        <?php elseif ($row['status'] == 'dikembalikan'): ?>
                                                            <span class="badge badge-success">Sudah Dikembalikan</span><br>
                                                            <small>Kondisi: <?= $row['kondisi_kembali']; ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center" style="white-space: nowrap;">

                                                        <?php if ($row['status'] == 'selesai' && empty($row['kondisi_kembali'])): ?>
                                                            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalKembali<?= $row['id']; ?>">
                                                                <i class="fas fa-undo"></i> Proses Kembali
                                                            </button>

                                                        <?php elseif ($row['status'] == 'selesai' && !empty($row['kondisi_kembali'])): ?>
                                                            <a href="<?= BASE_URL ?>cetak_ba_kembali&id=<?= $row['id']; ?>" target="_blank" class="btn btn-warning btn-sm shadow-sm" title="Cetak BA Pengembalian">
                                                                <i class="fas fa-print"></i> Cetak BA
                                                            </a>
                                                            <button class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalUploadKembali<?= $row['id']; ?>" title="Upload Arsip">
                                                                <i class="fas fa-upload"></i> Upload
                                                            </button>

                                                        <?php elseif ($row['status'] == 'dikembalikan'): ?>

                                                            <?php if ($row['file_ba_kembali']): ?>
                                                                <a href="<?= ASSETS_URL ?>arsip/<?= $row['file_ba_kembali']; ?>" target="_blank" class="btn btn-outline-success btn-sm" title="Lihat BA Pengembalian">
                                                                    <i class="fas fa-file-pdf"></i> BA
                                                                </a>
                                                            <?php endif; ?>

                                                            <?php if (!empty($row['foto_bukti_kembali'])): ?>
                                                                <a href="<?= ASSETS_URL ?>arsip/<?= $row['foto_bukti_kembali']; ?>" target="_blank" class="btn btn-outline-info btn-sm" title="Lihat Foto Bukti">
                                                                    <i class="fas fa-image"></i> Foto
                                                                </a>
                                                            <?php endif; ?>

                                                        <?php endif; ?>

                                                    </td>
                                                </tr>

                                                <div class="modal fade" id="modalKembali<?= $row['id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title">Cek Fisik Pengembalian Barang</h5>
                                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" action="<?= BASE_URL ?>simpan_kondisi" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                    <p>Barang: <b><?= $row['nama_barang']; ?></b><br>Peminjam: <b><?= $row['nama_peminjam']; ?></b></p>
                                                                    <hr>
                                                                    <div class="form-group">
                                                                        <label>Tanggal Dikembalikan <span class="text-danger">*</span></label>
                                                                        <input type="date" name="tgl_dikembalikan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Kondisi Barang Saat Dikembalikan <span class="text-danger">*</span></label>
                                                                        <select name="kondisi_kembali" class="form-control" required>
                                                                            <option value="">-- Pilih Kondisi --</option>
                                                                            <option value="Baik dan Lengkap">Baik dan Lengkap</option>
                                                                            <option value="Rusak Ringan">Rusak Ringan (Bisa Diperbaiki)</option>
                                                                            <option value="Rusak Berat">Rusak Berat</option>
                                                                            <option value="Hilang / Tidak Lengkap">Hilang / Tidak Lengkap</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="simpan_kondisi" class="btn btn-primary">Simpan Data & Lanjut Cetak</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="modalUploadKembali<?= $row['id']; ?>">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title">Finalisasi Pengembalian</h5>
                                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <form method="POST" action="<?= BASE_URL ?>upload_arsip_kembali" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                    <div class="alert alert-info small">
                                                                        Pastikan Berita Acara Pengembalian sudah dicetak dan ditandatangani oleh Admin Gudang dan Staf.
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>Upload BA Pengembalian (PDF) <span class="text-danger">*</span></label>
                                                                        <input type="file" name="file_ba_kembali" class="form-control-file" accept=".pdf" required>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label>Foto Bukti Pengembalian (JPG/PNG) <span class="text-danger">*</span></label>
                                                                        <input type="file" name="foto_bukti_kembali" class="form-control-file" accept="image/*" required>
                                                                        <small class="text-muted">Foto barang saat dikembalikan sebagai bukti kondisi fisik.</small>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="upload_arsip_kembali" class="btn btn-success">Selesaikan Pengembalian</button>
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