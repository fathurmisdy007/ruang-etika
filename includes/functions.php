<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }

    try {
        $connection = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        $connection->exec("SET time_zone = '+07:00'");
    } catch (PDOException $exception) {
        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, "Database belum terhubung. Jalankan MySQL di XAMPP, impor database.sql, lalu periksa pengaturan DB_* di config.php.\n");
            exit(1);
        }
        http_response_code(503);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="id"><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Database belum terhubung</title>'
            . '<body style="font-family:Arial,sans-serif;max-width:620px;margin:12vh auto;padding:24px;line-height:1.7">'
            . '<h1>Database belum terhubung</h1>'
            . '<p>Jalankan MySQL di XAMPP, impor <strong>database.sql</strong> melalui phpMyAdmin, '
            . 'lalu periksa pengaturan <strong>DB_*</strong> di <strong>config.php</strong>.</p>'
            . '<p>Petunjuk lengkap tersedia di README.md pada folder proyek.</p></body></html>';
        exit;
    }

    return $connection;
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return APP_BASE_PATH . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    if (strpbrk($path, "\r\n") !== false) {
        $path = '';
    }
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (!isset($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? null;
    $storedToken = $_SESSION['_csrf'] ?? null;
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !is_string($token)
        || !is_string($storedToken) || !hash_equals($storedToken, $token)) {
        http_response_code(403);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="id"><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>Permintaan tidak valid</title><body style="font-family:Arial,sans-serif;padding:32px;line-height:1.7">'
            . '<h1>Permintaan tidak valid</h1><p>Token keamanan tidak valid atau sesi sudah berakhir. '
            . 'Muat ulang halaman dan coba kembali.</p><a href="' . e(url('admin/login.php'))
            . '">Kembali ke halaman login</a></body></html>';
        exit;
    }
}

function current_admin(): ?array
{
    $admin = $_SESSION['admin'] ?? null;
    return is_array($admin) && isset($admin['id'], $admin['name'], $admin['email'])
        && (int) $admin['id'] > 0 ? $admin : null;
}

function require_admin(): void
{
    header('Cache-Control: no-store, private');
    if (current_admin() === null) {
        flash('error', 'Silakan login untuk mengakses halaman admin.');
        redirect('admin/login.php');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($message) ? $message : null;
}

function categories(): array
{
    return [
        'Etika Komunikasi',
        'Sopan Santun',
        'Menghargai Perbedaan',
        'Tanggung Jawab Konten',
        'Anti-Cyberbullying',
        'Privasi Orang Lain',
    ];
}

function slugify(string $text): string
{
    $text = trim($text);
    if (function_exists('iconv')) {
        $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($converted !== false) {
            $text = $converted;
        }
    }
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'artikel';
}

function normalize_tags(string $input): array
{
    $parts = preg_split('/[,;\r\n]+/u', $input) ?: [];
    $tags = [];
    $seen = [];
    foreach ($parts as $part) {
        $name = trim(preg_replace('/\s+/u', ' ', $part) ?? $part);
        if ($name === '') {
            continue;
        }
        $length = mb_strlen($name);
        if ($length < 2 || $length > 60 || !preg_match('/[\p{L}\p{N}]/u', $name)) {
            throw new RuntimeException('Setiap tag harus berisi huruf atau angka dan panjangnya 2–60 karakter.');
        }
        // Dua nama berbeda dapat menghasilkan slug yang sama; jangan simpan tautan ganda.
        $key = slugify($name);
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $tags[] = $name;
        }
    }
    if (count($tags) > 8) {
        throw new RuntimeException('Maksimal 8 tag untuk setiap artikel.');
    }
    return $tags;
}

function article_tags(int $articleId): array
{
    $statement = db()->prepare(
        'SELECT t.id, t.name, t.slug
         FROM tags t
         INNER JOIN article_tags at ON at.tag_id = t.id
         WHERE at.article_id = :article_id
         ORDER BY t.name'
    );
    $statement->execute(['article_id' => $articleId]);
    return $statement->fetchAll();
}

function sync_article_tags(int $articleId, array $tagNames): void
{
    $delete = db()->prepare('DELETE FROM article_tags WHERE article_id = :article_id');
    $delete->execute(['article_id' => $articleId]);

    $insertTag = db()->prepare(
        'INSERT INTO tags (name, slug) VALUES (:name, :slug)
         ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)'
    );
    $link = db()->prepare(
        'INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)'
    );
    foreach ($tagNames as $name) {
        $tagSlug = slugify($name);
        $insertTag->execute(['name' => $name, 'slug' => $tagSlug]);
        $tagId = (int) db()->lastInsertId();
        if ($tagId < 1) {
            throw new RuntimeException('Tag artikel tidak dapat disimpan.');
        }
        $link->execute(['article_id' => $articleId, 'tag_id' => $tagId]);
    }
}

