<?php
session_start();
require 'config/koneksi.php';
require 'config/GoogleAuthenticator.php'; // Panggil library

// Jika tidak ada session pending dari login.php, kembalikan ke login
if (!isset($_SESSION['pending_2fa_id'])) {
    header("Location: login.php");
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
                $_SESSION['temp_role'] = 'super_admin';
            } elseif ($pending_role == 'admin gudang') {
                $_SESSION['temp_role'] = 'admin';
            } else {
                $_SESSION['temp_role'] = $pending_role; 
            }

            // Hapus session pending
            unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_username'], $_SESSION['pending_2fa_nama'], $_SESSION['pending_2fa_role']);
            
            header("Location: pilih_role.php");
            exit;
        }
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi 2 Langkah - Aplikasi Pesona</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        .card-login {
            width: 100%;
            max-width: 450px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <div class="card card-login bg-white">
        <div class="card-body p-5 text-center">
            
            <h1 class="h4 text-gray-900 mb-4">Keamanan 2 Langkah (2FA)</h1>

            <?php if ($is_setup): ?>
                <div class="alert alert-warning small">
                    <b>Setup Pertama Kali:</b> Buka aplikasi Google Authenticator di HP Anda, lalu Scan QR Code di bawah ini.
                </div>
                <img src="<?= $qrCodeUrl ?>" alt="QR Code" class="mb-3 border p-2 rounded">
                <p class="small text-muted">Atau masukkan kunci ini secara manual:<br><b><?= $secret ?></b></p>
            <?php else: ?>
                <i class="fas fa-mobile-alt fa-4x text-primary mb-4"></i>
                <p class="text-muted small">Buka aplikasi Google Authenticator di HP Anda dan masukkan 6 digit kode yang muncul.</p>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger small">Kode tidak valid atau sudah kedaluwarsa.</div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <input type="text" name="otp_code" class="form-control text-center text-lg" placeholder="Masukkan 6 Digit Kode" maxlength="6" required autofocus autocomplete="off" style="font-size: 1.5rem; letter-spacing: 5px;">
                </div>
                <button type="submit" name="verify" class="btn btn-primary btn-block shadow-sm py-2">
                    Verifikasi Kode
                </button>
            </form>
            
            <hr>
            <a href="login.php" class="small text-danger">Batal & Kembali ke Login</a>

        </div>
    </div>

</body>
</html>