<?php

require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/AutentikasiController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/LogBarangController.php';
require_once __DIR__ . '/../controllers/PimpinanController.php';
require_once __DIR__ . '/../controllers/UserController.php';


$admin = new AdminController();
$autentikasi = new AutentikasiController();
$dashboard = new DashboardController();
$logBarang = new LogBarangController();
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

        // DATA BARANG BERGERAK
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
        case 'edit_data_stok_barang':
            $admin->edit_data_stok_barang();
            break;
        case 'hapus_data_barang':
            $admin->hapus_data_barang();
            break;

        // DATA BARANG TIDAK BERGERAK
        case 'data_barang_tg':
            $admin->data_barang_tg();
            break;
        case 'tambah_data_barang_tg':
            $admin->tambah_data_barang_tg();
            break;
        case 'edit_data_barang_tg':
            $admin->edit_data_barang_tg();
            break;
        case 'hapus_data_barang_tg':
            $admin->hapus_data_barang_tg();
            break;

        // PROSES PERSETUJUAN ADMIN
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

        // ASET SAYA
        case 'aset_saya':
            $user->aset_saya();
            break;

        // PROFIL PENGGUNA
        case 'profil':
            $user->profil_page();
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
            break;
        case 'ajax_load_laporan_permintaan':
            $pimpinan->ajax_load_laporan_permintaan();
            break;
        case 'laporan_permintaan':
            $pimpinan->laporan_permintaan_page();
            break;
        case 'cetak_laporan':
            $pimpinan->cetak_laporan();
            break;
        case 'laporan_stock_opname':
            $pimpinan->laporan_stock_opname_page();
            break;
        case 'proses_stock_opname':
            $pimpinan->proses_stock_opname();
            break;
        case 'ajax_load_per_orang':
            $pimpinan->ajax_load_per_orang();
            break;
        case 'ajax_load_per_item':
            $pimpinan->ajax_load_per_item();
            break;


        // ==========================================
        // RUTE LOG BARANG CONTROLLER
        // ==========================================
        case 'log_barang':
            $logBarang->log_barang_bergerak();
            break;
        case 'edit_log_stok_barang':
            $logBarang->edit_log_stok_barang();
            break;
        case 'ajax_load_log_barang_bergerak':
            $logBarang->ajax_load_log_barang_bergerak();
            break;
        case 'log_barang_tg':
            $logBarang->log_barang_tg();
            break;
        case 'ajax_load_log_barang_tg':
            $logBarang->ajax_load_log_barang_tg();
            break;

        // Default action or 404
        default:
            echo "Halaman tidak ditemukan.";
            break;
    }
} else {
    $dashboard->index();
}
