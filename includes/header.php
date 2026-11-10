<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/ui.php';
$page_title = $page_title ?? 'Etika dalam Bermedia Sosial';
$page_description = $page_description ?? 'Artikel edukasi Etika dalam Bermedia Sosial: berkomentar dengan sopan, menghargai perbedaan, bertanggung jawab atas unggahan, dan menghormati privasi orang lain.';
$active_page = $active_page ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="theme-color" content="#111111">
    <title><?= e($page_title) ?> — Ruang Etika</title>
    <link rel="icon" href="<?= e(url('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;450;500;550;600;650;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/editorial.css')) ?>?v=2">
    <script src="<?= e(url('assets/js/main.js')) ?>" defer></script>
</head>
<body class="public-site">
<a class="skip-link" href="#main-content">Lewati ke konten</a>
<header class="site-header">
    <div class="container nav-inner">
        <a href="<?= e(url()) ?>" class="brand" aria-label="Etika dalam Bermedia Sosial — Beranda">
            <span class="brand-icon" aria-hidden="true"><svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M16 2v7M16 23v7M2 16h7M23 16h7M9 3.9l3.5 6.1M19.5 22l3.5 6.1M3.9 9l6.1 3.5M22 19.5l6.1 3.5M3.9 23l6.1-3.5M22 12.5l6.1-3.5M9 28.1l3.5-6.1M19.5 10L23 3.9"/></g></svg></span>
            <span>ruang<span class="brand-serif">etika.</span><small>/ etika dalam bermedia sosial</small></span>
        </a>
        <nav class="desktop-nav" aria-label="Navigasi utama">
            <?php foreach (['beranda' => ['', 'Beranda'], 'artikel' => ['artikel-list.php', 'Artikel'], 'tentang' => ['tentang.php', 'Tentang Saya'], 'kontak' => ['kontak.php', 'Kontak']] as $key => [$path, $label]): ?>
                <a href="<?= e(url($path)) ?>" <?= $active_page === $key ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="btn btn-dark nav-cta" href="<?= e(url('artikel-list.php')) ?>">Mulai membaca <?= icon('arrow-up') ?></a>
        <button class="menu-toggle" type="button" aria-label="Buka navigasi" aria-controls="mobile-nav" aria-expanded="false"><?= icon('menu') ?></button>
    </div>
    <nav id="mobile-nav" class="mobile-nav" aria-label="Navigasi mobile" hidden>
        <a href="<?= e(url()) ?>" <?= $active_page === 'beranda' ? 'aria-current="page"' : '' ?>>Beranda</a>
        <a href="<?= e(url('artikel-list.php')) ?>" <?= $active_page === 'artikel' ? 'aria-current="page"' : '' ?>>Artikel</a>
        <a href="<?= e(url('tentang.php')) ?>" <?= $active_page === 'tentang' ? 'aria-current="page"' : '' ?>>Tentang Saya</a>
        <a href="<?= e(url('kontak.php')) ?>" <?= $active_page === 'kontak' ? 'aria-current="page"' : '' ?>>Kontak</a>
    </nav>
</header>
<main id="main-content">
