<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Tentang Saya';
$page_description = 'Tentang Fathur, penulis dan pengelola Ruang Etika yang membagikan edukasi etika dalam bermedia sosial.';
$active_page = 'tentang';
require __DIR__ . '/includes/header.php';
?>
<div id="top"></div>
<section class="container about-hero"><div><div class="eyebrow"><span class="dot"></span> TENTANG SAYA</div><h1>Belajar bersama,<br><em>berinteraksi dengan etika.</em></h1><p>Saya Fathur, penulis dan pengelola Ruang Etika. Website ini saya kembangkan sebagai media edukasi untuk membahas kebiasaan sederhana yang membantu kita saling menghargai di media sosial.</p></div><div class="about-symbol" aria-hidden="true"><?= icon('heart') ?><span>MENULIS DENGAN EMPATI<br>BERBAGI DENGAN TANGGUNG JAWAB</span></div></section>
<figure class="container about-image"><img src="<?= e(url('assets/images/hero.jpg')) ?>" alt="Sekelompok teman berdiskusi dan belajar bersama di depan laptop" width="1400" height="650"></figure>
<section class="container about-story section-space"><div><span class="eyebrow">MENGAPA SAYA MEMBUAT RUANG ETIKA</span><h2>Terhubung itu mudah.<br><em>Menjaga sikap perlu dilatih.</em></h2></div><div><p>Saya melihat komentar, foto, dan unggahan di media sosial dapat memengaruhi perasaan, nama baik, serta kenyamanan orang lain. Karena itu, saya ingin menghadirkan bacaan yang mengajak pembaca berhenti sejenak dan mempertimbangkan dampak tindakannya.</p><p>Artikel di Ruang Etika berfokus pada cara berkomentar dengan sopan, menghargai perbedaan, meminta izin sebelum membagikan cerita orang lain, serta menolak ujaran kebencian dan cyberbullying.</p><p>Saya menyusun materi dengan bahasa yang dekat dengan keseharian. Harapannya, setiap bacaan dapat diterapkan melalui tindakan kecil ketika menulis komentar, mengunggah foto, atau membagikan konten.</p></div></section>
<section class="about-values"><div class="container section-space"><div class="eyebrow">NILAI YANG SAYA JAGA</div><h2>Ruang yang baik,<br><em>dibangun bersama.</em></h2><div class="values-grid"><article><?= icon('heart') ?><h3>Empati lebih dulu</h3><p>Pertimbangkan perasaan orang lain sebelum berkomentar atau membagikan sesuatu.</p></article><article><?= icon('quote') ?><h3>Hargai perbedaan</h3><p>Tanggapi gagasan secara hormat tanpa merendahkan pribadi atau identitasnya.</p></article><article><?= icon('shield') ?><h3>Bertanggung jawab</h3><p>Minta izin, jaga batas pribadi, dan jangan ikut menyebarkan penghinaan.</p></article></div></div></section>
<section class="container closing-section about-closing"><span class="eyebrow">MARI TERHUBUNG</span><h2>Punya pertanyaan<br><em>atau masukan?</em></h2><a class="btn btn-dark" href="<?= e(url('kontak.php')) ?>">Kirim pesan <?= icon('arrow') ?></a></section>
<?php require __DIR__ . '/includes/footer.php'; ?>

