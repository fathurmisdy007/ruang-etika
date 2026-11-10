<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions.php';

if (current_admin()) {
    redirect('admin/index.php');
}

$email = '';
$error = '';
$notice = get_flash();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : '');
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $locked_until = (int) ($_SESSION['login_locked_until'] ?? 0);

    if ($locked_until > time()) {
        $error = 'Terlalu banyak percobaan. Tunggu ' . ($locked_until - time()) . ' detik sebelum mencoba lagi.';
    } else {
        if ($locked_until > 0) {
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
        }
        $account = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 190 && strlen($password) <= 1024) {
            $statement = db()->prepare('SELECT id, name, email, password FROM admins WHERE email = :email LIMIT 1');
            $statement->execute(['email' => $email]);
            $account = $statement->fetch();
        }
        if ($account && password_verify($password, $account['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin'] = ['id' => (int) $account['id'], 'name' => $account['name'], 'email' => $account['email']];
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
            flash('success', 'Selamat datang, ' . $account['name'] . '.');
            redirect('admin/index.php');
        }
        $_SESSION['login_attempts'] = (int) ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['login_locked_until'] = time() + 60;
            $error = 'Terlalu banyak percobaan. Tunggu 60 detik sebelum mencoba lagi.';
        } else {
            $error = 'Email atau kata sandi tidak sesuai.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk Admin — Etika dalam Bermedia Sosial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Lora:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <style>body{font-family:'DM Sans',sans-serif;background:#f5f6f0;color:#304635;margin:0}*{box-sizing:border-box}.login-layout{display:grid;grid-template-columns:1fr 1fr;min-height:100vh}.login-story{background:#294333;color:#f5f4e9;display:flex;flex-direction:column;justify-content:space-between;padding:55px clamp(35px,7vw,110px)}.login-logo{font-family:'Lora',serif;font-size:32px;letter-spacing:-1.5px}.login-story h1{font-family:'Lora',serif;font-size:clamp(40px,4.4vw,65px);font-weight:400;letter-spacing:-2px;line-height:1.2;max-width:490px;margin:28px 0}.login-story p{font-size:14px;line-height:1.9;color:#c6d1bf;max-width:350px}.login-content{display:flex;align-items:center;justify-content:center;padding:45px 28px}.login-form{width:100%;max-width:360px}.login-form h2{font-family:'Lora',serif;font-size:34px;font-weight:500;letter-spacing:-1px;margin:12px 0}.login-label{font-size:10px;letter-spacing:2px;font-weight:600;color:#859278}.login-muted{font-size:13px;line-height:1.7;color:#889080}.login-form label{display:block;font-size:12px;font-weight:600;margin:24px 0 9px}.login-form input{display:block;width:100%;padding:13px 14px;font:14px 'DM Sans',sans-serif;border:1px solid #d8e0d0;border-radius:5px;background:#fff;color:#304635;outline:none}.login-form input:focus{border-color:#6d8861;box-shadow:0 0 0 3px #e9efdf}.login-submit{width:100%;border:0;background:#2d4b36;color:white;padding:14px;border-radius:5px;font:600 13px 'DM Sans',sans-serif;margin-top:28px;cursor:pointer}.login-submit:hover{background:#203a28}.login-notice{font-size:13px;line-height:1.7;padding:12px 14px;border:1px solid #e6cfc6;border-radius:5px;background:#fff0e9;color:#94513b;margin-top:23px}.login-notice.success{background:#ebf2e4;border-color:#d2dfc6;color:#4d6d3f}.login-back{display:inline-block;font-size:12px;color:#6c805f;text-decoration:none;margin-top:28px}.login-form code{font-size:11px;background:#e9ede0;padding:2px 4px;border-radius:3px}@media(max-width:760px){.login-layout{grid-template-columns:1fr}.login-story{padding:30px 28px;gap:25px}.login-story h1{font-size:37px;margin:12px 0}.login-story .story-bottom{display:none}.login-story p{margin:0}.login-logo{font-size:25px}.login-content{padding:40px 28px 55px}} </style>
</head>
<body>
<main class="login-layout">
    <aside class="login-story">
        <a href="<?= e(url('index.php')) ?>" class="login-logo" style="color:inherit;text-decoration:none">ruangetika<span style="color:#bdce9d">.</span></a>
        <div><span class="login-label" style="color:#becba8">RUANG UNTUK BERBAGI WAWASAN</span><h1>Tulisan yang baik,<br>media sosial<br>yang lebih ramah.</h1><p>Kelola artikel tentang etika berkomunikasi, berbagi, dan menghormati orang lain di media sosial.</p></div>
        <p class="story-bottom" style="font-size:11px;letter-spacing:.5px">ETIKA DALAM BERMEDIA SOSIAL · RUANG ADMIN</p>
    </aside>
    <section class="login-content">
        <form class="login-form" method="post" action="<?= e(url('admin/login.php')) ?>">
            <span class="login-label">SELAMAT DATANG KEMBALI</span>
            <h2>Masuk ke ruang admin.</h2>
            <p class="login-muted">Gunakan akun admin untuk mengelola artikel.</p>
            <?php if ($notice): ?><div class="login-notice <?= ($notice['type'] ?? '') === 'error' ? '' : 'success' ?>" role="status"><?= e($notice['message']) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="login-notice" role="alert"><?= e($error) ?></div><?php endif; ?>
            <?= csrf_field() ?>
            <label for="email">Alamat email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="nama@email.com" maxlength="190" autocomplete="username" required autofocus>
            <label for="password">Kata sandi</label>
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" maxlength="1024" autocomplete="current-password" required>
            <button type="submit" class="login-submit">Masuk ke dashboard <span aria-hidden="true" style="float:right">↗</span></button>
            <p class="login-muted" style="font-size:11px;margin-top:20px">Belum memiliki akun? Buat admin pertama melalui terminal dengan <code>php admin/create-admin.php</code>.</p>
            <a href="<?= e(url('index.php')) ?>" class="login-back">← Kembali ke website</a>
        </form>
    </section>
</main>
</body>
</html>
