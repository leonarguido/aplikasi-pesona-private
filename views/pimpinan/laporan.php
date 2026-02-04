<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Transaksi Berhasil"; ?>
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
                    <div class="row">
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 border-bottom-primary">
                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Laporan Transaksi per Tanggal</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?= BASE_URL ?>cetak_laporan" method="POST" target="_blank">

                                        <div class="row">
                                            <div class="col">
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
                                            </div>
                                            <div class="col">
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
                    </div>

                    <!-- LAPORAN BARANG KELUAR -->
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card shadow mb-4" id="printableArea">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <button id="bulan_lalu_stok_barang" class="btn btn-sm btn-outline-primary mr-2">&laquo;</button>
                                        <h6 class="m-0 font-weight-bold text-primary" id="bulan_ini_stok_barang">
                                            Total Barang Keluar per Bulan: <?= date('F', mktime(0, 0, 0, $bulan_angka, 10)) . " " . $tahun_angka; ?>
                                        </h6>
                                        <button id="bulan_depan_stok_barang" class="btn btn-sm btn-outline-primary ml-2">&raquo;</button>
                                    </div>
                                    <input type="text" name="kalender" id="kalender_stok_barang" value="<?= $tahun_angka ?>-<?= $bulan_angka ?>" class="d-none d-sm-inline-block btn btn-sm btn-outline-primary shadow-sm" readonly>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="dataTableStok" width="100%" cellspacing="0">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th width="5%" style="text-align:center;">No</th>
                                                    <th width="10%">Kode Barang</th>
                                                    <th width="10%">Merk Barang</th>
                                                    <th width="20%">Nama Barang</th>
                                                    <th width="15%" style="text-align:center;">Total Barang Keluar</th>
                                                    <th width="10%" style="text-align:center;">Satuan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabel_stok_barang">
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LAPORAN RIWAYAT PERSETUJUAN -->
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card shadow mb-4" id="printableArea">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <button id="bulan_lalu_riwayat_persetujuan" class="btn btn-sm btn-outline-primary mr-2">&laquo;</button>
                                        <h6 class="m-0 font-weight-bold text-primary" id="bulan_ini_riwayat_persetujuan">
                                            Riwayat Persetujuan per Bulan: <?= date('F', mktime(0, 0, 0, $bulan_angka, 10)) . " " . $tahun_angka; ?>
                                        </h6>
                                        <button id="bulan_depan_riwayat_persetujuan" class="btn btn-sm btn-outline-primary ml-2">&raquo;</button>
                                    </div>
                                    <input type="text" name="kalender" id="kalender_riwayat_persetujuan" value="<?= $tahun_angka ?>-<?= $bulan_angka ?>" class="d-none d-sm-inline-block btn btn-sm btn-outline-primary shadow-sm" readonly>

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="dataTablePermintaan" width="100%" cellspacing="0">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th width="5%" style="text-align:center;">No</th>
                                                    <th width="10%">Tanggal Setuju</th>
                                                    <th width="10%">Pemohon</th>
                                                    <th width="30%">Rincian Barang (Disetujui)</th>
                                                    <th width="10%" style="text-align:center;">Jumlah</th>
                                                    <th width="10%" style="text-align:center;">Satuan</th>
                                                    <th width="10%">Admin Penyetuju</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabel_riwayat_persetujuan">
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
    </div>

    <?php require '../views/layout/footer.php'; ?>

    <script>
        // ==========================================
        // STOK BARANG
        // ==========================================

        // 1. LOAD STOK BARANG OTOMATIS (AJAX)
        function load_stok_barang($bulan_angka, $tahun_angka) {
            // console.log("Loading stok barang untuk bulan: " + $bulan_angka + ", tahun: " + $tahun_angka);
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_stok_barang',
                type: 'POST',
                data: {
                    bulan_angka_post: $bulan_angka,
                    tahun_angka_post: $tahun_angka
                },
                success: function(res) {
                    if ($.fn.DataTable.isDataTable('#dataTableStok')) {
                        $('#dataTableStok').DataTable().destroy();
                    }

                    $('#tabel_stok_barang').html(res);
                    $('#dataTableStok').DataTable({
                        "language": {
                            "search": "Cari Stok:",
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
        }
        // load awal page
        load_stok_barang(<?= json_encode($bulan_angka) ?>, <?= json_encode($tahun_angka) ?>);


        // 2. LOAD BULAN OTOMATIS
        $('#kalender_stok_barang').datepicker({
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months",
            language: "id",
            autoclose: true
        });

        function load_bulan_stok_barang($bulan_angka, $tahun_angka) {
            $('#bulan_ini_stok_barang').text('Total Barang Keluar per Bulan: ' + new Date($tahun_angka, $bulan_angka - 1).toLocaleString('id-ID', {
                month: 'long'
            }) + ' ' + $tahun_angka);

            $('#kalender_stok_barang').val($tahun_angka + '-' + String($bulan_angka).padStart(2, '0'));
        }
        // load awal bulan
        load_bulan_stok_barang(<?= ($bulan_angka) ?>, <?= $tahun_angka ?>);


        // 3. KLIK KALENDER STOK BARANG
        $(document).on('change', '#kalender_stok_barang', function() {
            let date = $(this).val(); // format YYYY-MM
            let parts = date.split('-');
            $tahun_angka = parseInt(parts[0]);
            $bulan_angka = parseInt(parts[1]);
            load_stok_barang($bulan_angka, $tahun_angka);
            load_bulan_stok_barang($bulan_angka, $tahun_angka);
        });


        // 4. KLIK NEXT BUTTON
        $(document).on('click', '#bulan_depan_stok_barang', function(e) {
            e.preventDefault();
            let date = $('#kalender_stok_barang').val(); // format YYYY-MM
            let parts = date.split('-');
            $bulan_angka = parseInt(parts[1]);
            $tahun_angka = parseInt(parts[0]);
            if ($bulan_angka == 12) {
                $bulan_angka = 1;
                $tahun_angka++;
            } else {
                $bulan_angka++;
            }
            // console.log($bulan_angka, $tahun_angka);
            load_stok_barang($bulan_angka, $tahun_angka);
            load_bulan_stok_barang($bulan_angka, $tahun_angka);
        });


        // 5. KLIK PREVIOUS BUTTON
        $(document).on('click', '#bulan_lalu_stok_barang', function(e) {
            e.preventDefault();
            let date = $('#kalender_stok_barang').val(); // format YYYY-MM
            let parts = date.split('-');
            $bulan_angka = parseInt(parts[1]);
            $tahun_angka = parseInt(parts[0]);
            if ($bulan_angka == 1) {
                $bulan_angka = 12;
                $tahun_angka--;
            } else {
                $bulan_angka--;
            }
            load_stok_barang($bulan_angka, $tahun_angka);
            load_bulan_stok_barang($bulan_angka, $tahun_angka);
        });



        // ==========================================
        // RIWAYAT PERSETUJUAN
        // ==========================================

        // 2. LOAD RIWAYAT PERSETUJUAN OTOMATIS (AJAX)
        function load_riwayat_persetujuan($bulan_angka, $tahun_angka) {
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_riwayat_persetujuan',
                type: 'POST',
                data: {
                    bulan_angka_post: $bulan_angka,
                    tahun_angka_post: $tahun_angka
                },
                success: function(res) {
                    if ($.fn.DataTable.isDataTable('#dataTablePermintaan')) {
                        $('#dataTablePermintaan').DataTable().destroy();
                    }

                    $('#tabel_riwayat_persetujuan').html(res);
                    $('#dataTablePermintaan').DataTable({
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
        }
        // load awal page
        load_riwayat_persetujuan(<?= ($bulan_angka) ?>, <?= $tahun_angka ?>)

        // 2. LOAD BULAN OTOMATIS
        $('#kalender_riwayat_persetujuan').datepicker({
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months",
            language: "id",
            autoclose: true
        });

        function load_bulan_riwayat_persetujuan($bulan_angka, $tahun_angka) {
            $('#bulan_ini_riwayat_persetujuan').text('Riwayat Persetujuan per Bulan: ' + new Date($tahun_angka, $bulan_angka - 1).toLocaleString('id-ID', {
                month: 'long'
            }) + ' ' + $tahun_angka);

            $('#kalender_riwayat_persetujuan').val($tahun_angka + '-' + String($bulan_angka).padStart(2, '0'));
        }
        // load awal bulan
        load_bulan_riwayat_persetujuan(<?= ($bulan_angka) ?>, <?= $tahun_angka ?>);


        // 3. KLIK KALENDER STOK BARANG
        $(document).on('change', '#kalender_riwayat_persetujuan', function() {
            let date = $(this).val(); // format YYYY-MM
            let parts = date.split('-');
            $tahun_angka = parseInt(parts[0]);
            $bulan_angka = parseInt(parts[1]);
            load_riwayat_persetujuan($bulan_angka, $tahun_angka);
            load_bulan_riwayat_persetujuan($bulan_angka, $tahun_angka);
        });


        // 4. KLIK NEXT BUTTON
        $(document).on('click', '#bulan_depan_riwayat_persetujuan', function(e) {
            e.preventDefault();
            let date = $('#kalender_riwayat_persetujuan').val(); // format YYYY-MM
            let parts = date.split('-');
            $bulan_angka = parseInt(parts[1]);
            $tahun_angka = parseInt(parts[0]);
            if ($bulan_angka == 12) {
                $bulan_angka = 1;
                $tahun_angka++;
            } else {
                $bulan_angka++;
            }
            // console.log($bulan_angka, $tahun_angka);
            load_riwayat_persetujuan($bulan_angka, $tahun_angka);
            load_bulan_riwayat_persetujuan($bulan_angka, $tahun_angka);
        });


        // 5. KLIK PREVIOUS BUTTON
        $(document).on('click', '#bulan_lalu_riwayat_persetujuan', function(e) {
            e.preventDefault();
            let date = $('#kalender_riwayat_persetujuan').val(); // format YYYY-MM
            let parts = date.split('-');
            $bulan_angka = parseInt(parts[1]);
            $tahun_angka = parseInt(parts[0]);
            if ($bulan_angka == 1) {
                $bulan_angka = 12;
                $tahun_angka--;
            } else {
                $bulan_angka--;
            }
            load_riwayat_persetujuan($bulan_angka, $tahun_angka);
            load_bulan_riwayat_persetujuan($bulan_angka, $tahun_angka);
        });

        if (window.innerHeight <= 700) {
            document.getElementById('accordionSidebar')
                .style.height = '100vh';
        }
    </script>
</body>

</html>