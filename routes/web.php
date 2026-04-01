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
        case 'role':
            $autentikasi->role_page();
            break;
        case 'pilih_role':
            $autentikasi->pilih_role();
            break;
        case 'kembali_role_asli':
            $autentikasi->kembali_role_asli();
            break;
        case 'kembali_role_staff':
            $autentikasi->kembali_role_staff();
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

        // DATA BARANG ASET BMN
        case 'data_aset_bmn':
            $admin->data_aset_bmn();
            break;
        case 'tambah_data_aset_bmn':
            $admin->tambah_data_aset_bmn();
            break;
        case 'edit_data_aset_bmn':
            $admin->edit_data_aset_bmn();
            break;
        case 'hapus_data_aset_bmn':
            $admin->hapus_data_aset_bmn();
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

        // DATA BARANG ASET BMN
        case 'input_peminjaman_barang':
            $admin->input_peminjaman_barang_page();
            break;
        case 'ajukan_pinjaman':
            $admin->ajukan_pinjaman();
            break;
        case 'update_pinjaman':
            $admin->update_pinjaman();
            break;
        case 'hapus_pinjaman':
            $admin->hapus_pinjaman();
            break;
        case 'upload_arsip_pinjaman':
            $admin->upload_arsip_pinjaman();
            break;
        case 'cetak_berita_acara':
            $admin->cetak_berita_acara();
            break;
        case 'pengembalian_barang':
            $admin->pengembalian_barang_page();
            break;
        case 'simpan_kondisi':
            $admin->simpan_kondisi();
            break;
        case 'cetak_ba_kembali':
            $admin->cetak_ba_kembali();
            break;
        case 'upload_arsip_kembali':
            $admin->upload_arsip_kembali();
            break;
        case 'hapus_pengembalian':
            $admin->hapus_pengembalian();
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

        case 'data_jabatan':
            $admin->data_jabatan_page();
            break;
        case 'tambah_data_jabatan':
            $admin->tambah_data_jabatan();
            break;
        case 'edit_data_jabatan':
            $admin->edit_data_jabatan();
            break;
        case 'hapus_data_jabatan':
            $admin->hapus_data_jabatan();
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

        // PEMINJAMAN ASET BMN
        case 'peminjaman_saya':
            $user->peminjaman_saya_page();
            break;
        case 'aksi_setuju':
            $user->aksi_setuju();
            break;
        case 'aksi_tolak':
            $user->aksi_tolak();
            break;

        // ASET SAYA
        case 'aset_saya':
            $user->aset_saya();
            break;
        case 'ganti_password':
            $user->ganti_password();
            break;
        case 'upload_paraf':
            $user->upload_paraf();
            break;

        // PROFIL PENGGUNA
        case 'profil_saya':
            $user->profil_page();
            break;

        // ==========================================
        // RUTE PIMPINAN CONTROLLER
        // ==========================================

        // LAPORAN
        case 'laporan_aset':
            $pimpinan->laporan_aset_page();
            break;
        case 'cetak_laporan_aset':
            $pimpinan->cetak_laporan_aset();
            break;

        case 'laporan_stok':
            $pimpinan->laporan_stok_page();
            break;
        case 'ajax_load_stok_barang':
            $pimpinan->ajax_load_stok_barang();
            break;
        case 'cetak_referensi_barang':
            $pimpinan->cetak_referensi_barang();
            break;

        case 'laporan_permintaan':
            $pimpinan->laporan_permintaan_page();
            break;
        case 'ajax_load_laporan_permintaan':
            $pimpinan->ajax_load_laporan_permintaan();
            break;

        case 'laporan_persediaan':
            $pimpinan->laporan_persediaan_page();
            break;
        case 'cetak_laporan_persediaan':
            $pimpinan->cetak_laporan_persediaan();
            break;

        // LAPORAN [CADANGAN]
        case 'laporan_transaksi':
            $pimpinan->laporan_transaksi_page();
            break;
        case 'ajax_load_riwayat_persetujuan':
            $pimpinan->ajax_load_riwayat_persetujuan();
            break;
        case 'ajax_load_laporan_stok_barang':
            $pimpinan->ajax_load_laporan_stok_barang();
            break;
        case 'cetak_laporan_transaksi':
            $pimpinan->cetak_laporan_transaksi();
            break;


        // ==========================================
        // RUTE LOG BARANG CONTROLLER
        // ==========================================
        case 'log_barang':
            $logBarang->log_barang_habis_pakai();
            break;
        case 'edit_log_stok_barang':
            $logBarang->edit_log_stok_barang();
            break;
        case 'ajax_load_log_barang_habis_pakai':
            $logBarang->ajax_load_log_barang_habis_pakai();
            break;
        case 'log_aset_bmn':
            $logBarang->log_aset_bmn();
            break;
        case 'ajax_load_log_aset_bmn':
            $logBarang->ajax_load_log_aset_bmn();
            break;

        // Default action or 404
        default:
            echo "Halaman tidak ditemukan.";
            break;
    }
} else {
    $dashboard->index();
}
