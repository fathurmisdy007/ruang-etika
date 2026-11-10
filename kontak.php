<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$values = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => ''
];

$errors = [];
$notice = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    foreach (array_keys($values) as $field) {
        $values[$field] = trim(
            is_string($_POST[$field] ?? null)
                ? $_POST[$field]
                : ''
        );
    }

    if (
        $values['name'] === '' ||
        mb_strlen($values['name']) > 100
    ) {
        $errors[] = 'Nama wajib diisi dan maksimal 100 karakter.';
    }

    if (
        !filter_var($values['email'], FILTER_VALIDATE_EMAIL) ||
        mb_strlen($values['email']) > 190
    ) {
        $errors[] = 'Masukkan alamat email yang valid.';
    }

    if (
        $values['subject'] === '' ||
        mb_strlen($values['subject']) > 150
    ) {
        $errors[] = 'Subjek wajib diisi dan maksimal 150 karakter.';
    }

    if (
        $values['message'] === '' ||
        mb_strlen($values['message']) < 10 ||
        mb_strlen($values['message']) > 5000
    ) {
        $errors[] = 'Pesan wajib diisi dengan panjang 10–5.000 karakter.';
    }

    if (!$errors) {
        $statement = db()->prepare(
            'INSERT INTO contact_messages
             (name, email, subject, message)
             VALUES (:name, :email, :subject, :message)'
        );

        $statement->execute($values);

        flash(
            'success',
            'Pesan berhasil dikirim. Terima kasih sudah menghubungi Ruang Etika.'
        );

        redirect('kontak.php');
    }
}

$page_title = 'Kontak';
$page_description = 'Kirim pertanyaan atau masukan kepada pengelola Ruang Etika.';
$active_page = 'kontak';

require __DIR__ . '/includes/header.php';
?>

<style>
    /* Layout halaman kontak */
    .public-site .contact-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr);
        gap: 60px;
        align-items: start;
        padding-top: 40px;
        padding-bottom: 100px;
    }

    /* Kotak utama formulir */
    .public-site .contact-form {
        display: flex;
        flex-direction: column;
        gap: 22px;
        width: 100%;
        min-width: 0;
        padding: 36px;
        background: #f5f8f5;
        border: 1px solid #cfdcd2;
        border-radius: 20px;
        box-shadow: 0 12px 35px rgba(34, 58, 42, 0.07);
        box-sizing: border-box;
    }

    /* Setiap kelompok input */
    .public-site .contact-field {
        display: flex;
        flex-direction: column;
        gap: 9px;
        width: 100%;
    }

    /* Label */
    .public-site .contact-field label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #26382c;
    }

    .public-site .contact-field label span {
        color: #c05d57;
    }

    /* Kotak input dan textarea */
    .public-site .contact-field input,
    .public-site .contact-field textarea {
        display: block;
        width: 100%;
        max-width: 100%;
        padding: 14px 16px;
        background: #ffffff;
        border: 1.5px solid #b9cbbd;
        border-radius: 10px;
        color: #25352b;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.5;
        outline: none;
        box-sizing: border-box;
        transition: border-color .2s, box-shadow .2s;
    }

    .public-site .contact-field input {
        min-height: 50px;
    }

    .public-site .contact-field textarea {
        min-height: 170px;
        resize: vertical;
    }

    /* Efek ketika input aktif */
    .public-site .contact-field input:focus,
    .public-site .contact-field textarea:focus {
        border-color: #477e61;
        box-shadow: 0 0 0 3px rgba(71, 126, 97, .14);
    }

    /* Keterangan panjang pesan */
    .public-site .contact-field small {
        font-size: 11px;
        color: #758177;
    }

    /* Tombol kirim */
    .public-site .contact-form .btn-dark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        align-self: flex-start;
        padding: 14px 25px;
        border: 1px solid #172c21;
        border-radius: 999px;
        background: #172c21;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }

    .public-site .contact-form .btn-dark:hover {
        background: #315943;
    }

    /* Notifikasi */
    .public-site .contact-notice {
        padding: 15px;
        border-radius: 10px;
        font-size: 13px;
        line-height: 1.6;
    }

    .public-site .contact-notice.success {
        background: #e6f3e9;
        border: 1px solid #b5dbc0;
        color: #245c37;
    }

    .public-site .contact-notice.error {
        background: #fff0ed;
        border: 1px solid #efc6bd;
        color: #934536;
    }

    .public-site .contact-notice ul {
        padding-left: 20px;
        list-style: disc;
    }

    /* Responsif tablet - DIPERBAIKI */
    <blade media|%20(max-width%3A%20900px)%20%7B>.public-site .contact-layout {
        grid-template-columns: 1fr;
        gap: 35px;
    }
    }

    /* Responsif HP - DIPERBAIKI */
    <blade media|%20(max-width%3A%20600px)%20%7B>.public-site .contact-layout {
        padding-top: 25px;
        padding-bottom: 65px;
    }

    .public-site .contact-form {
        padding: 22px 18px;
        border-radius: 16px;
    }

    .public-site .contact-form .btn-dark {
        width: 100%;
        align-self: stretch;
    }
    }
