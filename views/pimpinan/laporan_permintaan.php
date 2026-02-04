<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Permintaan"; ?>
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

                    <style>
                        @media print {
                            body * {
                                visibility: hidden;
                            }

                            #printableArea,
                            #printableArea * {
                                visibility: visible;
                            }

                            #printableArea {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                            }

                            .no-print {
                                display: none;
                            }
                        }
                    </style>

                    <div class="card shadow mb-4" id="printableArea">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Total Permintaan per Tanggal: <?= date('d F Y'); ?></h6>
                            <!-- <button onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-print fa-sm text-white-50"></i> Cetak / Simpan PDF
                            </button> -->
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th width="5%" style="text-align:center;">No</th>
                                            <th width="15%">Tanggal</th>
                                            <th width="15%">Pemohon</th>
                                            <th>Rincian Barang (Final)</th>
                                            <th width="15%" style="text-align:center;">Status</th>
                                            <th width="15%" class="no-print">Admin Eksekutor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel_laporan_permintaan">
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-5 d-none d-print-block">
                                <div class="col-4 offset-8 text-center">
                                    <p>Denpasar, <?= date('d F Y'); ?></p>
                                    <p>Mengetahui,</p>
                                    <br><br><br>
                                    <p><strong>( Pimpinan )</strong></p>
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
        function load_laporan_permintaan($status_permintaan) {
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_laporan_permintaan',
                type: 'POST',
                data: {
                    status_permintaan_post: $status_permintaan,
                },
                success: function(res) {
                    $('#dataTable').DataTable().destroy();

                    if (!$.fn.DataTable.isDataTable('#dataTable')) {
                        $('#tabel_laporan_permintaan').html(res);
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
    
                        $('#dataTable_filter').append(`
                        Filter:
                            <label>
                                <select name="status_permintaan" class="form-control form-control-sm">
                                    <option value="" ${$status_permintaan === '' ? 'selected' : ''}>Semua Status</option>
                                    <option value="menunggu" ${$status_permintaan === 'menunggu' ? 'selected' : ''}>Menunggu</option>
                                    <option value="disetujui" ${$status_permintaan === 'disetujui' ? 'selected' : ''}>Disetujui</option>
                                    <option value="ditolak" ${$status_permintaan === 'ditolak' ? 'selected' : ''}>Ditolak</option>
                                </select>
                            </label>
                        `);                    
                    }

                }
            });
        }

        // load awal page
        load_laporan_permintaan(<?= json_encode($status_permintaan) ?>);

        $(document).on('change', 'select[name="status_permintaan"]', function() {
            $status_permintaan = this.value;
            load_laporan_permintaan($status_permintaan);
        });

        if (window.innerHeight <= 700) {
            document.getElementById('accordionSidebar')
                .style.height = '100vh';
        }
    </script>
</body>

</html>