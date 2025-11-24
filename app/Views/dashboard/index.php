<div class="container">
    <h1><?php echo $title ?? 'Dashboard'; ?></h1>

    <!-- Kartu Statistik -->
    <div class="stat-cards-container">
        <div class="stat-card">
            <div class="stat-card-icon bg-blue">
                <i class="fas fa-microchip"></i>
            </div>
            <div>
                <div class="stat-card-title">Total Perangkat</div>
                <div class="stat-card-value" id="stat-total-controllers"><?php echo $stats['total_controllers'] ?? 0; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon bg-green">
                <i class="fas fa-wifi"></i>
            </div>
            <div>
                <div class="stat-card-title">Perangkat Online</div>
                <div class="stat-card-value" id="stat-online-controllers"><?php echo $stats['online_controllers'] ?? 0; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon bg-orange">
                <i class="fas fa-database"></i>
            </div>
            <div>
                <div class="stat-card-title">Total Tangki</div>
                <div class="stat-card-value" id="stat-total-tanks"><?php echo $stats['total_tanks'] ?? 0; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon bg-red">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="stat-card-title">Total Pengguna</div>
                <div class="stat-card-value" id="stat-total-users"><?php echo $stats['total_users'] ?? 0; ?></div>
            </div>
        </div>
    </div>

    <!-- Gauge Level Air -->
    <h2 style="margin-top: 30px;">Status Level Air Tangki</h2>
    <!-- PERBAIKAN: Kontainer ini sekarang menjadi grid utama untuk kartu-kartu gauge -->
    <div id="gauge-grid-container" class="gauge-container"></div>
    <p id="no-device-message" style="display: none;">Tidak ada perangkat yang terdaftar untuk ditampilkan.</p>
</div>

<!-- PERBAIKAN: CSS baru yang sesuai dengan struktur asli -->
<style>
    /* Pastikan kontainer utama adalah grid (ini mungkin sudah ada di style.css Anda, tapi aman untuk didefinisikan ulang) */
    .gauge-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    /* Jadikan kartu sebagai flex container untuk menata konten di dalamnya */
    .gauge-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Posisikan judul & gauge di atas, tombol di bawah */
    }
    /* Pastikan konten utama gauge (div yang berisi visualisasi) bisa tumbuh */
    .gauge-card > div:not(.gauge-actions) {
        flex-grow: 1;
    }
    .gauge-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        border-top: 1px solid #e0e0e0;
        background-color: #f9f9f9;
        margin-top: 10px; /* Beri sedikit jarak dari gauge */
    }
    .btn-action {
        padding: 5px 10px;
        font-size: 0.8rem;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        border: 1px solid #ccc;
    }
    .btn-action:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>

