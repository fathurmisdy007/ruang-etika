<?php
declare(strict_types=1);

// Pembuatan admin hanya melalui terminal, sehingga tidak ada formulir setup publik.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('Halaman tidak ditemukan.');
}
require_once dirname(__DIR__) . '/includes/functions.php';

function read_admin_input(string $prompt, bool $trim = true): string
{
    fwrite(STDOUT, $prompt);
    $line = fgets(STDIN);
    if ($line === false) {
        throw new RuntimeException('Input dibatalkan.');
    }
    return $trim ? trim($line) : rtrim($line, "\r\n");
}

$locked = false;
try {
    $pdo = db();
    if ((int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0) {
        fwrite(STDERR, "Akun admin sudah tersedia. Setup admin pertama dinonaktifkan.\n");
        exit(1);
    }
    fwrite(STDOUT, "\nBuat admin pertama — Etika dalam Bermedia Sosial\n");
    fwrite(STDOUT, "Tidak ada kata sandi bawaan. Data hanya disimpan setelah semua isian valid.\n\n");
    $name = read_admin_input('Nama admin: ');
    $email = strtolower(read_admin_input('Alamat email: '));
    if ($name === '' || mb_strlen($name) > 100) {
        throw new RuntimeException('Nama wajib diisi, maksimal 100 karakter.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
        throw new RuntimeException('Alamat email tidak valid atau lebih dari 190 karakter.');
    }
    fwrite(STDOUT, "Kata sandi minimal 12 karakter (akan terlihat saat diketik di terminal).\n");
    $password = read_admin_input('Kata sandi baru: ', false);
    $confirmation = read_admin_input('Ulangi kata sandi: ', false);
    if (strlen($password) < 12 || strlen($password) > 72) {
        throw new RuntimeException('Gunakan kata sandi antara 12 dan 72 byte.');
    }
    if (!hash_equals($password, $confirmation)) {
        throw new RuntimeException('Konfirmasi kata sandi tidak sama.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    unset($password, $confirmation);

    // Kunci juga mencegah dua proses terminal membuat admin pertama bersamaan.
    $locked = (int) $pdo->query("SELECT GET_LOCK('etika_sosmed_first_admin', 5)")->fetchColumn() === 1;
    if (!$locked) {
        throw new RuntimeException('Setup lain sedang berjalan. Coba lagi setelah beberapa saat.');
    }
    if ((int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0) {
        throw new RuntimeException('Akun admin sudah tersedia. Setup admin pertama dinonaktifkan.');
    }
    $statement = $pdo->prepare('INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)');
    $statement->execute(['name' => $name, 'email' => $email, 'password' => $hash]);
    $pdo->query("SELECT RELEASE_LOCK('etika_sosmed_first_admin')");
    $locked = false;
    fwrite(STDOUT, "\nAdmin berhasil dibuat. Buka /admin/login.php pada website lokal.\n");
    fwrite(STDOUT, "Email: " . $email . "\nGunakan kata sandi yang baru Anda buat.\n");
} catch (Throwable $exception) {
    if ($locked && isset($pdo)) {
        $pdo->query("SELECT RELEASE_LOCK('etika_sosmed_first_admin')");
    }
    $message = $exception instanceof PDOException
        ? 'Koneksi/operasi database gagal. Impor database.sql dan periksa konfigurasi database.'
        : $exception->getMessage();
    fwrite(STDERR, "\nGagal: " . $message . "\n");
    exit(1);
}
