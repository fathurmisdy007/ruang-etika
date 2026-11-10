<?php
require_once __DIR__ . '/includes/functions.php';
$search = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 180) : '';
$category = is_string($_GET['kategori'] ?? null) && in_array($_GET['kategori'], categories(), true) ? $_GET['kategori'] : '';
$articles = published_articles(null, $search, $category);
$page_title = 'Artikel Etika dalam Bermedia Sosial';
$active_page = 'artikel';
require __DIR__ . '/includes/header.php';
?>
<div id="top"></div>
<section class="container page-intro"><div class="eyebrow"><span class="dot"></span> JURNAL ETIKA DALAM BERMEDIA SOSIAL</div><h1>Saling menghargai.<br><em>Mulai dari interaksi kita.</em></h1><div class="intro-bottom"><p>Bacaan tentang sopan santun, tanggung jawab unggahan,<br>dan menghormati orang lain di media sosial.</p><form class="search-form" action="<?= e(url('artikel-list.php')) ?>" method="get" role="search"><label class="sr-only" for="search">Cari berdasarkan judul artikel</label><?= icon('search') ?><input id="search" name="q" type="search" maxlength="180" value="<?= e($search) ?>" placeholder="Cari judul artikel…"><?php if ($category): ?><input type="hidden" name="kategori" value="<?= e($category) ?>"><?php endif; ?><button type="submit" aria-label="Cari artikel"><?= icon('arrow') ?></button></form></div></section>
<section class="container article-library"><div class="category-filter" aria-label="Filter kategori"><?php foreach (array_merge([''], categories()) as $item): $query = array_filter(['q' => $search, 'kategori' => $item], fn($v) => $v !== ''); ?><a class="filter-chip <?= $category === $item ? 'active' : '' ?>" href="<?= e(url('artikel-list.php' . ($query ? '?' . http_build_query($query) : ''))) ?>" <?= $category === $item ? 'aria-current="true"' : '' ?>><?= e($item ?: 'Semua artikel') ?></a><?php endforeach; ?></div>
<div class="results-count"><p><?= count($articles) ?> artikel<?= $search !== '' ? ' untuk “' . e($search) . '”' : ' untuk menemani proses belajarmu' ?></p><?php if ($search !== '' || $category !== ''): ?><a href="<?= e(url('artikel-list.php')) ?>">Hapus filter ×</a><?php endif; ?></div>
<?php if ($articles): ?><div class="articles-grid library-grid"><?php foreach ($articles as $article) { article_card($article); } ?></div><?php else: ?><div class="empty-state"><?= icon('search') ?><h2>Belum menemukan bacaan yang pas.</h2><p>Coba kata kunci lain atau jelajahi semua kategori.</p><a class="btn btn-dark" href="<?= e(url('artikel-list.php')) ?>">Lihat semua artikel <?= icon('arrow') ?></a></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
