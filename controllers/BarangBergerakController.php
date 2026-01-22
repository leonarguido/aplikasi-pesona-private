<?php

class BarangBergerakController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?method=';

    // MASUK HALAMAN DAFTAR BARANG
    public function daftar_barang_page()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Inisialisasi Keranjang jika belum ada
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        require_once '../views/daftar_barang.php';
    }

    // A. PROSES TAMBAH KE KERANJANG
    public function tambah_keranjang_item()
    {
        session_start();

        if (isset($_POST['tambah_keranjang'])) {
            $id_barang   = $_POST['id_barang'];
            $nama_barang = $_POST['nama_barang'];
            $jumlah      = $_POST['jumlah'];
            $satuan      = $_POST['satuan'];
            $stok_max    = $_POST['stok_max'];

            // Cek apakah barang sudah ada di keranjang?
            $sudah_ada = false;
            foreach ($_SESSION['keranjang'] as $key => $item) {
                if ($item['id'] == $id_barang) {
                    // Kalau sudah ada, update jumlahnya
                    $_SESSION['keranjang'][$key]['jumlah'] += $jumlah;
                    // Validasi jangan sampai melebihi stok
                    if ($_SESSION['keranjang'][$key]['jumlah'] > $stok_max) {
                        $_SESSION['keranjang'][$key]['jumlah'] = $stok_max;
                    }
                    $sudah_ada = true;
                    break;
                }
            }

            // Jika belum ada, masukkan baru
            if (!$sudah_ada) {
                $_SESSION['keranjang'][] = [
                    'id' => $id_barang,
                    'nama' => $nama_barang,
                    'jumlah' => $jumlah,
                    'satuan' => $satuan,
                    'stok_max' => $stok_max
                ];
            }

            echo "<script>alert('Barang masuk keranjang!'); window.location='" . $this->base_url . "daftar_barang';</script>";
        }
    }

    // MASUK HALAMAN KERANJANG
    public function keranjang()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        require_once '../views/keranjang.php';
    }

    // A. PROSES HAPUS ITEM DARI KERANJANG
    public function hapus_keranjang_item()
    {
        session_start();

        if (isset($_GET['hapus'])) {
            $key = $_GET['hapus'];
            unset($_SESSION['keranjang'][$key]);
            // Reset urutan array agar rapi
            $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
            echo "<script>window.location='" . $this->base_url . "keranjang';</script>";
        }
    }

    // B. PROSES CHECKOUT/PENGAJUAN BARANG
    public function checkout_keranjang()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['checkout'])) {
            $user_id   = $_SESSION['user_id'];
            $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
            $tanggal   = date('Y-m-d');

            // Validasi Keranjang Kosong
            if (empty($_SESSION['keranjang'])) {
                echo "<script>alert('Keranjang kosong!'); window.location='" . $this->base_url . "daftar_barang.php';</script>";
                exit;
            }

            // A. INSERT HEADER (tb_permintaan) - HANYA SEKALI!
            $query_header = "INSERT INTO tb_permintaan (user_id, tanggal_permintaan, status, keperluan) 
                     VALUES ('$user_id', '$tanggal', 'menunggu', '$keperluan')";

            if (mysqli_query($koneksi, $query_header)) {
                // Ambil ID Permintaan yang baru saja dibuat
                $id_permintaan_baru = mysqli_insert_id($koneksi);

                // B. INSERT DETAIL (Looping Keranjang)
                $berhasil_detail = true;
                foreach ($_SESSION['keranjang'] as $item) {
                    $id_barang = $item['id'];
                    $jumlah    = $item['jumlah'];
                    $satuan    = $item['satuan'];

                    // Masukkan ke tb_detail_permintaan dengan ID Header yang SAMA
                    $q_detail = "INSERT INTO tb_detail_permintaan (permintaan_id, barang_id, jumlah, satuan) 
                         VALUES ('$id_permintaan_baru', '$id_barang', '$jumlah', '$satuan')";

                    if (!mysqli_query($koneksi, $q_detail)) {
                        $berhasil_detail = false;
                    }
                }

                if ($berhasil_detail) {
                    // Kosongkan Keranjang
                    unset($_SESSION['keranjang']);
                    echo "<script>alert('Permintaan berhasil diajukan! Satu surat untuk semua barang.'); window.location='" . $this->base_url . "permintaan_saya';</script>";
                } else {
                    echo "<script>alert('Gagal menyimpan detail barang.');</script>";
                }
            } else {
                echo "<script>alert('Gagal membuat permintaan: " . mysqli_error($koneksi) . "');</script>";
            }
        }
    }

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

        require_once '../views/data_barang.php';
    }

    // A. PROSES TAMBAH DATA BARANG
    public function tambah_data_barang()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['tambah'])) {
            $kode   = $_POST['kode_barang'];
            $nama   = $_POST['nama_barang'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['keterangan'];
            $stok   = $_POST['stok'];

            $cek = mysqli_query($koneksi, "SELECT * FROM tb_barang_bergerak WHERE kode_barang = '$kode'");
            if (mysqli_num_rows($cek) > 0) {
                echo "<script>alert('Kode Barang sudah ada!'); window.location='" . $this->base_url . "data_barang';</script>";
            } else {
                $query = "INSERT INTO tb_barang_bergerak (kode_barang, nama_barang, satuan, keterangan, stok) 
                  VALUES ('$kode', '$nama', '$satuan', '$desc', '$stok')";
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
            $nama   = $_POST['nama_barang'];
            $satuan = $_POST['satuan'];
            $desc   = $_POST['keterangan'];
            $stok   = $_POST['stok'];

            $query = "UPDATE tb_barang_bergerak SET nama_barang='$nama', satuan='$satuan', keterangan='$desc', stok='$stok' WHERE id='$id'";
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
            $query = "DELETE FROM tb_barang_bergerak WHERE id = '$id'";
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Barang berhasil dihapus!'); window.location='" . $this->base_url . "data_barang';</script>";
            }
        }
    }

    // D. PROSES INPUT DATA DARI EXCEL/CSV
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
                        // Kolom 0: Kode, 1: Nama, 2: Satuan, 3: Stok, 4: Ket
                        $kode   = mysqli_real_escape_string($koneksi, $data[0]);
                        $nama   = mysqli_real_escape_string($koneksi, $data[1]);
                        $satuan = mysqli_real_escape_string($koneksi, $data[2]);
                        $stok   = (int) $data[3];
                        $desc   = mysqli_real_escape_string($koneksi, $data[4]);

                        // Cek Duplikat Kode Barang
                        $cek = mysqli_query($koneksi, "SELECT kode_barang FROM tb_barang_bergerak WHERE kode_barang = '$kode'");

                        if (mysqli_num_rows($cek) == 0 && !empty($kode)) {
                            // Jika kode belum ada, Insert Baru
                            $query = "INSERT INTO tb_barang_bergerak (kode_barang, nama_barang, satuan, stok, keterangan) 
                              VALUES ('$kode', '$nama', '$satuan', '$stok', '$desc')";
                            mysqli_query($koneksi, $query);
                            $count++;
                        }
                    }
                    fclose($file);
                    echo "<script>alert('Berhasil mengimpor $count data barang!'); window.location='data_barang.php';</script>";
                }
            } else {
                echo "<script>alert('Pilih file terlebih dahulu!');</script>";
            }
        }
    }
}
