<?php

namespace app\Controllers;

class DetectionController {

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
    }

    public function index() {
        $data = ['title' => 'Deteksi Perangkat Real-time'];
        view('detection/index', $data);
    }
}