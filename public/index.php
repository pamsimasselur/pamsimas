<?php
// Front-Controller: Titik masuk tunggal untuk semua request.

// --- PENGATURAN LINGKUNGAN PRODUKSI ---
// Matikan tampilan error ke pengguna untuk keamanan.
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// Anda bisa mengatur error_log = /path/to/your/php-error.log di konfigurasi server

// Mulai atau lanjutkan session di setiap request
session_start();

// PERBAIKAN: Atur zona waktu secara global di titik masuk aplikasi.
date_default_timezone_set('Asia/Jakarta');

define('ROOT_PATH', dirname(__DIR__));

// Autoloader sederhana untuk memuat kelas secara otomatis
spl_autoload_register(function ($className) {
    // Mengonversi namespace (jika ada) menjadi path direktori
    $file = ROOT_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Memuat kelas inti secara manual
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/helpers.php'; // Memuat fungsi helper

// Inisialisasi router
$router = new Router();

// Memuat definisi rute dari file terpisah
require_once ROOT_PATH . '/routes/web.php';
require_once ROOT_PATH . '/routes/api.php';

// // ================= DEBUGGING =================
// echo "Rute GET yang terdaftar: ";
// var_dump($router->getRoutes()['GET']);
// // ============= AKHIR DEBUGGING =============

// Mendapatkan URI mentah dari request dan metode HTTP
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// --- Logika Pembersihan URI yang Disederhanakan untuk Produksi ---
// Karena .htaccess mengarahkan semuanya ke index.php di dalam folder public,
// $requestUri sudah merupakan path yang bersih relatif terhadap domain.
// Contoh: https://apps.selur.desa.id/controllers/1 -> $requestUri akan menjadi '/controllers/1'
$cleanUri = $requestUri ?: '/';

define('CURRENT_ROUTE_URI', $cleanUri); // Membuat URI bersih tersedia secara global

// Mencocokkan rute dan menjalankannya menggunakan URI yang sudah bersih
$router->dispatch($cleanUri, $method);
