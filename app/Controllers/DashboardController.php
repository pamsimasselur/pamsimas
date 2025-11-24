<?php

namespace app\Controllers;

use app\Models\Controller;
use app\Models\IndicatorSetting;
use app\Models\Tank;
use app\Models\User;

class DashboardController extends \app\Controllers\BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // PERBAIKAN: Pastikan variabel selalu berupa array, bahkan jika tidak ada data.
        // Gunakan operator null coalescing (??) untuk memberikan array kosong sebagai default.
        $controllers = Controller::getAll() ?: [];
        $tanks = Tank::getAll() ?: [];
        $users = User::getAll() ?: [];

        // Hitung statistik
        $totalControllers = count($controllers); // Sekarang aman karena dijamin array
        $onlineControllers = 0;
        // Loop ini sekarang aman karena $controllers dijamin adalah array.
        foreach ($controllers as $controller) {
            // Anggap online jika update dalam 5 menit terakhir (300 detik)
            if (strtotime($controller['last_update']) > (time() - 300)) {
                $onlineControllers++;
            }
        }

        // Ambil pengaturan indikator untuk menentukan template mana yang aktif
        // PERBAIKAN: Berikan nilai default yang aman jika pengaturan tidak ditemukan di database.
        // Ini mencegah fatal error di PHP 8+ jika mencoba mengakses array dari nilai 'false'.
        $indicatorSettings = IndicatorSetting::getSettings() ?: [
            'threshold_low' => 20,
            'color_low' => '#e74c3c',
            'threshold_medium' => 60,
            'color_medium' => '#f1c40f',
            'color_high' => '#2ecc71',
            'active_template_id' => 1, // Asumsikan template default memiliki ID 1
        ];

        $activeTemplateData = null;

        if ($indicatorSettings && isset($indicatorSettings['active_template_id'])) {
            $activeTemplate = \app\Models\GaugeTemplate::findById($indicatorSettings['active_template_id']);
            if ($activeTemplate) {
                $html_content = $activeTemplate['html_code'] ?? '';
                $clean_html = '';

                // Hanya proses jika ada konten HTML
                if (!empty($html_content)) {                    
                    // PERBAIKAN: Cek apakah ekstensi DOM/XML diaktifkan di server.
                    if (class_exists('DOMDocument')) {
                        // Ekstrak hanya konten di dalam <body> untuk menghindari konflik
                        $doc = new \DOMDocument();
                        @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html_content); // Tambahkan header untuk encoding
                        $body = $doc->getElementsByTagName('body')->item(0);
                        if ($body) {
                            foreach ($body->childNodes as $child) {
                                $clean_html .= $doc->saveHTML($child);
                            }
                        } else { $clean_html = $html_content; } // Fallback jika tidak ada body
                    } else {
                        // Fallback jika ekstensi DOM tidak ada: gunakan HTML mentah.
                        $clean_html = $html_content;
                    }
                }

                $activeTemplateData = [
                    'id' => $activeTemplate['id'],
                    'html' => $clean_html,
                    'css' => $activeTemplate['css_code'] ?? '',
                    'js' => $activeTemplate['js_code'] ?? ''
                ];
            }
        }

        $data = [
            'title' => 'Dashboard',
            'stats' => [
                'total_controllers' => $totalControllers,
                'online_controllers' => $onlineControllers,
                'total_tanks' => count($tanks), // Sekarang aman
                'total_users' => count($users)  // Sekarang aman
            ],
            'controllers' => $controllers,
            'indicator_settings' => $indicatorSettings,
            'active_template' => $activeTemplateData // Kirim data template yang sudah diproses
        ];

        view('dashboard/index', $data);
    }
}