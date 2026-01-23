<?php
session_start();
echo "<h1>DEBUGGING ROLE USER</h1>";
echo "<hr>";

if (isset($_SESSION['user_id'])) {
    echo "<h3>Halo, " . $_SESSION['nama'] . "!</h3>";
    echo "<p>Status Login: <strong>SUDAH LOGIN</strong></p>";
    
    // Tampilkan Role Mentah dari Database/Session
    echo "<p>Role Anda saat ini (di dalam sistem) adalah:</p>";
    echo "<h1 style='color: red; font-size: 50px;'>[" . $_SESSION['role'] . "]</h1>";
    
    echo "<hr>";
    echo "<p><strong>Analisa:</strong></p>";
    echo "<ul>";
    
    $role = $_SESSION['role'];
    
    // Cek kecocokan
    if ($role == 'admin gudang') {
        echo "<li style='color: green;'>✅ Teks role SUDAH BENAR ('admin gudang'). Menu seharusnya muncul.</li>";
    } elseif ($role == 'admin') {
        echo "<li style='color: orange;'>⚠️ Teks role adalah 'admin'. Pastikan kode sidebar Anda membolehkan 'admin' atau ubah data user ini menjadi 'admin gudang'.</li>";
    } else {
        echo "<li style='color: red;'>❌ Teks role TIDAK DIKENALI. Ini penyebab menu hilang!</li>";
    }
        
    // Cek spasi tersembunyi
    if (strlen($role) != strlen(trim($role))) {
        echo "<li style='color: red;'>❌ BAHAYA: Ada SPASI tersembunyi di awal/akhir role Anda! Gunakan fungsi trim().</li>";
    }
    
    echo "</ul>";
    echo "<br><a href='index.php'>Kembali ke Dashboard</a>";
} else {
    echo "<h3>Anda Belum Login. Silakan Login dulu sebagai Admin Gudang/Staf.</h3>";
}
?>