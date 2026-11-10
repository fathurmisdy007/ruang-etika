<?php
declare(strict_types=1);

if (!function_exists('current_admin')) {
    http_response_code(404);
    exit('Halaman tidak ditemukan.');
}

require_admin();

$page_title = $page_title ?? 'Dashboard';
$active_page = $active_page ?? 'dashboard';
$notice = get_flash();
$admin = current_admin();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">

    <title><?= e($page_title) ?> — Ruang Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Lora:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">

    <style>
        /* ====================================
           ADMIN BASE
        ==================================== */

        .admin-shell {
            font-family: 'DM Sans', sans-serif;
            background: #f6f7f3;
            color: #263e34;
            min-height: 100vh;
        }

        .admin-shell h1,
        .admin-shell h2 {
            font-family: 'Lora', serif;
        }

        .admin-shell * {
            box-sizing: border-box;
        }

        .admin-shell button {
            font-family: inherit;
            cursor: pointer;
        }

        .admin-shell a {
            text-decoration: none;
        }

        .admin-wrap {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
        }

        /* ====================================
           STICKY NAVBAR - FITUR BARU
        ==================================== */

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 1000;

            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e0e6df;

            box-shadow: 0 2px 12px rgba(38, 62, 52, 0.04);
        }

        .admin-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 22px 0;
        }

        .admin-brand {
            font-family: 'Lora', serif;
            font-size: 23px;
            font-weight: 600;
            letter-spacing: -.9px;
        }

        .admin-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            color: #789078;
        }

        /* ====================================
           NAVIGATION
        ==================================== */

        .admin-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .admin-nav a,
        .admin-nav button {
            font-size: 13px;
            padding: 10px 14px;
            border-radius: 6px;
            color: #607062;
        }

        .admin-nav a.is-active {
            background: #edf1e9;
            color: #294b37;
            font-weight: 700;
        }

        .admin-nav a:hover,
        .admin-nav button:hover {
            background: #f1f4ed;
        }

        .admin-nav a,
        .admin-nav button {
            white-space: nowrap;
        }

        /* ====================================
           CONTENT
        ==================================== */

        .admin-content {
            padding: 46px 0 65px;
        }

        .admin-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            margin-bottom: 30px;
        }

        .admin-heading h1 {
            font-size: clamp(30px, 4vw, 40px);
            letter-spacing: -1.2px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .admin-heading>div,
        .admin-form-panel {
            min-width: 0;
        }

        .admin-muted {
            color: #798078;
            font-size: 14px;
            line-height: 1.7;
        }

        /* ====================================
           BUTTON
        ==================================== */

        .admin-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 5px;
            background: #2b4937;
            color: #fff !important;
            border: 1px solid #2b4937;
            padding: 12px 18px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
        }

        .admin-button:hover {
            background: #1d3828;
        }

        .admin-button.secondary {
            background: #fff;
            color: #35523d !important;
            border-color: #dce2d8;
        }

        .admin-button.danger {
            background: #fff;
            color: #a44536 !important;
            border-color: #ead6d1;
        }

        /* ====================================
           PANELS
        ==================================== */

        .admin-panel {
            background: #fff;
            border: 1px solid #e1e6dc;
            border-radius: 10px;
            overflow: hidden;
        }

        .admin-panel-title {
            padding: 22px 25px;
            border-bottom: 1px solid #e9ede5;

            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
        }

        .admin-panel-title h2 {
            font-size: 21px;
        }

        /* ====================================
           TABLE
        ==================================== */

        .admin-table-wrap {
            overflow-x: auto;
        }

        .admin-table {
            border-collapse: collapse;
            width: 100%;
            text-align: left;
            font-size: 13px;
        }

        .admin-table th {
            padding: 14px 22px;
            background: #fafbf8;
            color: #81897e;
            font-weight: 500;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .admin-table td {
            padding: 19px 22px;
            border-top: 1px solid #edf0e8;
            vertical-align: middle;
        }

        .admin-table .article-title {
            font-weight: 600;
            font-size: 14px;
            max-width: 390px;
            display: block;
            line-height: 1.5;
        }

        .admin-badge {
            display: inline-block;
            padding: 5px 9px;
            font-size: 11px;
            border-radius: 5px;
            white-space: nowrap;
            background: #f2efe6;
            color: #86713d;
        }

        .admin-badge.published {
            background: #edf3e8;
            color: #42623c;
        }

        /* ====================================
           ACTIONS
        ==================================== */

        .admin-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            white-space: nowrap;
        }

        .admin-actions a,
        .admin-actions button {
            font-size: 12px;
            font-weight: 600;
            color: #50664b;
            cursor: pointer;
        }

        .admin-actions button {
            color: #a85143;
        }

        /* ====================================
           NOTICES
        ==================================== */

        .admin-notice {
            border: 1px solid #d2e0c5;
            background: #eff6e9;
            color: #3b6031;
            border-radius: 7px;
            padding: 14px 18px;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .admin-notice.error {
            background: #fff1ed;
            border-color: #efd3cb;
            color: #994a3a;
        }

        /* ====================================
           FORM FIELDS
        ==================================== */

        .admin-field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        .admin-field input:not([type=file]),
        .admin-field select,
        .admin-field textarea {
            display: block;
            width: 100%;
            border: 1px solid #dce2d7;
            border-radius: 5px;
            background: #fff;
            padding: 12px 13px;
            color: #2e4436;
            font: inherit;
            font-size: 14px;
            outline: none;
        }

        .admin-field input:focus,
        .admin-field select:focus,
        .admin-field textarea:focus {
            border-color: #6d8861;
            box-shadow: 0 0 0 3px #edf2e8;
        }

        .admin-field textarea {
            resize: vertical;
            line-height: 1.8;
        }

        .admin-field .admin-muted {
            font-size: 12px;
            margin-top: 7px;
        }

        .admin-field input[type=file] {
            display: block;
            width: 100%;
            font-size: 12px;
            color: #687560;
        }

        .admin-field input[type=file]::file-selector-button {
            border: 1px solid #dce2d7;
            border-radius: 4px;
            background: #f4f6ef;
            padding: 9px 12px;
            margin-right: 10px;
            color: #3f563c;
            cursor: pointer;
        }

        /* ====================================
           OTHER COMPONENTS
        ==================================== */

        .admin-empty {
            text-align: center;
            padding: 55px 22px;
            color: #7e8979;
            font-size: 14px;
        }

        .admin-stat {
            padding: 26px;
            background: #fff;
            border: 1px solid #e1e6dc;
            border-radius: 9px;
        }

        .admin-stat strong {
            display: block;
            font-family: 'Lora', serif;
            font-size: 39px;
            line-height: 1.2;
            margin: 15px 0 4px;
        }

        .admin-stat .admin-label {
            letter-spacing: 1px;
        }

        .admin-footer {
            padding: 22px 0;
            border-top: 1px solid #e1e6dc;
            color: #899080;
            font-size: 12px;
        }

        .admin-filter {
            padding: 20px 22px;
            border-bottom: 1px solid #e9ede5;
            display: flex;
            gap: 12px;
            align-items: end;
            flex-wrap: wrap;
        }

        .admin-filter .admin-field {
            flex: 1;
            min-width: 180px;
        }

        .admin-filter .admin-field.status-field {
            flex: 0 1 180px;
        }

        .admin-filter label {
            font-size: 11px;
        }

        .admin-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 24px;
        }

        .admin-form-panel {
            padding: 26px;
            display: grid;
            gap: 24px;
            align-content: start;
        }

        .admin-error-list {
            list-style: disc;
            padding-left: 18px;
            line-height: 1.9;
        }

        .admin-article-thumb {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 5px;
            flex-shrink: 0;
        }

        .admin-article-cell {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-count {
            font: 500 12px 'DM Sans', sans-serif;
            color: #85907e;
        }

        .admin-secondary-link {
            font-size: 13px;
            color: #587348;
        }

        .admin-submit-row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            padding-top: 25px;
        }

        /* ====================================
           RESPONSIVE TABLET
        ==================================== */

        <blade media|%20(max-width%3A%20800px)%20%7B>.admin-form-grid {
            grid-template-columns: 1fr;
        }

        .admin-topbar-inner {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding-top: 20px;
            padding-bottom: 14px;
        }

        .admin-nav {
            width: 100%;
            gap: 2px;
        }

        .admin-nav a,
        .admin-nav button {
            padding: 8px 10px;
        }

        .admin-nav .public-link {
            margin-left: auto;
        }

        .admin-heading {
            align-items: flex-start;
        }

        .admin-wrap {
            width: calc(100% - 32px);
        }

        .admin-content {
            padding-top: 30px;
        }

        .admin-table td,
        .admin-table th {
            padding: 14px 16px;
        }

        .admin-form-panel {
            padding: 21px;
        }
        }

        /* ====================================
           RESPONSIVE MOBILE
        ==================================== */

        <blade media|%20(max-width%3A%20540px)%20%7B>.admin-heading {
            flex-direction: column;
            gap: 18px;
        }

        .admin-heading>.admin-button {
            align-self: flex-start;
        }

        .admin-panel-title {
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 10px;
        }

        .admin-nav .public-link {
            margin-left: 0;
        }
        }
    </style>
