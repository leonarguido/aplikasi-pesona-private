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
                            <h6 class="m-0 font-weight-bold text-primary">Data Stok Per Tanggal: <?= date('d F Y'); ?></h6>
                            <button onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-print fa-sm text-white-50"></i> Cetak / Simpan PDF
                            </button>
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
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        // Ambil semua data barang diurutkan nama
                                        $query = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 ORDER BY nama_barang ASC");

                                        while ($row = mysqli_fetch_assoc($query)):
                                            // Logika Status Stok
                                            $status = "";
                                            $class_stok = "";
                                            if ($row['stok'] == 0) {
                                                $status = "<span class='badge badge-danger'>Habis</span>";
                                                $class_stok = "color: red; font-weight: bold;";
                                            } elseif ($row['stok'] < 10) { // Angka 10 bisa diubah sesuai kebijakan
                                                $status = "<span class='badge badge-warning'>Menipis</span>";
                                                $class_stok = "color: orange; font-weight: bold;";
                                            } else {
                                                $status = "<span class='badge badge-success'>Aman</span>";
                                                $class_stok = "color: green; font-weight: bold;";
                                            }
                                        ?>
                                            <tr>
                                                <td style="text-align:center;"><?= $no++; ?></td>
                                                <td><?= $row['kode_barang']; ?></td>
                                                <td><?= $row['merk_barang']; ?></td>
                                                <td><?= $row['nama_barang']; ?></td>
                                                <td style="text-align:center; font-size: 1.1em; <?= $class_stok; ?>">
                                                    <?= $row['stok']; ?>
                                                </td>
                                                <td style="text-align:center;"><?= $row['satuan']; ?></td>
                                                <td class="no-print"><?= $status; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
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
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable({
                    "language": {
                        "search": "Cari Stok:",
                        "lengthMenu": "Tampilkan _MENU_ antrian",
                        "zeroRecords": "Tidak ada stok yang cocok",
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