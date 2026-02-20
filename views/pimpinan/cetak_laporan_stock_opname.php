<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stock Opname ?></title>
    <?php
    define('BASE_URL', '/aplikasi-pesona-private/routes/web.php/?page='); 
    define('ASSETS_URL', '/aplikasi-pesona-private/assets/');
    ?>
    <style>
        body { font-family: 'Arial', serif; font-size: 12pt; margin: 0; padding: 20px; color: #000; }
        .container { width: 100%; max-width: 800px; margin: auto; }
        
        /* KOP SURAT */
        .header-table { width: 100%; border-bottom: 3px double black; margin-bottom: 20px; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo-kop { width: 100px; height: auto; }
        
        .text-kop { text-align: center; line-height: 1.2; }
        .text-kop h2 { margin: 0; font-size: 14pt; font-weight: normal; font-family: 'Arial', serif; }
        .text-kop h1 { margin: 5px 0; font-size: 16pt; font-weight: bold; font-family: 'Arial', serif; }
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

    <div class="content">
        <table>
            <tr><td width="50%"><strong>UAPPB-W</strong></td>        <td>: <strong>PROVINSI BALI</strong></td></tr>
            <tr><td width="50%"><strong>UAKPB</strong></td>          <td>: <strong>BPMP PROVINSI BALI</strong></td></tr>
            <tr><td width="50%"><strong>KODE UAKPB</strong></td>     <td>: <strong></strong></td></tr>
            <tr><td width="50%"><strong>TAHUN ANGGARAN</strong></td> <td>: <strong><?= date('Y'); ?></strong></td></tr>
        </table>
    </div>

    <div class="content">
        <table>
            <tr><td width="100%" style="text-align: center;"><strong>KARTU STOCK BARANG</strong></td><td></td></tr>
        </table>
    </div>

    <div class="content">
        <table>
            <tr><td width="50%"><strong>NAMA BARANG</strong></td>       <td>: <strong>PROVINSI BALI</strong></td></tr>
            <tr><td width="50%"><strong>SATUAN</strong></td>            <td>: <strong>BPMP PROVINSI BALI</strong></td></tr>
            <tr><td width="50%"><strong>KODE BARANG</strong></td>       <td>: <strong></strong></td></tr>
        </table>
    </div>

    <div class="content">
        <table>
            
        </table>
    </div>


    
</div>

</body>
</html>