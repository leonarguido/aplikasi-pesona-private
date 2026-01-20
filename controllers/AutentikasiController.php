<?php

class AutentikasiController
{
    public function login()
    {
        require_once __DIR__ . '/../views/login.php';
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

                    // SET SESSION
                    $_SESSION['user_id']   = $data['id'];
                    $_SESSION['username']  = $data['username'];
                    $_SESSION['full_name'] = $data['nama']; // Kolom 'nama'

                    // MAPPING ROLE (Database Mentor -> Sistem Kita)
                    $role_db = $data['role'];

                    if ($role_db == 'super admin') {
                        $_SESSION['role'] = 'super_admin';
                    } elseif ($role_db == 'admin gudang') {
                        $_SESSION['role'] = 'admin';
                    } elseif ($role_db == 'staff') {
                        $_SESSION['role'] = 'user';
                    } else {
                        $_SESSION['role'] = $role_db; // 'pimpinan'
                    }

                    // Redirect ke dashboard
                    header('Location: /aplikasi-pesona-private/');
                    exit;
                }
            } else {
                // Jika username tidak ditemukan, set error
                $error = true;
            }
            // Jika autentikasi gagal, tampilkan halaman login dengan error
            require_once __DIR__ . '/../views/login.php';
        }
    }

    public function logout()
    {
        require_once __DIR__ . '/../views/logout.php';
    }
}
