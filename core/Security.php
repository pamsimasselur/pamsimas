<?php

namespace core;

class Security {

    /**
     * Menghasilkan token CSRF baru jika belum ada di session.
     * @return string Token CSRF.
     */
    public static function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Menghasilkan input field HTML yang tersembunyi untuk token CSRF.
     * @return string HTML untuk input field.
     */
    public static function csrfField(): string {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Memverifikasi token CSRF yang dikirim melalui POST.
     * Jika tidak valid, hentikan eksekusi.
     */
    public static function verifyCsrfToken() {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Token tidak valid atau tidak ada
            http_response_code(403);
            die('<h1>403 Forbidden</h1><p>Aksi tidak diizinkan (validasi keamanan gagal).</p>');
        }
    }
}