<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan.');
}
verify_csrf();

$id = is_string($_POST['id'] ?? null)
    ? filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
if (!$id) {
    flash('error', 'Tag tidak ditemukan.');
    redirect('admin/tags.php');
}

$minimumCheck = db()->prepare(
    'SELECT at.article_id
     FROM article_tags at
     WHERE at.tag_id = :tag_id
       AND (SELECT COUNT(*) FROM article_tags current_tags WHERE current_tags.article_id = at.article_id) <= 2
     LIMIT 1'
);
$minimumCheck->execute(['tag_id' => $id]);
if ($minimumCheck->fetch()) {
    flash('error', 'Tag tidak dapat dihapus karena ada artikel yang harus tetap memiliki minimal 2 tag.');
    redirect('admin/tags.php');
}

$statement = db()->prepare('DELETE FROM tags WHERE id = :id');
$statement->execute(['id' => $id]);
flash($statement->rowCount() ? 'success' : 'error', $statement->rowCount() ? 'Tag berhasil dihapus.' : 'Tag tidak ditemukan.');
redirect('admin/tags.php');