</style>

<div id="top"></div>

<!-- HEADER KONTAK -->
<section class="container page-intro contact-intro">

    <div class="eyebrow">
        <span class="dot"></span>
        KONTAK
    </div>

    <h1>
        Mari berbagi<br>
        <em>pertanyaan dan masukan.</em>
    </h1>

    <div class="intro-bottom">
        <p>
            Gunakan formulir ini untuk menyampaikan
            masukan tentang artikel atau pembahasan
            etika dalam bermedia sosial.
        </p>
    </div>

</section>

<!-- KONTEN KONTAK -->
<section class="container contact-layout">

    <!-- INFORMASI KIRI -->
    <aside class="contact-copy">

        <span class="tiny-tag tag-pink">
            // Ruang untuk berdialog //
        </span>

        <h2>
            Pesanmu akan<br>
            dibaca dengan baik.
        </h2>

        <p>
            Isi setiap kolom dengan informasi yang jelas.
            Pesan tersimpan di dashboard admin dan digunakan
            untuk menanggapi pertanyaan atau masukan yang
            kamu kirim.
        </p>

        <div class="contact-note">
            <?= icon('heart') ?>
            <span>
                Sampaikan pendapat dengan sopan dan jangan
                mencantumkan data pribadi sensitif.
            </span>
        </div>

    </aside>

    <!-- FORMULIR KANAN -->
    <form class="contact-form" method="post" action="<?= e(url('kontak.php')) ?>">

        <?= csrf_field() ?>

        <!-- NOTIFIKASI BERHASIL -->
        <?php if ($notice): ?>
        <div class="contact-notice success" role="status">
            <?= e($notice['message']) ?>
        </div>
        <?php endif; ?>

        <!-- NOTIFIKASI ERROR -->
        <?php if ($errors): ?>
        <div class="contact-notice error" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- NAMA -->
        <div class="contact-field">
            <label for="name">
                Nama <span>*</span>
            </label>

            <input id="name" name="name" type="text" maxlength="100" value="<?= e($values['name']) ?>"
                autocomplete="name" placeholder="Masukkan nama kamu" required>
        </div>

        <!-- EMAIL -->
        <div class="contact-field">
            <label for="email">
                Email <span>*</span>
            </label>

            <input id="email" name="email" type="email" maxlength="190" value="<?= e($values['email']) ?>"
                autocomplete="email" placeholder="nama@email.com" required>
        </div>

        <!-- SUBJEK -->
        <div class="contact-field">
            <label for="subject">
                Subjek <span>*</span>
            </label>

            <input id="subject" name="subject" type="text" maxlength="150" value="<?= e($values['subject']) ?>"
                placeholder="Apa yang ingin dibahas?" required>
        </div>

        <!-- PESAN -->
        <div class="contact-field">
            <label for="message">
                Pesan <span>*</span>
            </label>

            <textarea id="message" name="message" rows="7" minlength="10" maxlength="5000"
                placeholder="Tulis pesan atau masukan kamu di sini..." required><?= e($values['message']) ?></textarea>

            <small>10–5.000 karakter.</small>
        </div>

        <!-- TOMBOL KIRIM -->
        <button class="btn btn-dark" type="submit">
            Kirim pesan
            <?= icon('arrow-up') ?>
        </button>

    </form>

</section>

<?php require __DIR__ . '/includes/footer.php'; ?>