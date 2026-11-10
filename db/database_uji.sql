-- IMPORT KHUSUS UJI PEMULIHAN: menggunakan database terpisah etika_sosmed_uji.
-- Tidak menulis ke database utama etika_sosmed.
-- Ruang Etika | Ekspor database untuk instalasi XAMPP baru.
-- Tidak menyertakan akun atau password admin bawaan.

CREATE DATABASE IF NOT EXISTS etika_sosmed_uji
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE etika_sosmed_uji;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY admins_email_unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    category VARCHAR(80) NOT NULL,
    thumbnail VARCHAR(255) NOT NULL DEFAULT 'assets/images/hero.jpg',
    image_alt VARCHAR(255) NOT NULL,
    image_source VARCHAR(120) NOT NULL,
    image_source_url VARCHAR(500) DEFAULT NULL,
    published_at DATETIME NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY articles_slug_unique (slug),
    KEY articles_public_index (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
INSERT IGNORE INTO articles
    (title, slug, content, category, thumbnail, image_alt, image_source, image_source_url, published_at, status)
VALUES
(
    'Di balik layar, ada manusia: seni berkomentar dengan empati',
    'seni-berkomentar-dengan-empati',
    'Sebuah komentar mungkin hanya membutuhkan beberapa detik untuk ditulis, tetapi bisa tinggal lama dalam pikiran orang yang membacanya. Di media sosial, kita mudah melihat foto profil sebagai akun semata. Padahal, di balik setiap layar ada manusia dengan pengalaman, perasaan, dan batas yang tidak selalu kita ketahui.

Empati dimulai dari jeda kecil sebelum menekan tombol kirim. Tanyakan kepada diri sendiri: apakah komentar ini membantu percakapan? Apakah saya akan mengucapkannya dengan cara yang sama jika orang itu berdiri di hadapan saya? Jeda tersebut memberi ruang untuk memilih kata yang lebih jernih, terutama ketika kita sedang kesal.

Berbeda pendapat tanpa merendahkan

Kita tidak perlu sepakat dengan semua unggahan. Perbedaan pandangan justru dapat memperluas pemahaman ketika disampaikan dengan hormat. Fokuskan tanggapan pada gagasan atau tindakan yang sedang dibahas, bukan pada penampilan, asal daerah, keluarga, atau identitas pembuat unggahan. Kritik yang jelas lebih berguna daripada ejekan yang mendapat banyak tanda suka.

Alih-alih menulis bahwa seseorang tidak tahu apa-apa, jelaskan bagian yang menurut kita kurang tepat dan berikan alasan. Misalnya, kita dapat mengatakan bahwa informasi pada paragraf tertentu berbeda dengan sumber yang kita baca. Undang orang tersebut memeriksa sumber bersama. Kalimat seperti ini membuka percakapan tanpa menuntut orang lain kehilangan harga diri.

Membaca konteks sebelum merespons

Potongan video, tangkapan layar, dan candaan sering kehilangan konteks ketika dibagikan ulang. Baca keterangan secara utuh, periksa percakapan sebelumnya, dan sadari bahwa nada tulisan dapat ditafsirkan berbeda. Jika maksudnya belum jelas, bertanyalah dengan tenang. Tidak semua ketidakjelasan perlu langsung dianggap sebagai serangan pribadi atau niat buruk.

Saat emosi naik, kita boleh menunda respons. Simpan draf, tinggalkan layar sebentar, lalu baca ulang ketika pikiran lebih tenang. Kita juga tidak wajib membalas setiap komentar. Mengakhiri percakapan yang berulang, membatasi interaksi, atau menggunakan fitur lapor untuk pelecehan merupakan cara menjaga ruang yang sehat, bukan kegagalan berdiskusi.

Menjadi bagian dari suasana yang aman

Empati juga berlaku ketika kita hanya menjadi penonton. Jangan ikut menyebarkan penghinaan atau menjadikan seseorang bahan tontonan. Bila aman, beri dukungan yang tidak menambah paparan terhadap orang yang diserang. Hindari mengunggah ulang materi yang mempermalukan korban hanya untuk menyatakan bahwa kita tidak setuju dengan pelakunya.

Jika komentar kita melukai orang lain, akui dampaknya secara jelas. Hapus atau perbaiki bagian yang bermasalah dan sampaikan permintaan maaf tanpa menyalahkan pembaca karena terlalu sensitif. Perubahan sikap lebih berarti daripada pembelaan panjang. Kita dapat belajar dari kesalahan tanpa menjadikan orang yang terluka sebagai pihak yang harus menenangkan kita.

Ruang komentar yang ramah dibangun dari pilihan sehari-hari: membaca dengan teliti, mengajukan pertanyaan, memberi koreksi yang berguna, dan berhenti saat percakapan tidak lagi sehat. Sebelum mengirim komentar berikutnya, sisakan satu detik untuk mengingat manusianya. Kata-kata yang kita pilih turut menentukan apakah orang lain merasa aman untuk ikut berbicara.',
    'Etika Komunikasi',
    'assets/images/communication.jpg',
    'Dua teman berbincang dan saling mendengarkan dengan penuh perhatian',
    'Unsplash',
    'https://images.unsplash.com/photo-1522202176988-66273c2fd55f',
    '2026-11-05 09:00:00',
    'published'
),
(
    'Sebelum mengunggah: pikirkan dampaknya pada orang lain',
    'bertanggung-jawab-sebelum-mengunggah',
    'Setiap unggahan memiliki audiens, tetapi dampaknya tidak selalu berhenti pada orang yang kita bayangkan. Foto teman, cerita di grup, atau potongan percakapan dapat menyebar lebih jauh setelah dibagikan. Tanggung jawab bermedia sosial dimulai ketika kita mempertimbangkan siapa yang ikut terbawa dalam cerita, bukan hanya seberapa menarik unggahan tersebut bagi pengikut kita.

Ambil jeda sebelum menekan tombol unggah. Tanyakan apakah konten ini bermanfaat, apakah ada pihak yang mungkin dipermalukan, dan apakah mereka sudah mengetahui rencana publikasinya. Unggahan yang mengundang tawa belum tentu menyenangkan bagi orang yang menjadi objeknya. Kedekatan dengan seseorang juga tidak memberi kita hak untuk menentukan batas nyamannya.

Cerita sendiri, hak orang lain

Kita boleh menceritakan pengalaman pribadi tanpa membuka seluruh rincian kehidupan orang lain. Saat menulis tentang pertengkaran, hindari menyertakan nama, wajah, tempat kerja, atau petunjuk yang membuat orang tersebut mudah dikenali. Pikirkan tujuan unggahan: apakah kita ingin menjelaskan pengalaman atau mengajak pengikut ikut menyerang seseorang? Kekecewaan tidak membenarkan mempermalukan pihak lain di depan publik.

Untuk foto bersama, minta persetujuan sebelum mengunggah dan menandai akun. Jelaskan siapa yang dapat melihatnya. Jika seorang teman keberatan, pilih foto lain atau batalkan unggahan. Hormati juga pembuat karya: gunakan materi sesuai izin dan cantumkan kredit yang pantas. Menemukan karya di media sosial tidak berarti kita boleh mengakuinya sebagai milik sendiri.

Berbagi ulang tetap pilihan kita

Meneruskan konten bukan tindakan tanpa tanggung jawab. Ketika kita membagikan ejekan, tuduhan, atau video orang dalam situasi memalukan, kita membantu memperluas dampaknya. Baca konteks percakapan dan pertimbangkan kepentingan orang yang ditampilkan. Banyaknya orang yang sudah membagikan tidak menjadi alasan untuk ikut memperbesar kerugian.

Jika ingin mengingatkan tentang perilaku yang buruk, sampaikan pelajarannya tanpa menyebarkan kembali penghinaan dan identitas orang yang terdampak. Hindari ajakan mendatangi akun seseorang, memberi komentar massal, atau menebak kehidupan pribadinya. Kritik terhadap tindakan dapat disampaikan dengan jelas tanpa mengubah unggahan menjadi undangan untuk menyerang.

Bertanggung jawab setelah mengunggah

Sesudah dipublikasikan, dengarkan keberatan dengan terbuka. Bila ada orang yang meminta fotonya dihapus, tanggapi dengan hormat dan lakukan penyesuaian yang diperlukan. Jika unggahan kita keliru atau menyakiti, perbaiki di tempat yang dapat dilihat audiens semula dan sampaikan permintaan maaf yang jelas. Jangan membebankan kepada orang yang terluka kewajiban untuk memaklumi kita.

Kebebasan berekspresi selalu bertemu dengan hak orang lain untuk diperlakukan secara bermartabat. Sebelum membagikan sesuatu hari ini, pilih kata, gambar, dan konteks yang dapat kita pertanggungjawabkan. Unggahan yang baik bukan hanya menarik perhatian, tetapi juga menghargai orang yang terlibat di dalamnya.',
    'Tanggung Jawab Konten',
    'assets/images/literacy.jpg',
    'Teman mempertimbangkan isi unggahan sebelum membagikannya melalui laptop',
    'Unsplash',
    'https://images.unsplash.com/photo-1516321318423-f06f85e504b3',
    '2026-11-04 10:00:00',
    'published'
),
(
    'Izin sebelum unggah: menghormati privasi orang lain',
    'menghormati-privasi-dan-batasan',
    'Seorang teman mengirim cerita pribadi melalui pesan langsung karena ia percaya kepada kita. Kepercayaan itu tidak otomatis menjadi izin untuk menampilkan ceritanya kepada pengikut, bahkan ketika niat kita ingin memberi dukungan. Etika bermedia sosial mengajak kita menjaga hak setiap orang untuk menentukan bagian hidup mana yang ingin ia bagikan sendiri.

Privasi bukan tanda bahwa seseorang sedang menyembunyikan kesalahan. Orang dapat memilih audiens yang berbeda untuk cerita yang berbeda. Kenyamanan berbagi dalam grup kecil tidak selalu berlaku di akun terbuka. Karena itu, jangan menganggap pesan, foto, atau percakapan yang kita terima sebagai bahan unggahan yang bebas digunakan.

Tanyakan izin yang jelas

Sebelum memublikasikan foto orang lain, beri tahu foto mana yang akan dipakai, di akun apa, dan untuk tujuan apa. Persetujuan untuk berfoto berbeda dengan persetujuan untuk diunggah atau ditandai. Seseorang mungkin setuju tampil di cerita sementara, tetapi keberatan jika gambarnya dipakai dalam unggahan lain. Tanyakan kembali bila tujuan dan audiens berubah.

Jawaban tidak cukup dihormati tanpa perdebatan. Hindari kalimat yang membuat orang merasa bersalah, seperti menyebutnya berlebihan atau tidak menghargai pertemanan. Diam juga bukan persetujuan. Jika belum mendapat jawaban, tunggu atau pilih materi yang tidak menampilkan dirinya. Memberi ruang untuk menolak membuat permintaan izin lebih bermakna.

Hormati batas dalam interaksi

Batas pribadi berlaku pula pada pertanyaan dan pesan. Jangan mendesak seseorang menjelaskan hubungan, masalah keluarga, atau keadaan pribadinya di kolom komentar. Ketika pesan belum dibalas, hindari mengirim tuntutan berturut-turut atau menegurnya di ruang publik. Orang lain berhak menentukan kapan dan bagaimana mereka bersedia berinteraksi.

Tangkapan layar percakapan juga membutuhkan pertimbangan. Menutup nama belum tentu cukup jika isi cerita, foto profil, atau konteksnya membuat orang mudah dikenali. Bila perlu membahas pelajaran dari sebuah percakapan, mintalah izin atau ceritakan secara umum tanpa rincian yang menunjuk kepada seseorang. Jangan menjadikan kepercayaan sebagai harga yang harus dibayar untuk konten menarik.

Saat seseorang meminta dihapus

Tanggapi permintaan menghapus foto atau penandaan dengan tenang. Jangan mengunggah permintaan tersebut sebagai bahan sindiran baru. Hapus materi yang menjadi tanggung jawab kita dan hentikan pembagian selanjutnya. Jika sudah telanjur diteruskan kepada teman, minta mereka berhenti membagikan dengan tetap menjaga informasi pribadi pihak yang terdampak.

Menghormati privasi merupakan kebiasaan dalam hubungan sehari-hari: bertanya sebelum membagikan, menerima batas tanpa mengejek, dan memperbaiki tindakan ketika seseorang merasa tidak nyaman. Kita tidak harus memahami seluruh alasan di balik suatu batas untuk menghormatinya. Di media sosial, rasa hormat sering dimulai dari keputusan sederhana untuk tidak memublikasikan cerita yang bukan milik kita.',
    'Privasi Orang Lain',
    'assets/images/communication.jpg',
    'Sekelompok teman berbicara dengan tetap menghormati batas pribadi',
    'Unsplash',
    'https://images.unsplash.com/photo-1522202176988-66273c2fd55f',
    '2026-11-03 08:30:00',
    'published'
),
(
    'Boleh berbeda: menyampaikan ketidaksetujuan dengan hormat',
    'berbeda-pendapat-dengan-hormat',
    'Linimasa mempertemukan orang dengan pengalaman, keyakinan, kebiasaan, dan sudut pandang yang beragam. Tidak semua unggahan akan sesuai dengan pendapat kita. Perbedaan itu tidak harus berakhir menjadi pertengkaran. Dengan sikap yang tepat, percakapan di media sosial dapat membantu kita memahami alasan orang lain sambil tetap menyampaikan pandangan sendiri secara jujur.

Mulailah dengan membaca isi unggahan secara utuh. Jangan hanya merespons satu kalimat yang paling memancing emosi. Perhatikan apa yang sedang dibicarakan dan tanyakan jika maksudnya belum jelas. Pertanyaan seperti apakah yang dimaksud bagian tertentu dapat membuka penjelasan, sementara tuduhan sering membuat kedua pihak langsung bersiap membela diri.

Bahas gagasannya, hormati orangnya

Ketika tidak sepakat, jelaskan bagian yang kita tanggapi beserta alasannya. Gunakan bahasa yang membicarakan isi, bukan menghakimi kemampuan atau harga diri penulisnya. Kalimat saya melihat persoalan ini berbeda karena memberi ruang bagi penjelasan. Sebaliknya, ejekan tentang kecerdasan, penampilan, asal daerah, atau keyakinan tidak membantu memahami pokok persoalan.

Hindari menganggap satu komentar mewakili seluruh kelompok. Setiap orang memiliki pengalaman yang tidak selalu kita ketahui. Kita dapat menolak pendapat tanpa memberi cap buruk pada identitas seseorang. Sebelum mengirim, baca kembali apakah pilihan kata kita akan tetap pantas jika percakapan berlangsung langsung di hadapannya.

Dengarkan tanpa harus selalu sepakat

Mendengarkan tidak berarti menyerahkan pendirian. Cobalah merangkum apa yang dipahami sebelum menjawab: apakah yang menjadi perhatianmu adalah hal ini? Kebiasaan tersebut mengurangi kemungkinan berdebat tentang sesuatu yang sebenarnya tidak dikatakan. Jika ada bagian yang masuk akal, kita boleh mengakuinya tanpa merasa kalah dalam percakapan.

Berikan kesempatan orang menjelaskan dan memperbaiki ucapannya. Jangan menumpuk pertanyaan, memotong dengan sindiran, atau mengajak pengikut datang untuk memenangkan jumlah komentar. Banyaknya tanda suka tidak menentukan siapa yang paling patut dihormati. Diskusi yang baik memberi ruang bagi semua pihak untuk berbicara tanpa harus takut dipermalukan.

Tahu kapan perlu berhenti

Tidak semua percakapan dapat diselesaikan dalam satu kolom komentar. Saat pembahasan mulai berulang, beri jeda dan akhiri dengan kalimat yang tenang. Kita dapat menyatakan bahwa pandangan masih berbeda dan memilih berhenti berdiskusi. Hormati pula orang yang lebih dahulu mengakhiri percakapan; jangan mengejarnya ke pesan pribadi atau unggahan lain.

Jika kita keliru memahami atau menyampaikan sesuatu dengan kasar, lakukan koreksi dan minta maaf secara jelas. Bersedia mengubah pendapat setelah mendengar alasan yang lebih baik menunjukkan tanggung jawab dalam berbicara. Tujuan percakapan bukan membuat lawan bicara kehilangan muka, melainkan memperjelas gagasan sambil menjaga martabat orang yang menyampaikannya.',
    'Menghargai Perbedaan',
    'assets/images/footprint.jpg',
    'Teman-teman berdiskusi dan bertukar pendapat secara terbuka',
    'Unsplash',
    'https://images.unsplash.com/photo-1519389950473-47ba0277781c',
    '2026-11-02 11:00:00',
    'published'
),
(
    'Jangan ikut menyerang: hentikan cyberbullying di linimasa',
    'menghindari-ujaran-kebencian-dan-cyberbullying',
    'Satu komentar mengejek dapat memancing puluhan balasan yang mengulang penghinaan yang sama. Bagi penonton, interaksi itu mungkin terlihat seperti hiburan singkat. Bagi orang yang menjadi sasaran, dampaknya dapat terbawa setelah layar ditutup. Menjaga etika bermedia sosial berarti menolak ikut mempermalukan seseorang, termasuk ketika serangan datang dari banyak akun sekaligus.

Cyberbullying dapat tampak sebagai ejekan yang berulang, penyebaran materi untuk mempermalukan, atau ajakan kepada orang lain agar menyerang satu akun. Ujaran yang merendahkan seseorang karena identitasnya juga menciptakan ruang yang tidak ramah. Candaan tidak menjadi alasan untuk mengabaikan keberatan orang yang dijadikan sasaran. Dengarkan dampaknya, bukan hanya niat orang yang membuatnya.

Kritik tanpa mengundang persekusi

Kita boleh mengkritik tindakan dan gagasan dengan alasan yang jelas. Namun kritik kehilangan manfaat ketika berubah menjadi penghinaan fisik, olok-olok keluarga, atau ajakan mendatangi akun seseorang. Sampaikan keberatan pada perilaku yang dibahas dan hindari membuka informasi pribadinya. Orang yang melakukan kesalahan tetap berhak diperlakukan secara manusiawi.

Sebelum memberi tanda suka atau membagikan unggahan, perhatikan apa yang sedang diperkuat. Tanggapan singkat yang mendukung ejekan bisa membuat pelaku merasa tindakannya diterima. Jangan memperbesar paparan materi yang mempermalukan dengan membagikannya kembali, bahkan ketika keterangannya bermaksud mengecam. Kita dapat menyatakan penolakan tanpa menampilkan ulang penghinaan tersebut.

Mendukung tanpa mengambil alih

Jika seseorang menjadi sasaran, tanyakan secara pribadi apakah ia ingin ditemani dan dukungan apa yang dibutuhkan. Jangan memaksanya menjelaskan pengalaman kepada publik atau membalas serangan. Hindari membagikan identitasnya untuk menarik simpati tanpa persetujuan. Memberi dukungan berarti menghormati pilihannya, termasuk ketika ia ingin berhenti membaca komentar sementara waktu.

Saat aman untuk menanggapi, gunakan kalimat singkat yang menolak perilaku merendahkan tanpa ikut mengejek pelakunya. Contohnya, pembahasan dapat dilanjutkan tanpa menyerang penampilan siapa pun. Gunakan fitur pelaporan yang tersedia untuk komentar yang melecehkan. Tujuannya menghentikan perlakuan buruk, bukan mengajak kelompok lain melakukan serangan balasan.

Perbaiki jika kita pernah terlibat

Jika pernah menulis atau meneruskan ejekan, berhenti dan hapus bagian yang merugikan. Sampaikan permintaan maaf yang mengakui tindakan serta dampaknya, tanpa menyalahkan sasaran karena dianggap terlalu sensitif. Jangan menuntut agar permintaan maaf segera diterima. Hormati jarak yang diminta dan tunjukkan perubahan melalui cara berinteraksi berikutnya.

Ruang komentar menjadi lebih baik ketika orang yang menyaksikan memilih untuk tidak ikut menertawakan. Kita dapat menjaga percakapan dengan menolak penghinaan, memberi dukungan yang diminta, dan tetap menghormati batas setiap orang. Setiap interaksi merupakan kesempatan untuk membuat media sosial lebih layak bagi semua yang hadir di dalamnya.',
    'Anti-Cyberbullying',
    'assets/images/hero.jpg',
    'Sekelompok teman saling mendukung saat menggunakan media sosial',
    'Unsplash',
    'https://images.unsplash.com/photo-1522071820081-009f0129c71c',
    '2026-11-01 09:00:00',
    'published'
);

INSERT IGNORE INTO tags (name, slug) VALUES
('Batas Pribadi', 'batas-pribadi'),
('Cyberbullying', 'cyberbullying'),
('Diskusi Sehat', 'diskusi-sehat'),
('Dukungan', 'dukungan'),
('Empati', 'empati'),
('Etika Berbagi', 'etika-berbagi'),
('Etika Berkomentar', 'etika-berkomentar'),
('Komunikasi', 'komunikasi'),
('Menghargai Perbedaan', 'menghargai-perbedaan'),
('Persetujuan', 'persetujuan'),
('Privasi', 'privasi'),
('Sopan Santun', 'sopan-santun'),
('Tanggung Jawab', 'tanggung-jawab'),
('Ujaran Kebencian', 'ujaran-kebencian');

INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'seni-berkomentar-dengan-empati' AND t.slug = 'empati';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'seni-berkomentar-dengan-empati' AND t.slug = 'etika-berkomentar';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'seni-berkomentar-dengan-empati' AND t.slug = 'komunikasi';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'bertanggung-jawab-sebelum-mengunggah' AND t.slug = 'tanggung-jawab';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'bertanggung-jawab-sebelum-mengunggah' AND t.slug = 'etika-berbagi';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'bertanggung-jawab-sebelum-mengunggah' AND t.slug = 'persetujuan';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'menghormati-privasi-dan-batasan' AND t.slug = 'privasi';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'menghormati-privasi-dan-batasan' AND t.slug = 'batas-pribadi';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'menghormati-privasi-dan-batasan' AND t.slug = 'persetujuan';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'berbeda-pendapat-dengan-hormat' AND t.slug = 'menghargai-perbedaan';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'berbeda-pendapat-dengan-hormat' AND t.slug = 'diskusi-sehat';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'berbeda-pendapat-dengan-hormat' AND t.slug = 'sopan-santun';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'menghindari-ujaran-kebencian-dan-cyberbullying' AND t.slug = 'cyberbullying';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'menghindari-ujaran-kebencian-dan-cyberbullying' AND t.slug = 'ujaran-kebencian';
INSERT IGNORE INTO article_tags (article_id, tag_id)
SELECT a.id, t.id FROM articles a CROSS JOIN tags t
WHERE a.slug = 'menghindari-ujaran-kebencian-dan-cyberbullying' AND t.slug = 'dukungan';

