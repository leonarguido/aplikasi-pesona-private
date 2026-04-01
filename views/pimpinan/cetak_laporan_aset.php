<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Aset</title>
    <?php
    define('BASE_URL', '/aplikasi-pesona-private/routes/web.php/?page=');
    define('ASSETS_URL', '/aplikasi-pesona-private/assets/');
    ?>
    <style>
        /* CSS UTAMA */
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
            padding: 20px;
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

        .judul {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
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

        .content {
            text-align: justify;
            line-height: 1.3;
            /* Spasi baris rapat */
        }

        /* ISI LAPORAN */
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
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .table-data th {
            background-color: #f0f0f0;
            text-align: center;
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
        <button onclick="window.print()" class="no-print btn-print">🖨️ Cetak Laporan Aset</button>

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
            <h3>LAPORAN ASET</h3>
        </div>

        <div class="content" style="margin-top: 30px;">
            <table class="table-data">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Barang</th>
                        <th>Peminjam (Staf)</th>
                        <th>Tgl Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "
                            SELECT p.*, u.nama AS nama_peminjam, u.nip AS nip_peminjam
                            FROM tb_peminjaman p
                            JOIN tb_user u ON p.user_id = u.id
                            WHERE p.deleted_at IS NULL
                            ORDER BY p.id DESC
                        ");

                    while ($row = mysqli_fetch_assoc($query)):
                    ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++; ?></td>
                            <td>
                                <b><?= $row['nama_barang']; ?></b> <br>
                                <small class="text-muted"><?= $row['merek']; ?> - <?= $row['nup']; ?></small>
                            </td>
                            <td>
                                <?= $row['nama_peminjam']; ?> <br>
                                <small>NIP: <?= $row['nip_peminjam']; ?></small>
                            </td>
                            <td>
                                <small>Serah: <?= date('d/m/Y', strtotime($row['tgl_serah_terima'])); ?></small><br>

                                <?php if ($row['tgl_kembali'] == NULL): ?>
                                    <small>Jangka Panjang</small>
                                <?php else: ?>
                                    <small class="text-danger">Kembali: <?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($row['status'] == 'menunggu_persetujuan'): ?>
                                    <small class="badge badge-warning">Menunggu Staf</small>
                                <?php elseif ($row['status'] == 'disetujui'): ?>
                                    <small class="badge badge-info">Disetujui (Belum Tanda Tangan)</small>
                                <?php elseif ($row['status'] == 'selesai'): ?>
                                    <small class="badge badge-success">Selesai / Aktif</small>
                                <?php elseif ($row['status'] == 'dikembalikan'): ?>
                                    <small class="badge badge-secondary">Sudah Dikembalikan</small>
                                <?php elseif ($row['status'] == 'ditolak'): ?>
                                    <small class="badge badge-danger">Ditolak Staf</small>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>