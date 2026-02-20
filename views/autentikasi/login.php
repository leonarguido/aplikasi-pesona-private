<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require __DIR__ . '/../layout/header.php';
    ?>
</head>

<body>
    <div class="main-pannel">
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper">
                <div class="content-wrapper align-items-center auth px-0">
                    <div class="row w-100 mx-0" style="background-image: url('<?= ASSETS_URL ?>img/bpmp/kantor_bpmp_bali_3.jpeg'); background-size: cover; background-repeat: no-repeat;">
                        <div class="col-lg-8 mx-auto">
                            <div class="auth-form-light py-5 px-4 px-sm-5" style="height: 100vh; opacity: 0.95;">

                                <div class="container">
                                    <div class="card o-hidden border-0 shadow-lg my-5">
                                        <div class="card-body p-0">
                                            <div class="p-5">
                                                <div class="text-center">
                                                    <h1 class="text-gray-900">PESONA</h1>
                                                    <H5 class="text-gray-900 mb-4">Penata Usahaan Stok Opname</H5>
                                                </div>

                                                <?php if (isset($error)) : ?>
                                                    <div class="alert alert-danger text-center" role="alert">
                                                        Username atau Password salah!
                                                    </div>
                                                <?php endif; ?>

                                                <form class="user" method="POST" action="<?= BASE_URL ?>autentikasi">

                                                    <div class="form-group">
                                                        <input type="text" name="username" class="form-control form-control-user" placeholder="Masukkan Username..." required>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" style="border-top-left-radius: 10rem; border-bottom-left-radius: 10rem; height: 3.1rem; padding: 1.5rem 1rem;" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text bg-white border-left-0" style="border-top-right-radius: 10rem; border-bottom-right-radius: 10rem; cursor: pointer;" onclick="togglePassword()">
                                                                    <i class="fas fa-eye text-gray-500" id="toggleIcon"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="submit" name="login" class="btn btn-primary btn-user btn-block">
                                                        Masuk
                                                    </button>
                                                </form>

                                                <hr>
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
        function togglePassword() {
            var input = document.getElementById("passwordInput");
            var icon = document.getElementById("toggleIcon");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); // Ganti icon jadi mata dicoret
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye"); // Ganti icon jadi mata biasa
            }
        }
    </script>

    <script src="<?= ASSETS_URL ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= ASSETS_URL ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= ASSETS_URL ?>vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= ASSETS_URL ?>js/sb-admin-2.min.js"></script>
</body>

</html>