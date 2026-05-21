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
    $otp_code = $_POST['kode_2fa']; // Menangkap gabungan 6 kotak OTP
    
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
        /* CSS KHUSUS UNTUK KOTAK OTP */
        .otp-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
            margin-top: 15px;
        }
        .otp-input {
            width: 45px;
            height: 55px;
            font-size: 24px;
            text-align: center;
            border: 1px solid #d1d3e2;
            border-radius: 8px;
            background-color: #f8f9fc;
            color: #5a5c69;
            transition: all 0.2s ease-in-out;
        }
        .otp-input:focus {
            outline: none;
            border-color: #4e73df;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2);
            background-color: #fff;
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
                <div class="alert alert-danger small">Kode OTP salah atau sudah kedaluwarsa!</div>
            <?php endif; ?>

            <form method="POST">
                <div class="otp-container">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" autofocus required>
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" required>
                </div>
                
                <input type="hidden" name="kode_2fa" id="kode_2fa_hidden" required>

                <button type="submit" name="verify" class="btn btn-primary btn-block shadow-sm py-2" id="btn-verify">
                    Verifikasi Kode
                </button>
            </form>
            
            <hr class="mt-4">
            <a href="login.php" class="small text-danger">Batal & Kembali ke Login</a>

        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('kode_2fa_hidden');

        inputs.forEach((input, index) => {
            // Pindah kotak otomatis saat angka diketik
            input.addEventListener('input', (e) => {
                // Cegah input huruf/simbol
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                if (e.target.value !== '') {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
                updateHiddenInput();
            });

            // Mundur ke kotak sebelumnya saat Backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '') {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });

            // Mendukung fitur Copy-Paste 6 digit sekaligus
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                
                pastedData.split('').forEach((char, i) => {
                    if (i < inputs.length) {
                        inputs[i].value = char;
                        if (i < inputs.length - 1) {
                            inputs[i + 1].focus();
                        }
                    }
                });
                updateHiddenInput();
            });
        });

        // Gabungkan 6 kotak ke dalam 1 input hidden
        function updateHiddenInput() {
            hiddenInput.value = Array.from(inputs).map(input => input.value).join('');
        }
    });
    </script>

</body>
</html>