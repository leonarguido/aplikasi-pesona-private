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
                            $_SESSION['temp_role'] = 'super_admin';
                        } elseif ($role_db == 'admin gudang') {
                            $_SESSION['temp_role'] = 'admin';
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
        }
    }

    public function role_page()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // Jika tidak ada session temp (langsung akses url), kembalikan ke login
        if (!isset($_SESSION['temp_user_id'])) {
            header("Location: index.php");
            exit;
        }

        // FORMAT NAMA ROLE ASLI UNTUK DITAMPILKAN
        $role_asli = $_SESSION['temp_role'];
        $nama_role_tampil = "";
        if ($role_asli == 'admin') {
            $nama_role_tampil = "Admin Gudang";
        } elseif ($role_asli == 'pimpinan') {
            $nama_role_tampil = "Pimpinan";
        } elseif ($role_asli == 'super_admin' || $role_asli == 'super admin') {
            $nama_role_tampil = "Super Admin";
        }

        // Lempar ke halaman pilih role
        require_once __DIR__ . '/../views/autentikasi/role_page.php';
    }

    public function pilih_role()
    {
        require __DIR__ . '/../config/koneksi.php';
        session_start();

        // LOGIKA KETIKA TOMBOL DIPILIH (Perbaikan variabel klik)
        if (isset($_POST['role_pilihan'])) {
            $role_pilihan = $_POST['role_pilihan'];

            // Set Session Utama yang sebenarnya (Perbaikan menyesuaikan login.php Anda)
            $_SESSION['user_id']   = $_SESSION['temp_user_id'];
            $_SESSION['username']  = $_SESSION['temp_username'];
            $_SESSION['full_name'] = $_SESSION['temp_full_name'];
            $_SESSION['role_asli'] = $_SESSION['temp_role']; // Simpan role asli untuk tombol "Kembali"

            if ($role_pilihan == 'staf') {
                $_SESSION['role'] = 'user'; // Menyamar jadi staf
            } else {
                $_SESSION['role'] = $_SESSION['temp_role']; // Masuk dengan role asli
            }

            // Hapus session temporary agar bersih
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['temp_username']);
            unset($_SESSION['temp_full_name']);
            unset($_SESSION['temp_role']);

            // Lanjut ke halaman utama
            header("Location: index.php");
            exit;
        }
    }

    public function kembali_role()
    {
        session_start();

        // Kembalikan role ke role asli
        if (isset($_SESSION['role_asli'])) {
            $_SESSION['role'] = $_SESSION['role_asli'];
        }

        // Langsung lemparkan kembali ke dashboard utama
        header("Location: index.php");
        exit;
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();

        header("Location: login.php");
        exit;
    }
}
