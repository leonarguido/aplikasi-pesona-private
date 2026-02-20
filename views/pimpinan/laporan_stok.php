<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Stok Barang"; ?>
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
                            <h6 class="m-0 font-weight-bold text-primary">Data Stok per Tanggal: <?= date('d F Y'); ?></h6>
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
                                            <th width="10%">Kode Barang</th>
                                            <th width="10%">Merk Barang</th>
                                            <th>Nama Barang</th>
                                            <th width="15%" style="text-align:center;">Sisa Stok</th>
                                            <th width="10%" style="text-align:center;">Satuan</th>
                                            <th width="15%" class="no-print">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabel_laporan_stok_barang">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../layout/footer.php'; ?>

    <script>
        function load_laporan_stok_barang($status_stok) {
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_laporan_stok_barang',
                type: 'POST',
                data: {
                    status_stok_post: $status_stok,
                },
                success: function(res) {
                    $('#dataTable').DataTable().destroy();

                    if (!$.fn.DataTable.isDataTable('#dataTable')) {
                        $('#tabel_laporan_stok_barang').html(res);
                        $('#dataTable').DataTable({
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
    
                        $('#dataTable_filter').append(`
                        Filter:
                            <label>
                                <select name="status_stok" class="form-control form-control-sm">
                                    <option value="" ${$status_stok === '' ? 'selected' : ''}>Semua Status</option>
                                    <option value="aman" ${$status_stok === 'aman' ? 'selected' : ''}>Aman</option>
                                    <option value="menipis" ${$status_stok === 'menipis' ? 'selected' : ''}>Menipis</option>
                                    <option value="habis" ${$status_stok === 'habis' ? 'selected' : ''}>Habis</option>
                                </select>
                            </label>
                        `);
                    }
                }
            });
        }
        // load awal page
        load_laporan_stok_barang(<?= json_encode($status_stok) ?>);

        $(document).on('change', 'select[name="status_stok"]', function() {
            $status_stok = this.value;
            load_laporan_stok_barang($status_stok);
        });

         
    </script>
</body>

</html>