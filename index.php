<?php
require_once __DIR__ . '/includes/functions.php';
$articles = published_articles();
$featured = null;
foreach ($articles as $article) {
    if ($article['category'] === 'Etika Komunikasi') { $featured = $article; break; }
}
$featured = $featured ?? ($articles[0] ?? null);
$latest = array_values(array_filter($articles, fn($article) => !$featured || $article['id'] !== $featured['id']));
$page_title = 'Etika dalam Bermedia Sosial';
$active_page = 'beranda';
require __DIR__ . '/includes/header.php';
?>
<div id="top"></div>
<section class="container hero">
    <div class="hero-copy">
        <div class="hero-kicker"><span aria-hidden="true">✳</span> Ruang belajar, ruang bertumbuh.</div>
        <h1>Lebih bijak.<br>Lebih peduli.<br>Di media sosial.</h1>
        <p>Setiap komentar dan unggahan punya dampak. Pelajari cara berinteraksi dengan sopan, menghargai perbedaan, dan menjaga batas orang lain.</p>
        <div class="hero-actions"><a class="btn btn-dark" href="<?= e(url('artikel-list.php')) ?>">Mulai membaca <?= icon('arrow-up') ?></a><a class="btn btn-outline" href="<?= e(url('tentang.php')) ?>">Tentang ruang ini</a></div>
    </div>
    <div class="hero-visual reference-collage">
        <div class="collage-wash" aria-hidden="true"></div>
        <div class="portrait-card"><div class="portrait-photo"><img src="<?= e(url('assets/images/communication.jpg')) ?>" alt="Percakapan hangat di antara teman yang sedang belajar bersama" width="650" height="850" fetchpriority="high"><span class="portrait-label">RUANG UNTUK SALING MEMAHAMI</span></div><div class="portrait-caption"><span class="portrait-avatar"><?= icon('heart') ?></span><p>Di balik layar,<br><strong>kita tetap manusia.</strong></p></div><a class="portrait-cta" href="<?= e(url('artikel-list.php?kategori=Etika%20Komunikasi')) ?>">Mulai dari empati <?= icon('arrow') ?></a></div>
        <span class="collage-sticker">✳ ✳ ✳ <span>baik itu menular.</span></span>
        <div class="collage-checks"><span><?= icon('check') ?> Hormati perbedaan</span><span><?= icon('check') ?> Jaga privasi bersama</span></div>
        <a class="mini-photo-card" href="<?= e(url('artikel-list.php?kategori=Tanggung%20Jawab%20Konten')) ?>"><span class="mini-photo-label">ETIKA BERBAGI</span><img src="<?= e(url('assets/images/literacy.jpg')) ?>" alt="Teman mempertimbangkan konten sebelum membagikannya" width="240" height="210"><span class="mini-photo-cta">Pikirkan dampaknya <?= icon('arrow-up') ?></span></a>
        <div class="pause-card"><span>SEBELUM KLIK KIRIM</span><strong>Jeda<br>dulu.</strong><p>Yang kita bagikan,<br>punya dampak.</p></div>
        <div class="collage-tags"><span>Hal baik dimulai dari hal kecil.</span><div><span>+ Empati</span><span>+ Privasi</span><span>+ Tanggung jawab</span></div></div>
    </div>
</section>
<div class="topic-strip"><div class="container"><span class="strip-intro">NILAI KECIL,<br>DAMPAK BERARTI.</span><a href="<?= e(url('artikel-list.php?kategori=Sopan%20Santun')) ?>">Sopan santun.</a><span class="topic-asterisk" aria-hidden="true">✳</span><a href="<?= e(url('artikel-list.php?kategori=Menghargai%20Perbedaan')) ?>">Saling menghargai.</a><span class="topic-asterisk" aria-hidden="true">✳</span><a href="<?= e(url('artikel-list.php?kategori=Privasi%20Orang%20Lain')) ?>">Jaga privasi.</a><span class="topic-asterisk" aria-hidden="true">✳</span><a href="<?= e(url('artikel-list.php?kategori=Tanggung%20Jawab%20Konten')) ?>">Tanggung jawab.</a></div></div>
<section class="container principle-panels" aria-label="Tiga kebiasaan baik bermedia sosial"><a href="<?= e(url('artikel-list.php?kategori=Etika%20Komunikasi')) ?>"><span>01</span><h2>Jeda sebelum komentar.</h2><p>PILIH KATA YANG TETAP MENGHARGAI.</p></a><a href="<?= e(url('artikel-list.php?kategori=Menghargai%20Perbedaan')) ?>"><span>02</span><h2>Hargai perbedaan.</h2><p>BAHAS GAGASAN, HORMATI ORANGNYA.</p></a><a href="<?= e(url('artikel-list.php?kategori=Privasi%20Orang%20Lain')) ?>"><span>03</span><h2>Minta izin dulu.</h2><p>CERITA ORANG LAIN PUNYA BATAS.</p></a></section>
<section class="container learning-heading"><span class="tiny-tag tag-pink">// Belajar jadi lebih bijak //</span><h2>Kebiasaan baik.<br>Media sosial yang lebih ramah.</h2><p>Kita tidak harus selalu sepakat. Kita tetap dapat<br>berkomunikasi dengan sopan dan saling menjaga.</p></section>
<?php if ($featured): ?>
<section class="container featured-section">
    <article class="featured-card">
        <div class="featured-copy"><span class="tiny-tag tag-coral">Pilihan redaksi · <?= e($featured['category']) ?></span><h3><a href="<?= e(url('artikel.php?slug=' . rawurlencode($featured['slug']))) ?>"><?= e($featured['title']) ?></a></h3><p><?= e(excerpt($featured['content'], 180)) ?></p><a class="text-link" href="<?= e(url('artikel.php?slug=' . rawurlencode($featured['slug']))) ?>">Baca selengkapnya <?= icon('arrow') ?></a><div class="featured-byline"><span><?= reading_time($featured['content']) ?> menit baca</span><span>·</span><span><?= e(format_date($featured['published_at'])) ?></span></div></div>
        <div class="featured-art"><a class="featured-image" href="<?= e(url('artikel.php?slug=' . rawurlencode($featured['slug']))) ?>" tabindex="-1" aria-hidden="true"><img src="<?= e(thumbnail_url($featured['thumbnail'])) ?>" alt="" loading="lazy" width="600" height="800"><span class="feature-image-note"><?= icon('heart') ?> Pendapat berbeda. Rasa hormat sama.</span></a><span class="feature-bubble bubble-coral"><?= icon('heart') ?> Mulai dengan empati.</span><div class="feature-message"><?= icon('check') ?><span>Sudahkah kita mencoba memahami?</span></div><div class="feature-mini-card"><img src="<?= e(url('assets/images/hero.jpg')) ?>" alt="Belajar bertukar perspektif bersama" loading="lazy" width="220" height="220"><span>Saling mendengar.</span></div></div>
    </article>
