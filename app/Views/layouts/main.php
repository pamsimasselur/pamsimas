<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'WLC Application'; ?></title>
    <!-- Memuat CSS dengan parameter versi untuk cache busting -->
    <link rel="stylesheet" href="/css/style.css?v=1.1">
    
    <!-- Slot untuk memuat file CSS spesifik halaman -->
    <?php if (isset($page_styles) && is_array($page_styles)): ?>
        <?php foreach ($page_styles as $style): ?>
            <link rel="stylesheet" href="/<?php echo $style; ?>?v=1.0">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Menambahkan library Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="main-container">
        <?php require_once 'sidebar.php'; // Memuat sidebar dari file terpisah ?>
        <main class="content">
            <?php require_once $content; // PERBAIKAN: Menggunakan variabel $content yang benar dari index.php ?>
        </main>
    </div>

    <!-- PERBAIKAN: Slot untuk memuat file JavaScript spesifik halaman -->
    <?php if (isset($page_scripts) && is_array($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="/<?php echo $script; ?>?v=1.0"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>