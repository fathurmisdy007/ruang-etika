<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
$search = trim(is_string($_GET['q'] ?? null) ? $_GET['q'] : '');
$status = is_string($_GET['status'] ?? null) ? $_GET['status'] : '';
if (!in_array($status, ['', 'draft', 'published'], true)) {
    $status = '';
}
$search = mb_substr($search, 0, 200);
$where = [];
$parameters = [];
if ($search !== '') {
    $where[] = 'title LIKE :search';
    $parameters['search'] = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search) . '%';
    $where[count($where) - 1] .= " ESCAPE '!'";
}
if ($status !== '') {
    $where[] = 'status = :status';
    $parameters['status'] = $status;
}
$sql = 'SELECT id, title, slug, category, thumbnail, status, published_at, updated_at FROM articles';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$statement = db()->prepare($sql . ' ORDER BY updated_at DESC, id DESC');
$statement->execute($parameters);
$articles = $statement->fetchAll();
$page_title = 'Kelola Artikel';
$active_page = 'articles';
require __DIR__ . '/_header.php';
?>
<div class="admin-heading">
    <div><p class="admin-label" style="margin-bottom:12px">KOLEKSI TULISAN</p><h1>Kelola artikel.</h1><p class="admin-muted" style="margin-top:12px">Setiap tulisan membantu membangun interaksi media sosial yang lebih beretika.</p></div>
    <a class="admin-button" href="<?= e(url('admin/article-form.php')) ?>"><span aria-hidden="true">＋</span> Tulis artikel</a>
</div>
<section class="admin-panel">
    <form class="admin-filter" method="get" action="<?= e(url('admin/articles.php')) ?>">
        <div class="admin-field"><label for="search">Cari berdasarkan judul</label><input id="search" type="search" name="q" value="<?= e($search) ?>" placeholder="Cari tulisan…" maxlength="200"></div>
        <div class="admin-field status-field"><label for="status">Status artikel</label><select id="status" name="status"><option value="">Semua status</option><option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option><option value="draft" <?= $status === 'draft' ? 'selected' : '' ?>>Draft</option></select></div>
        <button class="admin-button" type="submit">Terapkan</button>
        <?php if ($search !== '' || $status !== ''): ?><a class="admin-secondary-link" style="padding:12px 4px" href="<?= e(url('admin/articles.php')) ?>">Reset</a><?php endif; ?>
    </form>
    <div class="admin-panel-title"><h2>Daftar artikel</h2><span class="admin-count"><?= count($articles) ?> artikel ditemukan</span></div>
    <?php if (!$articles): ?><div class="admin-empty">Belum ada artikel yang sesuai. Coba pencarian lain atau <a class="admin-secondary-link" href="<?= e(url('admin/article-form.php')) ?>">tulis artikel baru</a>.</div>
    <?php else: ?>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th scope="col">Artikel</th><th scope="col">Status</th><th scope="col">Tanggal artikel</th><th scope="col">Aksi</th></tr></thead>
        <tbody><?php foreach ($articles as $article): ?>
        <tr>
            <td><div class="admin-article-cell"><img class="admin-article-thumb" src="<?= e(thumbnail_url($article['thumbnail'])) ?>" alt="" loading="lazy"><div><a class="article-title" href="<?= e(url('admin/article-form.php?id=' . (int) $article['id'])) ?>"><?= e($article['title']) ?></a><span class="admin-muted" style="font-size:11px"><?= e($article['category']) ?></span></div></div></td>
            <td><span class="admin-badge <?= $article['status'] === 'published' ? 'published' : '' ?>"><?= $article['status'] === 'published' ? 'Published' : 'Draft' ?></span></td>
            <td style="white-space:nowrap;color:#7e8878"><?= e(format_date($article['published_at'])) ?></td>
            <td><div class="admin-actions">
                <?php if ($article['status'] === 'published'): ?><a href="<?= e(url('artikel.php?slug=' . rawurlencode($article['slug']))) ?>">Lihat</a><?php endif; ?>
                <a href="<?= e(url('admin/article-form.php?id=' . (int) $article['id'])) ?>">Edit</a>
                <form method="post" action="<?= e(url('admin/delete.php')) ?>" data-confirm="Hapus artikel ini? Tindakan ini tidak dapat dibatalkan." style="margin:0">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $article['id'] ?>"><button type="submit">Hapus</button>
                </form>
            </div></td>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>
