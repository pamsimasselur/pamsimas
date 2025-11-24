<style>
    /* Hapus style spesifik, akan menggunakan style global dari card dan table */
    #device-list { list-style-type: none; padding: 0; }
    #device-list li { background-color: #fff; padding: 15px; margin-bottom: -1px; /* Untuk efek seperti baris tabel */ border: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
    #device-list li:first-child { border-top-left-radius: 5px; border-top-right-radius: 5px; }
    #device-list li:last-child { border-bottom-left-radius: 5px; border-bottom-right-radius: 5px; margin-bottom: 0; }
    #device-list .mac-address { font-family: monospace; font-weight: bold; }
    #device-list .register-btn { text-decoration: none; background-color: #28a745; color: white; padding: 8px 12px; border-radius: 5px; font-size: 0.9em; }
    #loading-spinner { text-align: center; padding: 20px; color: #777; }
    .status-indicator { width: 12px; height: 12px; background-color: #6c757d; border-radius: 50%; display: inline-block; animation: blink 1.5s infinite; }
    @keyframes blink { 50% { opacity: 0.5; } }
</style>

<div class="card">
    <h1><?php echo $title ?? 'Deteksi Perangkat'; ?></h1>
    <p>Halaman ini secara otomatis mencari perangkat baru yang terhubung ke jaringan tetapi belum terdaftar di sistem. Daftar akan diperbarui setiap 5 detik.</p>

    <div id="loading-spinner">
        <p><span class="status-indicator"></span> Memuat daftar perangkat yang terdeteksi...</p>
    </div>
    <div id="device-list-container" style="display: none;">
        <ul id="device-list">
            <!-- Daftar perangkat akan dimuat di sini oleh JavaScript -->
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deviceList = document.getElementById('device-list');
        const deviceListContainer = document.getElementById('device-list-container');
        const loadingSpinner = document.getElementById('loading-spinner');
        const baseUrl = ''; // Kosongkan untuk produksi di root domain

        function fetchDetectedDevices() {
            fetch(`${baseUrl}/api/detected-devices`)
                .then(response => response.json())
                .then(macAddresses => {
                    loadingSpinner.style.display = 'none';
                    deviceListContainer.style.display = 'block';
                    deviceList.innerHTML = ''; // Kosongkan daftar sebelum mengisi ulang

                    if (macAddresses.length === 0) {
                        const noDeviceItem = document.createElement('li');
                        noDeviceItem.textContent = 'Tidak ada perangkat baru yang terdeteksi saat ini.';
                        deviceList.appendChild(noDeviceItem);
                    } else {
                        macAddresses.forEach(mac => {
                            const listItem = document.createElement('li');
                            
                            const macSpan = document.createElement('span');
                            macSpan.className = 'mac-address';
                            macSpan.textContent = mac;

                            const registerLink = document.createElement('a');
                            registerLink.className = 'register-btn';
                            registerLink.href = `${baseUrl}/controllers/register?mac=${encodeURIComponent(mac)}`;
                            registerLink.textContent = 'Daftarkan Perangkat Ini';

                            listItem.appendChild(macSpan);
                            listItem.appendChild(registerLink);
                            deviceList.appendChild(listItem);
                        });
                    }
                })
                .catch(error => {
                    console.error('Gagal mengambil data perangkat:', error);
                    loadingSpinner.style.display = 'none';
                    deviceListContainer.style.display = 'block';
                    deviceList.innerHTML = '<li>Terjadi kesalahan saat memuat data. Silakan cek koneksi ke server.</li>';
                });
        }

        // Panggil fungsi pertama kali
        fetchDetectedDevices();

        // Atur interval untuk memanggil fungsi setiap 5 detik
        setInterval(fetchDetectedDevices, 5000);
    });
</script>