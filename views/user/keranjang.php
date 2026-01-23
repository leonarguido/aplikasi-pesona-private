<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Keranjang"; ?>
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
                    <h1 class="h3 mb-4 text-gray-800">Keranjang Permintaan</h1>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Item yang dipilih</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($_SESSION['keranjang'])): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">Keranjang masih kosong. Silakan pilih barang di katalog.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($_SESSION['keranjang'] as $key => $item): ?>
                                                        <tr>
                                                            <td><?= $key + 1; ?></td>
                                                            <td><?= $item['nama']; ?></td>
                                                            <td><?= $item['jumlah']; ?></td>
                                                            <td><?= $item['satuan']; ?></td>
                                                            <td>
                                                                <a href="<?= BASE_URL ?>hapus_keranjang_item&hapus=<?= $key; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus item ini?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?= BASE_URL ?>daftar_barang" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Barang</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-success text-white">
                                    <h6 class="m-0 font-weight-bold">Konfirmasi Pengajuan</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="<?= BASE_URL ?>checkout_keranjang">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Keperluan / Alasan</label>
                                            <textarea name="keperluan" class="form-control" rows="4" placeholder="Contoh: Kebutuhan ATK Bulanan Divisi IT..." required></textarea>
                                            <small class="text-muted">Keperluan ini berlaku untuk semua barang di keranjang.</small>
                                        </div>

                                        <hr>

                                        <?php if (!empty($_SESSION['keranjang'])): ?>
                                            <button type="submit" name="checkout" class="btn btn-primary btn-block btn-lg">
                                                <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-secondary btn-block" disabled>Keranjang Kosong</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
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