<?php

namespace app\Controllers;

use app\Models\PumpLog;
use app\Models\SensorLog; // PERBAIKAN: Tambahkan model SensorLog

class LogController {

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
    }

    public function pumpHistory() {
        $logs = PumpLog::getHistory(200); // Ambil 200 log terakhir

        $data = [
            'title' => 'Riwayat Aktivitas Pompa',
            'logs' => $logs
        ];

        view('logs/pump_history', $data);
    }

    /**
     * Menampilkan halaman riwayat log sensor.
     */
    public function sensorLogs() {
        $logs = SensorLog::getRecentLogs(200); // Ambil 200 log sensor terakhir

        $data = [
            'title' => 'Riwayat Data Sensor',
            'logs' => $logs
        ];

        view('logs/sensors', $data); // PERBAIKAN: Gunakan file view yang sudah ada
    }
}