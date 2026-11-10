<?php
if (!function_exists('current_admin') || !current_admin()) {
    http_response_code(404);
    exit('Halaman tidak ditemukan.');
}
?>
</main>
<footer class="admin-footer">
    <div class="admin-wrap" style="display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap"><span>Etika
            Bermedia Sosial · Ruang pengelolaan konten</span><span><?= e($admin['name'] ?? 'Admin') ?></span></div>
</footer>
<script src="<?= e(url('assets/js/main.js')) ?>" defer></script>
</body>

</html>