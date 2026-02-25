<?php
session_start();
require 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_permintaan = $_GET['id'];

// QUERY DATA
$query_header = "SELECT p.*, 
                 u_pemohon.nama AS nama_pemohon, u_pemohon.nip AS nip_pemohon, u_pemohon.paraf AS ttd_pemohon,
                 u_admin.nama AS nama_admin, u_admin.nip AS nip_admin, u_admin.paraf AS ttd_admin
                 FROM tb_permintaan p
                 JOIN tb_user u_pemohon ON p.user_id = u_pemohon.id
                 LEFT JOIN tb_user u_admin ON p.admin_id = u_admin.id
                 WHERE p.id = '$id_permintaan'";

$result_header = mysqli_query($koneksi, $query_header);
$data = mysqli_fetch_assoc($result_header);

if ($data['status'] != 'disetujui') {
    echo "<script>alert('Surat belum bisa dicetak karena status belum disetujui!'); window.close();</script>";
    exit;
}

// KONVERSI TANGGAL INDONESIA
$tanggal_sql = $data['tanggal_disetujui'];
$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$pecah_tgl = explode('-', $tanggal_sql);
$tgl = $pecah_tgl[2];
$bln = (int) $pecah_tgl[1];
$thn = $pecah_tgl[0];
$tanggal_indonesia = $tgl . ' ' . $bulan_indo[$bln] . ' ' . $thn;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permintaan Barang - #<?= $data['id']; ?></title>
    <style>
        /* UTAMA */
        body { font-family: 'Times New Roman', serif; font-size: 11pt; margin: 0; padding: 20px; color: #000; }
        .container { width: 100%; max-width: 800px; margin: auto; }
        
        /* KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double black; margin-bottom: 15px; padding-bottom: 5px; }
        .header-table td { vertical-align: middle; }
        .logo-kop { width: 90px; height: auto; }
        .text-kop { text-align: center; line-height: 1.1; }
        .text-kop h2 { margin: 0; font-size: 14pt; font-weight: normal; }
        .text-kop h1 { margin: 2px 0; font-size: 16pt; font-weight: bold; }
        .text-kop p { margin: 0; font-size: 10pt; }
        
        /* ISI */
        .content { margin-bottom: 15px; line-height: 1.3; }
        .table-data { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px; }
        .table-data th, .table-data td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: top; }
        .table-data th { background-color: #f0f0f0; text-align: center; }

        /* TANDA TANGAN (PERBAIKAN AGAR SEJAJAR) */
        .ttd-wrapper { 
            width: 100%; 
            display: table; 
            margin-top: 20px; 
            page-break-inside: avoid; /* Jangan potong tanda tangan */
        }
        
        .ttd-box { 
            display: table-cell; 
            width: 50%; 
            text-align: center; 
            vertical-align: top; /* Pastikan semua mulai dari atas */
        }
        
        /* Perbaikan: Tinggi Gambar Fixed 80px agar nama di bawahnya sejajar */
        .img-ttd { 
            width: 100px; 
            height: 80px;      /* TINGGI DIKUNCI */
            object-fit: contain; /* Agar gambar tidak gepeng */
            display: block; 
            margin: 5px auto; 
        }
        
        .space-ttd { 
            width: 100px; 
            height: 80px;     /* TINGGI DIKUNCI SAMA */
            display: block; 
            margin: 5px auto; 
        } 

        /* PRINT SETTINGS */
        @media print {
            .no-print { display: none; }
            @page { margin: 0; size: auto; }
            body { margin: 1.5cm 2cm; }
        }

        .btn-print {
            background: #4e73df; color: white; border: none; padding: 10px 20px; 
            border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold;
        }
        
        /* Helper untuk meratakan baris teks header ttd */
        .ttd-header {
            min-height: 40px; /* Minimal tinggi header teks (2 baris) */
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
            <td width="15%" style="text-align: center;">
                <img src="assets/img/logo_tutwuri.jpg" alt="Logo" class="logo-kop" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/b/b3/Logo_Tut_Wuri_Handayani.png'">
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
                $q_detail = mysqli_query($koneksi, "SELECT d.*, b.nama_barang, b.satuan, b.kode_barang 
                                                    FROM tb_detail_permintaan d
                                                    JOIN tb_barang_bergerak b ON d.barang_id = b.id
                                                    WHERE d.permintaan_id = '$id_permintaan'");
                $no = 1;
                while($item = mysqli_fetch_assoc($q_detail)):
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
            
            <?php 
            $path_ttd_pemohon = 'assets/img/ttd/' . $data['ttd_pemohon'];
            if(!empty($data['ttd_pemohon']) && file_exists($path_ttd_pemohon)): 
            ?>
                <img src="<?= $path_ttd_pemohon; ?>" class="img-ttd">
            <?php else: ?>
                <div class="space-ttd"></div>
            <?php endif; ?>

            <strong>( <?= $data['nama_pemohon']; ?> )</strong><br>
            NIP. <?= !empty($data['nip_pemohon']) ? $data['nip_pemohon'] : '-'; ?>
        </div>

        <div class="ttd-box">
            <div class="ttd-header">
                Denpasar, <?= $tanggal_indonesia; ?><br>
                Admin Gudang
            </div>

            <?php 
            $path_ttd_admin = 'assets/img/ttd/' . $data['ttd_admin'];
            if(!empty($data['ttd_admin']) && file_exists($path_ttd_admin)): 
            ?>
                <img src="<?= $path_ttd_admin; ?>" class="img-ttd">
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