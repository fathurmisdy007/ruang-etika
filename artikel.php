<?php
require_once __DIR__ . '/includes/functions.php';
$slug = is_string($_GET['slug'] ?? null) ? mb_substr($_GET['slug'], 0, 200) : '';
$article = $slug !== '' ? find_published_article($slug) : null;
if (!$article) { http_response_code(404); }
$page_title = $article ? $article['title'] : 'Artikel tidak ditemukan';
$page_description = $article ? excerpt($article['content'], 155) : 'Artikel yang Anda cari tidak ditemukan di Ruang Etika.';
$active_page = 'artikel';
$related = $article ? array_slice(array_values(array_filter(published_articles(), fn($row) => $row['id'] !== $article['id'])), 0, 3) : [];
$tags = $article ? article_tags((int) $article['id']) : [];
$source_url = $article && filter_var($article['image_source_url'] ?? '', FILTER_VALIDATE_URL)
    && in_array(parse_url($article['image_source_url'], PHP_URL_SCHEME), ['http', 'https'], true)
    ? $article['image_source_url'] : null;
require __DIR__ . '/includes/header.php';
?>
<div id="top"></div>
<?php if (!$article): ?>
<section class="container empty-state not-found"><span class="eyebrow">404 — HALAMAN TIDAK DITEMUKAN</span><h1>Cerita ini belum<br><em>ada di ruang kita.</em></h1><p>Artikel mungkin sudah dipindahkan atau belum diterbitkan.</p><a class="btn btn-dark" href="<?= e(url('artikel-list.php')) ?>">Kembali ke artikel <?= icon('arrow') ?></a></section>
<?php else: ?>
<article class="container article-detail">
    <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= e(url()) ?>">Beranda</a><?= icon('chevron') ?><a href="<?= e(url('artikel-list.php')) ?>">Artikel</a><?= icon('chevron') ?><span><?= e($article['category']) ?></span></nav>
    <header class="article-heading"><span class="category"><?= e($article['category']) ?></span><?php if ($tags): ?><div class="article-tags" aria-label="Tag artikel"><?php foreach ($tags as $tag): ?><span>#<?= e($tag['name']) ?></span><?php endforeach; ?></div><?php endif; ?><h1><?= e($article['title']) ?></h1><div class="article-byline"><span class="author-mark">re.</span><div>Redaksi Ruang Etika<small><?= e(format_date($article['published_at'])) ?> <span>·</span> <?= reading_time($article['content']) ?> menit baca</small></div></div></header>
    <figure class="detail-image"><img src="<?= e(thumbnail_url($article['thumbnail'])) ?>" alt="<?= e($article['image_alt']) ?>" width="1400" height="800"><figcaption><?= e($article['image_alt']) ?> · Sumber: <?php if ($source_url): ?><a href="<?= e($source_url) ?>" target="_blank" rel="noopener noreferrer"><?= e($article['image_source']) ?></a><?php else: ?><?= e($article['image_source']) ?><?php endif; ?></figcaption></figure>
    <div class="reading-layout"><aside class="reading-aside"><span class="eyebrow">SELAMAT MEMBACA</span><p>Ambil jeda.<br>Beri ruang untuk<br>perspektif baru.</p><?= icon('book') ?></aside><div class="article-body"><?php foreach (preg_split('/\R\s*\R/u', $article['content']) as $paragraph): if (trim($paragraph) === '') continue; ?><p><?= nl2br(e(trim($paragraph))) ?></p><?php endforeach; ?><div class="article-end-mark" aria-label="Akhir artikel">✳</div><div class="article-bottom-note"><?= icon('heart') ?><p>Etika dalam bermedia sosial tumbuh lewat kebiasaan.<br><strong>Mulai dari satu komentar yang menghargai.</strong></p></div><a class="text-link" href="<?= e(url('artikel-list.php')) ?>">← Kembali ke semua artikel</a></div></div>
</article>
<?php if ($related): ?><section class="related-section section-space"><div class="container"><div class="section-heading"><div><div class="eyebrow">LANJUTKAN PERJALANANMU</div><h2>Bacaan <em>berikutnya.</em></h2></div><a class="text-link" href="<?= e(url('artikel-list.php')) ?>">Semua artikel <?= icon('arrow') ?></a></div><div class="articles-grid library-grid"><?php foreach ($related as $item) { article_card($item); } ?></div></div></section><?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
