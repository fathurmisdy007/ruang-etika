<?php
/** Small, dependency-free outline icons used by the templates. */
function icon(string $name, string $class = ''): string
{
    $paths = [
        'arrow' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'arrow-up' => '<path d="M5 19 19 5M5 5h14v14"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/>',
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'close' => '<path d="m6 6 12 12M6 18 18 6"/>',
        'book' => '<path d="M12 5C8 2 3 3 3 3v16s5-1 9 2c4-3 9-2 9-2V3s-5-1-9 2Zm0 0v16"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/>',
        'shield' => '<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"/><path d="m8 12 3 3 5-6"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'chevron' => '<path d="m9 5 7 7-7 7"/>',
        'spark' => '<path d="m12 2 2.5 7.5L22 12l-7.5 2.5L12 22l-2.5-7.5L2 12l7.5-2.5L12 2Z"/>',
        'quote' => '<path d="M10 5H3v8h5c0 3-2 4-4 5m17-13h-7v8h5c0 3-2 4-4 5"/>',
    ];
    return '<svg class="icon ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? $paths['arrow']) . '</svg>';
}

function article_card(array $article, string $extraClass = ''): void
{
    $href = url('artikel.php?slug=' . rawurlencode($article['slug']));
    $tags = article_tags((int) $article['id']);
    ?>
    <article class="article-card <?= e($extraClass) ?>">
        <a class="card-image" href="<?= e($href) ?>" tabindex="-1">
            <img src="<?= e(thumbnail_url($article['thumbnail'])) ?>" alt="<?= e($article['image_alt'] ?: 'Gambar unggulan artikel ' . $article['title']) ?>" loading="lazy" width="760" height="520">
            <span class="image-arrow"><?= icon('arrow-up') ?></span>
        </a>
        <div class="card-meta"><span class="category"><?= e($article['category']) ?></span><span><?= reading_time($article['content']) ?> menit baca</span></div>
        <?php if ($tags): ?><div class="article-tags" aria-label="Tag artikel"><?php foreach ($tags as $tag): ?><span>#<?= e($tag['name']) ?></span><?php endforeach; ?></div><?php endif; ?>
        <h3><a href="<?= e($href) ?>"><?= e($article['title']) ?></a></h3>
        <p><?= e(excerpt($article['content'], 132)) ?></p>
        <div class="card-date"><?= e(format_date($article['published_at'])) ?><span class="small-line"></span>Ruang Etika</div>
    </article>
    <?php
}
