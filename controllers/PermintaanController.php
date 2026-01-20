<?php

class PermintaanController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?method=';

    // MASUK HALAMAN PERMINTAAN SAYA
    public function permintaan_saya_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. Cek Login (Hanya User/Staff)
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        require_once '../views/permintaan_saya.php';
    }

    // A. PROSES CETAK SURAT PERMINTAAN
    public function cetak_surat()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header("Location: index.php");
            exit;
        }

        $id_permintaan = $_GET['id'];

        // ============================================
        // QUERY 1: AMBIL DATA HEADER (User, Admin, Tgl)
        // ============================================
        // Perbaikan: Mengambil kolom 'paraf' tapi kita alias-kan jadi 'ttd_...' biar mudah
        $query_header = "SELECT p.*, 
                 u_pemohon.nama AS nama_pemohon, u_pemohon.nip AS nip_pemohon, u_pemohon.paraf AS ttd_pemohon,
                 u_admin.nama AS nama_admin, u_admin.nip AS nip_admin, u_admin.paraf AS ttd_admin
                 FROM tb_permintaan p
                 JOIN tb_user u_pemohon ON p.user_id = u_pemohon.id
                 LEFT JOIN tb_user u_admin ON p.admin_id = u_admin.id
                 WHERE p.id = '$id_permintaan'";

        $result_header = mysqli_query($koneksi, $query_header);
        $data = mysqli_fetch_assoc($result_header);

        // Validasi: Hanya bisa dicetak jika sudah DISETUJUI
        if ($data['status'] != 'disetujui') {
            echo "<script>alert('Surat belum bisa dicetak karena status belum disetujui!'); window.close();</script>";
            exit;
        }

        require_once '../views/cetak_surat.php';
    }

    // MASUK HALAMAN PERSETUJUAN
    public function persetujuan_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'staff' || $_SESSION['role'] == 'user') {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            exit;
        }
        require_once '../views/persetujuan.php';
    }

    // A. PROSES PERSETUJUAN
    public function proses_persetujuan()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['setuju'])) {
            $id_permintaan   = $_POST['id_permintaan'];
            $catatan_admin   = mysqli_real_escape_string($koneksi, $_POST['catatan_admin']);
            $admin_id        = $_SESSION['user_id'];
            $tanggal_acc     = date('Y-m-d');
            $qty_approved_array = $_POST['qty_approved'];

            $error_msg = "";
            $stok_aman = true;

            // TAHAP 1: VALIDASI
            foreach ($qty_approved_array as $id_detail => $jml_acc) {
                $q_check = mysqli_query($koneksi, "SELECT d.jumlah AS jml_minta, b.stok, b.nama_barang 
                                           FROM tb_detail_permintaan d 
                                           JOIN tb_barang_bergerak b ON d.barang_id = b.id 
                                           WHERE d.id = '$id_detail'");
                $data = mysqli_fetch_assoc($q_check);

                if ($jml_acc > $data['jml_minta']) {
                    $error_msg = "Gagal! Jumlah disetujui untuk '{$data['nama_barang']}' melebihi permintaan.";
                    $stok_aman = false;
                    break;
                }
                if ($jml_acc > $data['stok']) {
                    $error_msg = "Gagal! Stok '{$data['nama_barang']}' tidak cukup.";
                    $stok_aman = false;
                    break;
                }
                if ($jml_acc <= 0) {
                    $error_msg = "Gagal! Jumlah minimal 1.";
                    $stok_aman = false;
                    break;
                }
            }

            // TAHAP 2: EKSEKUSI
            if (!$stok_aman) {
                echo "<script>alert('$error_msg');</script>";
            } else {
                foreach ($qty_approved_array as $id_detail => $jml_acc) {
                    mysqli_query($koneksi, "UPDATE tb_detail_permintaan SET jumlah = '$jml_acc' WHERE id = '$id_detail'");
                    $q_b = mysqli_query($koneksi, "SELECT barang_id FROM tb_detail_permintaan WHERE id = '$id_detail'");
                    $d_b = mysqli_fetch_assoc($q_b);
                    $id_barang = $d_b['barang_id'];
                    mysqli_query($koneksi, "UPDATE tb_barang_bergerak SET stok = stok - $jml_acc WHERE id = '$id_barang'");
                }

                $query_update = "UPDATE tb_permintaan SET 
                         status = 'disetujui', 
                         tanggal_disetujui = '$tanggal_acc', 
                         admin_id = '$admin_id',
                         catatan = '$catatan_admin'
                         WHERE id = '$id_permintaan'";

                if (mysqli_query($koneksi, $query_update)) {
                    echo "<script>alert('Permintaan berhasil DISETUJUI!'); window.location='" . $this->base_url . "persetujuan';</script>";
                } else {
                    echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
                }
            }
        }
    }

    // B. PROSES PENOLAKA
    public function proses_penolakan()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['tolak'])) {
            $id_permintaan = $_POST['id_permintaan'];
            $catatan       = mysqli_real_escape_string($koneksi, $_POST['catatan']);
            $admin_id      = $_SESSION['user_id'];
            $tanggal_acc   = date('Y-m-d');

            $query = "UPDATE tb_permintaan SET 
              status = 'ditolak', 
              tanggal_ditolak = '$tanggal_acc', 
              admin_id = '$admin_id',
              catatan = '$catatan' 
              WHERE id = '$id_permintaan'";

            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Permintaan berhasil DITOLAK!'); window.location='" . $this->base_url . "persetujuan';</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
            }
        }
    }
}
