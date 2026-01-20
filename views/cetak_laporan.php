<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi <?= $tgl_mulai; ?> sd <?= $tgl_selesai; ?></title>
    <style>
        /* CSS untuk Tampilan Cetak */
        body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; }
        .container { width: 100%; margin: auto; padding: 10px; }
        
        /* Kop Laporan */
        .header { text-align: center; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 0; }

        /* Tabel Laporan */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 6px; vertical-align: top; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        /* Hilangkan elemen saat diprint */
        @media print {
            .no-print { display: none; }
            @page { size: landscape; } /* Cetak Landscape agar muat banyak */
        }

        .btn-back {
            background: #e74a3b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;
        }
        .btn-print {
            background: #4e73df; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print" style="margin-bottom: 20px;">
        <a href="laporan.php" class="btn-back">⬅ Kembali</a>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h2>Laporan Riwayat Persetujuan Barang</h2>
        <p>Periode: <?= date('d-m-Y', strtotime($tgl_mulai)); ?> s/d <?= date('d-m-Y', strtotime($tgl_selesai)); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal Setuju</th>
                <th width="15%">Pemohon</th>
                <th>Rincian Barang (Disetujui)</th>
                <th width="15%">Admin Penyetuju</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Query Header Permintaan Berdasarkan Tanggal Disetujui
            $query = "SELECT p.*, u.nama AS nama_pemohon, a.nama AS nama_admin
                      FROM tb_permintaan p
                      JOIN tb_user u ON p.user_id = u.id
                      LEFT JOIN tb_user a ON p.admin_id = a.id
                      WHERE p.status = 'disetujui' 
                      AND (p.tanggal_disetujui BETWEEN '$tgl_mulai' AND '$tgl_selesai')
                      ORDER BY p.tanggal_disetujui ASC";
            
            $result = mysqli_query($koneksi, $query);

            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)):
                    $id_req = $row['id'];
            ?>
            <tr>
                <td style="text-align: center;"><?= $no++; ?></td>
                <td style="text-align: center;"><?= date('d-m-Y', strtotime($row['tanggal_disetujui'])); ?></td>
                <td><?= $row['nama_pemohon']; ?></td>
                
                <td>
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php 
                        $q_detail = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang 
                                                            FROM tb_detail_permintaan d
                                                            JOIN tb_barang_bergerak b ON d.barang_id = b.id
                                                            WHERE d.permintaan_id = '$id_req'");
                        while($item = mysqli_fetch_assoc($q_detail)){
                            echo "<li>{$item['nama_barang']} : <b>{$item['jumlah']} {$item['satuan']}</b></li>";
                        }
                        ?>
                    </ul>
                </td>
                
                <td><?= $row['nama_admin']; ?></td>
            </tr>
            <?php 
                endwhile; 
            else:
            ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="width: 100%; margin-top: 50px; text-align: right;">
        <p>Denpasar, <?= date('d F Y'); ?></p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>( _______________________ )</strong></p>
    </div>
</div>

<!-- <script>
    // Otomatis muncul print dialog saat dibuka
    window.onload = function() {
        window.print();
    }
</script> -->

</body>
</html>