<?php
session_start();

// Jika tidak ada session temp (langsung akses url), kembalikan ke login
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: index.php");
    exit;
}

// FORMAT NAMA ROLE ASLI UNTUK DITAMPILKAN
$role_asli = $_SESSION['temp_role'];
$nama_role_tampil = "";
if ($role_asli == 'admin') {
    $nama_role_tampil = "Admin Gudang";
} elseif ($role_asli == 'pimpinan') {
    $nama_role_tampil = "Pimpinan";
} elseif ($role_asli == 'super_admin' || $role_asli == 'super admin') {
    $nama_role_tampil = "Super Admin";
}

// LOGIKA KETIKA TOMBOL DIPILIH (Perbaikan variabel klik)
if (isset($_POST['role_pilihan'])) {
    $role_pilihan = $_POST['role_pilihan'];
    
    // Set Session Utama yang sebenarnya (Perbaikan menyesuaikan login.php Anda)
    $_SESSION['user_id']   = $_SESSION['temp_user_id'];
    $_SESSION['username']  = $_SESSION['temp_username'];
    $_SESSION['full_name'] = $_SESSION['temp_full_name'];
    $_SESSION['role_asli'] = $_SESSION['temp_role']; // Simpan role asli untuk tombol "Kembali"
    
    if ($role_pilihan == 'staf') {
        $_SESSION['role'] = 'user'; // Menyamar jadi staf
    } else {
        $_SESSION['role'] = $_SESSION['temp_role']; // Masuk dengan role asli
    }
    
    // Hapus session temporary agar bersih
    unset($_SESSION['temp_user_id']);
    unset($_SESSION['temp_username']);
    unset($_SESSION['temp_full_name']);
    unset($_SESSION['temp_role']);
    
    // Lanjut ke halaman utama
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Hak Akses - Aplikasi Pesona</title>
    <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .role-btn { height: 120px; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 10px; border-radius: 15px;}
        .role-btn i { font-size: 2.5rem; }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-xl-6 col-lg-8 col-md-9 mt-5">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-5 text-center">
                        <div class="text-center mb-4">
                            <h1 class="h4 text-gray-900 mb-2">Selamat Datang, <?= $_SESSION['temp_full_name']; ?>!</h1>
                            <p class="text-muted">Silakan pilih ingin masuk sebagai apa hari ini:</p>
                        </div>

                        <form method="POST">
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
                            <a class="small text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Batal / Kembali ke Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>