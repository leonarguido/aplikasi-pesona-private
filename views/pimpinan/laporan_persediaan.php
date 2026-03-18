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
    <?php $judul_halaman = "Laporan Persediaan"; ?>
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
                        <div class="row">
                            <div class="col-lg-12 mb-4">
                                <div class="card shadow h-100">
                                    <div class="card-header py-3 border-bottom-primary">
                                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter"></i> Filter Laporan Persediaan Berdasarkan:</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= BASE_URL ?>cetak_laporan_persediaan" target="_blank" method="POST">

                                            <div class="row">
                                                <div class="col">

                                                    <div class="form-group">
                                                        <label class="font-weight-bold small text-secondary text-uppercase">Pilih Filter: <span class="text-danger">*</span></label>
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
                                                                <label class="font-weight-bold small text-secondary text-uppercase">Dari Tanggal <span class="text-danger">*</span></label>
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
                                                                <label class="font-weight-bold small text-secondary text-uppercase">Sampai Tanggal <span class="text-danger">*</span></label>
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
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label class="font-weight-bold small text-secondary text-uppercase">Kode UAKPB <span class="text-danger">*</span></label>
                                                    <input type="text" name="kode" class="form-control" placeholder="Masukkan Kode..." required>
                                                </div>
                                            </div>
                                            <hr>
                                            <button type="submit" name="cetak_laporan_persediaan" class="btn btn-primary btn-block shadow-sm">
                                                <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan
                                            </button>
                                    </div>
                                    </form>
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
            $('#selectItemFilter').attr('required', 'required');

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
                $('#selectItemFilter').attr('required', 'required');
                $('#selectPegawaiFilter').removeAttr('required');

                $('.select2-item').select2({
                    dropdownParent: $('#selectItemFilter').parent(),
                    placeholder: "Pilih Item...",
                    allowClear: true
                });
            } else if (kategori === 'pegawai') {
                $('#kolom_item').hide();
                $('#kolom_pegawai').show();
                $('#selectPegawaiFilter').attr('required', 'required');
                $('#selectItemFilter').removeAttr('required');

                $('.select2-pegawai').select2({
                    dropdownParent: $('#selectPegawaiFilter').parent(),
                    placeholder: "Pilih Pegawai...",
                    allowClear: true
                });
            } else if (kategori === 'tanggal') {
                $('#kolom_item').hide();
                $('#kolom_pegawai').hide();
                $('#selectItemFilter').removeAttr('required');
                $('#selectPegawaiFilter').removeAttr('required');
            }
        });
    </script>
</body>

</html>