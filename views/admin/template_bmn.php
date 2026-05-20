<?php
// Mengatur header agar browser membacanya sebagai file CSV yang bisa didownload
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="template_import_aset_bmn.csv"');

// Membuka output stream
$output = fopen('php://output', 'w');

// 1. Tulis Baris Judul (Header) - JANGAN DIUBAH URUTANNYA
fputcsv($output, array('Kode Barang', 'Nama Barang', 'Merek Barang', 'Satuan', 'Stok', 'Tahun Perolehan', 'NUP', 'Status', 'Keterangan'));

// 2. Tulis Contoh Data (Dummy) sebagai panduan user
fputcsv($output, array('LPT-001', 'Contoh Laptop ASUS', 'Toshiba', 'Unit', '3', '2020', '0000', 'Baik dan Lengkap', 'Contoh Keterangan'));
fputcsv($output, array('PYK-002', 'Contoh Proyektor', 'ACER', 'Unit', '1', '2022', '0000', 'Baik dan Lengkap', 'Contoh Keterangan'));
fputcsv($output, array('MOS-003', 'Contoh Mouse', 'Logitech', 'Unit', '5', '2021', '0000', 'Baik dan Lengkap', 'Contoh Keterangan'));

// Tutup file
fclose($output);
exit;
?>