<div class="card">
    <h1><?php echo $title; ?></h1>
    <p>Menampilkan riwayat aktivitas pompa menyala (ON) dan mati (OFF) beserta durasinya.</p>

    <table>
        <thead>
            <tr>
                <th>Waktu Kejadian</th>
                <th>Nama Tangki</th>
                <th>Status</th>
                <th>Durasi Menyala</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo date('d M Y, H:i:s', strtotime($log['record_time'])); ?></td>
                        <td><?php echo htmlspecialchars($log['tank_name'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($log['status'] == 1): ?>
                                <span style="color: var(--success-color); font-weight: bold;">ON</span>
                            <?php else: ?>
                                <span style="color: #e74c3c; font-weight: bold;">OFF</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['status'] == 0 && !is_null($log['duration_seconds'])): ?>
                                <?php echo floor($log['duration_seconds'] / 60); ?> menit <?php echo $log['duration_seconds'] % 60; ?> detik
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada riwayat aktivitas pompa.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>