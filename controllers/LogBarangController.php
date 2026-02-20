<?php

class LogBarangController
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
'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
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

    public function edit_log_stok_barang()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['edit_log'])) {
            $id     = $_POST['id'];
            $kode   = $_POST['kode_barang'];
            $stok_lama   = $_POST['stok_lama'];
            $stok_baru   = $_POST['stok_baru'];
            $desc   = $_POST['keterangan'];

            if ($stok_baru < $stok_lama) {
                $selisih = $stok_lama - $stok_baru;
                mysqli_query($koneksi, "UPDATE tb_barang_bergerak SET stok= stok-'$selisih' WHERE kode_barang='$kode' AND is_deleted=0");
            } else {
                $selisih = $stok_baru - $stok_lama;
                mysqli_query($koneksi, "UPDATE tb_barang_bergerak SET stok= stok+'$selisih' WHERE kode_barang='$kode' AND is_deleted=0");
            }

            $query = "UPDATE tb_log_barang_bergerak SET stok= '$stok_baru', keterangan='$desc' WHERE id='$id'";
            if (mysqli_query($koneksi, $query)) {
                $_SESSION['alert'] = [
                    'icon' => 'success',
'title' => 'Berhasil!',
                    'text' => 'Data log barang berhasil diupdate!',
                ];
                header("Location: " . $this->base_url . "log_barang");
                exit;
            }
        }
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
                    b.is_deleted,
                    l.aksi,
                    l.stok,
                    l.keterangan
                FROM tb_log_barang_bergerak l
                JOIN tb_user u ON l.admin_id = u.id
                JOIN tb_barang_bergerak b ON l.barang_id = b.id
                WHERE MONTH(l.tanggal) = '$bulan' AND YEAR(l.tanggal) = '$tahun'
            ");

            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                $aksi = "";
                $class_aksi = "";
                if ($row['aksi'] == "hapus barang") {
                    $aksi = "<span class='badge badge-danger'>Hapus Barang</span>";
                    $class_aksi = "color: red; font-weight: bold;";
                } elseif ($row['aksi'] == "edit barang") {
                    $aksi = "<span class='badge badge-warning'>Edit Barang</span>";
                    $class_aksi = "color: orange; font-weight: bold;";
                } elseif ($row['aksi'] == "tambah barang") {
                    $aksi = "<span class='badge badge-success'>Tambah barang</span>";
                    $class_aksi = "color: blue; font-weight: bold;";
                } elseif ($row['aksi'] == "tambah stok") {
                    $aksi = "<span class='badge badge-info'>Tambah Stok</span>";
                    $class_aksi = "color: blue; font-weight: bold;";
                } else {
                    $aksi = "<span class='badge badge-secondary'>Tidak ada aksi</span>";
                    $class_aksi = "color: gray; font-weight: bold;";
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
                ";
                if ($row['stok'] != null && $row['stok'] > 0 && $row['is_deleted'] == 0) {
                    echo "
                    <td>penambahan stok sebesar {$row['stok']} dengan keterangan {$row['keterangan']}
                        <button 
                            class='btn btn-info btn-sm btn-circle btn-edit'
                            data-id='{$row['id']}'
                            data-kode='{$row['kode_barang']}'
                            data-stok='{$row['stok']}'
                            data-keterangan='{$row['keterangan']}'
                            data-toggle='modal'
                            data-target='#modalEdit'>
                            <i class='fas fa-edit'></i>
                        </button>
                    </td>
                    </tr>
                    ";
                } elseif ($row['stok'] != null && $row['stok'] > 0 && $row['is_deleted'] == 1) {
                    echo "<td>penambahan stok sebesar {$row['stok']} dengan keterangan {$row['keterangan']}</td>
                    </tr>";
                } else {
                    echo "<td>{$row['keterangan']}</td>
                    </tr>";
                }
                $no++;
                // Melakukan <i {$class_aksi}>{$aksi}</i>
            }
            exit;
        }
    }

    // PROSES LOG DATA BARANG
    public function proses_log_barang_bergerak($id_admin, $data, $aksi)
    {
        require __DIR__ . '/../config/koneksi.php';

        if ($aksi == "tambah barang") {
            $id_barang = $data['id_barang'];
            $data_baru = $data['data_baru'];
            $log = "data {$data_baru['nama_barang']} telah ditambahkan oleh admin";

            $query = "INSERT INTO tb_log_barang_bergerak (admin_id, barang_id, aksi, keterangan, tanggal) 
                      VALUES ('$id_admin', '$id_barang', '$aksi', '$log', NOW())";

            mysqli_query($koneksi, $query);
        } elseif ($aksi == "edit barang" || $aksi == "tambah stok") {
            $id_barang = $data['id_barang'];
            $kolom = $data['kolom'];
            $data_baru = $data['data_baru'];
            $data_lama = $data['data_lama'];

            if (in_array('stok', $kolom)) {
                $query = "INSERT INTO tb_log_barang_bergerak (admin_id, barang_id, aksi, stok, keterangan, tanggal) 
                      VALUES ('$id_admin', '$id_barang', '$aksi', '$data_baru[stok]', '$data_baru[keterangan]', NOW())";

                mysqli_query($koneksi, $query);
            } else {

                $data_baru = array_intersect_key($data_baru, array_flip($kolom));
                $data_lama = array_intersect_key($data_lama, array_flip($kolom));

                $perubahan = [];
                foreach ($data_baru as $kolom => $nilai_baru) {
                    $nilai_lama = $data_lama[$kolom];

                    if ($nilai_lama != $nilai_baru) {
                        $kolom = str_replace('_', ' ', $kolom);
                        if ($nilai_baru == '' || $nilai_baru == null) {
                            $perubahan[] = "$kolom berubah dari $nilai_lama menjadi kosong/dihapus";
                        } elseif ($nilai_lama == '' || $nilai_lama == null) {
                            $perubahan[] = "$kolom berubah dari kosong menjadi $nilai_baru";
                        } else {
                            $perubahan[] = "$kolom berubah dari $nilai_lama menjadi $nilai_baru";
                        }
                    }
                }
                $log = implode(', ', $perubahan);

                $query = "INSERT INTO tb_log_barang_bergerak (admin_id, barang_id, aksi, keterangan, tanggal) 
                      VALUES ('$id_admin', '$id_barang', '$aksi', '$log', NOW())";

                mysqli_query($koneksi, $query);
            }
        } else {
            $id_barang = $data['id_barang'];
            $data_lama = $data['data_lama'];
            $log = "data {$data_lama['nama_barang']} telah dihapus oleh admin";

            $query = "INSERT INTO tb_log_barang_bergerak (admin_id, barang_id, aksi, keterangan, tanggal) 
                      VALUES ('$id_admin', '$id_barang', '$aksi', '$log', NOW())";

            mysqli_query($koneksi, $query);
        }
    }

    public function log_barang_tg()
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
'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
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

        require_once '../views/pimpinan/log_barang_tg.php';
    }


    public function ajax_load_log_barang_tg()
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
                FROM tb_log_barang_tidak_bergerak l
                JOIN tb_user u ON l.admin_id = u.id
                JOIN tb_barang_tidak_bergerak b ON l.barang_tidak_bergerak_id = b.id
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
    public function proses_log_barang_tg($id_admin, $id_barang, $aksi)
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if ($id_barang != null && $aksi != null) {
            $query = "INSERT INTO tb_log_barang_tidak_bergerak (admin_id, barang_tidak_bergerak_id, tanggal, aksi) 
                  VALUES ('$id_admin', '$id_barang', NOW(), '$aksi')";

            mysqli_query($koneksi, $query);
        }
    }
}
