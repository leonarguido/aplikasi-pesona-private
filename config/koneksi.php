<?php
// config/koneksi.php

$server   = "localhost";
$username = "root";      // Sesuaikan dengan user database 
$password = "";          // Sesuaikan dengan password database 
$database = "pesona"; // Sesuaikan dengan nama database 

$koneksi = mysqli_connect($server, $username, $password, $database);

if (!$koneksi) {
    die("Gagal terhubung ke database: " . mysqli_connect_error());
}
?>