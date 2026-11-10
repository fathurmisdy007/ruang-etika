<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();

$article = null;
$id = null;
if (isset($_GET['id'])) {
    $id = is_string($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
    if (!$id) {
        flash('error', 'Artikel tidak ditemukan.');
        redirect('admin/articles.php');
    }
    $statement = db()->prepare('SELECT * FROM articles WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $article = $statement->fetch();
    if (!$article) {
        flash('error', 'Artikel tidak ditemukan.');
        redirect('admin/articles.php');
    }
}

$currentTags = $article ? array_column(article_tags((int) $article['id']), 'name') : [];
$values = [
    'title' => $article['title'] ?? '',
    'slug' => $article['slug'] ?? '',
    'content' => $article['content'] ?? '',
    'category' => $article['category'] ?? categories()[0],
    'tags' => implode(', ', $currentTags),
    'image_alt' => $article['image_alt'] ?? '',
    'image_source' => $article['image_source'] ?? '',
    'image_source_url' => $article['image_source_url'] ?? '',
    'status' => $article['status'] ?? 'draft',
    'published_at' => $article ? (new DateTimeImmutable($article['published_at']))->format('Y-m-d\TH:i') : date('Y-m-d\TH:i'),
];
$errors = [];
$tagNames = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach (array_keys($values) as $field) {
        $values[$field] = trim(is_string($_POST[$field] ?? null) ? $_POST[$field] : '');
    }

    if ($values['title'] === '' || mb_strlen($values['title']) > 180) {
        $errors[] = 'Judul wajib diisi dan maksimal 180 karakter.';
    }
    if ($values['content'] === '' || mb_strlen($values['content']) > 100000) {
        $errors[] = 'Isi artikel wajib diisi dan maksimal 100.000 karakter.';
    }
    preg_match_all('/[\p{L}\p{N}]+/u', $values['content'], $wordMatches);
    if ($values['status'] === 'published' && count($wordMatches[0]) < 200) {
        $errors[] = 'Artikel published harus memiliki minimal 200 kata.';
    }
    if (!in_array($values['category'], categories(), true)) {
        $errors[] = 'Pilih kategori yang tersedia.';
    }
    if (!in_array($values['status'], ['draft', 'published'], true)) {
        $errors[] = 'Pilih status draft atau published.';
    }
    try {
        $tagNames = normalize_tags($values['tags']);
        if (count($tagNames) < 2) {
            $errors[] = 'Tambahkan minimal 2 tag yang berbeda.';
        }
    } catch (RuntimeException $exception) {
        $errors[] = $exception->getMessage();
    }
    if ($values['image_alt'] === '' || mb_strlen($values['image_alt']) > 255) {
        $errors[] = 'Alt text gambar wajib diisi dan maksimal 255 karakter.';
    }
    if ($values['image_source'] === '' || mb_strlen($values['image_source']) > 120) {
        $errors[] = 'Sumber gambar wajib diisi dan maksimal 120 karakter.';
    }
    if ($values['image_source_url'] !== '') {
        $sourceScheme = parse_url($values['image_source_url'], PHP_URL_SCHEME);
        if (!filter_var($values['image_source_url'], FILTER_VALIDATE_URL)
            || !in_array($sourceScheme, ['http', 'https'], true)
            || mb_strlen($values['image_source_url']) > 500) {
            $errors[] = 'URL sumber gambar harus berupa alamat HTTP/HTTPS yang valid dan maksimal 500 karakter.';
        }
    }

    $publishedDate = preg_match('/^[1-9][0-9]{3}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/D', $values['published_at'])
        ? DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $values['published_at'])
        : false;
    if (!$publishedDate || $publishedDate->format('Y-m-d\TH:i') !== $values['published_at']) {
        $errors[] = 'Tanggal dan waktu artikel tidak valid.';
    }

    $slugIsAutomatic = $values['slug'] === '';
    $slug = $slugIsAutomatic ? slugify($values['title']) : $values['slug'];
    if (strlen($slug) > 200 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug)) {
        $errors[] = 'Slug maksimal 200 karakter, gunakan huruf kecil a–z, angka, dan tanda hubung.';
    } elseif (!$errors) {
        $slugStatement = db()->prepare('SELECT id FROM articles WHERE slug = :slug AND id <> :id LIMIT 1');
        $slugStatement->execute(['slug' => $slug, 'id' => $id ?: 0]);
        if ($slugStatement->fetch()) {
            if ($slugIsAutomatic) {
                $baseSlug = $slug;
                $suffix = 2;
                do {
                    $slug = substr($baseSlug, 0, 190) . '-' . $suffix++;
                    $slugStatement->execute(['slug' => $slug, 'id' => $id ?: 0]);
                } while ($slugStatement->fetch());
            } else {
                $errors[] = 'Slug sudah digunakan artikel lain. Ubah slug atau kosongkan untuk membuatnya otomatis.';
            }
        }
    }

    $newThumbnail = null;
    if (!$errors && isset($_FILES['thumbnail']) && ($_FILES['thumbnail']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        try {
            $newThumbnail = upload_thumbnail($_FILES['thumbnail']);
        } catch (RuntimeException $exception) {
            $errors[] = $exception->getMessage();
        }
    }

    if (!$errors) {
        $parameters = [
            'title' => $values['title'],
            'slug' => $slug,
            'content' => $values['content'],
            'category' => $values['category'],
            'thumbnail' => $newThumbnail ?? ($article['thumbnail'] ?? 'assets/images/hero.jpg'),
            'image_alt' => $values['image_alt'],
            'image_source' => $values['image_source'],
            'image_source_url' => $values['image_source_url'] !== '' ? $values['image_source_url'] : null,
            'status' => $values['status'],
            'published_at' => $publishedDate->format('Y-m-d H:i:s'),
        ];

        try {
            db()->beginTransaction();
            if ($article) {
                $parameters['id'] = $id;
                $statement = db()->prepare(
                    'UPDATE articles
                     SET title = :title, slug = :slug, content = :content, category = :category,
                         thumbnail = :thumbnail, image_alt = :image_alt, image_source = :image_source,
                         image_source_url = :image_source_url, status = :status,
                         published_at = :published_at, updated_at = NOW()
                     WHERE id = :id'
                );
                $articleId = (int) $id;
            } else {
                $statement = db()->prepare(
                    'INSERT INTO articles
                        (title, slug, content, category, thumbnail, image_alt, image_source,
                         image_source_url, status, published_at)
                     VALUES
                        (:title, :slug, :content, :category, :thumbnail, :image_alt, :image_source,
                         :image_source_url, :status, :published_at)'
                );
            }
            $statement->execute($parameters);
            if (!$article) {
                $articleId = (int) db()->lastInsertId();
            }
            sync_article_tags($articleId, $tagNames);
            db()->commit();
        } catch (Throwable $exception) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            if ($newThumbnail !== null) {
                delete_thumbnail($newThumbnail);
            }
            $errors[] = $exception instanceof PDOException && $exception->getCode() === '23000'
                ? 'Slug atau tag sudah digunakan. Periksa kembali data artikel.'
                : 'Artikel belum dapat disimpan. Periksa koneksi database dan coba lagi.';
            error_log('Article save failed: ' . $exception->getMessage());
        }

        if (!$errors) {
            if ($newThumbnail !== null && $article && $article['thumbnail'] !== $newThumbnail) {
                delete_thumbnail($article['thumbnail']);
            }
            flash('success', $article ? 'Perubahan artikel berhasil disimpan.' : 'Artikel baru berhasil disimpan.');
            redirect('admin/articles.php');
        }
    }
}

