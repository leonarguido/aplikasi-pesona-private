<?php
session_start();

// 1. Cek login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    include 'views/error/403.php';
    exit();
}

// 2. Mapping folder (seperti disk Laravel)
$disks = [
    'arsip'         => 'arsip/',
    'berkas'        => 'berkas/',
    'img'           => 'img/',
    'img_berkas'    => 'img/berkas/',
    'img_bpmp'      => 'img/bpmp/',
    'img_ttd'       => 'img/ttd/',
];

$type = $_GET['type'] ?? '';
$file = $_GET['file'] ?? '';

// validasi
if (!array_key_exists($type, $disks)) {
    http_response_code(400);
    include 'views/error/400.php';
    exit();
}

$filename = basename(urldecode($file));

// path lengkap
$basePath = __DIR__ . '/assets/';
$fullPath = $basePath . $disks[$type] . $filename;

// cek file
if (!file_exists($fullPath)) {
    http_response_code(404);
    include 'views/error/404.php';
    exit();
}

// detect mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $fullPath);

// tampilkan file
header("Content-Type: $mime");
header('Content-Disposition: inline; filename="' . $filename . '"');
readfile($fullPath);
exit;
