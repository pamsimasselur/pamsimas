<?php

namespace app\Controllers;

use app\Models\Tank;
use app\Models\Pump;
use app\Models\Sensor;
use app\Models\IndicatorSetting; // Tambahkan ini
use app\Models\GaugeTemplate; // Tambahkan ini

class SettingController extends \app\Controllers\AdminController {

    public function __construct() {
        parent::__construct(); // Cukup panggil parent constructor
    }

    /**
     * Menampilkan halaman pengaturan.
     */
    public function tanks() {
        $data = [
            'title' => 'Pengaturan Tangki',
            'tanks' => Tank::getAll()
        ];

        view('settings/tanks', $data);
    }

    /**
     * Menampilkan form untuk menambah tangki baru.
     */
    public function createTank() {
        $data = [
            'title' => 'Tambah Tangki Baru',
            'form_action' => '/settings/tanks/create'
        ];
        view('settings/tank_form', $data);
    }

    /**
     * Menyimpan tangki baru ke database.
     */
    public function storeTank() {
        $data = [
            'tank_name' => $_POST['tank_name'] ?? '',
            'height' => $_POST['height'] ?? 0,
            'tank_shape' => $_POST['tank_shape'] ?? 'kotak',
            'length' => $_POST['length'] ?? null,
            'width' => $_POST['width'] ?? null,
            'diameter' => $_POST['diameter'] ?? null,
        ];

        Tank::create($data);
        header('Location: /settings/tanks');
        exit();
    }

    /**
     * Menampilkan form untuk mengedit tangki.
     */
    public function editTank($id) {
        $tank = Tank::findById($id);
        if (!$tank) {
            http_response_code(404);
            echo "Tangki tidak ditemukan.";
            exit();
        }

        $data = [
            'title' => 'Edit Tangki: ' . htmlspecialchars($tank['tank_name']),
            'tank' => $tank,
            'form_action' => '/settings/tanks/edit/' . $id
        ];
        view('settings/tank_form', $data);
    }

    /**
     * Memperbarui data tangki di database.
     */
    public function updateTank($id) {
        $data = [
            'tank_name' => $_POST['tank_name'] ?? '',
            'height' => $_POST['height'] ?? 0,
            'tank_shape' => $_POST['tank_shape'] ?? 'kotak',
            'length' => $_POST['length'] ?? null,
            'width' => $_POST['width'] ?? null,
            'diameter' => $_POST['diameter'] ?? null,
        ];

        if (empty($data['tank_name'])) {
            // Handle error
            header('Location: /settings/tanks/edit/' . $id);
            exit();
        }

        Tank::update((int)$id, $data);
        header('Location: /settings/tanks');
        exit();
    }

    /**
     * Menampilkan halaman pengaturan pompa.
     */
    public function pumps() {
        $data = [
            'title' => 'Pengaturan Pompa',
            'pumps' => Pump::getAll()
        ];

        view('settings/pumps', $data);
    }

    /**
     * Menampilkan form untuk menambah pompa baru.
     */
    public function createPump() {
        $data = [
            'title' => 'Tambah Pompa Baru',
            'form_action' => '/settings/pumps/create'
        ];
        view('settings/pump_form', $data);
    }

    /**
     * Menyimpan pompa baru ke database.
     */
    public function storePump() {
        $data = [
            'pump_name' => $_POST['pump_name'] ?? '',
            'flow_rate_lps' => $_POST['flow_rate_lps'] ?? 0,
            'power_watt' => $_POST['power_watt'] ?? 0,
            'delay_seconds' => $_POST['delay_seconds'] ?? 0,
        ];

        Pump::create($data);
        header('Location: /settings/pumps');
        exit();
    }

    /**
     * Menampilkan form untuk mengedit pompa.
     */
    public function editPump($id) {
        $pump = Pump::findById($id);
        if (!$pump) {
            http_response_code(404);
            echo "Pompa tidak ditemukan.";
            exit();
        }

        $data = [
            'title' => 'Edit Pompa: ' . htmlspecialchars($pump['pump_name']),
            'pump' => $pump,
            'form_action' => '/settings/pumps/edit/' . $id
        ];
        view('settings/pump_form', $data);
    }

