<?php

namespace app\Controllers;

class BaseController {
    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
    }
}