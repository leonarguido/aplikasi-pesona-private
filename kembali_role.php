<?php
session_start();

// Kembalikan role ke role asli
if (isset($_SESSION['role_asli'])) {
    $_SESSION['role'] = $_SESSION['role_asli'];
}

// Langsung lemparkan kembali ke dashboard utama
header("Location: index.php");
exit;
?>