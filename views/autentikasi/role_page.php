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
                                                    <h1 class="h4 text-gray-900 mb-2">Selamat Datang, <?= $_SESSION['temp_full_name']; ?>!</h1>
                                                    <p class="text-muted">Silakan pilih ingin masuk sebagai apa hari ini:</p>
                                                </div>

                                                <form method="POST" action="<?= BASE_URL ?>pilih_role">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <button type="submit" name="role_pilihan" value="asli" class="btn btn-primary btn-block role-btn shadow-sm">
                                                                <i class="fas fa-user-tie"></i>
                                                                Sebagai <?= $nama_role_tampil; ?>
                                                            </button>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <button type="submit" name="role_pilihan" value="staf" class="btn btn-success btn-block role-btn shadow-sm">
                                                                <i class="fas fa-users"></i>
                                                                Sebagai Staf (Peminjam)
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>

                                                <hr>
                                                <div class="text-center">
                                                    <a class="small text-danger" href="<?= BASE_URL ?>logout"><i class="fas fa-sign-out-alt"></i> Batal / Kembali ke Login</a>
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