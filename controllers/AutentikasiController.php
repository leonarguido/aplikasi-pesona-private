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

            // CEK TABEL: tb_user
            $query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");

            // Cek apakah username ada
            if (mysqli_num_rows($query) === 1) {
                $data = mysqli_fetch_assoc($query);

                // VERIFIKASI PASSWORD
                if (password_verify($password, $data['password'])) {

                    // JANGAN LANGSUNG MASUK!
                    // Simpan data di Session khusus 2FA Pending
                    $_SESSION['pending_2fa_id']       = $data['id'];
                    $_SESSION['pending_2fa_username'] = $data['username'];
                    $_SESSION['pending_2fa_nama']     = $data['nama'];
                    $_SESSION['pending_2fa_role']     = $data['role'];

                    // Arahkan ke halaman verifikasi Google Authenticator
                    header("Location: " . $this->base_url . "verifikasi_2fa");
                    exit;
                }
            }
            // Jika username salah atau password tidak terverifikasi
            $error = true;
        }

        require_once __DIR__ . '/../views/autentikasi/login.php';
    }

    public function verifikasi_2fa()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';
        require __DIR__ . '/../config/GoogleAuthenticator.php'; // Panggil library

        // Jika tidak ada session pending dari login.php, kembalikan ke login
        if (!isset($_SESSION['pending_2fa_id'])) {
            header("Location: " . $this->base_url . "login");
            exit;
        }

        $pending_id       = $_SESSION['pending_2fa_id'];
        $pending_username = $_SESSION['pending_2fa_username'];
        $pending_nama     = $_SESSION['pending_2fa_nama'];
        $pending_role     = $_SESSION['pending_2fa_role'];

        $ga = new PHPGangsta_GoogleAuthenticator();

        // Cek apakah user sudah punya secret key
        $query = mysqli_query($koneksi, "SELECT secret_key FROM tb_user WHERE id = '$pending_id'");
        $user_data = mysqli_fetch_assoc($query);

        $is_setup = false;

        if (empty($user_data['secret_key'])) {
            // Jika belum punya, buat kunci baru dan simpan ke DB
            $secret = $ga->createSecret();
            mysqli_query($koneksi, "UPDATE tb_user SET secret_key = '$secret' WHERE id = '$pending_id'");
            $is_setup = true;
        } else {
            // Jika sudah punya, gunakan yang ada
            $secret = $user_data['secret_key'];
        }

        // Generate URL QR Code menggunakan API alternatif yang aktif
        $nama_aplikasi = urlencode("Aplikasi Pesona");
        $otpauth_url = "otpauth://totp/{$nama_aplikasi}?secret={$secret}";

        // Menggunakan API dari goqr.me / qrserver
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth_url);

        $error = false;

        // PROSES JIKA TOMBOL SUBMIT OTP DITEKAN
        if (isset($_POST['verify'])) {
            $otp_code = $_POST['otp_code'];

            // Verifikasi kode OTP (toleransi waktu 2 * 30 detik)
            $checkResult = $ga->verifyCode($secret, $otp_code, 2);

            if ($checkResult) {
                // KODE BENAR! Jalankan logika pembagian Role dari login Anda sebelumnya

                if ($pending_role == 'staff') {
                    // LANGSUNG MASUK
                    $_SESSION['user_id']   = $pending_id;
                    $_SESSION['username']  = $pending_username;
                    $_SESSION['full_name'] = $pending_nama;
                    $_SESSION['role']      = 'user';

                    // Hapus session pending
                    unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_username'], $_SESSION['pending_2fa_nama'], $_SESSION['pending_2fa_role']);

                    header("Location: index.php");
                    exit;
                } else {
                    // TAHAN DI SESSION SEMENTARA UNTUK PILIH ROLE
                    $_SESSION['temp_user_id']   = $pending_id;
                    $_SESSION['temp_username']  = $pending_username;
                    $_SESSION['temp_full_name'] = $pending_nama;

                    if ($pending_role == 'super admin') {
                        $_SESSION['temp_role'] = 'super admin';
                    } elseif ($pending_role == 'admin bmn') {
                        $_SESSION['temp_role'] = 'admin bmn';
                    } elseif ($pending_role == 'admin bhp') {
                        $_SESSION['temp_role'] = 'admin bhp';
                    } elseif ($pending_role == 'pimpinan') {
                        $_SESSION['temp_role'] = 'pimpinan';
                    } else {
                        $_SESSION['temp_role'] = $pending_role;
                    }

                    // Hapus session pending
                    unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_username'], $_SESSION['pending_2fa_nama'], $_SESSION['pending_2fa_role']);

                    header("Location: " . $this->base_url . "role");
                    exit;
                }
            } else {
                $error = true;
            }
        }

        require __DIR__ . '/../views/autentikasi/verify_2fa.php';
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
