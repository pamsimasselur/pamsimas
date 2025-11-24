<?php

namespace app\Controllers;

use app\Models\User;

class UserController {

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        // Hanya Administrator yang boleh mengakses controller ini
        if ($_SESSION['user']['role'] !== 'Administrator') {
            http_response_code(403); // Forbidden
            echo "<h1>403 Forbidden</h1><p>Anda tidak memiliki hak akses ke halaman ini.</p>";
            exit();
        }
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index() {
        $users = User::getAll();

        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $users
        ];

        view('users/index', $data);
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create() {
        $data = [
            'title' => 'Tambah Pengguna Baru'
        ];
        view('users/create', $data);
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store() {
        // Ambil data dari form
        $fullName = $_POST['full_name'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $role = $_POST['role'] ?? '';

        // Validasi sederhana
        if (empty($fullName) || empty($username) || empty($password)) {
            $_SESSION['error'] = 'Semua field wajib diisi.';
            header('Location: /users/create');
            exit();
        }

        if ($password !== $passwordConfirmation) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
            header('Location: /users/create');
            exit();
        }

        if (User::findByUsername($username)) {
            $_SESSION['error'] = 'Username sudah digunakan.';
            header('Location: /users/create');
            exit();
        }

        // Hash password dan simpan pengguna
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        User::create($fullName, $username, $hashedPassword, $role);

        // Redirect ke halaman daftar pengguna
        header('Location: /users');
        exit();
    }
}
