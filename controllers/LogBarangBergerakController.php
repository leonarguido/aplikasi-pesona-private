<?php

class LogBarangBergerakController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?page=';

    public function log_barang_bergerak()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. Cek Akses (Hanya Pimpinan & Admin yang boleh lihat stok)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] != 'pimpinan') {
            // echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Akses Ditolak!',
            ];
            header("Location: index.php");
            exit;
        }

        // Load Bulan & Tahun Sekarang
        $bulan_angka = date('m');
        $tahun_angka = date('Y');

        if (isset($_POST['status_permintaan'])) {
            $status_permintaan = $_POST['status_permintaan'];
        } else {
            $status_permintaan = '';
        }

        require_once '../views/pimpinan/log_barang_bergerak.php';
    }


    public function ajax_load_log_barang_bergerak()
    {
        require __DIR__ . '/../config/koneksi.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $bulan = $_POST['bulan_angka_post'];
            $tahun = $_POST['tahun_angka_post'];

            $query = mysqli_query($koneksi, "
                SELECT 
                    l.id,
                    l.tanggal,
                    l.admin_id,
                    u.nama,
                    b.kode_barang,
                    b.nama_barang,
                    l.aksi
                FROM tb_log_barang_bergerak l
                JOIN tb_user u ON l.admin_id = u.id
                JOIN tb_barang_bergerak b ON l.barang_bergerak_id = b.id
                WHERE MONTH(l.tanggal) = '$bulan' AND YEAR(l.tanggal) = '$tahun'
            ");

            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                $aksi = "";
                $class_aksi = "";
                if ($row['aksi'] == "hapus") {
                    $aksi = "<span class='badge badge-danger'>Hapus</span>";
                    $class_aksi = "color: red; font-weight: bold;";
                } elseif ($row['aksi'] == "edit") {
                    $aksi = "<span class='badge badge-warning'>Edit</span>";
                    $class_aksi = "color: orange; font-weight: bold;";
                } else {
                    $aksi = "<span class='badge badge-success'>Tambah</span>";
                    $class_aksi = "color: green; font-weight: bold;";
                }

                echo "
                <tr>
                    <td>{$row['tanggal']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['kode_barang']}</td>
                    <td>{$row['nama_barang']}</td>
                    <td style='text-align:center; font-size: 1.1em; {$class_aksi}'>
                        {$aksi}
                    </td>
                </tr>";
                $no++;
                // Melakukan <i {$class_aksi}>{$aksi}</i>
            }
            exit;
        }
    }

    // PROSES LOG DATA BARANG
    public function proses_log_barang($id_admin, $id_barang, $aksi)
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if ($id_barang != null && $aksi != null) {
            $query = "INSERT INTO tb_log_barang_bergerak (admin_id, barang_bergerak_id, tanggal, aksi) 
                  VALUES ('$id_admin', '$id_barang', NOW(), '$aksi')";

            mysqli_query($koneksi, $query);
        }
    }
}