</head>

<body class="admin-shell">

    <!-- STICKY ADMIN NAVBAR -->
    <header class="admin-topbar">

        <div class="admin-wrap admin-topbar-inner">

            <!-- LOGO -->
            <a class="admin-brand" href="<?= e(url('admin/index.php')) ?>">
                ruangetika<span style="color:#7d946a">.</span>

                <span class="admin-label"
                    style="font:600 10px 'DM Sans',sans-serif;letter-spacing:1.5px;margin-left:10px">
                    RUANG ADMIN
                </span>
            </a>

            <!-- NAVIGATION -->
            <nav class="admin-nav" aria-label="Navigasi admin">

                <a class="<?= $active_page === 'dashboard' ? 'is-active' : '' ?>"
                    href="<?= e(url('admin/index.php')) ?>">
                    Dashboard
                </a>

                <a class="<?= $active_page === 'articles' ? 'is-active' : '' ?>"
                    href="<?= e(url('admin/articles.php')) ?>">
                    Artikel
                </a>

                <a class="<?= $active_page === 'tags' ? 'is-active' : '' ?>" href="<?= e(url('admin/tags.php')) ?>">
                    Tag
                </a>

                <a class="<?= $active_page === 'messages' ? 'is-active' : '' ?>"
                    href="<?= e(url('admin/messages.php')) ?>">
                    Pesan
                </a>

                <a class="public-link" href="<?= e(url('index.php')) ?>">
                    Lihat website ↗
                </a>

                <!-- LOGOUT -->
                <form method="post" action="<?= e(url('admin/logout.php')) ?>" style="margin:0">
                    <?= csrf_field() ?>

                    <button type="submit">
                        Keluar
                    </button>
                </form>

            </nav>

        </div>

    </header>

    <!-- MAIN CONTENT -->
    <main class="admin-wrap admin-content">

        <?php if ($notice): ?>
        <div role="status" class="admin-notice <?= ($notice['type'] ?? '') === 'error' ? 'error' : '' ?>">
            <?= e($notice['message']) ?>
        </div>
        <?php endif; ?>