$page_title = $article ? 'Edit Artikel' : 'Tulis Artikel';
$active_page = 'articles';
require __DIR__ . '/_header.php';
?>
<div class="admin-heading">
    <div><a class="admin-secondary-link" href="<?= e(url('admin/articles.php')) ?>">← Kembali ke artikel</a><h1 style="margin-top:18px"><?= $article ? 'Sempurnakan tulisan.' : 'Mulai cerita yang bermakna.' ?></h1><p class="admin-muted" style="margin-top:12px">Tulis edukasi yang berfokus pada etika dalam bermedia sosial.</p></div>
</div>
<?php if ($errors): ?><div class="admin-notice error" role="alert"><p style="font-weight:600;margin-bottom:5px">Periksa kembali isian berikut:</p><ul class="admin-error-list"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= e(url('admin/article-form.php' . ($article ? '?id=' . (int) $id : ''))) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
    <div class="admin-form-grid">
        <section class="admin-panel admin-form-panel" aria-label="Konten artikel">
            <h2 style="font-size:21px">Isi tulisan</h2>
            <div class="admin-field"><label for="title">Judul artikel <span style="color:#9a7149">*</span></label><input type="text" id="title" name="title" value="<?= e($values['title']) ?>" maxlength="180" placeholder="Judul yang mengundang rasa ingin tahu" required><p class="admin-muted">Singkat, jelas, dan mencerminkan isi tulisan.</p></div>
            <div class="admin-field"><label for="slug">Slug / alamat artikel</label><input type="text" id="slug" name="slug" value="<?= e($values['slug']) ?>" maxlength="200" pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="dibuat-otomatis-dari-judul"><p class="admin-muted">Kosongkan untuk membuat otomatis. Contoh: bijak-sebelum-membagikan.</p></div>
            <div class="admin-field"><label for="content">Isi artikel <span style="color:#9a7149">*</span></label><textarea id="content" name="content" rows="20" maxlength="100000" placeholder="Tuliskan perspektif, panduan, dan langkah sederhana yang dapat dilakukan pembaca…" required><?= e($values['content']) ?></textarea><p class="admin-muted">Artikel published minimal 200 kata. Pisahkan paragraf dengan satu baris kosong; HTML tidak diproses.</p></div>
        </section>
        <aside style="display:grid;gap:22px;align-content:start">
            <section class="admin-panel admin-form-panel" aria-label="Pengaturan publikasi">
                <h2 style="font-size:21px">Pengaturan artikel</h2>
                <div class="admin-field"><label for="category">Kategori <span style="color:#9a7149">*</span></label><select id="category" name="category" required><?php foreach (categories() as $category): ?><option value="<?= e($category) ?>" <?= $values['category'] === $category ? 'selected' : '' ?>><?= e($category) ?></option><?php endforeach; ?></select></div>
                <div class="admin-field"><label for="tags">Tag <span style="color:#9a7149">*</span></label><input type="text" id="tags" name="tags" value="<?= e($values['tags']) ?>" maxlength="500" placeholder="Empati, Sopan Santun" required><p class="admin-muted">Minimal 2 tag, pisahkan dengan koma. Maksimal 8 tag.</p></div>
                <div class="admin-field"><label for="status">Status <span style="color:#9a7149">*</span></label><select id="status" name="status" required><option value="draft" <?= $values['status'] === 'draft' ? 'selected' : '' ?>>Draft</option><option value="published" <?= $values['status'] === 'published' ? 'selected' : '' ?>>Published</option></select><p class="admin-muted">Artikel published langsung tampil pada website publik.</p></div>
                <div class="admin-field"><label for="published_at">Tanggal & waktu artikel <span style="color:#9a7149">*</span></label><input type="datetime-local" id="published_at" name="published_at" value="<?= e($values['published_at']) ?>" min="1000-01-01T00:00" max="9999-12-31T23:59" required></div>
            </section>
            <section class="admin-panel admin-form-panel" aria-label="Gambar artikel">
                <h2 style="font-size:21px">Gambar unggulan</h2>
                <?php if ($article && $article['thumbnail']): ?><img src="<?= e(thumbnail_url($article['thumbnail'])) ?>" alt="<?= e($article['image_alt']) ?>" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:5px"><p class="admin-muted" style="font-size:12px;margin-top:-12px">Gambar saat ini tetap digunakan jika tidak diganti.</p><?php endif; ?>
                <div class="admin-field"><label for="thumbnail"><?= $article ? 'Ganti gambar' : 'Pilih gambar' ?></label><input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp"><p class="admin-muted">JPG, PNG, atau WebP; maksimal 2 MB. Jika kosong, ilustrasi bawaan digunakan.</p></div>
                <div class="admin-field"><label for="image_alt">Alt text <span style="color:#9a7149">*</span></label><input type="text" id="image_alt" name="image_alt" value="<?= e($values['image_alt']) ?>" maxlength="255" placeholder="Jelaskan isi gambar secara singkat" required></div>
                <div class="admin-field"><label for="image_source">Nama sumber <span style="color:#9a7149">*</span></label><input type="text" id="image_source" name="image_source" value="<?= e($values['image_source']) ?>" maxlength="120" placeholder="Unsplash atau Dokumentasi pribadi" required></div>
                <div class="admin-field"><label for="image_source_url">URL sumber (opsional)</label><input type="url" id="image_source_url" name="image_source_url" value="<?= e($values['image_source_url']) ?>" maxlength="500" placeholder="https://…"></div>
                <?php if ($errors): ?><p class="admin-muted" style="font-size:12px">Pilih kembali file gambar jika sebelumnya sudah dipilih.</p><?php endif; ?>
            </section>
        </aside>
    </div>
    <div class="admin-submit-row"><button class="admin-button" type="submit"><?= $article ? 'Simpan perubahan' : 'Simpan artikel' ?> <span aria-hidden="true">↗</span></button><a class="admin-button secondary" href="<?= e(url('admin/articles.php')) ?>">Batal</a><span class="admin-muted" style="font-size:12px">Kolom bertanda * wajib diisi.</span></div>
</form>
<?php require __DIR__ . '/_footer.php'; ?>

