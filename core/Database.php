<?php
// Kelas untuk koneksi dan operasi database.

class Database {
    private static $instance = null;
    private $pdo;

    // HAPUS: Kredensial tidak boleh disimpan di sini.
    // private const DB_HOST = 'localhost';
    // private const DB_NAME = 'selurdesa_apps';
    // private const DB_USER = 'selurdesa';
    // private const DB_PASS = 'DS#selur@2025';

    private function __construct() {
        $config = require ROOT_PATH . '/config/database.php';

        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $options);
        } catch (PDOException $e) {
            // Hentikan output apa pun yang mungkin sudah ada di buffer
            if (ob_get_level()) {
                ob_end_clean();
            }
            // Pada lingkungan produksi, sebaiknya log error ini, bukan menampilkannya.
            // Alih-alih membuat crash, kirim respons error 503 Service Unavailable.
            http_response_code(503);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Layanan database tidak tersedia.']);
            exit(); // Hentikan eksekusi skrip dengan anggun.
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
