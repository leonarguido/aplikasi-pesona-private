<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stock Opname</title>
    <?php
    define('BASE_URL', 'https://pesona.bpmpbali.id/routes/web.php/?page=');
    define('ASSETS_URL', 'https://pesona.bpmpbali.id/assets/');
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
            border-bottom: 3px double black;
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

        .table-data th,
        .table-data td {
            border: 1px solid black;
            padding: 3px;
            text-align: left;
            vertical-align: top;
        }

        .table-data th {
            background-color: #f0f0f0;
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

        /* TOMBOL PRINT */
        @media print {
            .no-print {
                display: none;
            }

            @page {
                margin: 1cm;
                size: auto;
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
            <a href="<?= BASE_URL ?>laporan_stock_opname" class="btn-back">⬅ Kembali</a>
            <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
        </div>

        <div class="content">
            <table>
                <tr>
                    <td width="160px"><strong>UAPPB-W</strong></td>
                    <td>: <strong>PROVINSI BALI</strong></td>
                </tr>
                <tr>
                    <td width="160px"><strong>UAKPB</strong></td>
                    <td>: <strong>BPMP PROVINSI BALI</strong></td>
                </tr>
                <tr>
                    <td width="160px"><strong>KODE UAKPB</strong></td>
                    <td>: <strong></strong></td>
                </tr>
                <tr>
                    <td width="160px"><strong>TAHUN ANGGARAN</strong></td>
                    <td>: <strong><?= date('Y'); ?></strong></td>
                </tr>
            </table>
        </div>

        <?php if ($kategori == 'item'): ?>
            <div class="content">
                <table>
                    <tr>
                        <td width="100%" style="text-align: center;"><strong>KARTU STOCK BARANG</strong></td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <div class="content">
                <table>
                    <tr>
                        <td width="160px"><strong>NAMA BARANG</strong></td>
                        <td>: <strong><?= $data['data_barang']['nama_barang']; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="160px"><strong>SATUAN</strong></td>
                        <td>: <strong><?= $data['data_barang']['satuan']; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="160px"><strong>KODE BARANG</strong></td>
                        <td>: <strong><?= $data['data_barang']['kode_barang']; ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="content" style="margin-top: 30px;">
                <table class="table-data">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"><?= $tanggal_saldo_awal; ?></td>
                            <td>Saldo Awal</td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"></td>
                            <td style="text-align: center;"><?= $saldo_awal != 0 ? $saldo_awal : ''; ?></td>
                        </tr>
                        <?php
                        $no = 1;
                        foreach ($hasil as $row):
                        ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++; ?></td>
                                <td style="text-align: center;"><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= !empty($row['keterangan']) ? $row['keterangan'] : ''; ?></td>
                                <td style="text-align: center;"><?= $row['masuk'] != 0 ? $row['masuk'] : ''; ?></td>
                                <td style="text-align: center;"><?= $row['keluar'] != 0 ? $row['keluar'] : ''; ?></td>
                                <td style="text-align: center;"><?= $row['sisa'] != 0 ? $row['sisa'] : ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($kategori == 'pegawai'): ?>
            <div class="content">
                <table>
                    <tr>
                        <td width="100%" style="text-align: center;"><strong>KARTU PENGELUARAN BARANG</strong></td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <div class="content">
                <table>
                    <tr>
                        <td width="160px"><strong>NAMA PEGAWAI</strong></td>
                        <td>: <strong><?= $data_pegawai['nama']; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="160px"><strong>NIP</strong></td>
                        <td>: <strong><?= $data_pegawai['nip']; ?></strong></td>
                    </tr>
                    <tr>
                        <td width="160px"><strong>JABATAN</strong></td>
                        <td>: <strong><?= $data_pegawai['role']; ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="content" style="margin-top: 30px;">
                <table class="table-data">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pemohon</th>
                            <th>Rincian Barang (Disetujui)</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)):
                            $id_req = $row['id'];

                            // DETAIL BARANG
                            $q_detail = mysqli_query($koneksi, "
                            SELECT d.jumlah, d.satuan, b.nama_barang, b.merek_barang
                            FROM tb_detail_permintaan d
                            JOIN tb_barang_bergerak b ON d.barang_id = b.id
                            WHERE d.permintaan_id = '$id_req'
                        ");

                            $barang = [];
                            $jumlah = [];
                            $satuan = [];

                            while ($item = mysqli_fetch_assoc($q_detail)) {
                                $barang[] = $item['nama_barang'] . ' (' . $item['merek_barang'] . ')';
                                $jumlah[] = $item['jumlah'];
                                $satuan[] = $item['satuan'];
                            }
                        ?>

                            <tr>
                                <td style="text-align:center;"><?= $no ?></td>
                                <td style="text-align:center;"><?= date('d-m-Y', strtotime($row['tanggal_disetujui'])) ?></td>
                                <td><?= $row['nama_pemohon'] ?></td>

                                <td>
                                    <ul style="margin:0;padding-left:18px;">
                                        <?php foreach ($barang as $b): ?>
                                            <li><?= $b ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>

                                <td style="text-align:center;">
                                    <ul style="margin:0;list-style:none;padding-left:0;">
                                        <?php foreach ($jumlah as $j): ?>
                                            <li><?= $j ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>

                                <td style="text-align:center;">
                                    <ul style="margin:0;list-style:none;padding-left:0;">
                                        <?php foreach ($satuan as $s): ?>
                                            <li><?= $s ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>

                        <?php
                            $no++;
                        endwhile;
                        ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($kategori == 'tanggal'): ?>
            <div class="content">
                <table>
                    <tr>
                        <td width="100%" style="text-align: center;"><strong>KARTU PENGELUARAN BARANG</strong></td>
                        <td></td>
                    </tr>
                </table>
            </div>

            <div class="content">
                <table>
                    <tr>
                        <td width="50px">Tanggal</td>
                        <td>: <?= date('d-m-Y', strtotime($tgl_mulai)) ?> s.d <?= date('d-m-Y', strtotime($tgl_selesai)) ?></td>
                    </tr>
                </table>
                
                <table class="table-data" style="margin: 0;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Merek Barang</th>
                            <th>Nama Barang</th>
                            <th>Total Barang Keluar</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)):
                        ?>
                            <tr>
                                <td style="text-align:center;"><?= $no ?></td>
                                <td><?= $row['kode_barang'] ?></td>
                                <td><?= $row['merek_barang'] ?></td>
                                <td><?= $row['nama_barang'] ?></td>
                                <td style='text-align:center;'><?= $row['total_keluar'] ?></td>
                                <td style='text-align:center'><?= $row['satuan'] ?></td>
                            </tr>
                        <?php
                            $no++;
                        endwhile;
                        ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="content">
                <table>
                    <tr>
                        <td width="100%" style="text-align: center;"><strong>KARTU STOCK BARANG</strong></td>
                        <td></td>
                    </tr>
                </table>

                <p style="text-align: center; margin-top: 50px;"><em>Data tidak ditemukan</em></p>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>