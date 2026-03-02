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
            margin: 0; padding: 20px; color: #000; 
        }
        .container { width: 100%; max-width: 800px; margin: auto; }
        
        /* SETTING PRINT */
        @media print {
            .no-print { display: none; }
            @page { 
                size: A4;
                margin: 0; 
            }
            body { 
                margin-top: 1.5cm; 
                margin-bottom: 1.5cm; 
                margin-left: 2cm; 
                margin-right: 2cm; 
                padding: 0;
            }
            .container { max-width: 100%; }
        }

        /* KOP SURAT */
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 10px; padding-bottom: 5px; }
        .logo-kop { width: 85px; height: auto; }
        .kop-text { text-align: center; line-height: 1.1; }
        .kop-text h3 { margin: 0; font-size: 14pt; font-weight: normal; }
        .kop-text h2 { margin: 2px 0; font-size: 16pt; font-weight: bold; }
        .kop-text p { margin: 0; font-size: 10pt; }

        /* JUDUL */
        .judul { text-align: center; margin-top: 10px; margin-bottom: 15px; }
        .judul h3 { margin: 0; text-decoration: underline; font-size: 12pt; font-weight: bold; }
        .judul p { margin: 2px 0 0 0; font-size: 11pt; }

        /* KONTEN UTAMA */
        .content { 
            text-align: justify; 
            line-height: 1.3; 
        }
        
        /* TABEL IDENTITAS PIHAK */
        .table-pihak { width: 100%; margin-left: 10px; margin-bottom: 5px; }
        .table-pihak td { vertical-align: top; padding-bottom: 2px; }

        /* PASAL-PASAL */
        .pasal-title { text-align: center; font-weight: bold; margin: 5px 0; }
        ol { margin: 0; padding-left: 25px; margin-bottom: 5px; }
        li { margin-bottom: 2px; }
        
        /* INDENT UNTUK BARANG */
        .indent { padding-left: 25px; margin: 5px 0; }

        /* TANDA TANGAN */
        .ttd-container { 
            width: 100%; 
            margin-top: 20px; 
            display: table; 
            page-break-inside: avoid; 
        }
        .ttd-row { display: table-row; }
        .ttd-col { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
        .ttd-space { height: 60px; }

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

        .btn-print { background: #4e73df; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin-bottom: 20px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <button onclick="window.print()" class="no-print btn-print">🖨️ Cetak BA Pengembalian</button>

    <table class="kop-table">
        <tr>
            <td width="15%" align="center">
                <img src="<?= ASSETS_URL ?>img/bpmp/logo_tutwuri.jpg" alt="Logo" class="logo-kop">
            </td>
            <td class="kop-text">
                <h3>KEMENTERIAN PENDIDIKAN DASAR DAN MENENGAH</h3>
                <h2>BALAI PENJAMINAN MUTU PENDIDIKAN PROVINSI BALI</h2>
                <p>Jalan Letda Tantular Nomor 14 Niti Mandala, Denpasar</p>
                <p>Telp. (0361) 225666, Fax. (0361) 246682</p>
                <p>Laman: bpmpbali.kemdikdasmen.go.id, Pos-el: bpmpbali@kemdikbud.go.id</p>
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
            <tr><td width="20">1.</td><td width="80">Nama</td><td>: <b><?= $data['nama_admin']; ?></b></td></tr>
            <tr><td></td><td>NIP</td><td>: <?= !empty($data['nip_admin']) ? $data['nip_admin'] : '-'; ?></td></tr>
            <tr><td></td><td>Jabatan</td><td>: ................................................................</td></tr>
            <tr><td></td><td colspan="2">Selanjutnya disebut <b>PIHAK PERTAMA</b></td></tr>
        </table>

        <table class="table-pihak">
            <tr><td width="20">2.</td><td width="80">Nama</td><td>: <b><?= $data['nama_user']; ?></b></td></tr>
            <tr><td></td><td>NIP</td><td>: <?= !empty($data['nip_user']) ? $data['nip_user'] : '-'; ?></td></tr>
            <tr><td></td><td>Jabatan</td><td>: ................................................................</td></tr>
            <tr><td></td><td colspan="2">Selanjutnya disebut <b>PIHAK KEDUA</b></td></tr>
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
            <img src="<?= ASSETS_URL ?>img/ttd/<?= $data['paraf_user']; ?>" class="img-ttd">
        <b><?= $nama_kasubbag; ?></b><br>
        NIP. <?= $nip_kasubbag; ?>
        </div>
    </div>
</div>

</body>
</html>