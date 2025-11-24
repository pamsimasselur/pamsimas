<?php

namespace app\Controllers;

class AdminController extends BaseController {
    public function __construct() {
        parent::__construct(); // Panggil constructor parent untuk cek login

        if ($_SESSION['user']['role'] !== 'Administrator') {
            http_response_code(403); // Forbidden
            die("<h1>403 Forbidden</h1><p>Anda tidak memiliki hak akses ke halaman ini.</p>");
        }
    }
}