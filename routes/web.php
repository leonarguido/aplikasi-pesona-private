<?php 

require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/AutentikasiController.php';
require_once __DIR__ . '/../controllers/BarangBergerakController.php';
require_once __DIR__ . '/../controllers/BarangTidakBergerakController.php';
require_once __DIR__ . '/../controllers/LogBarangBergerakController.php';
require_once __DIR__ . '/../controllers/LaporanController.php';
require_once __DIR__ . '/../controllers/PermintaanController.php';
require_once __DIR__ . '/../controllers/UserController.php';


$dashboard = new DashboardController();
$autentikasi = new AutentikasiController();
$barangBergerak = new BarangBergerakController();
$barangTidakBergerak = new BarangTidakBergerakController();
$logBarangBergerak = new LogBarangBergerakController();
$laporan = new LaporanController();
$permintaan = new PermintaanController();
$user = new UserController();

if (isset($_GET['method'])) {
    $method = $_GET['method'];

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
// RUTE BARANG BERGERAK CONTROLLER
// ==========================================

        // DAFTAR BARANG DAN KERANJANG
        case 'daftar_barang':
            $barangBergerak->daftar_barang_page();
            break;
        case 'keranjang':
            $barangBergerak->keranjang();
            break;
        case 'tambah_keranjang_item':
            $barangBergerak->tambah_keranjang_item();
            break;
        case 'hapus_keranjang_item':
            $barangBergerak->hapus_keranjang_item();
            break;
        case 'checkout_keranjang':
            $barangBergerak->checkout_keranjang();
            break;

        // DATA BARANG
        case 'data_barang':
            $barangBergerak->data_barang_page();
            break;
        case 'import_excel_data_barang':
            $barangBergerak->import_excel_data_barang();
            break;
        case 'tambah_data_barang':
            $barangBergerak->tambah_data_barang();
            break;
        case 'edit_data_barang':
            $barangBergerak->edit_data_barang();
            break;
        case 'hapus_data_barang':
            $barangBergerak->hapus_data_barang();
            break;

// ==========================================
// RUTE LOG BARANG BERGERAK CONTROLLER
// ==========================================

        // LOG DATA BARANG
        case 'log_data_barang':
            $logBarangBergerak->log_data_barang_page();
            break;

// ==========================================
// RUTE PERMINTAAN CONTROLLER
// ==========================================

        // PERMINTAAN BARANG
        case 'permintaan_saya':
            $permintaan->permintaan_saya_page();
            break;
        case 'batalkan_permintaan_saya':
            $permintaan->batalkan_permintaan_saya();
            break;
        case 'edit_permintaan_saya':
            $permintaan->edit_permintaan_saya();
            break;
        case 'persetujuan':
            $permintaan->persetujuan_page();
            break;
        case 'proses_persetujuan':
            $permintaan->proses_persetujuan();
            break;
        case 'proses_penolakan':
            $permintaan->proses_penolakan();
            break;
        case 'cetak_surat':
            $permintaan->cetak_surat();
            break;
        
// ==========================================
// RUTE USER CONTROLLER
// ==========================================

        // USER MANAGEMENT
        case 'data_pengguna':
            $user->data_pengguna_page();
            break;
        case 'tambah_data_pengguna':
            $user->tambah_data_pengguna();
            break;
        case 'edit_data_pengguna':
            $user->edit_data_pengguna();
            break;
        case 'hapus_data_pengguna':
            $user->hapus_data_pengguna();
            break;
        case 'profil_saya':
            $user->profil_page();
            break;

// ==========================================
// RUTE LAPORAN CONTROLLER
// ==========================================

        case 'laporan':
            $laporan->laporan_page();
            break;
        case 'cetak_laporan':
            $laporan->cetak_laporan();
            break;        


        default:
            // Default action or 404
            echo "Halaman tidak ditemukan.";
            break;
    }
} else {
    $dashboard->index();
}   
?>