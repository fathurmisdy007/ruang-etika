<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode tidak diizinkan. Gunakan tombol Keluar di dashboard.');
}
verify_csrf();
unset($_SESSION['admin']);
session_regenerate_id(true);
flash('success', 'Anda berhasil keluar.');
redirect('admin/login.php');
