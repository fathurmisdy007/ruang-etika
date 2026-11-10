<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();

$editTag = null;
$errors = [];
$name = '';

if (isset($_GET['edit'])) {
    $editId = is_string($_GET['edit']) ? filter_var($_GET['edit'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
    if ($editId) {
        $statement = db()->prepare('SELECT id, name, slug FROM tags WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $editId]);
        $editTag = $statement->fetch() ?: null;
        $name = $editTag['name'] ?? '';
    }
    if (!$editTag) {
        flash('error', 'Tag tidak ditemukan.');
        redirect('admin/tags.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = is_string($_POST['id'] ?? null) && $_POST['id'] !== ''
        ? filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;
    $name = trim(preg_replace('/\s+/u', ' ', is_string($_POST['name'] ?? null) ? $_POST['name'] : '') ?? '');
    if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 60 || !preg_match('/[\p{L}\p{N}]/u', $name)) {
        $errors[] = 'Nama tag wajib berisi huruf atau angka dan panjangnya 2–60 karakter.';
    }
    if (!$errors) {
        $slug = slugify($name);
        try {
            if ($id) {
                $exists = db()->prepare('SELECT id FROM tags WHERE id = :id LIMIT 1');
                $exists->execute(['id' => $id]);
                if (!$exists->fetch()) {
                    throw new RuntimeException('Tag tidak ditemukan.');
                }
                $statement = db()->prepare('UPDATE tags SET name = :name, slug = :slug WHERE id = :id');
                $statement->execute(['name' => $name, 'slug' => $slug, 'id' => $id]);
                flash('success', 'Tag berhasil diperbarui.');
            } else {
                $statement = db()->prepare('INSERT INTO tags (name, slug) VALUES (:name, :slug)');
                $statement->execute(['name' => $name, 'slug' => $slug]);
                flash('success', 'Tag berhasil ditambahkan.');
            }
            redirect('admin/tags.php');
        } catch (PDOException $exception) {
            $errors[] = $exception->getCode() === '23000'
                ? 'Nama tag tersebut sudah digunakan.'
                : 'Tag belum dapat disimpan.';
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

$tags = db()->query(
    'SELECT t.id, t.name, t.slug, COUNT(at.article_id) AS article_count
     FROM tags t
     LEFT JOIN article_tags at ON at.tag_id = t.id
     GROUP BY t.id, t.name, t.slug
     ORDER BY t.name'
)->fetchAll();

$page_title = 'Kelola Tag';
$active_page = 'tags';
require __DIR__ . '/_header.php';
?>
<div class="admin-heading">
    <div><p class="admin-label" style="margin-bottom:12px">TAKSONOMI ARTIKEL</p><h1>Kelola tag.</h1><p class="admin-muted" style="margin-top:12px">Tambah atau ubah tag yang membantu pembaca mengenali topik artikel.</p></div>
</div>
<?php if ($errors): ?><div class="admin-notice error" role="alert"><ul class="admin-error-list"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="admin-form-grid">
    <section class="admin-panel admin-form-panel" aria-label="Daftar tag">
        <div class="admin-panel-title" style="padding:0 0 18px"><h2><?= count($tags) ?> tag</h2></div>
        <?php if (!$tags): ?><div class="admin-empty">Belum ada tag.</div><?php else: ?>
        <div class="admin-table-wrap"><table class="admin-table">
            <thead><tr><th>Nama</th><th>Slug</th><th>Artikel</th><th>Aksi</th></tr></thead>
            <tbody><?php foreach ($tags as $tag): ?><tr>
                <td><strong><?= e($tag['name']) ?></strong></td>
                <td><?= e($tag['slug']) ?></td>
                <td><?= (int) $tag['article_count'] ?></td>
                <td><div class="admin-actions"><a href="<?= e(url('admin/tags.php?edit=' . (int) $tag['id'])) ?>">Edit</a><form method="post" action="<?= e(url('admin/tag-delete.php')) ?>" data-confirm="Hapus tag ini? Tag akan dilepas dari artikel terkait."><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $tag['id'] ?>"><button type="submit">Hapus</button></form></div></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
        <?php endif; ?>
    </section>
    <aside>
        <form class="admin-panel admin-form-panel" method="post" action="<?= e(url('admin/tags.php')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $editTag ? (int) $editTag['id'] : '' ?>">
            <h2 style="font-size:21px"><?= $editTag ? 'Edit tag' : 'Tambah tag' ?></h2>
            <div class="admin-field"><label for="name">Nama tag</label><input id="name" name="name" type="text" maxlength="60" value="<?= e($name) ?>" placeholder="Contoh: Empati" required><p class="admin-muted">Slug dibuat otomatis dari nama tag.</p></div>
            <button class="admin-button" type="submit"><?= $editTag ? 'Simpan perubahan' : 'Tambah tag' ?></button>
            <?php if ($editTag): ?><a class="admin-secondary-link" href="<?= e(url('admin/tags.php')) ?>">Batal mengedit</a><?php endif; ?>
        </form>
    </aside>
</div>
<?php require __DIR__ . '/_footer.php'; ?>