<!-- PERBAIKAN: Muat library DevExtreme yang diperlukan untuk template dx-gauge -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn3.devexpress.com/jslib/17.1.6/js/dx.all.js"></script>
<!-- PERBAIKAN: Muat library JSCharting yang diperlukan untuk template baru -->
<script src="https://code.jscharting.com/latest/jscharting.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = ''; // Kosongkan untuk produksi di root domain
    
    // Ambil data template aktif yang sudah disiapkan oleh PHP (dari backup)
    const activeTemplate = <?php echo json_encode($active_template ?? null); ?>;

    // Jika ada template aktif dan memiliki CSS, inject langsung saat halaman dimuat
    if (activeTemplate && activeTemplate.css) {
        const styleEl = document.createElement('style');
        styleEl.id = `template-style-${activeTemplate.id}`;
        styleEl.textContent = activeTemplate.css;
        document.head.appendChild(styleEl);
    }

    // Jika ada template aktif dan memiliki JavaScript, inject dan jalankan
    if (activeTemplate && activeTemplate.js) {
        const scriptEl = document.createElement('script');
        scriptEl.id = `template-script-${activeTemplate.id}`;
        scriptEl.textContent = activeTemplate.js;
        document.body.appendChild(scriptEl);
    }

    const gaugeContainer = document.getElementById('gauge-grid-container');

    function updateDashboard() {
        fetch(`${baseUrl}/api/dashboard-data`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // 1. Perbarui Kartu Statistik
                document.getElementById('stat-total-controllers').textContent = data.stats.total_controllers;
                document.getElementById('stat-online-controllers').textContent = data.stats.online_controllers;
                document.getElementById('stat-total-tanks').textContent = data.stats.total_tanks;
                document.getElementById('stat-total-users').textContent = data.stats.total_users;

                // Ambil semua data yang diperlukan dari API
                const settings = data.indicator_settings;

                // PERBAIKAN: Logika untuk menghapus kartu yang sudah tidak aktif
                const existingCards = new Set(Array.from(gaugeContainer.querySelectorAll('.gauge-card:not(.gauge-card-placeholder)')).map(el => el.id));

                // 2. Render dan Perbarui Gauge secara dinamis
                if (data.controllers && data.controllers.length > 0) {
                    document.getElementById('no-device-message').style.display = 'none';

                    data.controllers.forEach(controller => {
                        const cardId = `gauge-card-${controller.id}`;
                        existingCards.delete(cardId); // Hapus dari daftar yang akan dihapus karena masih aktif

                        // Cek apakah gauge sudah ada di DOM, jika belum, buat dari template
                        let gaugeCard = document.getElementById(cardId);
                        if (!gaugeCard && activeTemplate && activeTemplate.html) {
                            let html = activeTemplate.html
                                .replace(/{{CONTROLLER_ID}}/g, controller.id)
                                .replace(/{{TANK_NAME}}/g, controller.tank_name || 'N/A');
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = html;
                            gaugeCard = tempDiv.querySelector('.gauge-card');
                            if (!gaugeCard) return; // Lewati jika template tidak valid
                            gaugeCard.id = `gauge-card-${controller.id}`;

                            // Jika ada fungsi inisialisasi, panggil
                            if (typeof window.initGauge === 'function') {
                                window.initGauge(gaugeCard);
                            }
                            
                            // PERBAIKAN: Tambahkan div untuk tombol di dalam kartu
                            const actionsDiv = document.createElement('div');
                            actionsDiv.className = 'gauge-actions';
                            gaugeCard.appendChild(actionsDiv);

                            gaugeContainer.appendChild(gaugeCard);
                        }

                        // --- LOGIKA BARU: Update tombol aksi di dalam kartu ---
                        if (gaugeCard) {
                            const actionsDiv = gaugeCard.querySelector('.gauge-actions');
                            if (actionsDiv) {
                                const isManualMode = (controller.control_mode === 'MANUAL');
                                const newMode = isManualMode ? 'AUTO' : 'MANUAL';
                                const newStatus = (controller.status === 'ON') ? 'OFF' : 'ON';

                                actionsDiv.innerHTML = `
                                    <button class="btn-action btn-mode-toggle" 
                                            data-mac="${controller.mac_address}" 
                                            data-new-mode="${newMode}">
                                        Set ${newMode}
                                    </button>
                                    <button class="btn-action btn-pump-toggle" 
                                            data-mac="${controller.mac_address}" 
                                            data-new-status="${newStatus}"
                                            ${!isManualMode ? 'disabled' : ''}
                                            title="${!isManualMode ? 'Hanya aktif di mode MANUAL' : `Set Pompa ${newStatus}`}">
                                        Set ${newStatus}
                                    </button>
                                `;
                            }
                        }

                        // Pastikan gaugeCard ada sebelum melanjutkan
                        if (!gaugeCard) return;

                        // --- LOGIKA BARU: Update atau buat indikator sinyal ---
                        let signalIndicator = gaugeCard.querySelector('.signal-indicator');
                        if (!signalIndicator) {
                            signalIndicator = document.createElement('div');
                            signalIndicator.className = 'signal-indicator';
                            gaugeCard.appendChild(signalIndicator);
                        }

                        const rssi = controller.rssi;
                        let signalClass = '';
                        let activeBars = 0; // Jumlah bar yang aktif

                        if (rssi && rssi != 0) {
                            if (rssi > -67) {
                                signalClass = 'signal-good'; // Kuat (hijau)
                                activeBars = 4;
                            } else if (rssi > -80) {
                                signalClass = 'signal-medium'; // Sedang (oranye)
                                activeBars = 3;
                            } else {
                                signalClass = 'signal-weak'; // Lemah (merah)
                                activeBars = 2;
                            }
                        }

                        // Hapus kelas lama dan tambahkan yang baru
                        signalIndicator.classList.remove('signal-good', 'signal-medium', 'signal-weak');
                        if (signalClass) signalIndicator.classList.add(signalClass);
                        
                        // Buat HTML untuk bar sinyal
                        signalIndicator.innerHTML = `
                            <span class="signal-bar ${activeBars >= 1 ? 'active' : ''}"></span>
                            <span class="signal-bar ${activeBars >= 2 ? 'active' : ''}"></span>
                            <span class="signal-bar ${activeBars >= 3 ? 'active' : ''}"></span>
                            <span class="signal-bar ${activeBars >= 4 ? 'active' : ''}"></span>
                        `;

                        // --- LOGIKA BARU: Update atau buat indikator status pompa ---
                        let pumpIndicator = gaugeCard.querySelector('.pump-indicator');
                        if (!pumpIndicator) {
                            pumpIndicator = document.createElement('div');
                            pumpIndicator.className = 'pump-indicator';
                            gaugeCard.appendChild(pumpIndicator);
                        }

                        const pumpStatus = controller.status; // Sekarang bisa 'ON', 'OFF', atau 'RESTING'
                        
                        let pumpClass = 'pump-off'; // Default
                        let pumpText = pumpStatus; // Teks sama dengan status

                        if (pumpStatus === 'ON') {
                            pumpClass = 'pump-on';
                        } else if (pumpStatus === 'RESTING') {
                            pumpClass = 'pump-resting';
                        } else {
                            // Untuk status 'OFF' atau status lain yang tidak dikenal
                            pumpClass = 'pump-off';
                            pumpText = 'OFF';
                        }

                        // Hapus kelas lama dan tambahkan yang baru
                        pumpIndicator.classList.remove('pump-on', 'pump-off', 'pump-resting');
                        pumpIndicator.classList.add(pumpClass);

                        // Update ikon dan teks
                        pumpIndicator.innerHTML = `
                            <i class="fas fa-power-off"></i> 
                            <span style="margin-left: 4px;">${pumpText}</span>
                        `;

                        // --- LOGIKA JAVASCRIPT UNIVERSAL ---
                        const waterLevel = controller.latest_water_level || 0;
                        let fillColor;
                        if (parseFloat(waterLevel) > parseFloat(settings.threshold_medium)) {
                            fillColor = settings.color_high;
                        } else if (parseFloat(waterLevel) > parseFloat(settings.threshold_low)) {
                            fillColor = settings.color_medium;
                        } else {
                            fillColor = settings.color_low;
                        }

                        // Pastikan gaugeCard ada sebelum mencoba mengupdatenya
                        if (gaugeCard) {
                            // Panggil fungsi update universal yang disediakan oleh script template
                            if (typeof window.updateGauge === 'function') {
                                // Jika template menyediakan fungsi update sendiri, gunakan itu.
                                window.updateGauge(gaugeCard, waterLevel, fillColor);
                            } else {
                                // JIKA TIDAK: Gunakan logika universal untuk template sederhana.
                                const elementsToUpdate = gaugeCard.querySelectorAll('[data-update-style]');
                                elementsToUpdate.forEach(el => {
                                    const styleProp = el.dataset.updateStyle;

                                    // PERBAIKAN: Cek 'styleProp', bukan 'transformType'
                                    if (styleProp === 'degrees') {
                                        const finalValue = (waterLevel / 100) * 270;
                                        // PERBAIKAN: Set properti kustom '--percentage', bukan 'styleProp'
                                        el.style.setProperty('--percentage', `${finalValue}deg`);
                                        el.style.setProperty('--fill-color', fillColor);
                                    } else if (styleProp === 'percentage') {
                                        el.style.width = `${waterLevel}%`;
                                        el.style.height = `${waterLevel}%`;
                                        el.style.backgroundColor = fillColor;
                                    }
                                });

                                // PERBAIKAN: Tambahkan kembali logika untuk update teks
                                const textElement = gaugeCard.querySelector('.value') || gaugeCard.querySelector('.tank-gauge-text') || gaugeCard.querySelector('.simple-bar-gauge-text');
                                if (textElement) {
                                    textElement.textContent = `${Math.round(waterLevel)}%`;
                                }
                            }
                        }
                    });
                } else {
                    document.getElementById('no-device-message').style.display = 'block';
                    gaugeContainer.innerHTML = ''; // Kosongkan jika tidak ada perangkat
                }

                // Hapus kartu dari perangkat yang sudah tidak ada
                existingCards.forEach(cardId => {
                    document.getElementById(cardId)?.remove();
                });

                // --- LOGIKA BARU: Selalu tampilkan kartu placeholder ---
                // Hapus placeholder lama jika ada, untuk menghindari duplikasi
                const oldPlaceholder = document.getElementById('gauge-card-placeholder');
                if (oldPlaceholder) {
                    oldPlaceholder.remove();
                }

                // Buat elemen placeholder baru sebagai link
                const placeholderCard = document.createElement('a');
                placeholderCard.id = 'gauge-card-placeholder';
                placeholderCard.className = 'gauge-card gauge-card-placeholder'; // Pastikan style untuk ini ada
                placeholderCard.href = `${baseUrl}/detect`; // Arahkan ke halaman deteksi
                placeholderCard.innerHTML = `
                    <div class="placeholder-icon"><i class="fas fa-plus-circle"></i></div>
                    <div class="gauge-title">Tambah Perangkat</div>
                `;
                
                gaugeContainer.appendChild(placeholderCard);
            })
            .catch(error => console.error('Gagal memperbarui dashboard:', error));
    }

    // --- LOGIKA BARU: Menangani klik tombol aksi ---

    // Fungsi untuk mengirim perintah ke API
    function sendApiCommand(mac, action, value) {
        console.log(`Sending command: MAC=${mac}, Action=${action}, Value=${value}`);
        fetch(`${baseUrl}/api/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mac: mac, action: action, value: value })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Server response:', data);
            updateDashboard(); // Memicu pembaruan data dashboard segera
        })
        .catch(error => console.error('Error sending command:', error));
    }

    // Gunakan event delegation pada kontainer utama
    gaugeContainer.addEventListener('click', function(event) {
        const target = event.target;

        if (target.matches('.btn-mode-toggle')) {
            const mac = target.dataset.mac;
            const newMode = target.dataset.newMode;
            if (confirm(`Anda yakin ingin mengubah mode perangkat ${mac} menjadi ${newMode}?`)) {
                sendApiCommand(mac, 'set_mode', newMode);
            }
        } else if (target.matches('.btn-pump-toggle') && !target.disabled) { // Hanya jalankan jika tombol tidak di-disable
            const mac = target.dataset.mac;
            const newStatus = target.dataset.newStatus;
            sendApiCommand(mac, 'set_status', newStatus);
        }
    });
    
    // Inisialisasi dan pembaruan periodik (diubah ke 5 detik)
    updateDashboard();
    setInterval(updateDashboard, 5000);
});
</script>