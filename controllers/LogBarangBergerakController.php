<?php

class LogBarangBergerakController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?page=';

    public function log_data_barang_page()
    {
        require_once '../views/log_data_barang.php';
    }
}
