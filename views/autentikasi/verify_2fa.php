<!DOCTYPE html>
<html lang="id">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
</head>

<body>
    <div class="main-pannel">
        <div class="container-scroller">
            <div class="page-body-wrapper full-page-wrapper">
                <div class="content-wrapper align-items-center auth px-0">
                    <div class="row w-100 mx-0" style="background-image: url('<?= ASSETS_URL ?>img/bpmp/kantor_bpmp_bali_3.jpeg'); background-size: cover; background-repeat: no-repeat;">
                        <div class="col-lg-8 mx-auto">
                            <div class="auth-form-light py-5 px-4 px-sm-5 d-flex flex-row align-items-center justify-content-between" style="height: 100vh; opacity: 0.95;">

                                <div class="container">
                                    <div class="card o-hidden border-0 shadow-lg my-5">
                                        <div class="card-body p-0">
                                            <div class="p-5">
                                                <div class="text-center">
                                                    <h1 class="h4 text-gray-900 mb-2">Keamanan 2 Langkah (2FA)</h1>
                                                    <p class="text-muted">Silakan masukkan 6 digit kode dari aplikasi Google Authenticator</p>
                                                </div>

                                                <div class="text-center">
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

</body>

</html>