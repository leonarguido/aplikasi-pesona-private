<?php
session_start();
require 'config/koneksi.php';

// Jika sudah login, lempar ke index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

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
            $_SESSION['full_name'] = $data['nama']; 
            
            // MAPPING ROLE
            $role_db = $data['role'];
            if ($role_db == 'super admin') {
                $_SESSION['role'] = 'super_admin';
            } elseif ($role_db == 'admin gudang') {
                $_SESSION['role'] = 'admin';
            } elseif ($role_db == 'staff') {
                $_SESSION['role'] = 'user'; 
            } else {
                $_SESSION['role'] = $role_db;
            }

            // Login Sukses
            header("Location: index.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplikasi Pesona</title>
    
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Membuat body memenuhi layar dan konten di tengah */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #4e73df; /* Warna Primary SB Admin */
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            background-size: cover;
        }

        /* Styling Kartu Login */
        .card-login {
            width: 100%;
            max-width: 450px; /* Lebar maksimal agar tidak terlalu lebar di layar besar */
            border-radius: 15px; /* Sudut membulat modern */
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); /* Bayangan halus */
        }

        /* Styling Input Form */
        .form-control {
            height: 50px;
            border-radius: 10px; /* Sedikit membulat, bukan pill */
            padding: 10px 20px;
            font-size: 1rem;
        }

        /* Styling Input Group untuk Password */
        .input-group-text {
            border-radius: 0 10px 10px 0;
            background-color: white;
            border-left: none;
        }
        
        #passwordInput {
            border-radius: 10px 0 0 10px;
            border-right: none;
        }

        #passwordInput:focus + .input-group-append .input-group-text {
            border-color: #bac8f3; /* Warna border saat fokus */
        }

        /* Styling Tombol */
        .btn-login {
            height: 50px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            background-color: #4e73df;
            border: none;
            transition: 0.3s;
        }
        
        .btn-login:hover {
            background-color: #2e59d9;
            transform: translateY(-2px); /* Efek naik sedikit saat hover */
        }

        .login-title {
            color: #444;
            font-weight: 300;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>

    <div class="card card-login bg-white">
        <div class="card-body p-5">
            
            <div class="text-center mb-5">
                <h1 class="login-title">Login Aplikasi Pesona</h1>
                <p class="text-muted small">Silakan masukkan username dan password</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger text-center shadow-sm" role="alert" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-circle mr-1"></i> Username atau Password salah!
                </div>
            <?php endif; ?>

            <form class="user" method="POST">
                
                <div class="form-group mb-4">
                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username..." required autofocus>
                </div>
                
                <div class="form-group mb-4">
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required>
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" onclick="togglePassword()" style="cursor: pointer;">
                                <i class="fas fa-eye text-gray-400" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary btn-block btn-login shadow-sm">
                    Masuk
                </button>
                
            </form>

            <div class="text-center mt-4">
                <small class="text-gray-500">Copyright &copy; Aplikasi Pesona 2026</small>
            </div>

        </div>
    </div>

    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="assets/js/sb-admin-2.min.js"></script>

    <script>
    function togglePassword() {
        var input = document.getElementById("passwordInput");
        var icon = document.getElementById("toggleIcon");
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
    </script>

</body>
</html>