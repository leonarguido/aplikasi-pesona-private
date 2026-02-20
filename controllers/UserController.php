<?php

class UserController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?page=';

    // MASUK HALAMAN DAFTAR BARANG
    public function daftar_barang_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Inisialisasi Keranjang jika belum ada
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        require_once '../views/user/daftar_barang.php';
    }

    // A. PROSES TAMBAH KE KERANJANG
    public function tambah_keranjang_item()
    {
        session_start();

        if (isset($_POST['tambah_keranjang'])) {
            $id_barang   = $_POST['id_barang'];
            $nama_barang = $_POST['nama_barang'];
            $merk_barang = $_POST['merk_barang'];
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
                    'merk' => $merk_barang,
                    'jumlah' => $jumlah,
                    'satuan' => $satuan,
                    'stok_max' => $stok_max
                ];
            }

            // echo "<script>alert('Barang masuk keranjang!'); window.location='" . $this->base_url . "daftar_barang';</script>";
            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Barang masuk keranjang!',
            ];
            header("Location: " . $this->base_url . "daftar_barang");
            exit;
        }
    }

    // MASUK HALAMAN KERANJANG
    public function keranjang()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        require_once '../views/user/keranjang.php';
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

            header("Location: " . $this->base_url . "keranjang");
            exit;
        }
    }

    // B. PROSES CHECKOUT/PENGAJUAN BARANG
    public function checkout_keranjang()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['checkout'])) {
            $user_id   = $_SESSION['user_id'];
            $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
            $tanggal   = date('Y-m-d');

            // Validasi Keranjang Kosong
            if (empty($_SESSION['keranjang'])) {
                // echo "<script>alert('Keranjang kosong!'); window.location='" . $this->base_url . "daftar_barang';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Keranjang kosong!',
                ];
                header("Location: " . $this->base_url . "daftar_barang");
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
                    // echo "<script>alert('Permintaan berhasil diajukan! Satu surat untuk semua barang.'); window.location='" . $this->base_url . "permintaan_saya';</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'success',
                        'title' => 'Berhasil!',
                        'text' => 'Permintaan berhasil diajukan! Satu surat untuk semua barang',
                    ];
                    header("Location: " . $this->base_url . "permintaan_saya");
                    exit;
                } else {
                    // echo "<script>alert('Gagal menyimpan detail barang.');</script>";
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Gagal menyimpan detail barang',
                    ];
                    header("Location: " . $this->base_url . "permintaan_saya");
                    exit;
                }
            } else {
                // echo "<script>alert('Gagal membuat permintaan: " . mysqli_error($koneksi) . "');</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal membuat permintaan' + mysqli_error($koneksi),
                ];
                header("Location: " . $this->base_url . "permintaan_saya");
                exit;
            }
        }
    }

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

        require_once '../views/user/permintaan_saya.php';
    }

    // A. PROSES BATALKAN PERMINTAAN SAYA
    public function batalkan_permintaan_saya()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_GET['batal_id'])) {
            $id_batal = $_GET['batal_id'];
            $id_user = $_SESSION['user_id'];
            // Cek keamanan: Pastikan permintaan milik user ini dan status 'menunggu'
            $cek = mysqli_query($koneksi, "SELECT status FROM tb_permintaan WHERE id='$id_batal' AND user_id='$id_user'");
            $d = mysqli_fetch_assoc($cek);

            if ($d && $d['status'] == 'menunggu') {
                mysqli_query($koneksi, "DELETE FROM tb_detail_permintaan WHERE permintaan_id='$id_batal'");
                mysqli_query($koneksi, "DELETE FROM tb_permintaan WHERE id='$id_batal'");
                // echo "<script>alert('Permintaan berhasil dibatalkan.'); window.location='" . $this->base_url . "permintaan_saya';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Permintaan berhasil dibatalkan!',
                ];
                header("Location: " . $this->base_url . "permintaan_saya");
                exit;
            } else {
                // echo "<script>alert('Gagal! Permintaan tidak bisa dibatalkan.'); window.location='" . $this->base_url . "permintaan_saya';</script>";
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal! Permintaan tidak bisa dibatalkan',
                ];
                header("Location: " . $this->base_url . "permintaan_saya");
                exit;
            }
        }
    }

    // B. PROSES EDIT PERMINTAAN SAYA
    public function edit_permintaan_saya()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['update_permintaan'])) {
            $id_details = $_POST['id_detail']; // Array ID Detail
            $jumlahs    = $_POST['jumlah'];    // Array Jumlah Baru

            for ($i = 0; $i < count($id_details); $i++) {
                $curr_id  = $id_details[$i];
                $curr_jml = $jumlahs[$i];
                mysqli_query($koneksi, "UPDATE tb_detail_permintaan SET jumlah='$curr_jml' WHERE id='$curr_id'");
            }

            // echo "<script>alert('Perubahan jumlah berhasil disimpan!'); window.location='" . $this->base_url . "permintaan_saya';</script>";
            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Perubahan jumlah berhasil disimpan!',
            ];
            header("Location: " . $this->base_url . "permintaan_saya");
            exit;
        }
    }

    // C. PROSES CETAK SURAT PERMINTAAN
    public function cetak_surat()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header("Location: index.php");
            exit;
        }

        $id_permintaan = $_GET['id'];

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
            // echo "<script>alert('Surat belum bisa dicetak karena status belum disetujui!'); window.close();</script>";
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Gagal!',
                'text' => 'Surat belum bisa dicetak karena status belum disetujui!',
            ];
            header("Location: " . $this->base_url . "cetak_surat");
            exit;
        } else {
            $tanggal_sql = $data['tanggal_disetujui']; // Format: YYYY-MM-DD

            // 1. Daftar Nama Bulan Indonesia
            $bulan_indo = [
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            ];

            // 2. Pecah tanggal
            $pecah_tgl = explode('-', $tanggal_sql);
            $tgl = $pecah_tgl[2];
            $bln = (int) $pecah_tgl[1]; // Ubah '02' jadi 2 agar cocok dengan array
            $thn = $pecah_tgl[0];

            // 3. Gabungkan (Contoh: 03 Februari 2026)
            $tanggal_indonesia = $tgl . ' ' . $bulan_indo[$bln] . ' ' . $thn;
        }

        require_once '../views/user/cetak_surat.php';
    }

    // MASUK HALAMAN PROFIL PENGGUNA
    public function profil_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // Ambil Role & ID User
        $role    = $_SESSION['role'];
        $id_user = $_SESSION['user_id'];
        $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id = '$id_user'"));

        require_once '../views/user/profil.php';
    }

    public function ganti_password()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['ganti_password'])) {
            $id_user = $_SESSION['user_id'];
            $pass_lama = $_POST['password_lama'];
            $pass_baru = $_POST['password_baru'];
            $pass_konf = $_POST['konfirmasi_password_baru'];


            // // konfirmasi password agar 8 karakter, ada huruf besar, kecil, angka, dan simbol
            // $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            // if (!preg_match($pattern, $pass_baru)) {
            //     $_SESSION['alert'] = [
            //         'icon' => 'error',
            //         'title' => 'Password baru harus minimal 8 karakter, mengandung huruf besar, kecil, angka, dan simbol!',
            //     ];
            //     header("Location: " . $this->base_url . "profil_saya");
            //     exit;
            // }

            // Ambil password saat ini dari database
            $query = mysqli_query($koneksi, "SELECT password FROM tb_user WHERE id='$id_user'");
            $data = mysqli_fetch_assoc($query);

            // Validasi password lama
            if (!password_verify($pass_lama, $data['password'])) {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Password lama salah!',
                ];
                header("Location: " . $this->base_url . "profil_saya");
                exit;
            }

            // Validasi konfirmasi password baru harus minimal 6 karakter saja (biar gak ribet, karena ini aplikasi internal)
            if (strlen($pass_baru) < 6) {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Password baru harus minimal 6 karakter!',
                ];
                header("Location: " . $this->base_url . "profil_saya");
                exit;
            }

            // Validasi konfirmasi password baru
            if ($pass_baru !== $pass_konf) {
                $_SESSION['alert'] = [
                    'icon' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Konfirmasi password baru tidak cocok!',
                ];
                header("Location: " . $this->base_url . "profil_saya");
                exit;
            }

            // Update password baru (hash terlebih dahulu)
            $pass_baru_hashed = password_hash($pass_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE tb_user SET password='$pass_baru_hashed' WHERE id='$id_user'");

            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Password berhasil diubah!',
            ];
            header("Location: " . $this->base_url . "profil_saya");
            exit;
        }
    }

    public function upload_paraf()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();
        $id_user = $_SESSION['user_id'];

        // Upload Tanda Tangan
        if (isset($_POST['upload_paraf'])) {
            $paraf_name = null;
            if (!empty($_FILES['paraf']['name'])) {
                $filename   = $_FILES['paraf']['name'];
                $filesize   = $_FILES['paraf']['size'];
                $ext        = pathinfo($filename, PATHINFO_EXTENSION);
                $allowed    = ['png', 'jpg', 'jpeg'];

                if (!in_array(strtolower($ext), $allowed)) {
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Format TTD harus PNG, JPG, atau JPEG!',
                    ];
                    header("Location: " . $this->base_url . "profil_saya");
                    exit;
                }
                if ($filesize > 2000000) {
                    $_SESSION['alert'] = [
                        'icon' => 'error',
                        'title' => 'Gagal!',
                        'text' => 'Ukuran file TTD terlalu besar (Max 2MB)!',
                    ];
                    header("Location: " . $this->base_url . "profil_saya");
                    exit;
                }

                $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                $target_dir = __DIR__ . '/../assets/img/ttd/';
                $target_path = $target_dir . $paraf_name;
                move_uploaded_file($_FILES['paraf']['tmp_name'], $target_path);
            }

            // Update database dengan nama file paraf
            mysqli_query($koneksi, "UPDATE tb_user SET paraf='$paraf_name' WHERE id='$id_user'");

            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Tanda tangan berhasil diupload!',
            ];
            header("Location: " . $this->base_url . "profil_saya");
            exit;
        }
    }
}
