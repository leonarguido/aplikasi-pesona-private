<!DOCTYPE html>
<html lang="en">

<head>
    <?php require 'layout/header.php'; ?>
</head>

<body id="page-top">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="row">
            <div class="col-md-2">
                <?php require 'layout/sidebar.php'; ?>
            </div>
            <div class="col-md-10">
                <?php require 'layout/topbar.php'; ?>
                <div class="container-fluid mt-4">
                    <h1 class="h3 mb-4 text-gray-800">Laporan Transaksi Barang</h1>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Filter Laporan per Tanggal</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?= BASE_URL ?>cetak_laporan" method="POST" target="_blank">

                                        <div class="form-group">
                                            <label class="font-weight-bold">Dari Tanggal</label>
                                            <input type="date" name="tgl_mulai" class="form-control" required
                                                value="<?= date('Y-m-01'); ?>">
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Sampai Tanggal</label>
                                            <input type="date" name="tgl_selesai" class="form-control" required
                                                value="<?= date('Y-m-d'); ?>">
                                        </div>

                                        <hr>

                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-print"></i> Cetak Laporan (PDF)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Petunjuk:</h5>
                                <ul class="pl-3">
                                    <li>Pilih rentang tanggal transaksi yang ingin dicetak.</li>
                                    <li>Klik tombol <b>Cetak Laporan</b>.</li>
                                    <li>Sistem akan membuka tab baru.</li>
                                    <li>Gunakan fitur <b>"Save as PDF"</b> atau <b>"Simpan sebagai PDF"</b> pada menu printer browser Anda.</li>
                                </ul>
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