<!DOCTYPE html>
<html lang="en">

<head>
    <?php require __DIR__ . '/../layout/header.php'; ?>
    <?php $judul_halaman = "Profil Saya"; ?>
</head>

<body id="page-top">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content" class="row">
            <div class="col-md-2">
                <?php require __DIR__ . '/../layout/sidebar.php'; ?>
            </div>
            <div class="col-md-10">
                <?php require __DIR__ . '/../layout/topbar.php'; ?>
                <div class="container-fluid mt-4">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4 border-left-primary">
                                <div class="card-header py-3">
                                    <h5 class="m-0 mb-3 font-weight-bold text-primary">Selamat Datang, <?= $_SESSION['full_name']; ?>!</h5>
                                    <p>Anda login sebagai <strong><?= ucfirst($role); ?></strong>. Halaman ini merupakan halaman profil Anda yang berisi data pribadi dan tanda tangan digital Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Anda</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <input type="text" class="form-control" value="<?= $data['nama']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control" value="<?= $data['username']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>NIP</label>
                                        <input type="text" class="form-control" value="<?= $data['nip']; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Role</label>
                                        <input type="text" class="form-control" value="<?= ucfirst($data['role']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Password Anda</label>
                                        <input type="password" class="form-control" value="rahasia" readonly>
                                        <small class="form-text text-muted">Password tidak akan ditampilkan untuk keamanan.</small>
                                    </div>
                                    <a href="#" data-toggle="modal" data-target="#gantiPasswordModal">Ganti password? Silahkan klik disini</a>
                                </div>
                            </div>
                        </div>

                        <!-- modal ganti password -->
                        <div class="modal fade" id="gantiPasswordModal" tabindex="-1" role="dialog" aria-labelledby="gantiPasswordModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="gantiPasswordModalLabel">Ganti Password</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form method="POST" action="<?= BASE_URL ?>ganti_password">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Password Lama</label>
                                                <input type="password" name="password_lama" class="form-control" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Password Baru</label>
                                                <input type="password" id="password_baru" name="password_baru" class="form-control" required minlength="6">
                                                <small id="password_error" class="text-danger"></small>
                                            </div>
                                            <div class="form-group">
                                                <label>Konfirmasi Password Baru</label>
                                                <input type="password" id="konfirmasi_password_baru" name="konfirmasi_password_baru" class="form-control" required>
                                                <small id="konfirmasi_error" class="text-danger"></small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" name="ganti_password" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Tanda Tangan</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4><?= $data['nama']; ?></h4>
                                    <p class="text-muted">Role: <?= ucfirst($data['role']); ?></p>

                                    <hr>

                                    <h5>Tanda Tangan Digital</h5>
                                    <?php if (empty($data['paraf'])): ?>
                                        <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> TTD belum tersedia. Silahkan upload tanda tangan Anda.</p>
                                        <div class="mb-3">
                                            <img src="<?= ASSETS_URL ?>img/no_photo.png" class="img-thumbnail" width="200">
                                        </div>

                                        <form method="POST" action="<?= BASE_URL ?>upload_paraf" enctype="multipart/form-data">
                                            <div class="form-group text-left">
                                                <label class="font-weight-bold">Upload File Tanda Tangan (Paraf)</label>
                                                <input type="file" id="paraf_file" name="paraf" accept="image/*" class="form-control-file" required>
                                                <small class="text-muted">Format: JPG/PNG, Transparan lebih baik.</small>
                                                <small id="paraf_null" class="text-danger"></small>
                                            </div>
                                            <button type="button" onclick="validateParaf()" class="btn btn-primary btn-block">Simpan Tanda Tangan</button>

                                            <div class="modal fade" id="modalKonfirmasi" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">Konfirmasi Upload</h5>
                                                            <button class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Tanda tangan hanya bisa di upload sekali. Anda yakin ingin menyimpan tanda tangan ini?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" name="upload_paraf" class="btn btn-success"><i class="fas fa-check"></i> Simpan Tanda Tangan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="mb-3">
                                            <img src="<?= ASSETS_URL ?>img/ttd/<?= $data['paraf']; ?>" class="img-thumbnail" width="200">
                                            <p class="small text-success mt-2"><i class="fas fa-check-circle"></i> TTD Tersedia</p>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require '../views/layout/footer.php'; ?>

    <script>
        const password = document.getElementById("password_baru");
        const konfirmasi = document.getElementById("konfirmasi_password_baru");

        const passwordError = document.getElementById("password_error");
        const konfirmasiError = document.getElementById("konfirmasi_error");

        const parafInput = document.getElementById("paraf_file");
        const parafNullError = document.getElementById("paraf_null");

        function validatePassword() {
            if (password.value.length < 6) {
                passwordError.textContent = "Password minimal 6 karakter.";
                return false;
            } else {
                passwordError.textContent = "";
                return true;
            }
        }

        function validateKonfirmasi() {
            if (konfirmasi.value !== password.value) {
                konfirmasiError.textContent = "Konfirmasi password tidak sama.";
                return false;
            } else {
                konfirmasiError.textContent = "";
                return true;
            }
        }

        password.addEventListener("input", () => {
            validatePassword();
            validateKonfirmasi();
        });

        konfirmasi.addEventListener("input", validateKonfirmasi);

        function validateParaf() {
            if (!parafInput.value) {
                parafNullError.textContent = "Silahkan pilih file tanda tangan.";
                $('#modalKonfirmasi').modal('hide');
                return false;
            } else {
                parafNullError.textContent = "";
                $('#modalKonfirmasi').modal('show');
                return true;
            }
        }

        parafInput.addEventListener("change", () => {
            if (parafInput.value) {
                parafNullError.textContent = "";
            }
        });        
    </script>
</body>

</html>