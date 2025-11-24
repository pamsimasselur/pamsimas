<div class="card">
    <h1><?php echo $title; ?></h1>
    <p>Atur warna, batas level, dan pilih template tampilan untuk gauge di dashboard. Anda juga bisa mengelola template dari halaman ini.</p>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Daftar Template -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 40px;">
        <h2 style="margin: 0; font-size: 1.2em;">Manajemen Template</h2>
        <a href="/templates/create" class="btn btn-success"><i class="fas fa-plus-circle"></i> Tambah Template Baru</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Nama Template</th>
                <th>Deskripsi</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 25%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($templates as $template): ?>
                <tr>
                    <td><?php echo htmlspecialchars($template['name']); ?></td>
                    <td><?php echo htmlspecialchars($template['description']); ?></td>
                    <td>
                        <?php if ($template['id'] == $settings['active_template_id']): ?>
                            <span class="status-badge status-online">Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="action-buttons">
                        <button type="button" class="btn btn-sm btn-primary activate-btn" data-template-id="<?php echo $template['id']; ?>" <?php echo ($template['id'] == $settings['active_template_id']) ? 'disabled' : ''; ?>>Aktifkan</button>
                        <?php if (!$template['is_core']): ?>
                            <a href="/templates/edit/<?php echo $template['id']; ?>" class="btn btn-sm btn-info" title="Edit"><i class="fas fa-edit"></i></a>
                            <button type="submit" form="delete-form-<?php echo $template['id']; ?>" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            <form id="delete-form-<?php echo $template['id']; ?>" action="/templates/delete/<?php echo $template['id']; ?>" method="POST" style="display: none;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?');"></form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Form Pengaturan Warna dan Batas -->
    <form action="/settings/display" method="POST">
        <!-- Input tersembunyi untuk menyimpan template aktif -->
        <input type="hidden" name="active_template_id" id="active_template_id" value="<?php echo $settings['active_template_id']; ?>">
        
        <h2 style="margin-top: 40px; font-size: 1.2em;">Pengaturan Warna & Batas Level</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Batas Atas (%)</th>
                    <th>Warna</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Rendah (Merah)</strong></td>
                    <td><input type="number" name="threshold_low" value="<?php echo $settings['threshold_low']; ?>" min="1" max="99" required> <small>0% s/d nilai ini</small></td>
                    <td><input type="color" name="color_low" value="<?php echo $settings['color_low']; ?>"></td>
                </tr>
                <tr>
                    <td><strong>Sedang (Kuning)</strong></td>
                    <td><input type="number" name="threshold_medium" value="<?php echo $settings['threshold_medium']; ?>" min="1" max="99" required> <small>Di atas batas rendah s/d nilai ini</small></td>
                    <td><input type="color" name="color_medium" value="<?php echo $settings['color_medium']; ?>"></td>
                </tr>
                <tr>
                    <td><strong>Tinggi (Hijau)</strong></td>
                    <td><small>Di atas batas sedang s/d 100%</small></td>
                    <td><input type="color" name="color_high" value="<?php echo $settings['color_high']; ?>"></td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan Warna</button>
        </div>
    </form>
</div>