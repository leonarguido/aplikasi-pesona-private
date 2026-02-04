<?php

class PimpinanController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?page=';

    public function laporan_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // Cek Akses (Hanya Admin)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user') {
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

        require_once '../views/pimpinan/laporan.php';
    }

    public function ajax_load_stok_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $bulan = $_POST['bulan_angka_post'];
            $tahun = $_POST['tahun_angka_post'];

            $query = mysqli_query($koneksi, "
                SELECT 
                    b.kode_barang,
                    b.merk_barang,
                    b.nama_barang,
                    b.satuan,
                    SUM(d.jumlah) AS total_keluar
                FROM tb_detail_permintaan d
                JOIN tb_permintaan p ON d.permintaan_id = p.id
                JOIN tb_barang_bergerak b ON d.barang_id = b.id
                WHERE 
                    p.tanggal_disetujui IS NOT NULL
                    AND MONTH(p.tanggal_disetujui) = '$bulan'
                    AND YEAR(p.tanggal_disetujui) = '$tahun'
                GROUP BY d.barang_id
            ");

            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                echo "
                <tr>
                    <td style='text-align:center'>{$no}</td>
                    <td>{$row['kode_barang']}</td>
                    <td>{$row['merk_barang']}</td>
                    <td>{$row['nama_barang']}</td>
                    <td style='text-align:center; font-size:1.1em; color:green; font-weight:bold'>
                        {$row['total_keluar']}
                    </td>
                    <td style='text-align:center'>{$row['satuan']}</td>
                </tr>";
                $no++;
            }
            exit;
        }
    }

    public function ajax_load_riwayat_persetujuan()
    {
        require __DIR__ . '/../config/koneksi.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $bulan = $_POST['bulan_angka_post'];
            $tahun = $_POST['tahun_angka_post'];

            $query = mysqli_query($koneksi, "
            SELECT 
                p.*, 
                u.nama AS nama_pemohon, 
                a.nama AS nama_admin
            FROM tb_permintaan p
            JOIN tb_user u ON p.user_id = u.id
            LEFT JOIN tb_user a ON p.admin_id = a.id
            WHERE 
                p.status = 'disetujui'
                AND p.tanggal_disetujui IS NOT NULL
                AND MONTH(p.tanggal_disetujui) = '$bulan'
                AND YEAR(p.tanggal_disetujui) = '$tahun'
            ORDER BY p.tanggal_disetujui DESC
        ");

            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                $id_req = $row['id'];

                // DETAIL BARANG
                $q_detail = mysqli_query($koneksi, "
                    SELECT d.jumlah, d.satuan, b.nama_barang, b.merk_barang
                    FROM tb_detail_permintaan d
                    JOIN tb_barang_bergerak b ON d.barang_id = b.id
                    WHERE d.permintaan_id = '$id_req'
                ");

                $barang = $jumlah = $satuan = [];

                while ($item = mysqli_fetch_assoc($q_detail)) {
                    $barang[] = $item['nama_barang'] . ' (' . $item['merk_barang'] . ')';
                    $jumlah[] = $item['jumlah'];
                    $satuan[] = $item['satuan'];
                }

                // OUTPUT HTML
                echo "<tr>
                    <td style='text-align:center;'>{$no}</td>
                    <td style='text-align:center;'>" . date('d-m-Y', strtotime($row['tanggal_disetujui'])) . "</td>
                    <td>{$row['nama_pemohon']}</td>

                    <td><ul style='margin:0;padding-left:18px;'>";
                foreach ($barang as $b) {
                    echo "<li>$b</li>";
                }
                echo "</ul></td>

                    <td style='text-align:center;'><ul style='margin:0;list-style:none;padding-left:0;'>";
                foreach ($jumlah as $j) {
                    echo "<li>$j</li>";
                }
                echo "</ul></td>

                    <td style='text-align:center;'><ul style='margin:0;list-style:none;padding-left:0;'>";
                foreach ($satuan as $s) {
                    echo "<li>$s</li>";
                }
                echo "</ul></td>

                    <td>{$row['nama_admin']}</td>
                </tr>";

                $no++;
            }
            exit;
        }
    }


    public function laporan_stok_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. Cek Akses (Hanya Pimpinan & Admin yang boleh lihat stok)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'staff') {
            // echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Akses Ditolak!',
            ];
            header("Location: index.php");
            exit;
        }

        if (isset($_POST['status_stok'])) {
            $status_stok = $_POST['status_stok'];
        } else {
            $status_stok = '';
        }

        require_once '../views/pimpinan/laporan_stok.php';
    }

    public function ajax_load_laporan_stok_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['status_stok_post'])) {
                $status_stok = $_POST['status_stok_post'];
            } else {
                $status_stok = '';
            }

            if ($status_stok == "habis") {
                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 AND stok=0 ORDER BY nama_barang ASC");
            } else if ($status_stok == "menipis") {
                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 AND stok>0 AND stok<=10 ORDER BY nama_barang ASC");
            } else if ($status_stok == "aman") {
                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 AND stok>10 ORDER BY nama_barang ASC");
            } else {
                $query = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 ORDER BY nama_barang ASC");
            }

            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                $status = "";
                $class_stok = "";
                if ($row['stok'] == 0) {
                    $status = "<span class='badge badge-danger'>Habis</span>";
                    $class_stok = "color: red; font-weight: bold;";
                } elseif ($row['stok'] <= 10) { // Angka 10 bisa diubah sesuai kebijakan
                    $status = "<span class='badge badge-warning'>Menipis</span>";
                    $class_stok = "color: orange; font-weight: bold;";
                } else {
                    $status = "<span class='badge badge-success'>Aman</span>";
                    $class_stok = "color: green; font-weight: bold;";
                }

                echo "
                <tr>
                    <td style='text-align:center;'>{$no}</td>
                    <td>{$row['kode_barang']}</td>
                    <td>{$row['merk_barang']}</td>
                    <td>{$row['nama_barang']}</td>
                    <td style='text-align:center; font-size: 1.1em; {$class_stok}'>
                        {$row['stok']}
                    </td>
                    <td style='text-align:center;'>{$row['satuan']}</td>
                    <td class='no-print'>{$status}</td>
                </tr>";
                $no++;
            }
            exit;
        }
    }

    public function laporan_permintaan_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. Cek Akses (Hanya Pimpinan & Admin yang boleh lihat stok)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'staff') {
            // echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Akses Ditolak!',
            ];
            header("Location: index.php");
            exit;
        }

        if (isset($_POST['status_permintaan'])) {
            $status_permintaan = $_POST['status_permintaan'];
        } else {
            $status_permintaan = '';
        }

        require_once '../views/pimpinan/laporan_permintaan.php';
    }

    public function ajax_load_laporan_permintaan()
    {
        require __DIR__ . '/../config/koneksi.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['status_permintaan_post'])) {
                $status_permintaan = $_POST['status_permintaan_post'];
            } else {
                $status_permintaan = '';
            }

            $sql = "SELECT p.*, u.nama AS nama_pemohon, a.nama AS nama_admin
                    FROM tb_permintaan p
                    JOIN tb_user u ON p.user_id = u.id
                    LEFT JOIN tb_user a ON p.admin_id = a.id";

            if ($status_permintaan != "") {
                $sql .= " WHERE p.status = '$status_permintaan'";
            }

            $sql .= " ORDER BY p.id DESC";

            $query = mysqli_query($koneksi, $sql);

            $no = 1;
            while ($hist = mysqli_fetch_assoc($query)) {
                $id_hist = $hist['id'];

                echo "
                <tr>
                    <td style='text-align:center;'>{$no}</td>
                    <td>{$hist['tanggal_permintaan']}</td>
                    <td class='font-weight-bold text-primary'>{$hist['nama_pemohon']}</td>
                    <td>
                        <ul class='pl-3 mb-0' style='font-size: 0.9rem;'>
                            ";
                $q_detail_hist = mysqli_query($koneksi, "SELECT d.jumlah, d.satuan, b.nama_barang, b.satuan, b.merk_barang
                                                    FROM tb_detail_permintaan d 
                                                    JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                                    WHERE d.permintaan_id = '$id_hist'");

                while ($dh = mysqli_fetch_assoc($q_detail_hist)) {
                    echo "<li class='mb-1'>{$dh['nama_barang']} ({$dh['merk_barang']}) : <b>{$dh['jumlah']} {$dh['satuan']}</b></li>";
                };
                echo "
                        </ul>
                    </td>

                    <td class='text-center'>
                    ";
                if ($hist['status'] == 'menunggu') {
                    echo "<span class='badge badge-warning px-2 py-1'>Menunggu</span>";
                } elseif ($hist['status'] == 'disetujui') {
                    echo "<span class='badge badge-success px-2 py-1'>Disetujui</span>";
                } else {
                    echo "<span class='badge badge-danger px-2 py-1'>Ditolak</span>
                            <div class='small text-danger mt-1 font-italic'>'{$hist['catatan']}'</div>";
                }
                echo "
                    </td>
                    <td class='small text-muted'>";
                if ($hist['status'] == 'menunggu') {
                    echo "<div style='text-align:center;'>-</div>";
                } else {
                    echo "<i class='fas fa-user-shield'></i> {$hist['nama_admin']}";
                }
                echo "
                    </td>
                </tr>";
                $no++;
            }
            exit;
        }
    }

    public function cetak_laporan()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

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
