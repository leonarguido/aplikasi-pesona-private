<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #d1d3e2;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>

    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Laporan Stock Opname"; ?>
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
                            <div class="card shadow h-100">
                                <div class="card-header py-3 border-bottom-primary">
                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Laporan Stock Opname Berdasarkan:</h6>
                                </div>
                                <div class="card-body">
                                    <form action="<?= BASE_URL ?>proses_stock_opname" method="POST">

                                        <div class="row">
                                            <div class="col">

                                                <div class="form-group">
                                                    <label class="font-weight-bold small text-secondary text-uppercase">Pilih Filter:</label>
                                                    <label class="ml-3">
                                                        <input type="radio" name="kategori" value="item" checked> Item
                                                    </label>
                                                    <label class="ml-3">
                                                        <input type="radio" name="kategori" value="pegawai"> Pegawai
                                                    </label>
                                                    <label class="ml-3">
                                                        <input type="radio" name="kategori" value="tanggal"> Tanggal
                                                    </label>
                                                </div>
                                                <div class="row">
                                                    <div class="col" id="kolom_item">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold small text-secondary text-uppercase">Pilih Item</label>
                                                            <select name="item" id="selectItemFilter" class="form-control select2-item">
                                                                <option value="">-- Pilih Item --</option>
                                                                <?php foreach ($list_barang as $brg): ?>
                                                                    <option value="<?= $brg['id']; ?>">
                                                                        <?= $brg['kode_barang']; ?> (<?= $brg['nama_barang']; ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col" id="kolom_pegawai" style="display:none;">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold small text-secondary text-uppercase">Pilih Pegawai</label>
                                                            <select name="pegawai" id="selectPegawaiFilter" class="form-control select2-pegawai">
                                                                <option value="">-- Pilih Pegawai --</option>
                                                                <?php foreach ($list_pegawai as $pgw): ?>
                                                                    <option value="<?= $pgw['id']; ?>">
                                                                        <?= $pgw['nip']; ?> (<?= $pgw['nama']; ?>)
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="kolom_tanggal">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold small text-secondary text-uppercase">Dari Tanggal</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                                </div>
                                                                <input type="date" name="tgl_mulai" class="form-control bg-light border-0 small"
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
                                            </div>
                                        </div>
                                        <hr>
                                        <button type="submit" name="proses_stock_opname" class="btn btn-primary btn-block shadow-sm">
                                            <i class="fas fa-search fa-sm text-white-50"></i> Submit
                                        </button>
                                </div>
                                </form>
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
            $('.select2-item').select2({
                dropdownParent: $('#selectItemFilter').parent(),
                placeholder: "Pilih Item...",
                allowClear: true
            });

            $('.select2-pegawai').select2({
                dropdownParent: $('#selectPegawaiFilter').parent(),
                placeholder: "Pilih Pegawai...",
                allowClear: true
            });
        });

        // ==========================================
        // FILTER LAPORAN
        // ==========================================
        $(document).on('change', 'input[name="kategori"]', function() {
            let kategori = $(this).val();
            if (kategori === 'item') {
                $('#kolom_item').show();
                $('#kolom_pegawai').hide();

                $('.select2-item').select2({
                    dropdownParent: $('#selectItemFilter').parent(),
                    placeholder: "Pilih Item...",
                    allowClear: true
                });
            } else if (kategori === 'pegawai') {
                $('#kolom_item').hide();
                $('#kolom_pegawai').show();

                $('.select2-pegawai').select2({
                    dropdownParent: $('#selectPegawaiFilter').parent(),
                    placeholder: "Pilih Pegawai...",
                    allowClear: true
                });
            } else if (kategori === 'tanggal') {
                $('#kolom_item').hide();
                $('#kolom_pegawai').hide();
            }
        });
    </script>
</body>

</html>