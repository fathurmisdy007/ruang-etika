<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();

$messages = db()->query(
    'SELECT id, name, email, subject, message, created_at
     FROM contact_messages
     ORDER BY created_at DESC, id DESC'
)->fetchAll();

$page_title = 'Pesan Kontak';
$active_page = 'messages';
require __DIR__ . '/_header.php';
?>
<div class="admin-heading">
    <div><p class="admin-label" style="margin-bottom:12px">KOTAK MASUK</p><h1>Pesan kontak.</h1><p class="admin-muted" style="margin-top:12px">Pesan yang dikirim melalui formulir kontak publik.</p></div>
    <span class="admin-count"><?= count($messages) ?> pesan</span>
</div>
<section class="admin-panel">
    <?php if (!$messages): ?><div class="admin-empty">Belum ada pesan kontak.</div><?php else: ?>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Pengirim</th><th>Subjek dan pesan</th><th>Dikirim</th></tr></thead>
        <tbody><?php foreach ($messages as $message): ?><tr>
            <td><strong><?= e($message['name']) ?></strong><br><a class="admin-secondary-link" href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></td>
            <td style="min-width:360px"><strong><?= e($message['subject']) ?></strong><p class="admin-muted" style="margin-top:8px;white-space:pre-line"><?= e($message['message']) ?></p></td>
            <td style="white-space:nowrap"><?= e(format_date($message['created_at'])) ?></td>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/_footer.php'; ?>

