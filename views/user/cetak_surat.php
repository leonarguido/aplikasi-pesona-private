<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permintaan Barang - #<?= $data['id']; ?></title>
    <?php
    define('BASE_URL', '/aplikasi-pesona-private/routes/web.php/?page='); 
    define('ASSETS_URL', '/aplikasi-pesona-private/assets/');
    ?>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; margin: 0; padding: 20px; color: #000; }
        .container { width: 100%; max-width: 800px; margin: auto; }
        
        /* KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double black; margin-bottom: 20px; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo-kop { width: 100px; height: auto; }
        
        .text-kop { text-align: center; line-height: 1.2; }
        .text-kop h2 { margin: 0; font-size: 14pt; font-weight: normal; font-family: 'Times New Roman', serif; }
        .text-kop h1 { margin: 5px 0; font-size: 16pt; font-weight: bold; font-family: 'Times New Roman', serif; }
        .text-kop p { margin: 0; font-size: 10pt; }
        
        /* ISI SURAT */
        .content { margin-bottom: 30px; line-height: 1.5; }
        
        /* TABEL BARANG */
        .table-data { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        .table-data th, .table-data td { border: 1px solid black; padding: 8px; text-align: left; vertical-align: top; }
        .table-data th { background-color: #f0f0f0; text-align: center; }

        /* TANDA TANGAN */
        .ttd-wrapper { width: 100%; display: table; margin-top: 50px; }
        .ttd-box { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
        .img-ttd { width: 100px; height: auto; display: block; margin: 10px auto; }
        .space-ttd { height: 80px; } 

        /* TOMBOL PRINT */
        @media print {
            .no-print { display: none; }
            @page { margin: 2cm; }
        }
        .btn-print {
            background: #4e73df; color: white; border: none; padding: 10px 20px; 
            border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <button onclick="window.print()" class="no-print btn-print">🖨️ Cetak Surat</button>

    <table class="header-table">
        <tr>
            <td width="15%" style="text-align: center;">
                <img src="<?= ASSETS_URL ?>img/logo_tutwuri.jpg" alt="Logo" class="logo-kop">
            </td>
            <td width="85%" class="text-kop">
                <h2>KEMENTERIAN PENDIDIKAN DASAR<br>DAN MENENGAH</h2>
                <h1>BALAI PENJAMINAN MUTU PENDIDIKAN PROVINSI BALI</h1>
                <p>Jalan Letda Tantular Nomor 14 Niti Mandala, Denpasar</p>
                <p>Telp. (0361) 225666, Fax. (0361) 246682</p>
                <p>Pos el: bpmpbali@kemdikbud.go.id, Laman: www.bpmpbali.kemdikdasmen.go.id</p>
            </td>
        </tr>
    </table>

    <div class="content">
        <p><strong>Nomor Surat :</strong> #REQ-<?= sprintf("%04d", $data['id']); ?></p>
        <p><strong>Perihal :</strong> Bukti Serah Terima Barang (ATK)</p>
        
        <br>
        <p>Yang bertanda tangan di bawah ini:</p>
        <table style="width: 100%; margin-left: 20px; margin-bottom: 10px;">
            <tr><td width="150">Nama</td><td>: <?= $data['nama_pemohon']; ?></td></tr>
            <tr><td>NIP</td><td>: <?= !empty($data['nip_pemohon']) ? $data['nip_pemohon'] : '-'; ?></td></tr>
            <tr><td>Keperluan</td><td>: <?= $data['keperluan']; ?></td></tr>
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
                // QUERY 2: AMBIL DETAIL BARANG
                $q_detail = mysqli_query($koneksi, "SELECT d.*, b.nama_barang, b.satuan, b.kode_barang, b.merk_barang 
                                                    FROM tb_detail_permintaan d
                                                    JOIN tb_barang_bergerak b ON d.barang_id = b.id
                                                    WHERE d.permintaan_id = '$id_permintaan'");
                $no = 1;
                while($item = mysqli_fetch_assoc($q_detail)):
                ?>
                <tr>
                    <td style="text-align: center;"><?= $no++; ?></td>
                    <td><?= $item['nama_barang']; ?> (<?= $item['merk_barang']; ?>) <br><small>Kode: <?= $item['kode_barang']; ?></small></td>
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
            <br>
            <p>Yang Menerima,</p>
            
            <?php 
            $path_ttd_pemohon = ASSETS_URL . 'img/ttd/' . $data['ttd_pemohon'];
            if(!empty($data['ttd_pemohon'])): 
            ?>
                <img src="<?= ASSETS_URL ?>img/ttd/<?= $data['ttd_pemohon']; ?>" class="img-ttd">
            <?php else: ?>
                <div class="space-ttd"></div>
            <?php endif; ?>

            <strong>( <?= $data['nama_pemohon']; ?> )</strong><br>
            NIP. <?= !empty($data['nip_pemohon']) ? $data['nip_pemohon'] : '-'; ?>
        </div>

        <div class="ttd-box">
            <p>Denpasar, <?= $tanggal_indonesia; ?>,<br>Admin Gudang</p>
            <?php 
            if(!empty($data['ttd_admin'])): 
            ?>
                <img src="<?= ASSETS_URL ?>img/ttd/<?= $data['ttd_admin']; ?>" class="img-ttd">
            <?php else: ?>
                <div class="space-ttd"></div>
            <?php endif; ?>

            <strong>( <?= $data['nama_admin']; ?> )</strong><br>
            NIP. <?= !empty($data['nip_admin']) ? $data['nip_admin'] : '-'; ?>
        </div>
    </div>
</div>

</body>
</html>