</section>
<?php endif; ?>
<section class="container education-feature" aria-labelledby="education-title"><div class="education-art"><div class="balance-portrait"><img src="<?= e(url('assets/images/hero.jpg')) ?>" alt="Teman saling mendukung dan menolak perundungan di media sosial" loading="lazy" width="600" height="800"><span class="balance-caption"><?= icon('heart') ?> Dukung tanpa mempermalukan.</span></div><div class="wellbeing-note"><span class="wellbeing-dot"></span> HENTIKAN EJEKAN, JANGAN IKUT MENYEBARKAN</div><div class="time-note"><?= icon('shield') ?><span>Jangan ikut menyerang.<small>Jaga martabat setiap orang.</small></span></div></div><div class="education-feature-copy"><span class="tiny-tag tag-blue">Anti-Cyberbullying</span><h2 id="education-title">Jangan ikut<br>menambah luka.</h2><p>Jangan menyukai atau membagikan ejekan. Dukung orang yang menjadi sasaran, hormati pilihannya, dan laporkan komentar yang melecehkan.</p><a class="text-link" href="<?= e(url('artikel-list.php?kategori=Anti-Cyberbullying')) ?>">Pelajari cara bersikap <?= icon('arrow') ?></a></div></section>
<section class="manifesto-section"><div class="container manifesto-inner"><div class="manifesto-copy"><span class="tiny-tag tag-pink">// Tentang ruang ini //</span><p><strong>Media sosial mempertemukan kita.<br>Empati yang mendekatkan.</strong> Ruang Etika menemani kita belajar berkomunikasi dengan sopan, bertanggung jawab, dan menghormati batas orang lain.</p><span class="manifesto-spark" aria-hidden="true">✳</span></div><div class="manifesto-action"><h2>Satu ruang.<br>Banyak perspektif.</h2><p>Tidak harus sepakat untuk saling menghargai.<br>Kenali nilai yang kita jaga bersama.</p><a class="btn btn-light" href="<?= e(url('tentang.php')) ?>">Kenali Ruang Etika <?= icon('arrow-up') ?></a></div></div></section>
<section class="container pause-section" aria-labelledby="pause-title"><div class="pause-section-intro"><span class="tiny-tag tag-outline">// Sebuah pengingat kecil //</span><h2 id="pause-title">Sebelum menekan kirim,</h2></div><p class="giant-word">pikirkan lagi.</p><div class="pause-questions"><span><?= icon('check') ?> Apakah kata-kata ini menghargai?</span><span><?= icon('heart') ?> Sudah mendapat izin?</span><span><?= icon('shield') ?> Apakah ini dapat menyakiti?</span></div></section>
<section class="latest-section section-space">
    <div class="container"><div class="section-heading"><div><span class="tiny-tag tag-coral">Bacaan terbaru</span><h2>Perspektif baru,<br>dimulai dari satu bacaan.</h2></div><a class="btn btn-outline" href="<?= e(url('artikel-list.php')) ?>">Semua artikel <?= icon('arrow-up') ?></a></div>
        <?php if ($latest || $featured): ?><div class="articles-grid home-grid"><?php foreach (array_slice($latest ?: $articles, 0, 4) as $article) { article_card($article); } ?></div><?php else: ?><div class="empty-state"><?= icon('book') ?><h3>Ruang ini segera berisi cerita.</h3><p>Artikel edukasi akan hadir di sini setelah diterbitkan oleh admin.</p></div><?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
