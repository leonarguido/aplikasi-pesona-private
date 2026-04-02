<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Permintaan Barang - <?= $data['id']; ?></title>
    <?php
    define('BASE_URL', '/aplikasi-pesona-private/routes/web.php/?page=');
    define('ASSETS_URL', '/aplikasi-pesona-private/assets/');
    ?>
    <style>
        /* UTAMA */
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
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
            font-family: 'Monserrat', sans-serif;
            width: 100%;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-area {
            text-align: center;
        }

        .logo-kop {
            width: 70px;
        }

        .text-kop h1 {
            margin: 0;
            font-size: 20.5pt;
            padding-right: 15px;
        }

        .info-kop {
            border-left: 3px solid #067AC1;
            padding-left: 15px;
        }

        .info-kop h2 {
            margin: 0;
            font-size: 12pt;
        }

        .info-kop p {
            margin: 2px 0;
            font-size: 10pt;
        }

        .bold {
            font-weight: bold;
        }

        .keterangan-kop {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 2px 0;
        }

        .logo-simbol {
            width: 14px;
        }

        .kontak {
            display: flex;
            gap: 20px;
        }

        .biru {
            color: #067AC1;
        }

        .oranye {
            color: #F58220;
        }

        /* ISI SURAT */
        .content {
            width: 80%;
            margin: auto;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .table-data th,
        .table-data td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        .table-data th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .ttd-wrapper {
            width: 100%;
            display: table;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .ttd-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .img-ttd {
            width: 100px;
            height: 80px;
            object-fit: contain;
            display: block;
            margin: 5px auto;
        }

        .space-ttd {
            width: 100px;
            height: 80px;
            display: block;
            margin: 5px auto;
        }

        /* PRINT SETTINGS */
        @media print {
            .no-print {
                display: none;
            }

            .header-table {
                width: 100%;
            }

            .content {
                width: 80%;
                margin: auto;
            }

            @page {
                paper: A4;
                margin: 0;
                padding: 0;
                size: auto;
            }

            body {
                margin: 1cm 0.5cm;
            }
        }

        .btn-print {
            background: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Helper untuk meratakan baris teks header ttd */
        .ttd-header {
            min-height: 40px;
            line-height: 1.5;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <button onclick="window.print()" class="no-print btn-print">🖨️ Cetak Surat</button>

        <table class="header-table">
            <tr>
                <td width="10%" class="logo-area">
                    <img src="<?= ASSETS_URL ?>img/logo_tut_wuri_handayani.png"
                        class="logo-kop"
                        onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_Tut_Wuri_Handayani.png'">
                </td>

                <td width="25%" class="text-kop">
                    <h1>
                        <span class="biru">Kemen</span><span class="oranye">dikdasmen</span>
                    </h1>
                </td>

                <td width="65%" class="info-kop">
                    <h2 class="biru">Kementerian Pendidikan Dasar dan Menengah</h2>
                    <p class="bold">Balai Penjaminan Mutu Pendidikan Provinsi Bali</p>
                    <p>Jalan Letda Tantular Nomor 14 Niti Mandala Renon, Denpasar Timur, Denpasar, 80234</p>
                    <p class="keterangan-kop"><img src="<?= ASSETS_URL ?>img/simbol_web.png" class="logo-simbol">
                        <span>www.kemendikdasmen.go.id</span>
                    </p>
                    <div class="kontak">
                        <p class="keterangan-kop">
                            <img src="<?= ASSETS_URL ?>img/simbol_question.png" class="logo-simbol">
                            <span>(0361) 225666</span>
                        </p>
                        <p class="keterangan-kop">
                            <img src="<?= ASSETS_URL ?>img/simbol_call.png" class="logo-simbol">
                            <span>177</span>
                        </p>
                    </div>
                </td>
            </tr>
        </table>

        <div class="content">
            <p><strong>Nomor Surat :</strong> #REQ-<?= sprintf("%04d", $data['id']); ?></p>
            <p><strong>Perihal :</strong> Bukti Serah Terima Barang (ATK)</p>

            <p>Yang bertanda tangan di bawah ini:</p>
            <table style="width: 100%; margin-left: 20px; margin-bottom: 10px;">
                <tr>
                    <td width="150">Nama</td>
                    <td>: <?= $data['nama_pemohon']; ?></td>
                </tr>
                <tr>
                    <td>NIP</td>
                    <td>: <?= !empty($data['nip_pemohon']) ? $data['nip_pemohon'] : '-'; ?></td>
                </tr>
                <tr>
                    <td>Keperluan</td>
                    <td>: <?= $data['keperluan']; ?></td>
                </tr>
            </table>

            <p>Telah menerima barang dengan rincian sebagai berikut:</p>

            <table class="table-data">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Barang</th>
                        <th width="15%">Jumlah</th>
                        <th width="15%">Satuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_detail = mysqli_query($koneksi, "SELECT d.*, b.nama_barang, b.satuan, b.kode_barang 
                                                    FROM tb_detail_permintaan d
                                                    JOIN tb_barang_habis_pakai b ON d.barang_id = b.id
                                                    WHERE d.permintaan_id = '$id_permintaan'");
                    $no = 1;
                    while ($item = mysqli_fetch_assoc($q_detail)):
                    ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++; ?></td>
                            <td><?= $item['nama_barang']; ?> <br><small>Kode: <?= $item['kode_barang']; ?></small></td>
                            <td style="text-align: center;"><?= $item['jumlah']; ?></td>
                            <td style="text-align: center;"><?= $item['satuan']; ?></td>
                            <td><?= !empty($data['catatan']) ? $data['catatan'] : '-'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p>Demikian berita acara serah terima barang ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="ttd-wrapper">
            <div class="ttd-box">
                <div class="ttd-header">
                    &nbsp;<br>
                    Yang Menerima,
                </div>

                <img src="<?= FILE_URL ?>file.php?type=img_ttd&file=<?= urlencode($data['ttd_pemohon']); ?>" class="img-ttd">

                <strong>( <?= $data['nama_pemohon']; ?> )</strong><br>
                NIP. <?= !empty($data['nip_pemohon']) ? $data['nip_pemohon'] : '-'; ?>
            </div>

            <div class="ttd-box">
                <div class="ttd-header">
                    Denpasar, <?= $tanggal_indonesia; ?><br>
                    Admin Gudang
                </div>

                <img src="<?= FILE_URL ?>file.php?type=img_ttd&file=<?= urlencode($data['ttd_admin']); ?>" class="img-ttd">

                <strong>( <?= $data['nama_admin']; ?> )</strong><br>
                NIP. <?= !empty($data['nip_admin']) ? $data['nip_admin'] : '-'; ?>
            </div>
        </div>
    </div>

</body>

</html>