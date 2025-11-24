<h1><?php echo $title ?? 'Daftarkan Perangkat Baru'; ?></h1>
<p>Lengkapi detail di bawah ini untuk mendaftarkan perangkat ke dalam sistem.</p>

<form action="/controllers/register" method="POST" style="max-width: 500px;">
    <div style="margin-bottom: 15px;">
        <label for="mac_address" style="display: block; margin-bottom: 5px;">MAC Address</label>
        <input type="text" id="mac_address" name="mac_address" value="<?php echo htmlspecialchars($mac_address); ?>" readonly style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #eee;">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="tank_id" style="display: block; margin-bottom: 5px;">Pilih Tangki</label>
        <select id="tank_id" name="tank_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Pilih Tangki --</option>
            <?php foreach ($tanks as $tank): ?>
                <option value="<?php echo $tank['id']; ?>"><?php echo htmlspecialchars($tank['tank_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="pump_id" style="display: block; margin-bottom: 5px;">Pilih Pompa</label>
        <select id="pump_id" name="pump_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Pilih Pompa --</option>
            <?php foreach ($pumps as $pump): ?>
                <option value="<?php echo $pump['id']; ?>"><?php echo htmlspecialchars($pump['pump_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label for="sensor_id" style="display: block; margin-bottom: 5px;">Pilih Sensor</label>
        <select id="sensor_id" name="sensor_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">-- Pilih Sensor --</option>
            <?php foreach ($sensors as $sensor): ?>
                <option value="<?php echo $sensor['id']; ?>"><?php echo htmlspecialchars($sensor['sensor_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <p style="font-size: 0.9em; color: #555;">Catatan: Pengaturan seperti 'Jarak Tangki Penuh' dan 'Pemicu Pompa' akan diambil secara otomatis dari pengaturan sensor yang dipilih.</p>

    <div>
        <button type="submit" style="background-color: #3498db; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Daftarkan Perangkat</button>
        <a href="/detect" style="display: inline-block; margin-left: 10px; color: #333;">Batal</a>
    </div>
</form>