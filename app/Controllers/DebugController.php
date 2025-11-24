<?php

namespace app\Controllers;

use app\Models\User;
use Database;

/**
 * Controller ini HANYA untuk tujuan debugging.
 * Hapus atau ganti namanya setelah masalah teratasi.
 */
class DebugController {

    public function testLogin() {
        // Setup halaman HTML sederhana untuk output
        echo "<!DOCTYPE html><html lang='en'><head><title>Debug Login</title><style>body { font-family: monospace; line-height: 1.6; padding: 20px; background-color: #f0f2f5; } .step { margin-bottom: 15px; padding: 15px; border-left: 4px solid #ccc; background-color: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); } .ok { border-color: #28a745; color: #28a745; } .fail { border-color: #e74c3c; color: #e74c3c; } h1, h2 { color: #333; } pre { background-color: #e9ecef; padding: 10px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;}</style></head><body>";
        echo "<h1>Halaman Debug Proses Login</h1>";
        echo "<p>Gunakan URL seperti: <code>/debug/login?user=NAMA_USER&pass=PASSWORD_ANDA</code></p>";

        $username = $_GET['user'] ?? null;
        $password = $_GET['pass'] ?? null;

        if (!$username || !$password) {
            echo "<div class='step fail'><strong>Error:</strong> Harap berikan parameter 'user' dan 'pass' di URL.</div>";
            echo "</body></html>";
            return;
        }

        echo "<h2>Memulai Proses untuk User: '" . htmlspecialchars($username) . "'</h2>";

        // Langkah 1: Cek Koneksi Database
        echo "<div class='step'>";
        echo "<strong>Langkah 1: Cek Koneksi Database</strong><br>";
        try {
            Database::getInstance()->getConnection();
            echo "<span class='ok'>[OK] Koneksi ke database berhasil.</span>";
        } catch (\Exception $e) {
            echo "<span class='fail'>[GAGAL] Tidak dapat terhubung ke database. Error: " . $e->getMessage() . "</span>";
            echo "</div></body></html>";
            return;
        }
        echo "</div>";

        // Langkah 2: Cari Pengguna di Database
        echo "<div class='step'>";
        echo "<strong>Langkah 2: Mencari pengguna berdasarkan username</strong><br>";
        $user = User::findByUsername($username);
        if ($user) {
            echo "<span class='ok'>[OK] Pengguna ditemukan.</span><br>";
            echo "Data Pengguna:<pre>" . print_r($user, true) . "</pre>";
        } else {
            echo "<span class='fail'>[GAGAL] Pengguna dengan username '" . htmlspecialchars($username) . "' tidak ditemukan di database.</span>";
            echo "</div></body></html>";
            return;
        }
        echo "</div>";

        // Langkah 3: Validasi Format Hash Password
        echo "<div class='step'>";
        echo "<strong>Langkah 3: Validasi format hash password dari database (Penting untuk PHP 8+)</strong><br>";
        $dbPasswordHash = $user['password'] ?? null;
        if (empty($dbPasswordHash)) {
            echo "<span class='fail'>[GAGAL] Kolom password di database kosong.</span>";
        } elseif (strlen($dbPasswordHash) < 60) {
            echo "<span class='fail'>[GAGAL] Panjang hash password hanya " . strlen($dbPasswordHash) . " karakter. Seharusnya 60 atau lebih. Kemungkinan hash terpotong saat disimpan. Pastikan kolom password di tabel 'users' adalah VARCHAR(255).</span>";
        } else {
            echo "<span class='ok'>[OK] Format hash password terlihat valid (panjang " . strlen($dbPasswordHash) . " karakter).</span>";
        }
        echo "</div>";

        // Langkah 4: Verifikasi Password
        echo "<div class='step'>";
        echo "<strong>Langkah 4: Memverifikasi password yang diinput dengan hash dari database</strong><br>";
        if (password_verify($password, $dbPasswordHash)) {
            echo "<span class='ok'>[OK] Verifikasi password berhasil! Pengguna seharusnya bisa login.</span>";
        } else {
            echo "<span class='fail'>[GAGAL] Verifikasi password gagal. Password yang Anda masukkan tidak cocok dengan yang ada di database.</span>";
        }
        echo "</div>";

        echo "</body></html>";
    }
}