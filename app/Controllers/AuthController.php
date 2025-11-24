<?php

namespace app\Controllers;

use app\Models\User;

class AuthController {

    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm() {
        // Jika sudah login, redirect ke dashboard
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit();
        }
        // Tampilkan view login tanpa menggunakan layout utama
        view('auth/login', [], false);
    }

    /**
     * Memproses data dari form login.
     */
    public function login() {
        // Verifikasi token CSRF
        \core\Security::verifyCsrfToken();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByUsername($username);

        // PERBAIKAN: Tambahkan "pengaman" untuk PHP 8.2
        // Cek apakah user ada dan hash password dari DB terlihat valid sebelum memverifikasi.
        // password_verify() di PHP 8+ bisa menyebabkan fatal error jika hash tidak valid.
        $isPasswordValid = false;
        if ($user && !empty($user['password']) && strlen($user['password']) >= 60) {
            $isPasswordValid = password_verify($password, $user['password']);
        }

        if ($isPasswordValid) {
            // Simpan informasi user ke session, jangan simpan password
            $_SESSION['user'] = [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ];
            header('Location: /'); // Redirect ke dashboard
            exit();
        } else {
            // Jika gagal, kembali ke halaman login dengan pesan error
            $_SESSION['error'] = 'Username atau password salah.';
            header('Location: /login');
            exit();
        }
    }

    /**
     * Menghancurkan session dan logout pengguna.
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit();
    }
}