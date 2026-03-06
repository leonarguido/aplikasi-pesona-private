<?php
session_start();

// Pastikan yang mengakses ini adalah user yang sudah login dan BUKAN staf
if (isset($_SESSION['role']) && $_SESSION['role'] != 'user' && $_SESSION['role'] != 'staff') {
    
    // 1. Simpan role sakti (asli) saat ini ke memori 'role_asli'
    $_SESSION['role_asli'] = $_SESSION['role'];
    
    // 2. Ubah role utama menjadi staf (user)
    $_SESSION['role'] = 'user';
}

// Langsung lemparkan kembali ke dashboard utama agar sidebar menyesuaikan
header("Location: index.php");
exit;
?>