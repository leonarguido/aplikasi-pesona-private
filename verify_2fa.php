<?php
session_start();
require 'config/koneksi.php';
require 'config/GoogleAuthenticator.php'; 

// Jika tidak ada session pending, kembalikan ke login
if (!isset($_SESSION['pending_2fa_id'])) {
    header("Location: login.php");
    exit;
}

$pending_id       = $_SESSION['pending_2fa_id'];
$pending_username = $_SESSION['pending_2fa_username'];
$pending_nama     = $_SESSION['pending_2fa_nama'];
$pending_role     = $_SESSION['pending_2fa_role'];

$ga = new PHPGangsta_GoogleAuthenticator();

// Ambil data user, termasuk status verifikasi
$query = mysqli_query($koneksi, "SELECT secret_key, is_2fa_verified FROM tb_user WHERE id = '$pending_id'");
$user_data = mysqli_fetch_assoc($query);

$secret = $user_data['secret_key'];
$is_first_time = false;

// LOGIKA BARU: Jika secret kosong ATAU sudah punya tapi belum pernah diverifikasi sukses
if (empty($secret) || $user_data['is_2fa_verified'] == 0) {
    if (empty($secret)) {
        $secret = $ga->createSecret();
        mysqli_query($koneksi, "UPDATE tb_user SET secret_key = '$secret' WHERE id = '$pending_id'");
    }
    $is_first_time = true; 
}

// Generate URL QR Code
$nama_aplikasi = urlencode("Aplikasi Pesona");
$otpauth_url = "otpauth://totp/{$nama_aplikasi}?secret={$secret}";
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth_url);

$error = false;

// PROSES VERIFIKASI
if (isset($_POST['verify'])) {
    $otp_code = $_POST['kode_2fa']; 
    $checkResult = $ga->verifyCode($secret, $otp_code, 2);

    if ($checkResult) {
        // TANDAI SUDAH TERVERIFIKASI PERMANEN
        mysqli_query($koneksi, "UPDATE tb_user SET is_2fa_verified = 1 WHERE id = '$pending_id'");
        
        // Logika Role
        if ($pending_role == 'staff') {
            $_SESSION['user_id']   = $pending_id;
            $_SESSION['username']  = $pending_username;
            $_SESSION['full_name'] = $pending_nama; 
            $_SESSION['role']      = 'user'; 
            unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_username'], $_SESSION['pending_2fa_nama'], $_SESSION['pending_2fa_role']);
            header("Location: index.php");
        } else {
            $_SESSION['temp_user_id']   = $pending_id;
            $_SESSION['temp_username']  = $pending_username;
            $_SESSION['temp_full_name'] = $pending_nama;
            $_SESSION['temp_role']      = ($pending_role == 'super admin') ? 'super_admin' : (($pending_role == 'admin gudang') ? 'admin' : $pending_role);
            unset($_SESSION['pending_2fa_id'], $_SESSION['pending_2fa_username'], $_SESSION['pending_2fa_nama'], $_SESSION['pending_2fa_role']);
            header("Location: pilih_role.php");
        }
        exit;
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
    <title>Verifikasi 2 Langkah</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(180deg, #4e73df 10%, #224abe 100%); }
        .card-login { width: 100%; max-width: 450px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .otp-container { display: flex; justify-content: center; gap: 10px; margin: 20px 0; }
        .otp-input { width: 45px; height: 55px; font-size: 24px; text-align: center; border: 1px solid #d1d3e2; border-radius: 8px; background: #f8f9fc; }
        .otp-input:focus { outline: none; border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2); }
    </style>
</head>
<body>
    <div class="card card-login bg-white">
        <div class="card-body p-5 text-center">
            <h1 class="h4 text-gray-900 mb-4">Keamanan 2 Langkah (2FA)</h1>

            <?php if ($is_first_time): ?>
                <div class="alert alert-warning small"><b>Setup 2FA:</b> Scan QR Code ini untuk menghubungkan akun Anda.</div>
                <img src="<?= $qrCodeUrl ?>" class="mb-3 border p-2 rounded">
            <?php else: ?>
                <i class="fas fa-mobile-alt fa-4x text-primary mb-4"></i>
                <p class="text-muted small">Masukkan 6 digit kode dari aplikasi Google Authenticator.</p>
            <?php endif; ?>

            <form method="POST">
                <div class="otp-container">
                    <?php for($i=0; $i<6; $i++): ?>
                        <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                    <?php endfor; ?>
                </div>
                <input type="hidden" name="kode_2fa" id="kode_2fa_hidden" required>
                <button type="submit" name="verify" class="btn btn-primary btn-block py-2">Verifikasi Kode</button>
            </form>
            <hr><a href="login.php" class="small text-danger">Batal & Kembali ke Login</a>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll('.otp-input');
        const hidden = document.getElementById('kode_2fa_hidden');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                if (e.target.value && index < 5) inputs[index + 1].focus();
                hidden.value = Array.from(inputs).map(i => i.value).join('');
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
            });
        });
    });
    </script>
</body>
</html>