function excerpt(string $text, int $limit = 140): string
{
    $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    $limit = max(1, $limit);
    $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    if ($length <= $limit) {
        return $text;
    }
    $shortened = function_exists('mb_substr') ? mb_substr($text, 0, $limit, 'UTF-8') : substr($text, 0, $limit);
    $lastSpace = strrpos($shortened, ' ');
    if ($lastSpace !== false && $lastSpace > $limit / 2) {
        $shortened = substr($shortened, 0, $lastSpace);
    }
    return rtrim($shortened, " \t\n\r\0\x0B.,;:") . '…';
}

function reading_time(string $text): int
{
    $words = preg_match_all('/[\p{L}\p{N}]+/u', strip_tags($text));
    return max(1, (int) ceil(($words ?: 0) / 200));
}

function format_date(string $value): string
{
    try {
        $date = new DateTimeImmutable($value);
    } catch (Exception $exception) {
        return '—';
    }
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return $date->format('j') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
}

function thumbnail_url(?string $path): string
{
    $valid = is_string($path) && (
        preg_match('~^assets/images/[a-zA-Z0-9_-]+\.jpg$~D', $path)
        || preg_match('~^uploads/[a-f0-9]{32}\.(?:jpg|png|webp)$~D', $path)
    );
    return url($valid && is_file(APP_ROOT . '/' . $path) ? $path : 'assets/images/hero.jpg');
}

function published_articles(?int $limit = null, string $search = '', string $category = ''): array
{
    $sql = "SELECT * FROM articles WHERE status = 'published'";
    $parameters = [];
    if ($search !== '') {
        $sql .= " AND title LIKE :search ESCAPE '!'";
        $parameters[':search'] = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search) . '%';
    }
    if ($category !== '') {
        $sql .= ' AND category = :category';
        $parameters[':category'] = $category;
    }
    $sql .= ' ORDER BY published_at DESC, id DESC';
    if ($limit !== null) {
        $sql .= ' LIMIT :limit';
    }
    $statement = db()->prepare($sql);
    foreach ($parameters as $name => $value) {
        $statement->bindValue($name, $value, PDO::PARAM_STR);
    }
    if ($limit !== null) {
        $statement->bindValue(':limit', max(0, $limit), PDO::PARAM_INT);
    }
    $statement->execute();
    return $statement->fetchAll();
}

function find_published_article(string $slug): ?array
{
    $statement = db()->prepare("SELECT * FROM articles WHERE slug = :slug AND status = 'published' LIMIT 1");
    $statement->execute([':slug' => $slug]);
    $article = $statement->fetch();
    return $article === false ? null : $article;
}

function upload_thumbnail(array $file): string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('Ukuran gambar maksimal 2 MB.');
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException($error === UPLOAD_ERR_NO_FILE ? 'Pilih gambar thumbnail terlebih dahulu.' : 'Gambar gagal diunggah. Silakan coba kembali.');
    }
    $temporaryPath = $file['tmp_name'] ?? null;
    if (!is_string($temporaryPath) || !is_uploaded_file($temporaryPath)) {
        throw new RuntimeException('Berkas unggahan tidak valid.');
    }
    $size = filesize($temporaryPath);
    if ($size === false || $size < 1 || $size > 2 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar harus lebih dari 0 byte dan maksimal 2 MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $image = @getimagesize($temporaryPath);
    if (!is_string($mime) || !isset($allowedTypes[$mime]) || $image === false
        || ($image['mime'] ?? '') !== $mime || $image[0] < 1 || $image[1] < 1) {
        throw new RuntimeException('Gunakan gambar JPEG, PNG, atau WebP yang valid.');
    }
    $uploadDirectory = APP_ROOT . '/uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        throw new RuntimeException('Folder uploads tidak dapat dibuat. Periksa izin folder proyek.');
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
    if (!move_uploaded_file($temporaryPath, $uploadDirectory . '/' . $filename)) {
        throw new RuntimeException('Gambar tidak dapat disimpan. Periksa izin folder uploads.');
    }
    return 'uploads/' . $filename;
}

function delete_thumbnail(?string $path): void
{
    if (!is_string($path) || !preg_match('~^uploads/[a-f0-9]{32}\.(?:jpg|png|webp)$~D', $path)) {
        return;
    }
    // Jangan hapus gambar yang masih dipakai artikel lain atau saat pengecekan DB gagal.
    try {
        $reference = db()->prepare('SELECT COUNT(*) FROM articles WHERE thumbnail = :thumbnail');
        $reference->execute(['thumbnail' => $path]);
        if ((int) $reference->fetchColumn() > 0) {
            return;
        }
    } catch (Throwable $exception) {
        error_log('Thumbnail cleanup skipped: ' . $exception->getMessage());
        return;
    }
    $directory = realpath(APP_ROOT . '/uploads');
    $resolved = realpath(APP_ROOT . '/' . $path);
    if ($directory !== false && $resolved !== false && dirname($resolved) === $directory && is_file($resolved)) {
        @unlink($resolved);
    }
}
