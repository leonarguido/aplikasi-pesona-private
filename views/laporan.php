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
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3 border-bottom-primary">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Laporan per Tanggal</h6>
                            </div>
                            <div class="card-body">
                                <form action="<?= BASE_URL ?>cetak_laporan" method="POST" target="_blank">

                                    <div class="form-group">
                                        <label class="font-weight-bold small text-secondary text-uppercase">Dari Tanggal</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" name="tgl_mulai" class="form-control bg-light border-0 small" required
                                                value="<?= date('Y-m-01'); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold small text-secondary text-uppercase">Sampai Tanggal</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" name="tgl_selesai" class="form-control bg-light border-0 small" required
                                                value="<?= date('Y-m-d'); ?>">
                                        </div>
                                    </div>

                                    <hr>

                                    <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                        <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan (PDF)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow h-100 border-left-info">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            <i class="fas fa-info-circle"></i> Petunjuk Penggunaan
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Cara Cetak Laporan</div>
                                        <div class="mt-3 text-gray-600 small">
                                            <ul class="pl-3 mb-0">
                                                <li class="mb-2">Pilih rentang tanggal transaksi yang ingin dicetak pada formulir di samping.</li>
                                                <li class="mb-2">Klik tombol biru bertuliskan <b>Cetak Laporan (PDF)</b>.</li>
                                                <li class="mb-2">Sistem akan membuka tab baru berisi pratinjau laporan siap cetak.</li>
                                                <li>Gunakan fitur browser <b>(Ctrl+P)</b> lalu pilih <b>"Save as PDF"</b> atau <b>"Simpan sebagai PDF"</b> pada opsi printer untuk menyimpan file.</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-file-pdf fa-2x text-gray-300"></i>
                                    </div>
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