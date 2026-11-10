<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan. Hapus artikel melalui daftar artikel.');
}
verify_csrf();
$id = is_string($_POST['id'] ?? null) ? filter_var($_POST['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
if (!$id) {
    flash('error', 'Artikel tidak ditemukan.');
    redirect('admin/articles.php');
}
$statement = db()->prepare('SELECT id, thumbnail FROM articles WHERE id = :id LIMIT 1');
$statement->execute(['id' => $id]);
$article = $statement->fetch();
if (!$article) {
    flash('error', 'Artikel tidak ditemukan atau sudah dihapus.');
    redirect('admin/articles.php');
}
$statement = db()->prepare('DELETE FROM articles WHERE id = :id');
$statement->execute(['id' => $id]);
delete_thumbnail($article['thumbnail']);
flash('success', 'Artikel berhasil dihapus.');
redirect('admin/articles.php');
