<?php

class AdminController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?method=';

    // MASUK HALAMAN DATA BARANG
    public function data_barang_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // Cek Login & Role
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'pimpinan') {
            echo "<script>alert('Anda tidak memiliki akses!'); window.location='index.php';</script>";
            exit;
        }

        require_once '../views/admin/data_barang.php';
    }

    // A. PROSES TAMBAH DATA BARANG
    public function tambah_data_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['tambah'])) {
            $kode   = $_POST['kode_barang'];
            $merk   = $_POST['merk_barang'];
            $nama   = $_POST['nama_barang'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['keterangan'];
            $stok   = $_POST['stok'];

            $cek = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 AND kode_barang = '$kode'");
            if (mysqli_num_rows($cek) > 0) {
                echo "<script>alert('Kode Barang sudah ada!'); window.location='" . $this->base_url . "data_barang';</script>";
            } else {
                $query = "INSERT INTO tb_barang_bergerak (kode_barang, merk_barang, nama_barang, satuan, keterangan, stok) 
                  VALUES ('$kode', '$merk', '$nama', '$satuan', '$desc', '$stok')";
                if (mysqli_query($koneksi, $query)) {
                    echo "<script>alert('Barang berhasil ditambahkan!'); window.location='" . $this->base_url . "data_barang';</script>";
                } else {
                    echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
                }
            }
        }
    }


    // B. PROSES EDIT DATA BARANG
    public function edit_data_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['edit'])) {
            $id     = $_POST['id'];
            $merk   = $_POST['merk_barang'];
            $nama   = $_POST['nama_barang'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['keterangan'];
            $stok   = $_POST['stok'];

            $query = "UPDATE tb_barang_bergerak SET merk_barang='$merk', nama_barang='$nama', satuan='$satuan', keterangan='$desc', stok='$stok' WHERE id='$id'";
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Data berhasil diupdate!'); window.location='" . $this->base_url . "data_barang';</script>";
            }
        }
    }

    // C. PROSES HAPUS DATA BARANG
    public function hapus_data_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "UPDATE tb_barang_bergerak SET is_deleted=1 WHERE id = '$id'";
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Barang berhasil dihapus!'); window.location='" . $this->base_url . "data_barang';</script>";
            }
        }
    }

    // D. DOWNLOAD TEMPLATE EXCEL/CSV
    public function template_barang()
    {
        require_once '../views/admin/template_barang.php';
    }

    // E. PROSES INPUT DATA DARI EXCEL/CSV
    public function import_excel_data_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['import_excel'])) {
            // Cek apakah file diupload
            if (isset($_FILES['file_excel']['name']) && $_FILES['file_excel']['name'] != "") {

                $filename = $_FILES['file_excel']['tmp_name'];
                $ext = pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION);

                // Validasi Ekstensi harus CSV
                if ($ext != 'csv') {
                    echo "<script>alert('Format file harus .CSV (Comma Separated Values)!');</script>";
                } else {
                    $file = fopen($filename, "r");
                    $count = 0; // Hitung data sukses

                    // Lewati baris pertama (Header Judul) agar tidak ikut ter-input
                    fgetcsv($file);

                    while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                        // Mapping Data dari Excel/CSV ke Variabel
                        // Kolom 0: Kode,  1: Nama, 2: Merk, 3: Satuan, 4: Stok, 5: Ket
                        $kode   = mysqli_real_escape_string($koneksi, $data[0]);
                        $nama   = mysqli_real_escape_string($koneksi, $data[1]);
                        $merk   = mysqli_real_escape_string($koneksi, $data[2]);
                        $satuan = mysqli_real_escape_string($koneksi, $data[3]);
                        $stok   = (int) $data[4];
                        $desc   = mysqli_real_escape_string($koneksi, $data[5]);

                        // Cek Duplikat Kode Barang
                        $cek = mysqli_query($koneksi, "SELECT kode_barang FROM tb_barang_bergerak WHERE kode_barang = '$kode'");

                        if (mysqli_num_rows($cek) == 0 && !empty($kode)) {
                            // Jika kode belum ada, Insert Baru
                            $query = "INSERT INTO tb_barang_bergerak (kode_barang, merk_barang, nama_barang, satuan, stok, keterangan) 
                              VALUES ('$kode', '$merk', '$nama', '$satuan', '$stok', '$desc')";
                            mysqli_query($koneksi, $query);
                            $count++;
                        }
                    }
                    fclose($file);
                    echo "<script>alert('Berhasil mengimpor $count data barang!'); window.location='" . $this->base_url . "data_barang';</script>";
                }
            } else {
                echo "<script>alert('Pilih file terlebih dahulu!');</script>";
            }
        }
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
        require_once '../views/admin/persetujuan.php';
    }

    public function riwayat_persetujuan_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. Cek Akses
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'staff' || $_SESSION['role'] == 'user') {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            exit;
        }

        require_once '../views/admin/riwayat_persetujuan.php';
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

    // B. PROSES PENOLAKAN
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

    // MASUK HALAMAN DATA PENGGUNA
    public function data_pengguna_page()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        // 1. Cek Login & Akses
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Hanya Super Admin yang boleh akses halaman ini
        if ($_SESSION['role'] != 'super_admin') {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            exit;
        }

        require_once '../views/admin/data_pengguna.php';
    }

    // A. PROSES TAMBAH USER
    public function tambah_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['tambah_user'])) {
            $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
            $username = mysqli_real_escape_string($koneksi, $_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role     = $_POST['role']; // Ambil value dari select option

            // Upload Tanda Tangan
            $paraf_name = null;
            if (!empty($_FILES['paraf']['name'])) {
                $filename   = $_FILES['paraf']['name'];
                $filesize   = $_FILES['paraf']['size'];
                $ext        = pathinfo($filename, PATHINFO_EXTENSION);
                $allowed    = ['png', 'jpg', 'jpeg'];

                if (!in_array(strtolower($ext), $allowed)) {
                    echo "<script>alert('Format TTD harus PNG, JPG, atau JPEG!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                    exit;
                }
                if ($filesize > 2000000) {
                    echo "<script>alert('Ukuran file TTD terlalu besar (Max 2MB)!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                    exit;
                }

                $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['paraf']['tmp_name'], 'assets/img/ttd/' . $paraf_name);
            }

            $q = "INSERT INTO tb_user (nama, nip, username, password, role, paraf) 
          VALUES ('$nama', '$nip', '$username', '$password', '$role', '$paraf_name')";

            if (mysqli_query($koneksi, $q)) {
                echo "<script>alert('User Berhasil Ditambahkan!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                echo "<script>alert('Gagal menambah user: " . mysqli_error($koneksi) . "');</script>";
            }
        }
    }

    // B. PROSES EDIT USER
    public function edit_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';
        // var_dump($_POST);
        // exit;

        if (isset($_POST['edit_user'])) {
            $id       = $_POST['id_user'];
            $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
            $username = mysqli_real_escape_string($koneksi, $_POST['username']);
            $role     = $_POST['role'];

            // Password Logic
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $q_pass = ", password='$password'";
            } else {
                $q_pass = "";
            }

            // TTD Logic
            $q_ttd = "";
            if (!empty($_FILES['paraf']['name'])) {
                $filename   = $_FILES['paraf']['name'];
                $ext        = pathinfo($filename, PATHINFO_EXTENSION);
                $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['paraf']['tmp_name'], 'assets/img/ttd/' . $paraf_name);
                $q_ttd = ", paraf='$paraf_name'";
            }

            $q = "UPDATE tb_user SET 
          nama='$nama', nip='$nip', username='$username', role='$role' $q_pass $q_ttd
          WHERE id='$id'";

            if (mysqli_query($koneksi, $q)) {
                echo "<script>alert('Data User Diupdate!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                echo "<script>alert('Gagal update!');</script>";
            }
        }
    }

    // C. PROSES HAPUS USER
    public function hapus_data_pengguna()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_GET['hapus'])) {
            $id = $_GET['hapus'];
            $q_img = mysqli_query($koneksi, "SELECT paraf FROM tb_user WHERE id='$id'");
            $row_img = mysqli_fetch_assoc($q_img);
            if ($row_img['paraf'] != null && file_exists('assets/img/ttd/' . $row_img['paraf'])) {
                unlink('assets/img/ttd/' . $row_img['paraf']);
            }

            $q = "DELETE FROM tb_user WHERE id='$id'";
            if (mysqli_query($koneksi, $q)) {
                echo "<script>alert('User Dihapus!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                echo "<script>alert('Gagal hapus!');</script>";
            }
        }
    }

}
?>