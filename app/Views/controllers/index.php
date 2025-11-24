<?php
// File ini adalah VIEW. Isinya hanya untuk presentasi (HTML).
// Semua logika inisialisasi sudah ditangani oleh public/index.php.
?>

<!-- PERBAIKAN: Tambahkan CSS untuk Modal Laporan Sinkronisasi -->
<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1000;
        display: none; justify-content: center; align-items: center;
    }
    .modal-content {
        background: white; padding: 25px; border-radius: 8px;
        width: 90%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 15px;
    }
    .modal-title { margin: 0; font-size: 1.5rem; }
    .modal-close { font-size: 1.5rem; font-weight: bold; cursor: pointer; border: none; background: none; }
    .report-table { width: 100%; border-collapse: collapse; }
    .report-table th, .report-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    .report-table th { background-color: #f2f2f2; }
    .value-old { color: #e74c3c; text-decoration: line-through; }
    .value-new { color: #27ae60; font-weight: bold; }
</style>

<!-- PERBAIKAN: Tambahkan struktur HTML untuk Modal -->
<div id="sync-report-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title" class="modal-title">Laporan Sinkronisasi</h2>
            <button id="modal-close-btn" class="modal-close">&times;</button>
        </div>
        <div id="modal-body" class="modal-body">
            <!-- Konten laporan akan dimasukkan di sini oleh JavaScript -->
        </div>
    </div>
</div>

<div class="card">
    <h1><?php echo $title ?? 'Pengaturan Perangkat'; ?></h1>
    <p>Daftar semua perangkat kontroler yang terdaftar dalam sistem.</p>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px;">
        <a href="/detect" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Tambah Perangkat Baru
        </a>
    </div>

    <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Tangki</th>
                    <th>MAC Address</th>
                    <th>Status</th>
                    <th>Mode</th>
                    <th>Level Air</th>
                    <th>Sinyal</th>
                    <th>Update Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($controllers)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">Tidak ada perangkat yang terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($controllers as $controller): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($controller['tank_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($controller['mac_address']); ?></td>
                            <td>
                                <?php $isOnline = (time() - strtotime($controller['last_update'])) < 300; ?>
                                <span class="status-badge <?php echo $isOnline ? 'status-online' : 'status-offline'; ?>">
                                    <?php echo $isOnline ? 'ONLINE' : 'OFFLINE'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(strtoupper($controller['control_mode'])); ?></td>
                            <td><?php echo isset($controller['latest_water_level']) ? round($controller['latest_water_level']) . '%' : 'N/A'; ?></td>
                            <td><?php echo isset($controller['rssi']) && $controller['rssi'] != 0 ? $controller['rssi'] . ' dBm' : 'N/A'; ?></td>
                            <td><?php echo htmlspecialchars($controller['last_update']); ?></td>
                            <td class="action-buttons">
                                <a href="/controllers/<?php echo $controller['id']; ?>" class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                <!-- Tombol Hapus dengan Konfirmasi -->
                                <form action="/controllers/delete/<?php echo $controller['id']; ?>" method="POST" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus perangkat ini? Perintah reboot akan dikirim dan perangkat akan dihapus dari sistem.');">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus Perangkat">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <!-- PERBAIKAN: Tambahkan tombol untuk sinkronisasi data master -->
                                <!-- PERBAIKAN: Ubah dari form menjadi tombol biasa yang akan ditangani JavaScript -->
                                <button class="btn btn-sm btn-warning btn-sync" 
                                        data-id="<?php echo $controller['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($controller['tank_name'] ?? $controller['mac_address']); ?>"
                                        title="Sinkronkan dengan data master">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
    </table>
</div>

<!-- PERBAIKAN: Tambahkan JavaScript untuk menangani modal dan AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('sync-report-modal');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    const modalBody = document.getElementById('modal-body');

    // Fungsi untuk menutup modal
    function closeModal() {
        modal.style.display = 'none';
    }

    modalCloseBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Tambahkan event listener ke semua tombol sinkronisasi
    document.querySelectorAll('.btn-sync').forEach(button => {
        button.addEventListener('click', function() {
            const controllerId = this.dataset.id;
            const controllerName = this.dataset.name;

            if (!confirm(`Anda yakin ingin menyinkronkan data untuk "${controllerName}"?`)) {
                return;
            }

            // Tampilkan loading di modal
            modalBody.innerHTML = '<p>Menyinkronkan data, harap tunggu...</p>';
            modal.style.display = 'flex';

            // Kirim permintaan ke server
            fetch(`/controllers/sync/${controllerId}`, { // PERBAIKAN: Hapus '/wlc' jika aplikasi di root domain
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                // Bangun konten HTML untuk laporan
                let reportHtml = `<p>${data.message}</p>`;
                if (data.status === 'success' && data.changes.length > 0) {
                    reportHtml += '<table class="report-table"><tr><th>Pengaturan</th><th>Nilai Lama</th><th>Nilai Baru</th></tr>';
                    data.changes.forEach(change => {
                        reportHtml += `<tr>
                            <td>${change.setting}</td>
                            <td><span class="value-old">${change.old_value}</span></td>
                            <td><span class="value-new">${change.new_value}</span></td>
                        </tr>`;
                    });
                    reportHtml += '</table>';
                } else if (data.status === 'success' && data.changes.length === 0) {
                    reportHtml += '<p>Tidak ada perubahan data yang terdeteksi. Data sudah sinkron.</p>';
                }
                modalBody.innerHTML = reportHtml;
            })
            .catch(error => {
                console.error('Error:', error);
                modalBody.innerHTML = '<p class="error">Terjadi kesalahan saat menghubungi server.</p>';
            });
        });
    });
});
</script>