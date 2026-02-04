<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Log Barang Bergerak"; ?>
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
                        <div class="col-lg-12 mb-4">
                            <div class="card shadow mb-4" id="printableArea">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <button id="bulan_lalu_log" class="btn btn-sm btn-outline-primary mr-2">&laquo;</button>
                                        <h6 class="m-0 font-weight-bold text-primary" id="bulan_ini_log">
                                            Log Barang per Bulan: <?= date('F', mktime(0, 0, 0, $bulan_angka, 10)) . " " . $tahun_angka; ?>
                                        </h6>
                                        <button id="bulan_depan_log" class="btn btn-sm btn-outline-primary ml-2">&raquo;</button>
                                    </div>
                                    <input type="text" name="kalender" id="kalender_log" value="<?= $tahun_angka ?>-<?= $bulan_angka ?>" class="d-none d-sm-inline-block btn btn-sm btn-outline-primary shadow-sm" readonly>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="dataTableLog" width="100%" cellspacing="0">
                                            <thead class="bg-dark text-white">
                                                <tr>
                                                    <th width="10%">Tanggal</th>
                                                    <th width="10%">Admin</th>
                                                    <th width="10%">Kode Barang</th>
                                                    <th width="20%">Nama Barang</th>
                                                    <th width="10%" style="text-align:center;">Histori Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabel_log">
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
        // LOG BARANG
        // ==========================================

        // 1. LOAD LOG BARANG OTOMATIS (AJAX)
        function load_log($bulan_angka, $tahun_angka) {
            // console.log("Loading LOG BARANG untuk bulan: " + $bulan_angka + ", tahun: " + $tahun_angka);
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_log_barang_bergerak',
                type: 'POST',
                data: {
                    bulan_angka_post: $bulan_angka,
                    tahun_angka_post: $tahun_angka
                },
                success: function(res) {
                    if ($.fn.DataTable.isDataTable('#dataTableLog')) {
                        $('#dataTableLog').DataTable().destroy();
                    }

                    $('#tabel_log').html(res);
                    $('#dataTableLog').DataTable({
                        "order": [[0, 'desc']],
                        "language": {
                            "search": "Cari Log:",
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
        load_log(<?= json_encode($bulan_angka) ?>, <?= json_encode($tahun_angka) ?>);


        // 2. LOAD BULAN OTOMATIS
        $('#kalender_log').datepicker({
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months",
            language: "id",
            autoclose: true
        });

        function load_bulan_log($bulan_angka, $tahun_angka) {
            $('#bulan_ini_log').text('Log Barang Bergerak per Bulan: ' + new Date($tahun_angka, $bulan_angka - 1).toLocaleString('id-ID', {
                month: 'long'
            }) + ' ' + $tahun_angka);

            $('#kalender_log').val($tahun_angka + '-' + String($bulan_angka).padStart(2, '0'));
        }
        // load awal bulan
        load_bulan_log(<?= json_encode($bulan_angka) ?>, <?= json_encode($tahun_angka) ?>);


        // 3. KLIK KALENDER LOG BARANG
        $(document).on('change', '#kalender_log', function() {
            let date = $(this).val(); // format YYYY-MM
            let parts = date.split('-');
            $tahun_angka = parseInt(parts[0]);
            $bulan_angka = parseInt(parts[1]);
            load_log($bulan_angka, $tahun_angka);
            load_bulan_log($bulan_angka, $tahun_angka);
        });


        // 4. KLIK NEXT BUTTON
        $(document).on('click', '#bulan_depan_log', function(e) {
            e.preventDefault();
            let date = $('#kalender_log').val(); // format YYYY-MM
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
            load_log($bulan_angka, $tahun_angka);
            load_bulan_log($bulan_angka, $tahun_angka);
        });


        // 5. KLIK PREVIOUS BUTTON
        $(document).on('click', '#bulan_lalu_log', function(e) {
            e.preventDefault();
            let date = $('#kalender_log').val(); // format YYYY-MM
            let parts = date.split('-');
            $bulan_angka = parseInt(parts[1]);
            $tahun_angka = parseInt(parts[0]);
            if ($bulan_angka == 1) {
                $bulan_angka = 12;
                $tahun_angka--;
            } else {
                $bulan_angka--;
            }
            load_log($bulan_angka, $tahun_angka);
            load_bulan_log($bulan_angka, $tahun_angka);
        });


        if (window.innerHeight <= 700) {
            document.getElementById('accordionSidebar')
                .style.height = '100vh';
        }
    </script>
</body>

</html>