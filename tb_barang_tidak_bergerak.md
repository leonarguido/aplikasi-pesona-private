-- 1. Hapus tabel lama jika ada (agar struktur bersih)
DROP TABLE IF EXISTS `tb_barang_tidak_bergerak`;

-- 2. Buat tabel baru dengan struktur yang benar
CREATE TABLE `tb_barang_tidak_bergerak` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(50) DEFAULT NULL COMMENT 'NIP Penanggung Jawab',
  `nama_barang` varchar(255) NOT NULL,
  `kode_barang` varchar(50) DEFAULT NULL,
  `merk_barang` varchar(100) DEFAULT NULL,
  `satuan` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `berkas` varchar(255) DEFAULT NULL COMMENT 'File Berita Acara (PDF/Gambar)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
