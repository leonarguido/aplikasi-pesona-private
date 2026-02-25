<?php

class AdminController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?page=';
    protected $assets_path = __DIR__ . '/../assets/img/'; // direktori penyimpanan file paraf

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
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
            ];
            header("Location: index.php");
            exit;
        }

        require_once '../views/admin/data_barang.php';
    }

    // A. PROSES TAMBAH DATA BARANG
    public function tambah_data_barang()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['tambah'])) {
            $kode   = $_POST['kode_barang'];
            $merek   = $_POST['merek_barang'];
            $nama   = $_POST['nama_barang'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['keterangan'];
            $stok   = $_POST['stok'];

            $cek = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE is_deleted=0 AND kode_barang = '$kode'");
            if (mysqli_num_rows($cek) > 0) {
                // echo "<script>alert('Kode Barang sudah ada!'); window.location='" . $this->base_url . "data_barang';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Kode Barang sudah ada!',
                ];
                header("Location: " . $this->base_url . "data_barang");
                exit;
            } else {
                $query = "INSERT INTO tb_barang_bergerak (kode_barang, merek_barang, nama_barang, satuan, keterangan, stok) 
                  VALUES ('$kode', '$merek', '$nama', '$satuan', '$desc', '$stok')";

                if (mysqli_query($koneksi, $query)) {
                    // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
                    $id_admin = $_SESSION['user_id'];
                    $id_barang = mysqli_insert_id($koneksi);
                    $q = mysqli_query($koneksi, "SELECT nama_barang FROM tb_barang_bergerak WHERE id='$id_barang'");
                    $data_baru = mysqli_fetch_assoc($q);

                    $data = [
                        'id_barang' => $id_barang,
                        'data_baru' => $data_baru
                    ];
                    $log_barang->proses_log_barang_bergerak($id_admin, $data, "tambah barang");

                    $_SESSION['alert'] = [
                        'icon' => 'success',
                        'title' => 'Berhasil!',
                        'text' => 'Barang berhasil ditambahkan!',
                    ];
                    header("Location: " . $this->base_url . "data_barang");
                    exit;
                } else {
                    // echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Gagal menambahkan barang: ' . mysqli_error($koneksi),
                    ];
                    header("Location: " . $this->base_url . "data_barang");
                    exit;
                }
            }
        }
    }


    // B. PROSES EDIT DATA BARANG
    public function edit_data_barang()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['edit'])) {
            $id     = $_POST['id'];
            $nama   = $_POST['nama_barang'];
            $merek   = $_POST['merek_barang'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['keterangan'];

            // KEBUTUHAN LOG BARANG
            $kolom = ['nama_barang', 'merek_barang', 'satuan', 'keterangan'];
            $data_baru = $_POST;
            $q = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE id='$id'");
            $data_lama = mysqli_fetch_assoc($q);

            $query = "UPDATE tb_barang_bergerak SET merek_barang='$merek', nama_barang='$nama', satuan='$satuan', keterangan='$desc' WHERE id='$id'";
            if (mysqli_query($koneksi, $query)) {
                // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
                $id_admin = $_SESSION['user_id'];
                $data = [
                    'id_barang' => $id,
                    'kolom' => $kolom,
                    'data_baru' => $data_baru,
                    'data_lama' => $data_lama
                ];
                $log_barang->proses_log_barang_bergerak($id_admin, $data, "edit barang");

                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data barang berhasil diupdate!',
                ];
                header("Location: " . $this->base_url . "data_barang");
                exit;
            }
        }
    }

    // C. PROSES EDIT DATA STOK
    public function edit_data_stok_barang()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['edit_stok'])) {
            $id     = $_POST['id'];
            $stok   = $_POST['stok'];
            $desc   = $_POST['keterangan'];

            // KEBUTUHAN LOG BARANG
            $kolom = ['stok', 'keterangan'];
            $data_baru = $_POST;
            $data_lama = '';

            $query = "UPDATE tb_barang_bergerak SET stok= stok+'$stok' WHERE id='$id'";
            if (mysqli_query($koneksi, $query)) {
                // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
                $id_admin = $_SESSION['user_id'];
                $data = [
                    'id_barang' => $id,
                    'kolom' => $kolom,
                    'data_baru' => $data_baru,
                    'data_lama' => $data_lama
                ];
                $log_barang->proses_log_barang_bergerak($id_admin, $data, "tambah stok");

                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data barang berhasil diupdate!',
                ];
                header("Location: " . $this->base_url . "data_barang");
                exit;
            }
        }
    }

    // C. PROSES HAPUS DATA BARANG
    public function hapus_data_barang()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $query = "UPDATE tb_barang_bergerak SET is_deleted=1 WHERE id = '$id'";
            if (mysqli_query($koneksi, $query)) {
                // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
                $id_admin = $_SESSION['user_id'];
                $q = mysqli_query($koneksi, "SELECT nama_barang FROM tb_barang_bergerak WHERE id='$id'");
                $data_lama = mysqli_fetch_assoc($q);

                $data = [
                    'id_barang' => $id,
                    'data_lama' => $data_lama
                ];
                $log_barang->proses_log_barang_bergerak($id_admin, $data, "hapus barang");

                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Barang berhasil dihapus!',
                ];
                header("Location: " . $this->base_url . "data_barang");
                exit;
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
        session_start();

        if (isset($_POST['import_excel'])) {
            // Cek apakah file diupload
            if (isset($_FILES['file_excel']['name']) && $_FILES['file_excel']['name'] != "") {

                $filename = $_FILES['file_excel']['tmp_name'];
                $ext = pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION);

                // Validasi Ekstensi harus CSV
                if ($ext != 'csv') {
                    // echo "<script>alert('Format file harus .CSV (Comma Separated Values)!');</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Format file harus .CSV (Comma Separated Values)!',
                    ];
                    var_dump($_SESSION);
                    header("Location: " . $this->base_url . "data_barang");
                    exit;
                } else {
                    $file = fopen($filename, "r");
                    $count = 0; // Hitung data sukses

                    // Lewati baris pertama (Header Judul) agar tidak ikut ter-input
                    fgetcsv($file);

                    while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {
                        // Mapping Data dari Excel/CSV ke Variabel
                        // Kolom 0: Kode,  1: Nama, 2: Merek, 3: Satuan, 4: Stok, 5: Ket
                        $kode   = mysqli_real_escape_string($koneksi, $data[0]);
                        $nama   = mysqli_real_escape_string($koneksi, $data[1]);
                        $merek   = mysqli_real_escape_string($koneksi, $data[2]);
                        $satuan = mysqli_real_escape_string($koneksi, $data[3]);
                        $stok   = (int) $data[4];
                        $desc   = mysqli_real_escape_string($koneksi, $data[5]);

                        // Cek Duplikat Kode Barang
                        $cek = mysqli_query($koneksi, "SELECT kode_barang FROM tb_barang_bergerak WHERE kode_barang = '$kode'");

                        if (mysqli_num_rows($cek) == 0 && !empty($kode)) {
                            // Jika kode belum ada, Insert Baru
                            $query = "INSERT INTO tb_barang_bergerak (kode_barang, merek_barang, nama_barang, satuan, stok, keterangan) 
                              VALUES ('$kode', '$merek', '$nama', '$satuan', '$stok', '$desc')";
                            mysqli_query($koneksi, $query);
                            $count++;
                        }
                    }
                    fclose($file);
                    // echo "<script>alert('Berhasil mengimpor $count data barang!'); window.location='" . $this->base_url . "data_barang';</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'success',
                        'title' => 'Berhasil!',
                        'text' => "Berhasil mengimpor $count data barang!",
                    ];
                    header("Location: " . $this->base_url . "data_barang");
                    exit;
                }
            } else {
                // echo "<script>alert('Pilih file terlebih dahulu!');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Pilih file terlebih dahulu!',
                ];
                header("Location: " . $this->base_url . "data_barang");
                exit;
            }
        }
    }

    // MASUK HALAMAN DATA BARANG TIDAK BERGERAK
    public function data_barang_tg()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // Cek Login & Role
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'admin gudang' && $_SESSION['role'] != 'super_admin' && $_SESSION['role'] != 'pimpinan')) {
            header("Location: login.php");
            exit;
        }
        if ($_SESSION['role'] == 'user' || $_SESSION['role'] == 'pimpinan') {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
            ];
            header("Location: index.php");
            exit;
        }

        // AMBIL DATA PEGAWAI (UNTUK DROPDOWN)
        $list_pegawai = [];
        $q_pgw = mysqli_query($koneksi, "SELECT id, nip, nama FROM tb_user WHERE nip IS NOT NULL AND nip != '' ORDER BY nama ASC");
        while ($p = mysqli_fetch_assoc($q_pgw)) {
            $list_pegawai[] = $p;
        }

        require_once '../views/admin/data_barang_tg.php';
    }

    // A. PROSES TAMBAH DATA BARANG
    public function tambah_data_barang_tg()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['tambah'])) {
            $nip    = mysqli_real_escape_string($koneksi, $_POST['nip']);
            $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
            $kode   = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
            $merek   = mysqli_real_escape_string($koneksi, $_POST['merek_barang']);
            $satuan = $_POST['satuan'];
            $jumlah = $_POST['jumlah'];
            $ket    = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

            $nama_file = null;
            if (!empty($_FILES['berkas']['name'])) {
                $file_tmp  = $_FILES['berkas']['tmp_name'];
                $nama_modifikasi = $this->perbaiki_nama_file($_FILES['berkas']['name']);
                $file_name = time() . "_" . $nama_modifikasi;
                if (!is_dir($this->assets_path . "berkas/")) {
                    mkdir($this->assets_path . "berkas/", 0777, true);
                }
                move_uploaded_file($file_tmp, $this->assets_path . "berkas/" . $file_name);
                $nama_file = $file_name;
            }

            $row = mysqli_query($koneksi, "SELECT id FROM tb_user WHERE nip = '$nip'");
            $data_user = mysqli_fetch_assoc($row);
            $id_user = $data_user['id'];

            $q = "INSERT INTO tb_barang_tidak_bergerak (user_id, nip, nama_barang, kode_barang, merek_barang, satuan, jumlah, keterangan, berkas) 
            VALUES ('$id_user', '$nip', '$nama', '$kode', '$merek', '$satuan', '$jumlah', '$ket', '$nama_file')";

            if (mysqli_query($koneksi, $q)) {
                // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
                $id_admin = $_SESSION['user_id'];
                $id_barang = mysqli_insert_id($koneksi);
                $log_barang->proses_log_barang_tg($id_admin, $id_barang, "tambah barang");

                // echo "<script>alert('Data Berhasil Ditambahkan!'); window.location='barang_tidak_bergerak.php';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data berhasil ditambahkan!',
                ];
                header("Location: " . $this->base_url . "data_barang_tg");
                exit;
            } else {
                // echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal:' + mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "data_barang_tg");
                exit;
            }
        }
    }


    // B. PROSES EDIT DATA BARANG
    public function edit_data_barang_tg()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['edit'])) {
            $id     = $_POST['id'];
            $nip    = mysqli_real_escape_string($koneksi, $_POST['nip']);
            $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
            $kode   = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
            $merek   = mysqli_real_escape_string($koneksi, $_POST['merek_barang']);
            $satuan = $_POST['satuan'];
            $jumlah = $_POST['jumlah'];
            $ket    = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

            $query_file = "";
            if (!empty($_FILES['berkas']['name'])) {
                $file_tmp  = $_FILES['berkas']['tmp_name'];
                $nama_modifikasi = $this->perbaiki_nama_file($_FILES['berkas']['name']);
                $file_name = time() . "_" . $nama_modifikasi;
                move_uploaded_file($file_tmp, $this->assets_path . "berkas/" . $file_name);
                $query_file = ", berkas='$file_name'";
            }

            $row = mysqli_query($koneksi, "SELECT id FROM tb_user WHERE nip = '$nip'");
            $data_user = mysqli_fetch_assoc($row);
            $id_user = $data_user['id'];

            $q = "UPDATE tb_barang_tidak_bergerak SET 
            user_id='$id_user', nip='$nip', nama_barang='$nama', kode_barang='$kode', merek_barang='$merek', 
            satuan='$satuan', jumlah='$jumlah', keterangan='$ket' $query_file 
            WHERE id='$id'";

            if (mysqli_query($koneksi, $q)) {
                // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
                $id_admin = $_SESSION['user_id'];
                $id_barang = $id;
                $log_barang->proses_log_barang_tg($id_admin, $id_barang, "edit barang");

                // echo "<script>alert('Data Berhasil Diupdate!'); window.location='barang_tidak_bergerak.php';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data berhasil diupdate!',
                ];
                header("Location: " . $this->base_url . "data_barang_tg");
                exit;
            }
        }
    }

    // C. PROSES HAPUS DATA BARANG
    public function hapus_data_barang_tg()
    {
        $log_barang = new LogBarangController();
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_GET['hapus'])) {
            $id = $_GET['hapus'];
            $q_cek = mysqli_query($koneksi, "SELECT berkas FROM tb_barang_tidak_bergerak WHERE id='$id'");
            $d_cek = mysqli_fetch_assoc($q_cek);
            if ($d_cek['berkas'] && file_exists($this->assets_path . "berkas/" . $d_cek['berkas'])) {
                unlink($this->assets_path . "berkas/" . $d_cek['berkas']);
            }
            // mysqli_query($koneksi, "DELETE FROM tb_barang_tidak_bergerak WHERE id='$id'");
            mysqli_query($koneksi, "UPDATE tb_barang_tidak_bergerak SET is_deleted=1 WHERE id = '$id'");
            // LOG BARANG DILAKUKAN SETELAH EKSEKUSI
            $id_admin = $_SESSION['user_id'];
            $id_barang = $id;
            $log_barang->proses_log_barang_tg($id_admin, $id_barang, "hapus barang");

            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Data berhasil dihapus!',
            ];
            header("Location: " . $this->base_url . "data_barang_tg");
            exit;
        }
    }

    public function perbaiki_nama_file($nama_file)
    {
        $nama_file = preg_replace('/[^A-Za-z0-9_-]/', '_', $nama_file);
        $nama_file = preg_replace('/_+/', '_', $nama_file);
        $nama_file = trim($nama_file, '_');

        return $nama_file;
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
            // echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
            ];
            header("Location: " . $this->base_url);
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
            // echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
            ];
            header("Location: " . $this->base_url);
            exit;
        }

        require_once '../views/admin/riwayat_persetujuan.php';
    }

    // A. PROSES PERSETUJUAN
    public function proses_persetujuan()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

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
                // echo "<script>alert('$error_msg');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => $error_msg,
                ];
                header("Location: " . $this->base_url . "persetujuan");
                exit;
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
                    // echo "<script>alert('Permintaan berhasil DISETUJUI!'); window.location='" . $this->base_url . "persetujuan';</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'success',
                        'title' => 'Berhasil!',
                        'text' => 'Permintaan berhasil DISETUJUI!',
                    ];
                    header("Location: " . $this->base_url . "persetujuan");
                    exit;
                } else {
                    // echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Error:' + mysqli_error($koneksi),
                    ];
                    header("Location: " . $this->base_url . "persetujuan");
                    exit;
                }
            }
        }
    }

    // B. PROSES PENOLAKAN
    public function proses_penolakan()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

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
                // echo "<script>alert('Permintaan berhasil DITOLAK!'); window.location='" . $this->base_url . "persetujuan';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Permintaan berhasil DITOLAK!',
                ];
                header("Location: " . $this->base_url . "persetujuan");
                exit;
            } else {
                // echo "<script>alert('Error: " . mysqli_error($koneksi) . "');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Error:' + mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "persetujuan");
                exit;
            }
        }
    }

    // MASUK HALAMAN PEMINJAMAN ASET BMN
    public function input_peminjaman_barang_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'admin gudang' && $_SESSION['role'] != 'super_admin')) {
            header("Location: index.php");
            exit;
        }

        // 1. AMBIL DATA STAF (Untuk Dropdown Peminjam)
        $list_pegawai = [];
        // Ambil user yang role-nya BUKAN admin
        $q_pgw = mysqli_query($koneksi, "SELECT id, nip, nama FROM tb_user WHERE role != 'admin' AND role != 'super_admin' ORDER BY nama ASC");
        while ($p = mysqli_fetch_assoc($q_pgw)) {
            $list_pegawai[] = $p;
        }

        require_once '../views/admin/input_peminjaman_barang.php';
    }

    // A. TAMBAH PENGAJUAN BARU (Admin Input)
    public function ajukan_pinjaman()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();
        $id_admin_login = $_SESSION['user_id'];

        if (isset($_POST['ajukan_pinjam'])) {
            $nama_barang    = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
            $merek          = mysqli_real_escape_string($koneksi, $_POST['merek']);
            $kode_barang    = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
            $nup            = mysqli_real_escape_string($koneksi, $_POST['nup']);
            $tahun_perolehan = mysqli_real_escape_string($koneksi, $_POST['tahun_perolehan']);

            $id_penerima    = $_POST['id_penerima']; // ID Staf
            $tgl_serah      = $_POST['tgl_serah_terima'];

            // LOGIKA TANGGAL KEMBALI
            if (isset($_POST['jangka_panjang'])) {
                $tgl_kembali_sql = "NULL";
            } else {
                $tgl_kembali_input = $_POST['tgl_kembali'];
                $tgl_kembali_sql = "'$tgl_kembali_input'";
            }

            if (empty($id_penerima) || empty($nama_barang)) {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Harap lengkapi nama barang dan penerima!',
                ];
                header("Location: " . $this->base_url . "input_peminjaman_barang");
                exit;
            } else {
                $query = "INSERT INTO tb_peminjaman 
                  (admin_id, user_id, nama_barang, merek, kode_barang, nup, tahun_perolehan, tgl_serah_terima, tgl_kembali, status)
                  VALUES 
                  ('$id_admin_login', '$id_penerima', '$nama_barang', '$merek', '$kode_barang', '$nup', '$tahun_perolehan', '$tgl_serah', $tgl_kembali_sql, 'menunggu_persetujuan')";

                if (mysqli_query($koneksi, $query)) {
                    $_SESSION['alert'] = [
                        'icon' => 'success',
                        'title' => 'Berhasil!',
                        'text' => 'Pengajuan berhasil dibuat! Menunggu persetujuan staf.',
                    ];
                    header("Location: " . $this->base_url . "input_peminjaman_barang");
                    exit;
                } else {
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Gagal membuat pengajuan: ' . mysqli_error($koneksi),
                    ];
                    header("Location: " . $this->base_url . "input_peminjaman_barang");
                    exit;
                }
            }
        }
    }

    // B. EDIT PENGAJUAN (UPDATE DATA) - BARU DITAMBAHKAN
    public function update_pinjaman()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['update_pinjam'])) {
            $id_edit        = $_POST['id_edit'];
            $nama_barang    = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
            $merek          = mysqli_real_escape_string($koneksi, $_POST['merek']);
            $kode_barang    = mysqli_real_escape_string($koneksi, $_POST['kode_barang']);
            $nup            = mysqli_real_escape_string($koneksi, $_POST['nup']);
            $tahun_perolehan = mysqli_real_escape_string($koneksi, $_POST['tahun_perolehan']);

            $id_penerima    = $_POST['id_penerima'];
            $tgl_serah      = $_POST['tgl_serah_terima'];

            // LOGIKA TANGGAL KEMBALI (SAMA SEPERTI INPUT)
            if (isset($_POST['jangka_panjang'])) {
                $tgl_kembali_sql = "NULL";
            } else {
                $tgl_kembali_input = $_POST['tgl_kembali'];
                $tgl_kembali_sql = "'$tgl_kembali_input'";
            }

            $query_update = "UPDATE tb_peminjaman SET 
                     user_id='$id_penerima', 
                     nama_barang='$nama_barang', 
                     merek='$merek', 
                     kode_barang='$kode_barang', 
                     nup='$nup', 
                     tahun_perolehan='$tahun_perolehan', 
                     tgl_serah_terima='$tgl_serah', 
                     tgl_kembali=$tgl_kembali_sql 
                     WHERE id='$id_edit'";

            if (mysqli_query($koneksi, $query_update)) {
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data Pengajuan Berhasil Diupdate!',
                ];
                header("Location: " . $this->base_url . "input_peminjaman_barang");
                exit;
            } else {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal Update: ' . mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "input_peminjaman_barang");
                exit;
            }
        }
    }

    // C. HAPUS DATA
    public function hapus_pinjaman()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_GET['hapus'])) {
            $id = $_GET['hapus'];
            mysqli_query($koneksi, "DELETE FROM tb_peminjaman WHERE id='$id'");
            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Data Peminjaman Dihapus!',
            ];
            header("Location: " . $this->base_url . "input_peminjaman_barang");
            exit;
        }
    }

    // D. CETAK BERITA ACARA (PDF)
    public function cetak_berita_acara()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header("Location: index.php");
            exit;
        }

        $id_pinjam = $_GET['id'];

        // ============================================
        // 1. AMBIL DATA PEMINJAMAN
        // ============================================
        $query = "SELECT p.*, 
          u_admin.nama AS nama_admin, u_admin.nip AS nip_admin,
          u_user.nama AS nama_user, u_user.nip AS nip_user,
          u_user.paraf AS paraf_user
          FROM tb_peminjaman p
          LEFT JOIN tb_user u_admin ON p.admin_id = u_admin.id
          LEFT JOIN tb_user u_user ON p.user_id = u_user.id
          WHERE p.id = '$id_pinjam'";

        $result = mysqli_query($koneksi, $query);
        $data = mysqli_fetch_assoc($result);

        if (!$data) {
            die("Data tidak ditemukan.");
        }

        // ============================================
        // 2. DATA PIMPINAN
        // ============================================
        $q_pimpinan = mysqli_query($koneksi, "SELECT nama, nip FROM tb_user WHERE role='pimpinan' LIMIT 1");
        $pimpinan = mysqli_fetch_assoc($q_pimpinan);

        $nama_kasubbag = $pimpinan ? $pimpinan['nama'] : "........................................";
        $nip_kasubbag  = $pimpinan ? $pimpinan['nip'] : ".......................";

        // ============================================
        // 3. FUNGSI TANGGAL
        // ============================================
        function hariIndo($tanggal)
        {
            $hari = date('D', strtotime($tanggal));
            $list = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
            return $list[$hari];
        }
        function bulanIndo($tanggal)
        {
            $bulan = date('m', strtotime($tanggal));
            $list = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
            return $list[$bulan];
        }

        $tgl_transaksi = $data['tgl_serah_terima'];
        $hari_ini  = hariIndo($tgl_transaksi);
        $tgl_angka = date('d', strtotime($tgl_transaksi));
        $bln_nama  = bulanIndo($tgl_transaksi);
        $thn_angka = date('Y', strtotime($tgl_transaksi));

        require_once '../views/admin/cetak_berita_acara.php';
    }

    // E. UPLOAD ARSIP (FINALISASI)
    public function upload_arsip_pinjaman()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['upload_arsip'])) {
            $id_pinjam = $_POST['id'];
            $target_dir = __DIR__ . '/../assets/arsip/';

            // Upload Berita Acara (PDF)
            $ba_name = null;
            if (!empty($_FILES['file_ba']['name'])) {
                $ba_tmp = $_FILES['file_ba']['tmp_name'];
                $ba_name = time() . "_BA_" . $_FILES['file_ba']['name'];

                $target_path = $target_dir . $ba_name;
                move_uploaded_file($ba_tmp, $target_path);
            }

            // Upload Foto Bukti (JPG)
            $foto_name = null;
            if (!empty($_FILES['foto_bukti']['name'])) {
                $foto_tmp = $_FILES['foto_bukti']['tmp_name'];
                $foto_name = time() . "_FOTO_" . $_FILES['foto_bukti']['name'];

                $target_path = $target_dir . $foto_name;
                move_uploaded_file($foto_tmp, $target_path);
            }

            // Update DB -> Status Selesai
            $q_update = "UPDATE tb_peminjaman SET file_ba_signed='$ba_name', foto_bukti='$foto_name', status='selesai' WHERE id='$id_pinjam'";

            if (mysqli_query($koneksi, $q_update)) {
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Arsip Berhasil Diupload! Transaksi Selesai.',
                ];
                header("Location: " . $this->base_url . "input_peminjaman_barang");
                exit;
            }
        }
    }

    // PENGEMBALIAN BARANG
    public function pengembalian_barang_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // CEK AKSES: HANYA ADMIN
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'admin gudang' && $_SESSION['role'] != 'super_admin')) {
            header("Location: index.php");
            exit;
        }

        require_once '../views/admin/pengembalian_barang.php';
    }

    // A. SIMPAN DATA KONDISI & TANGGAL KEMBALI (Tahap 1)
    public function simpan_kondisi()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['simpan_kondisi'])) {
            $id_pinjam = $_POST['id'];
            $tgl_kembali_aktual = $_POST['tgl_dikembalikan'];
            $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi_kembali']);

            // Update data kondisi, tapi status tetap 'selesai' sementara waktu sampai arsip diupload
            $query = "UPDATE tb_peminjaman SET tgl_dikembalikan='$tgl_kembali_aktual', kondisi_kembali='$kondisi' WHERE id='$id_pinjam'";

            if (mysqli_query($koneksi, $query)) {
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data kondisi tersimpan! Silakan cetak BA Pengembalian lalu upload arsipnya.',
                ];
                header("Location: " . $this->base_url . "pengembalian_barang");
                exit;
            } else {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal menyimpan data kondisi: ' . mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "pengembalian_barang");
                exit;
            }
        }
    }

    // B. CETAK BERITA ACARA PENGEMBALIAN
    public function cetak_ba_kembali()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header("Location: index.php");
            exit;
        }

        $id_pinjam = $_GET['id'];

        // ============================================
        // 1. AMBIL DATA PEMINJAMAN
        // ============================================
        $query = "SELECT p.*, 
          u_admin.nama AS nama_admin, u_admin.nip AS nip_admin,
          u_user.nama AS nama_user, u_user.nip AS nip_user,
          u_user.paraf AS paraf_user
          FROM tb_peminjaman p
          LEFT JOIN tb_user u_admin ON p.admin_id = u_admin.id
          LEFT JOIN tb_user u_user ON p.user_id = u_user.id
          WHERE p.id = '$id_pinjam'";

        $result = mysqli_query($koneksi, $query);
        $data = mysqli_fetch_assoc($result);

        if (!$data) {
            die("Data tidak ditemukan.");
        }

        // ============================================
        // 2. DATA PIMPINAN (KASUBBAG UMUM)
        // ============================================
        $q_pimpinan = mysqli_query($koneksi, "SELECT nama, nip FROM tb_user WHERE role='pimpinan' LIMIT 1");
        $pimpinan = mysqli_fetch_assoc($q_pimpinan);

        $nama_kasubbag = $pimpinan ? $pimpinan['nama'] : "........................................";
        $nip_kasubbag  = $pimpinan ? $pimpinan['nip'] : ".......................";

        // ============================================
        // 3. FUNGSI TANGGAL
        // ============================================
        $tgl_transaksi = !empty($data['tgl_dikembalikan']) ? $data['tgl_dikembalikan'] : date('Y-m-d');

        function hariIndo($tanggal)
        {
            $hari = date('D', strtotime($tanggal));
            $list = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
            return $list[$hari];
        }
        function bulanIndo($tanggal)
        {
            $bulan = date('m', strtotime($tanggal));
            $list = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
            return $list[$bulan];
        }

        $hari_ini  = hariIndo($tgl_transaksi);
        $tgl_angka = date('d', strtotime($tgl_transaksi));
        $bln_nama  = bulanIndo($tgl_transaksi);
        $thn_angka = date('Y', strtotime($tgl_transaksi)); // Tahun Angka (2026)

        require_once '../views/admin/cetak_ba_kembali.php';
    }

    // C. UPLOAD ARSIP PENGEMBALIAN & FOTO BUKTI (Tahap 2 - FINALISASI)
    public function upload_arsip_kembali()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['upload_arsip_kembali'])) {
            $id_pinjam = $_POST['id'];
            $target_dir = __DIR__ . '/../assets/arsip/';

            // 1. Upload Berita Acara (PDF)
            $ba_name = null;
            if (!empty($_FILES['file_ba_kembali']['name'])) {
                $ba_tmp = $_FILES['file_ba_kembali']['tmp_name'];
                $ba_name = time() . "_BA_KEMBALI_" . $_FILES['file_ba_kembali']['name'];

                if (!is_dir("assets/arsip/")) {
                    mkdir("assets/arsip/", 0777, true);
                }
                $target_path = $target_dir . $ba_name;
                move_uploaded_file($ba_tmp, $target_path);
            }

            // 2. Upload Foto Bukti Pengembalian (JPG/PNG) - BARU DITAMBAHKAN
            $foto_name = null;
            if (!empty($_FILES['foto_bukti_kembali']['name'])) {
                $foto_tmp = $_FILES['foto_bukti_kembali']['tmp_name'];
                $foto_name = time() . "_FOTO_KEMBALI_" . $_FILES['foto_bukti_kembali']['name'];

                if (!is_dir("assets/arsip/")) {
                    mkdir("assets/arsip/", 0777, true);
                }
                $target_path = $target_dir . $foto_name;
                move_uploaded_file($foto_tmp, $target_path);
            }

            // Update DB: Simpan kedua file dan ubah status jadi 'dikembalikan'
            $q_update = "UPDATE tb_peminjaman SET file_ba_kembali='$ba_name', foto_bukti_kembali='$foto_name', status='dikembalikan' WHERE id='$id_pinjam'";

            if (mysqli_query($koneksi, $q_update)) {
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Pengembalian Selesai! Arsip dan Foto Bukti berhasil disimpan.',
                ];
                header("Location: " . $this->base_url . "pengembalian_barang");
                exit;
            } else {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal menyimpan data pengembalian: ' . mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "pengembalian_barang");
                exit;
            }
        }
    }

    // MASUK HALAMAN DATA PENGGUNA
    public function data_pengguna_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. Cek Login & Akses
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Hanya Super Admin yang boleh akses halaman ini
        if ($_SESSION['role'] != 'super_admin') {
            // echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Akses Ditolak!',
            ];
            header("Location: " . $this->base_url);
            exit;
        }

        require_once '../views/admin/data_pengguna.php';
    }

    // A. PROSES TAMBAH USER
    public function tambah_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

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
                    // echo "<script>alert('Format TTD harus PNG, JPG, atau JPEG!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Format TTD harus PNG, JPG, atau JPEG!',
                    ];
                    header("Location: " . $this->base_url . "data_pengguna");
                    exit;
                }
                if ($filesize > 2000000) {
                    // echo "<script>alert('Ukuran file TTD terlalu besar (Max 2MB)!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Ukuran file TTD terlalu besar (Max 2MB)!',
                    ];
                    header("Location: " . $this->base_url . "data_pengguna");
                    exit;
                }

                $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['paraf']['tmp_name'], $this->assets_path . 'ttd/' . $paraf_name);
            }

            $q = "INSERT INTO tb_user (nama, nip, username, password, role, paraf) 
          VALUES ('$nama', '$nip', '$username', '$password', '$role', '$paraf_name')";

            if (mysqli_query($koneksi, $q)) {
                // echo "<script>alert('User Berhasil Ditambahkan!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'User Berhasil Ditambahkan!',
                ];
                header("Location: " . $this->base_url . "data_pengguna");
                exit;
            } else {
                // echo "<script>alert('Gagal menambah user: " . mysqli_error($koneksi) . "');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal menambah user: ' . mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "data_pengguna");
                exit;
            }
        }
    }

    // B. PROSES EDIT USER
    public function edit_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();
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
                move_uploaded_file($_FILES['paraf']['tmp_name'], $this->assets_path . 'ttd/' . $paraf_name);
                $q_ttd = ", paraf='$paraf_name'";
            }

            $q = "UPDATE tb_user SET 
          nama='$nama', nip='$nip', username='$username', role='$role' $q_pass $q_ttd
          WHERE id='$id'";

            if (mysqli_query($koneksi, $q)) {
                // echo "<script>alert('Data User Diupdate!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Data User Diupdate!',
                ];
                header("Location: " . $this->base_url . "data_pengguna");
                exit;
            } else {
                // echo "<script>alert('Gagal update!');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal update!',
                ];
                header("Location: " . $this->base_url . "data_pengguna");
                exit;
            }
        }
    }

    // C. PROSES HAPUS USER
    public function hapus_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_GET['hapus'])) {
            $id = $_GET['hapus'];
            $q_img = mysqli_query($koneksi, "SELECT paraf FROM tb_user WHERE id='$id'");
            $row_img = mysqli_fetch_assoc($q_img);
            if ($row_img['paraf'] != null && file_exists($this->assets_path . 'ttd/' . $row_img['paraf'])) {
                unlink($this->assets_path . 'ttd/' . $row_img['paraf']);
            }

            $q = "DELETE FROM tb_user WHERE id='$id'";
            if (mysqli_query($koneksi, $q)) {
                // echo "<script>alert('User Dihapus!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'User Dihapus!',
                ];
                header("Location: " . $this->base_url . "data_pengguna");
                exit;
            } else {
                // echo "<script>alert('Gagal hapus!');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal hapus!',
                ];
                header("Location: " . $this->base_url . "data_pengguna");
                exit;
            }
        }
    }
}
