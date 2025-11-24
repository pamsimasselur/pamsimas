<?php

namespace app\Models;

class SensorLog {
    /**
     * Membuat entri log sensor baru di database.
     * Fungsi ini dirancang khusus untuk tabel `sensor_logs`.
     *
     * @param array $data Data log yang akan disimpan.
     * @return bool
     */
    public static function create(array $data): bool {
        $pdo = \Database::getInstance()->getConnection();
        
        // Tentukan apakah timestamp disediakan (untuk log offline) atau tidak (untuk log real-time)
        $sql = "INSERT INTO sensor_logs (controller_id, water_percentage, water_level, rssi, record_time) 
                VALUES (:controller_id, :water_percentage, :water_level, :rssi, :record_time)";
        
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([
            ':controller_id' => $data['controller_id'],
            ':water_percentage' => $data['water_percentage'],
            ':water_level' => $data['water_level'],
            ':rssi' => $data['rssi'],
            // Jika record_time tidak ada, gunakan waktu server saat ini
            ':record_time' => $data['record_time'] ?? date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mengambil log sensor terbaru dengan join ke nama tangki.
     *
     * @param int $limit Jumlah log yang akan diambil.
     * @return array
     */
    public static function getRecentLogs(int $limit = 200): array {
        $pdo = \Database::getInstance()->getConnection();
        $sql = "
            SELECT 
                sl.record_time,
                sl.water_percentage,
                t.tank_name
            FROM sensor_logs sl
            JOIN controllers c ON sl.controller_id = c.id
            LEFT JOIN tank_configurations t ON c.tank_id = t.id
            ORDER BY sl.record_time DESC
            LIMIT :limit";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}