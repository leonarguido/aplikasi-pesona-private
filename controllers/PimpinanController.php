<?php

class PimpinanController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?method=';

    public function laporan_page()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        // Cek Akses (Hanya Admin)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user') {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            exit;
        }

        require_once '../views/pimpinan/laporan.php';
    }

    public function laporan_stok_page()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        // 1. Cek Akses (Hanya Pimpinan & Admin yang boleh lihat stok)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'staff') {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            exit;
        }

        require_once '../views/pimpinan/laporan_stok.php';
    }

    public function cetak_laporan()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        // Cek Login
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Ambil Tanggal dari URL
        if (isset($_POST['tgl_mulai']) && isset($_POST['tgl_selesai'])) {
            $tgl_mulai = $_POST['tgl_mulai'];
            $tgl_selesai = $_POST['tgl_selesai'];
        } else {
            echo "Tanggal belum dipilih.";
            exit;
        }

        require_once '../views/pimpinan/cetak_laporan.php';
    }
}
?>