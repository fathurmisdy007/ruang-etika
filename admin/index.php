<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
$totals = db()->query("SELECT COUNT(*) AS total, COALESCE(SUM(status = 'published'), 0) AS published, COALESCE(SUM(status = 'draft'), 0) AS drafts FROM articles")->fetch();
$recent = db()->query('SELECT id, title, slug, category, thumbnail, status, published_at, updated_at FROM articles ORDER BY updated_at DESC, id DESC LIMIT 5')->fetchAll();
$page_title = 'Dashboard';
$active_page = 'dashboard';
require __DIR__ . '/_header.php';
?>
<div class="admin-heading">
    <div><p class="admin-label" style="margin-bottom:12px">RUANG PENGELOLAAN KONTEN</p><h1>Halo, <?= e($admin['name']) ?>.</h1><p class="admin-muted" style="margin-top:12px">Mulai tulisan baru dan bagikan perspektif yang bermakna.</p></div>
    <a class="admin-button" href="<?= e(url('admin/article-form.php')) ?>"><span aria-hidden="true">＋</span> Tulis artikel</a>
</div>
<section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8" aria-label="Ringkasan artikel">
    <div class="admin-stat"><span class="admin-label">SEMUA ARTIKEL</span><strong><?= (int) $totals['total'] ?></strong><span class="admin-muted">Tulisan dalam koleksi</span></div>
    <div class="admin-stat"><span class="admin-label">DIPUBLIKASIKAN</span><strong><?= (int) $totals['published'] ?></strong><span class="admin-muted">Sudah dapat dibaca publik</span></div>
    <div class="admin-stat"><span class="admin-label">DRAFT</span><strong><?= (int) $totals['drafts'] ?></strong><span class="admin-muted">Menunggu sentuhan terakhir</span></div>
</section>
<section class="admin-panel">
    <div class="admin-panel-title"><h2>Tulisan terbaru</h2><a class="admin-secondary-link" href="<?= e(url('admin/articles.php')) ?>">Semua artikel ↗</a></div>
    <?php if (!$recent): ?><div class="admin-empty">Belum ada artikel. Mulai dengan <a class="admin-secondary-link" href="<?= e(url('admin/article-form.php')) ?>">menulis artikel pertama</a>.</div>
    <?php else: ?>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th scope="col">Artikel</th><th scope="col">Status</th><th scope="col">Diperbarui</th><th scope="col">Aksi</th></tr></thead>
        <tbody><?php foreach ($recent as $article): ?>
        <tr>
            <td><div class="admin-article-cell"><img class="admin-article-thumb" src="<?= e(thumbnail_url($article['thumbnail'])) ?>" alt="" loading="lazy"><div><a class="article-title" href="<?= e(url('admin/article-form.php?id=' . (int) $article['id'])) ?>"><?= e($article['title']) ?></a><span class="admin-muted" style="font-size:11px"><?= e($article['category']) ?></span></div></div></td>
            <td><span class="admin-badge <?= $article['status'] === 'published' ? 'published' : '' ?>"><?= $article['status'] === 'published' ? 'Published' : 'Draft' ?></span></td>
            <td style="white-space:nowrap;color:#7e8878"><?= e(format_date($article['updated_at'])) ?></td>
            <td><a class="admin-secondary-link" href="<?= e(url('admin/article-form.php?id=' . (int) $article['id'])) ?>">Edit ↗</a></td>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
