<!DOCTYPE html>
<html lang="id">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>

    <style>
        .otp-container { display: flex; justify-content: center; gap: 10px; margin: 20px 0; }
        .otp-input { width: 45px; height: 55px; font-size: 24px; text-align: center; border: 1px solid #d1d3e2; border-radius: 8px; background: #f8f9fc; }
        .otp-input:focus { outline: none; border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2); }
    </style>
</head>

<body>
    <div class="main-pannel">
        <div class="container-scroller">
            <div class="page-body-wrapper full-page-wrapper">
                <div class="content-wrapper align-items-center auth px-0">
                    <div class="row w-100 mx-0" style="background-image: url('<?= ASSETS_URL ?>img/bg_pesona.png'); background-size: cover; background-repeat: no-repeat;">
                        <div class="col-lg-6 mx-auto">
                            <div class="auth-form-light py-5 px-4 px-sm-5 d-flex flex-row align-items-center justify-content-between" style="height: 100vh; opacity: 0.95;">

                                <div class="container">
                                    <div class="card o-hidden border-0 shadow-lg my-5">
                                        <div class="card-body p-0">
                                            <div class="p-5">
                                                <div class="text-center">
                                                    <h1 class="h4 text-gray-900 mb-2">Keamanan 2 Langkah (2FA)</h1>
                                                </div>

                                                <div class="text-center">
                                                    <?php if ($is_first_time): ?>
                                                        <div class="alert alert-warning small"><b>Setup 2FA:</b> Scan QR Code ini untuk menghubungkan akun Anda.</div>
                                                        <img src="<?= $qrCodeUrl ?>" class="border p-2 rounded">
                                                    <?php else: ?>
                                                        <i class="fas fa-mobile-alt fa-4x text-primary mt-4 mb-4"></i>
                                                        <p class="text-muted small">Masukkan 6 digit kode dari aplikasi Google Authenticator.</p>
                                                    <?php endif; ?>

                                                    <form method="POST">
                                                        <div class="otp-container">
                                                            <?php for ($i = 0; $i < 6; $i++): ?>
                                                                <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <input type="hidden" name="kode_2fa" id="kode_2fa_hidden" required>
                                                        <button type="submit" name="verify" class="btn btn-primary btn-block py-2">Verifikasi Kode</button>
                                                    </form>

                                                    <hr class="mt-4">

                                                    <a href="<?= $this->base_url ?>login" class="small text-danger">Batal & Kembali ke Login</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
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