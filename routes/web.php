<?php

require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/AutentikasiController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/LogBarangBergerakController.php';
require_once __DIR__ . '/../controllers/PimpinanController.php';
require_once __DIR__ . '/../controllers/UserController.php';


$admin = new AdminController();
$autentikasi = new AutentikasiController();
$dashboard = new DashboardController();
$logBarangBergerak = new LogBarangBergerakController();
$pimpinan = new PimpinanController();
$user = new UserController();

if (isset($_GET['page'])) {
    $method = $_GET['page'];

    switch ($method) {

        // ==========================================
        // RUTE AUTENTIKASI CONTROLLER
        // ==========================================

        // AUTENTIKASI
        case 'login':
            $autentikasi->login();
            break;
        case 'autentikasi':
            $autentikasi->autentikasi();
            break;
        case 'logout':
            $autentikasi->logout();
            break;

        // ==========================================
        // RUTE ADMIN CONTROLLER
        // ==========================================

        // DATA BARANG
        case 'data_barang':
            $admin->data_barang_page();
            break;
        case 'template_barang':
            $admin->template_barang();
            break;
        case 'import_excel_data_barang':
            $admin->import_excel_data_barang();
            break;
        case 'tambah_data_barang':
            $admin->tambah_data_barang();
            break;
        case 'edit_data_barang':
            $admin->edit_data_barang();
            break;
        case 'hapus_data_barang':
            $admin->hapus_data_barang();
            break;

        case 'persetujuan':
            $admin->persetujuan_page();
            break;
        case 'riwayat_persetujuan':
            $admin->riwayat_persetujuan_page();
            break;
        case 'proses_persetujuan':
            $admin->proses_persetujuan();
            break;
        case 'proses_penolakan':
            $admin->proses_penolakan();
            break;

        // USER MANAGEMENT
        case 'data_pengguna':
            $admin->data_pengguna_page();
            break;
        case 'tambah_data_pengguna':
            $admin->tambah_data_pengguna();
            break;
        case 'edit_data_pengguna':
            $admin->edit_data_pengguna();
            break;
        case 'hapus_data_pengguna':
            $admin->hapus_data_pengguna();
            break;

        // ==========================================
        // RUTE USER CONTROLLER
        // ==========================================

        // DAFTAR BARANG DAN KERANJANG
        case 'daftar_barang':
            $user->daftar_barang_page();
            break;
        case 'keranjang':
            $user->keranjang();
            break;
        case 'tambah_keranjang_item':
            $user->tambah_keranjang_item();
            break;
        case 'hapus_keranjang_item':
            $user->hapus_keranjang_item();
            break;
        case 'checkout_keranjang':
            $user->checkout_keranjang();
            break;

        // PERMINTAAN BARANG
        case 'permintaan_saya':
            $user->permintaan_saya_page();
            break;
        case 'batalkan_permintaan_saya':
            $user->batalkan_permintaan_saya();
            break;
        case 'edit_permintaan_saya':
            $user->edit_permintaan_saya();
            break;
        case 'cetak_surat':
            $user->cetak_surat();
            break;

        // PROFIL PENGGUNA
        case 'profil_saya':
            $user->profil_page();
            break;

        // ==========================================
        // RUTE LOG BARANG BERGERAK CONTROLLER
        // ==========================================

        // LOG DATA BARANG
        case 'log_data_barang':
            $logBarangBergerak->log_data_barang_page();
            break;

        // ==========================================
        // RUTE PIMPINAN CONTROLLER
        // ==========================================

        // LAPORAN
        case 'laporan':
            $pimpinan->laporan_page();
            break;
        case 'laporan_stok':
            $pimpinan->laporan_stok_page();
            break;
        case 'ajax_load_stok_barang':
            $pimpinan->ajax_load_stok_barang();
            break;
        case 'ajax_load_riwayat_persetujuan':
            $pimpinan->ajax_load_riwayat_persetujuan();
            break;
        case 'ajax_load_laporan_stok_barang':
            $pimpinan->ajax_load_laporan_stok_barang();
        case 'ajax_load_laporan_permintaan':
            $pimpinan->ajax_load_laporan_permintaan();
        case 'laporan_permintaan':
            $pimpinan->laporan_permintaan_page();
            break;
        case 'cetak_laporan':
            $pimpinan->cetak_laporan();
            break;


        // ==========================================
        // RUTE PIMPINAN CONTROLLER
        // ==========================================
        case 'log_barang':
            $logBarangBergerak->log_barang_bergerak();
            break;
        case 'ajax_load_log_barang_bergerak':
            $logBarangBergerak->ajax_load_log_barang_bergerak();
            break;

        // Default action or 404
        default:
            echo "Halaman tidak ditemukan.";
            break;
    }
} else {
    $dashboard->index();
}
