<?php

class AutentikasiController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?page=';
    protected $assets_path = __DIR__ . '/../assets/img/'; // direktori penyimpanan file paraf

    public function login()
    {
        require_once __DIR__ . '/../views/autentikasi/login.php';
    }

    public function autentikasi()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        if (isset($_POST['login'])) {
            $username = mysqli_real_escape_string($koneksi, $_POST['username']);
            $password = $_POST['password'];

            // CEK TABEL BARU: tb_user
            $query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");

            // Cek apakah username ada
            if (mysqli_num_rows($query) === 1) {
                $data = mysqli_fetch_assoc($query);

                // VERIFIKASI PASSWORD
                if (password_verify($password, $data['password'])) {

                    $role_db = $data['role'];

                    // JIKA ROLE ADALAH STAF (USER BIASA) -> Langsung Set Session Utama & Masuk
                    if ($role_db == 'staff') {
                        $_SESSION['user_id']   = $data['id'];
                        $_SESSION['username']  = $data['username'];
                        $_SESSION['full_name'] = $data['nama'];
                        $_SESSION['role']      = 'user';

                        header("Location: index.php");
                        exit;
                    }
                    // JIKA ROLE ADALAH ADMIN/PIMPINAN/SUPERADMIN -> Tahan di Session Sementara
                    else {
                        $_SESSION['temp_user_id']   = $data['id'];
                        $_SESSION['temp_username']  = $data['username'];
                        $_SESSION['temp_full_name'] = $data['nama'];

                        // MAPPING ROLE ASLI
                        if ($role_db == 'super admin') {
                            $_SESSION['temp_role'] = 'super admin';
                        } elseif ($role_db == 'admin bmn') {
                            $_SESSION['temp_role'] = 'admin bmn';
                        } elseif ($role_db == 'admin bhp') {
                            $_SESSION['temp_role'] = 'admin bhp';
                        } else {
                            $_SESSION['temp_role'] = $role_db; // Untuk pimpinan
                        }

                        // Lempar ke halaman pilih role
                        header("Location: " . $this->base_url . "role");
                        exit;
                    }
                }
            }
            // Jika username salah atau password tidak terverifikasi
            $error = true;
            require_once __DIR__ . '/../views/autentikasi/login.php';
        }
    }

    public function role_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // 1. CEK JIKA SUDAH PUNYA SESI UTAMA (Efek tombol Back)
        if (isset($_SESSION['user_id'])) {
            // Jika dia sudah login penuh dan menekan back ke halaman ini,
            // dia harus logout dulu atau gunakan tombol "Kembali ke Akun Asli" di sidebar.
            // Kita lempar kembali ke dashboard yang sedang aktif.
            header("Location: index.php");
            exit;
        }

        // 2. CEK JIKA TIDAK ADA SESI SEMENTARA
        if (!isset($_SESSION['temp_user_id'])) {
            header("Location: index.php");
            exit;
        }

        // FORMAT NAMA ROLE ASLI UNTUK DITAMPILKAN
        $role_asli = $_SESSION['temp_role'];
        $nama_role_tampil = "";
        if ($role_asli == 'admin bmn') {
            $nama_role_tampil = "Admin BMN";
        } elseif ($role_asli == 'admin bhp') {
            $nama_role_tampil = "Admin BHP";
        } elseif ($role_asli == 'pimpinan') {
            $nama_role_tampil = "Pimpinan";
        } elseif ($role_asli == 'super admin') {
            $nama_role_tampil = "Super Admin";
        }

        require_once __DIR__ . '/../views/autentikasi/role_page.php';
    }

    public function pilih_role()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // LOGIKA KETIKA TOMBOL DIPILIH (Perbaikan variabel klik)
        if (isset($_POST['role_pilihan'])) {
            $role_pilihan = $_POST['role_pilihan'];

            // Set Session Utama yang sebenarnya
            $_SESSION['user_id']   = $_SESSION['temp_user_id'];
            $_SESSION['username']  = $_SESSION['temp_username'];
            $_SESSION['full_name'] = $_SESSION['temp_full_name'];
            $_SESSION['role_asli'] = $_SESSION['temp_role'];

            if ($role_pilihan == 'staf') {
                $_SESSION['role'] = 'user'; // Menyamar jadi staf
            } else {
                $_SESSION['role'] = $_SESSION['temp_role']; // Masuk dengan role asli
            }

            // Gunakan teknik pengalihan (PRG pattern) untuk mencegah form resubmission
            header("Location: index.php", true, 303);
            exit;
        }
    }

    public function kembali_role_asli()
    {
        session_start();

        // Kembalikan role ke role asli
        if (isset($_SESSION['role_asli'])) {
            $_SESSION['role'] = $_SESSION['role_asli'];
        }

        header("Location: index.php");
        exit;
    }

    public function kembali_role_staff()
    {
        session_start();

        // Pastikan yang mengakses ini adalah user yang sudah login dan BUKAN staf
        if (isset($_SESSION['role']) && $_SESSION['role'] != 'user' && $_SESSION['role'] != 'staff') {

            // 1. Simpan role sakti (asli) saat ini ke memori 'role_asli'
            $_SESSION['role_asli'] = $_SESSION['role'];

            // 2. Ubah role utama menjadi staf (user)
            $_SESSION['role'] = 'user';
        }

        header("Location: index.php");
        exit;
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: " . $this->base_url . "login");
        exit;
    }
}
