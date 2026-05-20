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
                            SELECT p.*, b.nama_barang, b.kode_barang, b.merek_barang, b.tahun_perolehan, b.nup, u.nama AS nama_peminjam, u.nip AS nip_peminjam
                            FROM tb_peminjaman p
                            JOIN tb_aset_bmn b ON p.bmn_id = b.id
                            JOIN tb_user u ON p.user_id = u.id
                            WHERE p.deleted_at IS NULL
                            ORDER BY p.id DESC
                        ");

                                            while ($row = mysqli_fetch_assoc($query)):
                                            ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td data-toggle="modal" data-target="#modalDetail<?= $row['id']; ?>" title="Detail Pengajuan">
                                                        <b><?= $row['nama_barang']; ?></b> <br>
                                                        <small class="text-muted"><?= $row['merek_barang']; ?> - <?= $row['nup']; ?></small>
                                                    </td>
                                                    <td data-toggle="modal" data-target="#modalDetail<?= $row['id']; ?>" title="Detail Pengajuan">
                                                        <?= $row['nama_peminjam']; ?> <br>
                                                        <small>NIP: <?= $row['nip_peminjam']; ?></small>
                                                    </td>
                                                    <td data-toggle="modal" data-target="#modalDetail<?= $row['id']; ?>" title="Detail Pengajuan">
                                                        <small>Serah: <?= date('d/m/Y', strtotime($row['tgl_serah_terima'])); ?></small><br>

                                                        <?php if ($row['tgl_kembali'] == NULL): ?>
                                                            <span class="badge badge-light text-primary border border-primary small">Jangka Panjang</span>
                                                        <?php else: ?>
                                                            <small class="text-danger">Kembali: <?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td data-toggle="modal" data-target="#modalDetail<?= $row['id']; ?>" title="Detail Pengajuan" class="text-center">
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

                                                <div class="modal fade" id="modalDetail<?= $row['id']; ?>">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-warning text-white">
                                                                <h5 class="modal-title">Detail Pengajuan Barang</h5>
                                                                <button class="close text-white" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id_edit" value="<?= $row['id']; ?>">
                                                                <div class="row">
                                                                    <div class="col-md-6 border-right">
                                                                        <h6 class="font-weight-bold text-primary mb-3">Data Barang</h6>
                                                                        <div class="form-group">
                                                                            <label>Nama Barang</label>
                                                                            <input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" readonly>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Merek</label>
                                                                            <input type="text" name="merek" class="form-control" value="<?= $row['merek_barang']; ?>" readonly>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <div class="form-group">
                                                                                    <label>Kode Barang</label>
                                                                                    <input type="text" name="kode_barang" class="form-control" value="<?= $row['kode_barang']; ?>" readonly>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="form-group">
                                                                                    <label>NUP</label>
                                                                                    <input type="text" name="nup" class="form-control" value="<?= $row['nup']; ?>" readonly>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Tahun Perolehan</label>
                                                                            <input type="number" name="tahun_perolehan" class="form-control" value="<?= $row['tahun_perolehan']; ?>" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <h6 class="font-weight-bold text-primary mb-3">Data Peminjaman</h6>

                                                                        <div class="form-group">
                                                                            <label>Yang Menerima (Staf)</label>
                                                                            <select name="id_penerima" class="form-control select2-modal-edit" style="width: 100%" disabled>
                                                                                <option value="<?= $row['user_id']; ?>" selected>
                                                                                    <?= $row['nama_peminjam']; ?> (NIP: <?= $row['nip_peminjam']; ?>)
                                                                                </option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label>Tanggal Serah Terima</label>
                                                                            <input type="text" name="tgl_serah_terima" class="form-control" value="<?= date('d/m/Y', strtotime($row['tgl_serah_terima'])); ?>" readonly>
                                                                        </div>

                                                                        <div class="form-group bg-light p-2 rounded border">
                                                                            <label>Tanggal Kembali</label>
                                                                            <div class="custom-control custom-checkbox mb-2">
                                                                                <input type="checkbox" class="custom-control-input" id="checkEdit<?= $row['id']; ?>" name="jangka_panjang" value="1" <?= ($row['tgl_kembali'] == NULL) ? 'checked' : ''; ?> disabled>
                                                                                <label class="custom-control-label small text-primary font-weight-bold" for="checkEdit<?= $row['id']; ?>">
                                                                                    Peminjaman Jangka Panjang
                                                                                </label>
                                                                            </div>
                                                                            <input type="text" name="tgl_kembali" id="inputTglEdit<?= $row['id']; ?>" class="form-control" value="<?= ($row['tgl_kembali']) ? date('d/m/Y', strtotime($row['tgl_kembali'])) : '-'; ?>" <?= ($row['tgl_kembali'] == NULL) ? 'disabled' : ''; ?> readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            </div>

                                                            <script>
                                                                document.addEventListener("DOMContentLoaded", function() {
                                                                    const check = document.getElementById('checkEdit<?= $row['id']; ?>');
                                                                    const input = document.getElementById('inputTglEdit<?= $row['id']; ?>');

                                                                    if (check && input) {
                                                                        check.addEventListener('change', function() {
                                                                            if (this.checked) {
                                                                                input.value = '';
                                                                                input.disabled = true;
                                                                                input.removeAttribute('required');
                                                                            } else {
                                                                                input.disabled = false;
                                                                                input.setAttribute('required', '');
                                                                            }
                                                                        });
                                                                    }
                                                                });
                                                            </script>
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