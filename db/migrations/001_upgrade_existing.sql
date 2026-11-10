-- MIGRASI NON-DESTRUKTIF: untuk database etika_sosmed LAMA (yang belum memiliki tag/kontak).
-- JANGAN dijalankan sebelum BACKUP database lengkap melalui phpMyAdmin -> Export -> SQL.
-- Jangan impor ulang database.sql pada database aktif, karena berisi data contoh.
-- Skrip ini hanya menambah kolom dan tabel yang belum ada, tanpa menghapus isi lama.
-- Jalankan satu kali via phpMyAdmin -> pilih etika_sosmed -> Import file ini.
-- Perubahan DDL MySQL/MariaDB tidak dapat di-rollback; simpan backup sebelum mengimpor.
USE etika_sosmed;

-- Beberapa versi awal belum memiliki metadata sumber dan alt text gambar.
SET @alter_sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'articles' AND COLUMN_NAME = 'image_alt') = 0,
    'ALTER TABLE articles ADD COLUMN image_alt VARCHAR(255) NOT NULL DEFAULT '''' AFTER thumbnail',
    'SELECT 1'
);
PREPARE stmt FROM @alter_sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @alter_sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'articles' AND COLUMN_NAME = 'image_source') = 0,
    'ALTER TABLE articles ADD COLUMN image_source VARCHAR(120) NOT NULL DEFAULT '''' AFTER image_alt',
    'SELECT 1'
);
PREPARE stmt FROM @alter_sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @alter_sql = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'articles' AND COLUMN_NAME = 'image_source_url') = 0,
    'ALTER TABLE articles ADD COLUMN image_source_url VARCHAR(500) DEFAULT NULL AFTER image_source',
    'SELECT 1'
);
PREPARE stmt FROM @alter_sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS tags (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) NOT NULL,
    slug VARCHAR(80) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY tags_name_unique (name),
    UNIQUE KEY tags_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS article_tags (
    article_id INT UNSIGNED NOT NULL,
    tag_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    CONSTRAINT article_tags_article_fk FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT article_tags_tag_fk FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY contact_messages_created_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Untuk artikel lama yang belum punya dua tag: TAMBAH RELASI TAG SAJA.
-- Artikel, akun admin, gambar, isi, status, dan tanggal tidak diubah.
INSERT IGNORE INTO tags (name, slug) VALUES
    ('Etika Bermedia Sosial', 'etika-bermedia-sosial'),
    ('Interaksi Beretika', 'interaksi-beretika');

INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id
FROM articles a
CROSS JOIN tags t
WHERE t.slug IN ('etika-bermedia-sosial', 'interaksi-beretika')
  AND (SELECT COUNT(*) FROM article_tags at WHERE at.article_id = a.id) < 2;

-- Sesudah migrasi, cek artikel lama di dashboard: isi alt text dan sumber gambar
-- yang belum terisi, serta pastikan minimal 3 artikel published memiliki 200+ kata.
