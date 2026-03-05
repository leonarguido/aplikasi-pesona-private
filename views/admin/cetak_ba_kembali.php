<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>BA Pengembalian - <?= $data['nama_barang']; ?></title>
    <?php
    define('BASE_URL', '/aplikasi-pesona-private/routes/web.php/?page=');
    define('ASSETS_URL', '/aplikasi-pesona-private/assets/');
    ?>
    <style>
        /* CSS LAYAR UTAMA */
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

        /* PRINT SETTINGS */
        @media print {
            .no-print {
                display: none;
            }

            .header-table {
                width: 100%;
            }

            .content {
                width: 90%;
                margin: auto;
            }

            @page {
                margin: 0;
                padding: 0;
                size: A4;
            }

            body {
                margin: 0.6cm;
            }
        }

        /* FORMAT SURAT (KOMPAK 1 HALAMAN) */
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

        /* JUDUL */
        .judul {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .judul h3 {
            margin: 0;
            text-decoration: underline;
            font-size: 12pt;
            font-weight: bold;
        }

        .judul p {
            margin: 2px 0 0 0;
            font-size: 11pt;
        }

        /* KONTEN UTAMA */
        .content {
            text-align: justify;
            line-height: 1.3;
        }

        /* TABEL IDENTITAS PIHAK */
        .table-pihak {
            width: 100%;
            margin-left: 10px;
            margin-bottom: 5px;
        }

        .table-pihak td {
            vertical-align: top;
            padding-bottom: 2px;
        }

        /* PASAL-PASAL */
        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin: 5px 0;
        }

        ol {
            margin: 0;
            padding-left: 25px;
            margin-bottom: 5px;
        }

        li {
            margin-bottom: 2px;
        }

        /* INDENT UNTUK BARANG */
        .indent {
            padding-left: 25px;
            margin: 5px 0;
        }

        /* TANDA TANGAN */
        .ttd-container {
            width: 100%;
            margin-top: 20px;
            display: table;
            page-break-inside: avoid;
        }

        .ttd-row {
            display: table-row;
        }

        .ttd-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .ttd-space {
            height: 60px;
        }

        .ttd-center {
            text-align: center;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .img-ttd {
            width: 100px;
            height: auto;
            display: block;
            margin: 0px auto;
        }

        .btn-print {
            background: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <button onclick="window.print()" class="no-print btn-print">🖨️ Cetak BA Pengembalian</button>

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

        <div class="judul">
            <h3>BERITA ACARA PENGEMBALIAN BARANG</h3>
            <p>Nomor : ....................................................</p>
        </div>

        <div class="content">
            <p style="margin-bottom: 10px;">
                Pada hari ini, <b><?= $hari_ini; ?></b> tanggal <b><?= $tgl_angka; ?></b> bulan <b><?= $bln_nama; ?></b> tahun <b><?= $thn_angka; ?></b>, kami yang bertanda tangan di bawah ini :
            </p>

            <table class="table-pihak">
                <tr>
                    <td width="20">1.</td>
                    <td width="80">Nama</td>
                    <td>: <b><?= $data['nama_admin']; ?></b></td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIP</td>
                    <td>: <?= !empty($data['nip_admin']) ? $data['nip_admin'] : '-'; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Jabatan</td>
                    <td>: <?= $data['jabatan_admin']; ?> </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">Selanjutnya disebut <b>PIHAK PERTAMA</b></td>
                </tr>
            </table>

            <table class="table-pihak">
                <tr>
                    <td width="20">2.</td>
                    <td width="80">Nama</td>
                    <td>: <b><?= $data['nama_user']; ?></b></td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIP</td>
                    <td>: <?= !empty($data['nip_user']) ? $data['nip_user'] : '-'; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Jabatan</td>
                    <td>: <?= $data['jabatan_user']; ?> </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">Selanjutnya disebut <b>PIHAK KEDUA</b></td>
                </tr>
            </table>

            <p style="margin-top: 5px;">Mengembalikan barang (BMN) berupa :</p>
            <div class="indent">
                <p>
                    - 1 (satu) unit <b><?= $data['nama_barang']; ?></b>, merk <b><?= $data['merek']; ?></b>, S/N: ............................................... yang diadakan melalui BPMP Prov Bali Tahun <b><?= $data['tahun_perolehan']; ?></b> dengan kodefikasi <b>(<?= $data['kode_barang']; ?> / NUP: <?= $data['nup']; ?>)</b>.
                </p>
            </div>

            <p style="margin-top: 5px;">dengan ketentuan sebagai berikut:</p>

            <div class="pasal-title">Pasal 1</div>
            <ol type="a">
                <li>PIHAK KEDUA mengembalikan kepada PIHAK PERTAMA barang-barang seperti tersebut di atas.</li>
                <li>PIHAK PERTAMA menerima dalam keadaan baik dan lengkap barang-barang tersebut di atas.</li>
            </ol>

            <div class="pasal-title">Pasal 2</div>
            <ol type="a">
                <li>Dengan adanya pengembalian tersebut, maka selanjutnya barang-barang tersebut di atas menjadi tanggung jawab PIHAK PERTAMA.</li>
                <li>Biaya pemeliharaan barang-barang tersebut di atas, tetap menjadi tanggung jawab BPMP Provinsi Bali.</li>
            </ol>

            <p style="margin-top: 10px;">Demikian Berita Acara Pengembalian Barang ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="ttd-container">
            <div class="ttd-row">
                <div class="ttd-col">
                    PIHAK KEDUA,<br>
                    <div class="ttd-space"></div>
                    <b><?= $data['nama_user']; ?></b><br>
                    NIP. <?= $data['nip_user']; ?>
                </div>

                <div class="ttd-col">
                    PIHAK PERTAMA,<br>
                    <div class="ttd-space"></div>
                    <b><?= $data['nama_admin']; ?></b><br>
                    NIP. <?= $data['nip_admin']; ?>
                </div>
            </div>
        </div>

        <div class="ttd-center">
            Mengetahui/Menyetujui :<br>
            Kasubbag Umum BPMP Prov Bali,<br>
            <div class="ttd-space">
                <!-- <img src="<?= ASSETS_URL ?>img/ttd/<?= $data['paraf_user']; ?>" class="img-ttd"> -->
                <div class="ttd-space"></div>
                <b><?= $nama_kasubbag; ?></b><br>
                NIP. <?= $nip_kasubbag; ?>
            </div>
        </div>
    </div>

</body>

</html>