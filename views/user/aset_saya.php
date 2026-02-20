<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Barang Tidak Bergerak"; ?>
</head>

<body id="page-top">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="row">
                <div class="col-md-2">
                    <?php require __DIR__ . '/../layout/sidebar.php'; ?>
                </div>
                <div class="col-md-10">
                    <?php require __DIR__ . '/../layout/topbar.php'; ?>
                    <div class="container-fluid mt-4">

                        <div class="alert alert-info shadow-sm border-left-info" role="alert">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                </div>
                                <div class="col">
                                    <span class="small text-uppercase font-weight-bold">Penanggung Jawab:</span><br>
                                    <span class="h5 font-weight-bold text-gray-800"><?= $d_user['nama']; ?></span>
                                    <span class="badge badge-light ml-2">NIP: <?= !empty($nip_user) ? $nip_user : 'Belum diisi'; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list"></i> Daftar Barang Tidak Bergerak Anda</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Kode Barang</th>
                                                <th>Nama Barang</th>
                                                <th>Merk</th>
                                                <th>Jumlah</th>
                                                <th>Lokasi / Keterangan</th>
                                                <th class="text-center">Berita Acara</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Cek apakah User punya NIP
                                            if (!empty($nip_user)) {
                                                $no = 1;
                                                // Query mencari barang berdasarkan NIP user yang login
                                                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang_tidak_bergerak WHERE nip = $nip_user AND is_deleted=0 ORDER BY nama_barang ASC");

                                                // Cek jika data ditemukan
                                                if (mysqli_num_rows($query) > 0) {
                                                    while ($row = mysqli_fetch_assoc($query)):
                                            ?>
                                                        <tr>
                                                            <td><?= $no++; ?></td>
                                                            <td><span class="badge badge-secondary"><?= $row['kode_barang'] ? $row['kode_barang'] : '-'; ?></span></td>
                                                            <td class="font-weight-bold text-primary"><?= $row['nama_barang']; ?></td>
                                                            <td><?= $row['merk_barang'] ? $row['merk_barang'] : '-'; ?></td>
                                                            <td><?= $row['jumlah']; ?> <?= $row['satuan']; ?></td>
                                                            <td><?= $row['keterangan']; ?></td>
                                                            <td class="text-center">
                                                                <?php if ($row['berkas']): ?>
                                                                    <a href="<?= ASSETS_URL ?>img/berkas/<?= $row['berkas']; ?>" target="_blank" class="btn btn-sm btn-info shadow-sm" title="Download Berita Acara">
                                                                        <i class="fas fa-download"></i> Unduh
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="text-muted small">Tidak ada</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                            <?php
                                                    endwhile;
                                                } else {
                                                    echo '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data aset yang terdaftar atas NIP Anda.</td></tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="7" class="text-center text-danger py-4">NIP Anda belum terdaftar di sistem. Silakan hubungi Admin untuk update data profil.</td></tr>';
                                            }
                                            ?>
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
            $('#dataTable').DataTable();
        });
    </script>
</body>

</html>