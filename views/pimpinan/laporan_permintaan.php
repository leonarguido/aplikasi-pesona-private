<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Permintaan"; ?>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            font-size: 0.875rem;
            height: 32px !important;
            border: 1px solid #d1d3e2;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 0.875rem;
            line-height: 32px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            font-size: 0.875rem;
            height: 32px !important;
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
                                <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan</h6>
                                <!-- <button onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                    <i class="fas fa-print fa-sm text-white-50"></i> Cetak / Simpan PDF
                                </button> -->
                                <div>
                                    <label>
                                        <select name="status" id="selectStatusFilter" class="form-control form-control-sm select2-status">
                                            <option value="">Semua Status</option>
                                            <option value="disetujui" ${$status==='disetujui' ? 'selected' : '' }>Disetujui</option>
                                            <option value="menunggu" ${$status==='menunggu' ? 'selected' : '' }>Menunggu</option>
                                            <option value="ditolak" ${$status==='ditolak' ? 'selected' : '' }>Ditolak</option>
                                            <option value="selesai" ${$status==='selesai' ? 'selected' : '' }>Selesai</option>
                                            <option value="batal_otomatis" ${$status==='batal_otomatis' ? 'selected' : '' }>Batal Otomatis</option>
                                        </select>
                                    </label>
                                    <label>
                                        <select name="pegawai" id="selectPegawaiFilter" class="form-control form-control-lg select2-pegawai">
                                            <option value="">Semua Pegawai</option>
                                            <?php $query_pegawai = mysqli_query($koneksi, "SELECT * FROM tb_user"); ?>
                                            <?php while ($row_pegawai = mysqli_fetch_assoc($query_pegawai)): ?>
                                                <option value="<?= $row_pegawai['id']; ?>" <?= (isset($_POST['pegawai']) && $_POST['pegawai'] == $row_pegawai['id']) ? 'selected' : ''; ?>>
                                                    <?= $row_pegawai['nama']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </label>
                                </div>
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
            $('.select2-pegawai').select2({
                dropdownParent: $('#selectPegawaiFilter').parent()
            });
            $('.select2-status').select2({
                dropdownParent: $('#selectStatusFilter').parent()
            });
        });

        // load awal page
        load_laporan_permintaan_pegawai(<?= json_encode($pegawai) ?>);

        function load_laporan_permintaan_status($status) {
            $('select[name="pegawai"]').val('');
            $('#selectPegawaiFilter').val('').trigger('change.select2');
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_laporan_permintaan_status',
                type: 'POST',
                data: {
                    status_post: $status
                },
                success: function(res) {
                    $('#dataTable').DataTable().destroy();

                    if (!$.fn.DataTable.isDataTable('#dataTable')) {
                        $('#tabel_laporan_permintaan').html(res);
                        $('#dataTable').DataTable({
                            "language": {
                                "search": "Cari:",
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
                }
            });
        }

        $(document).on('change', 'select[name="status"]', function() {
            $status = this.value;
            load_laporan_permintaan_status($status);
        });

        function load_laporan_permintaan_pegawai($pegawai) {
            $('select[name="status"]').val('');
            $('#selectStatusFilter').val('').trigger('change.select2');
            $.ajax({
                url: '<?= BASE_URL ?>ajax_load_laporan_permintaan_pegawai',
                type: 'POST',
                data: {
                    pegawai_post: $pegawai
                },
                success: function(res) {
                    $('#dataTable').DataTable().destroy();

                    if (!$.fn.DataTable.isDataTable('#dataTable')) {
                        $('#tabel_laporan_permintaan').html(res);
                        $('#dataTable').DataTable({
                            "language": {
                                "search": "Cari:",
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
                }
            });
        }

        $(document).on('change', 'select[name="pegawai"]', function() {
            $pegawai = this.value;
            load_laporan_permintaan_pegawai($pegawai);
        });
    </script>
</body>

</html>