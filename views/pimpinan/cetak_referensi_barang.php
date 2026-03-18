<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Referensi Barang</title>
    <?php
    define('BASE_URL', '/aplikasi-pesona-private/routes/web.php/?page=');
    define('ASSETS_URL', '/aplikasi-pesona-private/assets/');
    ?>
    <style>
        body {
            font-family: 'Arial', serif;
            font-size: 12pt;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: auto;
        }

        /* KOP SURAT */
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo-kop {
            width: 100px;
            height: auto;
        }

        .text-kop {
            text-align: center;
            line-height: 1.3;
        }

        .text-kop h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: normal;
            font-family: 'Arial', serif;
        }

        .text-kop h1 {
            margin: 5px 0;
            font-size: 16pt;
            font-weight: bold;
            font-family: 'Arial', serif;
        }

        .text-kop p {
            margin: 0;
            font-size: 10pt;
        }

        /* ISI SURAT */
        .content {
            margin-bottom: 15px;
            line-height: 1.2;
        }

        /* TABEL BARANG */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .table-data td {
            padding: 3px;
            vertical-align: top;
        }

        .table-data th {
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            padding: 3px;
            text-align: center;
        }

        /* TANDA TANGAN */
        .ttd-wrapper {
            width: 100%;
            display: table;
            margin-top: 50px;
        }

        .ttd-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .img-ttd {
            width: 100px;
            height: auto;
            display: block;
            margin: 10px auto;
        }

        .space-ttd {
            height: 80px;
        }

        /* PRINT SETTINGS */
        @media print {

            @page {
                size: A4;
                margin: 1cm;
            }

            .no-print {
                display: none;
            }

            thead {
                display: table-header-group;
            }

            .print-header {
                position: running(header);
            }

        }

        .btn-back {
            background: #e74a3b;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .btn-print {
            background: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="no-print" style="margin-bottom: 20px;">
            <a href="<?= BASE_URL ?>laporan_stok" class="btn-back">⬅ Kembali</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
        </div>

        <div class="print-header">
            <div class="content">
                <table>
                    <tr>
                        <td width="100%" style="text-align: center;"><strong>REFERENSI BARANG</strong></td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <?php 
            $total_data = mysqli_num_rows($data);
            $total_halaman = ceil($total_data / 35); 
            ?>
            <div class="content">
                <table class="table-data">
                    <thead>
                        <tr>
                            <td width="110px">KODE UAKPB</td>
                            <td width="300px">: <?= $kode ?></td>
                            <td width="110px">Tanggal</td>
                            <td>: <?= date('d-m-Y'); ?></td>
                        </tr>
                        <tr>
                            <td width="110px">UAKPB</td>
                            <td width="300px">: BPMP PROVINSI BALI</td>
                            <td width="110px">Halaman</td>
                            <td>: 1 dari <?= $total_halaman ?></td>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="content">
            <table class="table-data">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="20%">Kode Barang</th>
                        <th width="20%">Satuan</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>

                <?php $no = 1;
                $page = 2;
                foreach ($data as $row): ?>
                    <tr>
                        <td style="text-align: center;"><?= $no ?></td>
                        <td style="text-align: center;"><?= $row['kode_barang'] ?></td>
                        <td style="text-align: center;"><?= $row['satuan'] ?></td>
                        <td><?= $row['nama_barang'] ?> <?php if ($row['merek_barang']): ?> (<?= $row['merek_barang'] ?>) <?php else: ?> (Tidak ada merek) <?php endif ?></td>
                    </tr>
                    <?php if ($no % 35 == 0): ?>
                        </tbody>
            </table>

            <div class="page-break"></div>

            <div class="print-header">
                <div class="content">
                    <table>
                        <tr>
                            <td width="100%" style="text-align: center;"><strong>REFERENSI BARANG</strong></td>
                            <td></td>
                        </tr>
                    </table>
                </div>

                <div class="content">
                    <table class="table-data">
                        <thead>
                            <tr>
                                <td width="110px">KODE UAKPB</td>
                                <td width="300px">: </td>
                                <td width="110px">Tanggal</td>
                                <td>: <?= date('d-m-Y'); ?></td>
                            </tr>
                            <tr>
                                <td width="110px">UAKPB</td>
                                <td width="300px">: BPMP PROVINSI BALI</td>
                                <td width="110px">Halaman</td>
                                <td>: <?= $page++ ?> dari <?= $total_halaman ?></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <table class="table-data">
                <thead>
                    <tr>
                        <th width="10%">No</th>
                        <th width="20%">Kode Barang</th>
                        <th width="20%">Satuan</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                <?php endif; ?>

            <?php $no++;
                endforeach; ?>
        </div>
    </div>

</body>

</html>