<?php
// Konfigurasi Database untuk Aplikasi Keuangan Pesantren

// Konfigurasi MySQL (untuk XAMPP/Laragon)
define('DB_TYPE', 'sqlite'); // mysql atau sqlite
define('DB_HOST', 'localhost');
define('DB_NAME', 'pesantren_keuangan');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosong untuk XAMPP default

// Konfigurasi SQLite (alternatif jika tidak menggunakan MySQL)
define('SQLITE_PATH', __DIR__ . '/db/pesantren_keuangan.db');

// Konfigurasi Aplikasi
define('APP_NAME', 'Sistem Keuangan Pesantren');
define('APP_VERSION', '1.0.0');

// Kategori transaksi yang tersedia
$kategori_list = [
    'Perlengkapan',
    'Administrasi', 
    'Pendidikan',
    'Keamanan',
    'Kesehatan',
    'Kebersihan',
    'Tim Media',
    'Organisasi',
    'Humas'
];

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (set ke 0 untuk production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
