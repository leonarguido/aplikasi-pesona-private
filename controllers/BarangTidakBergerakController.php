<?php 

class BarangTidakBergerakController {
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?method=';
    
    public function daftar_barang_page() {
        require_once '../views/daftar_barang.php';
    }
}

?>