    /**
     * Memperbarui data pompa di database.
     */
    public function updatePump($id) {
        $data = [
            'pump_name' => $_POST['pump_name'] ?? '',
            'flow_rate_lps' => $_POST['flow_rate_lps'] ?? 0,
            'power_watt' => $_POST['power_watt'] ?? 0,
            'delay_seconds' => $_POST['delay_seconds'] ?? 0,
        ];

        Pump::update((int)$id, $data);
        header('Location: /settings/pumps');
        exit();
    }

    /**
     * Menampilkan halaman pengaturan sensor per perangkat.
     */
    public function sensors() {
        $data = [
            'title' => 'Pengaturan Sensor',
            // Menggunakan model Sensor yang baru
            'sensors' => Sensor::getAll()
        ];

        view('settings/sensors', $data);
    }

    /**
     * Menampilkan form untuk menambah sensor baru.
     */
    public function createSensor() {
        $data = [
            'title' => 'Tambah Sensor Baru',
            'form_action' => '/settings/sensors/create'
        ];
        view('settings/sensor_form', $data);
    }

    /**
     * Menyimpan sensor baru ke database.
     */
    public function storeSensor() {
        $data = [
            'sensor_name' => $_POST['sensor_name'] ?? '',
            'sensor_type' => $_POST['sensor_type'] ?? '',
            'full_tank_distance' => $_POST['full_tank_distance'] ?? 0,
            'trigger_percentage' => $_POST['trigger_percentage'] ?? 0,
        ];

        // Validasi sederhana
        if (empty($data['sensor_name'])) {
            $_SESSION['error'] = 'Nama sensor wajib diisi.';
            header('Location: /settings/sensors/create');
            exit();
        }

        Sensor::create($data);
        header('Location: /settings/sensors');
        exit();
    }

    /**
     * Menampilkan form untuk mengedit sensor.
     */
    public function editSensor($id) {
        $sensor = Sensor::findById($id);
        if (!$sensor) {
            http_response_code(404);
            echo "Sensor tidak ditemukan.";
            exit();
        }

        $data = [
            'title' => 'Edit Sensor: ' . htmlspecialchars($sensor['sensor_name']),
            'sensor' => $sensor,
            'form_action' => '/settings/sensors/edit/' . $id
        ];
        view('settings/sensor_form', $data);
    }

    /**
     * Memperbarui data sensor di database.
     */
    public function updateSensor($id) {
        $data = [
            'sensor_name' => $_POST['sensor_name'] ?? '',
            'sensor_type' => $_POST['sensor_type'] ?? '',
            'full_tank_distance' => $_POST['full_tank_distance'] ?? 0,
            'trigger_percentage' => $_POST['trigger_percentage'] ?? 0, // PERBAIKAN: Tambahkan koma yang hilang
        ];

        Sensor::update((int)$id, $data);
        header('Location: /settings/sensors');
        exit();
    }

    /**
     * Menampilkan dan memproses halaman gabungan untuk Pengaturan Tampilan.
     */
    public function displaySettings() {
        // Jika form disubmit (metode POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'threshold_low' => (int)$_POST['threshold_low'],
                'color_low' => $_POST['color_low'],
                'threshold_medium' => (int)$_POST['threshold_medium'],
                'color_medium' => $_POST['color_medium'],
                'color_high' => $_POST['color_high'],
                'active_template_id' => (int)$_POST['active_template_id'],
            ];
            IndicatorSetting::updateSettings($data);
            $_SESSION['success_message'] = 'Pengaturan indikator berhasil diperbarui.';
            header('Location: /settings/display');
            exit();
        }

        // Jika hanya menampilkan halaman (metode GET)
        $data = [
            'title' => 'Pengaturan Tampilan',
            'settings' => IndicatorSetting::getSettings(),
            'templates' => GaugeTemplate::getAll(),
            // PERBAIKAN: Tentukan file JS yang akan dimuat untuk halaman ini
            'page_scripts' => ['js/indicator-settings.js']
        ];
        view('settings/indicators', $data);
    }

    /**
     * Menampilkan dan memproses halaman pengaturan indikator.
     * Metode ini sekarang usang dan akan dihapus nanti.
     * Untuk sementara, arahkan ke halaman baru.
     */
    public function indicators() {
        header('Location: /settings/display');
        exit();
    }
}