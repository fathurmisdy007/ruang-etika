
# Ruang Etika — Etika dalam Bermedia Sosial

Ruang Etika adalah website artikel edukasi yang membahas etika dalam bermedia sosial. Website ini dikembangkan sebagai tugas mata kuliah Literasi Digital, Program Studi Sistem Informasi, Fakultas Sains dan Teknologi, UIN Sunan Ampel Surabaya.

Website bertujuan meningkatkan kesadaran tentang pentingnya komunikasi yang sopan, tanggung jawab dalam membagikan konten, menghargai perbedaan, mencegah cyberbullying, dan menjaga privasi orang lain.

## 1. Identitas Proyek

| Keterangan | Informasi |
| --- | --- |
| Nama proyek | Ruang Etika |
| Tema | Etika dalam Bermedia Sosial |
| Mata kuliah | Literasi Digital |
| Nama mahasiswa | Fathur Rahman Misdyanto |
| NIM | 09010626011 |
| Program studi | Sistem Informasi |
| Fakultas | Sains dan Teknologi |
| Universitas | UIN Sunan Ampel Surabaya |
| Tahun | 2026 |
| Jenis proyek | Website artikel edukasi |

## 2. Teknologi yang Digunakan

- PHP Native sebagai backend.
- MySQL/MariaDB sebagai database.
- Apache melalui XAMPP sebagai web server lokal.
- HTML5 sebagai struktur halaman.
- Tailwind CSS Play CDN dan CSS kustom untuk tampilan.
- JavaScript untuk interaktivitas sederhana.
- Visual Studio Code sebagai editor kode.
- Git untuk version control.
- GitHub sebagai repositori proyek.

Proyek tidak menggunakan WordPress, Composer, npm, maupun framework backend. Penggunaan PHP Native melalui VS Code telah mendapat izin dosen pengampu sebagai alternatif WordPress.

## 3. Fitur Website

### 3.1 Halaman Pengunjung

**Beranda**

- Menampilkan identitas dan pengantar Ruang Etika.
- Menampilkan artikel pilihan.
- Menyediakan navigasi menuju halaman lain.

**Artikel**

- Menampilkan daftar artikel edukasi.
- Pencarian artikel berdasarkan judul.
- Filter artikel berdasarkan kategori.
- Halaman detail artikel.
- Informasi kategori, tanggal, dan tag.
- Gambar unggulan dengan alt text dan sumber.

**Tentang Saya**

- Menjelaskan tujuan dan latar belakang Ruang Etika.
- Memperkenalkan website sebagai media edukasi.

**Kontak**

- Formulir nama, email, subjek, dan pesan.
- Validasi data formulir.
- Penyimpanan pesan ke database.
- Notifikasi pengiriman pesan.

### 3.2 Dashboard Administrator

Administrator memiliki akses untuk:

- Login dan logout.
- Melihat ringkasan artikel.
- Menambah artikel.
- Mengedit artikel.
- Menghapus artikel.
- Mengatur kategori dan tag.
- Mengunggah gambar unggulan.
- Mengisi alt text dan sumber gambar.
- Mengatur status Draft atau Published.
- Melihat pesan masuk dari formulir kontak.

Akses administrator dilindungi autentikasi berbasis session.

## 4. Konten dan Ketentuan Artikel

Website menyediakan lima artikel contoh berbahasa Indonesia yang khusus membahas etika dalam bermedia sosial.

Setiap artikel contoh memiliki:

- Minimal 200 kata.
- Kategori yang relevan.
- Minimal dua tag.
- Gambar unggulan.
- Teks alternatif (alt text).
- Informasi sumber gambar.

Artikel mencakup pembahasan tentang:

1. Etika berkomentar dengan empati.
2. Tanggung jawab sebelum mengunggah konten.
3. Menghormati privasi dan batasan orang lain.
4. Menghargai perbedaan dalam interaksi digital.
5. Mencegah perilaku cyberbullying.

Artikel tambahan dapat dibuat melalui dashboard admin.

## 5. Persyaratan Sistem

Sebelum menjalankan proyek, siapkan:

- Windows dengan XAMPP terinstal.
- PHP versi 8.1 atau lebih baru.
- Apache dan MySQL/MariaDB.
- Browser modern.
- Visual Studio Code.
- Git untuk pengelolaan repositori.

Ekstensi PHP yang diperlukan:

- `pdo_mysql`
- `mbstring`
- `fileinfo`

Beberapa aset menggunakan Tailwind CDN dan Google Fonts sehingga memerlukan koneksi internet.

## 6. Cara Instalasi di XAMPP

### Langkah 1 — Menyiapkan proyek

Ekstrak atau salin folder proyek ke:

```text
D:\xampp\htdocs\literasi-porto
```

Sesuaikan lokasi jika XAMPP menggunakan direktori lain.

### Langkah 2 — Menjalankan server

Buka XAMPP Control Panel.

Aktifkan:

- Apache
- MySQL

Pastikan kedua layanan berjalan tanpa error.

### Langkah 3 — Mengimpor database baru

Buka:

<http://localhost/phpmyadmin/>

Untuk instalasi baru:

1. Pilih menu Import pada halaman server phpMyAdmin.
2. Pilih file `db/database.sql`.
3. Klik Import.
4. Pastikan database `etika_sosmed` berhasil dibuat.

Database contoh berisi lima tabel:

| Tabel | Fungsi |
| --- | --- |
| `admins` | Akun administrator |
| `articles` | Konten artikel |
| `tags` | Data tag artikel |
| `article_tags` | Relasi artikel dan tag |
| `contact_messages` | Pesan formulir kontak |

File SQL menyediakan lima artikel contoh dan tidak berisi akun admin bawaan.

**PERINGATAN:**

Jangan mengimpor ulang file SQL instalasi baru pada database yang sudah berisi artikel, akun admin, atau pesan pengguna.

Untuk database lama, gunakan prosedur migrasi pada bagian 10.

### Langkah 4 — Konfigurasi database

Konfigurasi standar proyek adalah:

| Konfigurasi | Nilai |
| --- | --- |
| DB_HOST | 127.0.0.1 |
| DB_PORT | 3306 |
| DB_NAME | etika_sosmed |
| DB_USER | root (fallback lokal) |
| DB_PASS | Kosong (fallback lokal) |

Untuk keamanan, disarankan membuat pengguna database khusus dengan hak hanya pada database `etika_sosmed`.

Caranya:

1. Buka phpMyAdmin.
2. Pilih User accounts.
3. Buat pengguna baru, misalnya `ruang_etika_app`.
4. Gunakan password kuat.
5. Berikan hak akses yang dibutuhkan pada database `etika_sosmed`, seperti SELECT, INSERT, UPDATE, dan DELETE.
6. Salin `config.local.example.php` menjadi `config.local.php`.
7. Isi konfigurasi database sesuai akun yang dibuat.

Contoh:

```php
<?php
return [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_NAME' => 'etika_sosmed',
    'DB_USER' => 'ruang_etika_app',
    'DB_PASS' => 'ISI_PASSWORD_PRIBADI',
];
```

Jangan mengunggah `config.local.php` ke GitHub.

### Langkah 5 — Menjalankan website

Buka:

<http://localhost/literasi-porto/>

Halaman administrator:

<http://localhost/literasi-porto/admin/login.php>

Jika folder proyek menggunakan nama berbeda, sesuaikan URL localhost.

## 7. Membuat Akun Administrator

Akun administrator pertama dibuat melalui terminal, bukan registrasi publik.

Buka PowerShell pada direktori proyek.

Jalankan:

```powershell
cd D:\xampp\htdocs\literasi-porto
D:\xampp\php\php.exe admin/create-admin.php
```

Kemudian masukkan:

1. Nama administrator.
2. Email administrator.
3. Password minimal 12 karakter.
4. Konfirmasi password.

Password disimpan menggunakan `password_hash()`.

Setelah akun berhasil dibuat, login melalui:

<http://localhost/literasi-porto/admin/login.php>

Catatan:

- Tidak tersedia akun admin default.
- Skrip hanya digunakan untuk membuat admin pertama.
- Jangan membagikan password administrator.
- Jika akun sudah tersedia, jangan menjalankan proses setup ulang.

## 8. Struktur Folder Proyek

```text
literasi-porto/
│
├── index.php
├── artikel-list.php
├── artikel.php
├── tentang.php
├── kontak.php
│
├── config.php
├── config.local.example.php
├── database.sql
├── README.md
├── .gitignore
├── .htaccess
│
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── create-admin.php
│   ├── articles.php
│   ├── article-form.php
│   ├── delete.php
│   ├── tags.php
│   ├── tag-delete.php
│   ├── messages.php
│   ├── _header.php
│   └── _footer.php
│
├── includes/
│   ├── functions.php
│   ├── ui.php
│   ├── header.php
│   ├── footer.php
│   └── .htaccess
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── editorial.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       ├── hero.jpg
│       ├── communication.jpg
│       ├── literacy.jpg
│       ├── footprint.jpg
│       ├── privacy.jpg
│       ├── balance.jpg
│       └── favicon.svg
│
├── db/
│   ├── database.sql
│   ├── database_uji.sql
│   ├── SEBELUM_IMPORT_DATABASE.txt
│   └── migrations/
│       └── 001_upgrade_existing.sql
│
└── uploads/
    ├── .htaccess
    └── .gitkeep
```

Folder `uploads/` digunakan untuk menyimpan gambar yang diunggah melalui dashboard admin.

## 9. Keamanan Website

Website menerapkan mekanisme keamanan dasar:

1. Password administrator menggunakan hashing.
2. Login admin menggunakan session.
3. Query database menggunakan PDO prepared statements.
4. Formulir perubahan data dilindungi CSRF.
5. Input pengguna divalidasi pada server.
6. Output teks di-escape untuk mengurangi risiko XSS.
7. Upload gambar dibatasi berdasarkan ukuran dan jenis file.
8. Berkas internal dibatasi aksesnya melalui `.htaccess`.
9. Kredensial lokal dikecualikan menggunakan `.gitignore`.
10. Akses database disarankan memakai pengguna khusus.

File rahasia yang tidak boleh dibagikan:

- `config.local.php`
- `.env`
- Password database.
- Kredensial administrator.

Jangan memasukkan data pribadi sensitif ke dalam artikel, database contoh, atau screenshot dokumentasi.

## 10. Backup dan Migrasi Database Lama

Bagian ini khusus untuk proyek yang sudah memiliki database aktif.

**Jangan menghapus database atau mengimpor ulang data contoh.**

Langkah aman:

1. Buka phpMyAdmin.
2. Pilih database `etika_sosmed`.
3. Klik Export.
4. Pilih format SQL.
5. Simpan backup di luar direktori website.
6. Cadangkan juga folder `uploads/`.
7. Cadangkan konfigurasi lokal secara pribadi.

Jika database lama belum memiliki struktur terbaru, gunakan:

```text
db/migrations/001_upgrade_existing.sql
```

Jalankan migrasi hanya setelah backup berhasil dibuat dan struktur database lama diperiksa.

Migrasi ditujukan untuk menambahkan struktur yang diperlukan tanpa menghapus data existing.

Petunjuk lebih lanjut tersedia di:

```text
db/SEBELUM_IMPORT_DATABASE.txt
```

Jangan mengunggah backup database yang berisi data pribadi ke repositori publik.

## 11. Git dan GitHub

Git digunakan untuk mencatat perubahan kode secara bertahap.

### Inisialisasi repositori

Buka terminal pada folder proyek:

```bash
git init
git branch -M main
git status
```

Jika proyek sudah merupakan repositori Git, tidak perlu menjalankan `git init` ulang.

### Menyimpan perubahan

```bash
git add .
git commit -m "Menyiapkan website Ruang Etika"
```

Contoh pesan commit untuk pengembangan bertahap:

```text
Menyiapkan struktur website
Menambahkan halaman publik
Mengimplementasikan CRUD artikel
Menambahkan kategori dan tag
Melengkapi formulir kontak
Memperbaiki keamanan dan dokumentasi
```

Pesan di atas hanya contoh. Riwayat commit harus mencerminkan perubahan yang benar-benar dilakukan.

Tugas mensyaratkan minimal lima commit bermakna yang dikerjakan secara bertahap.

### Menghubungkan GitHub

Buat repositori baru melalui:

<https://github.com/>

Kemudian jalankan:

```bash
git remote add origin https://github.com/USERNAME/NAMA_REPO.git
git push -u origin main
```

Ganti `USERNAME` dan `NAMA_REPO` dengan akun serta nama repositori sebenarnya.

### Memeriksa riwayat commit

```bash
git log --oneline
```

### Keamanan repositori

- Pastikan `.gitignore` berfungsi.
- Jangan commit `config.local.php`.
- Jangan membagikan password.
- Aktifkan 2FA pada akun GitHub.
- Gunakan repositori Private dengan dosen sebagai collaborator, atau Public dengan email khusus tugas.
- Periksa isi database SQL sebelum dipublikasikan.

File `db/database.sql` adalah paket database contoh. Untuk mencadangkan perubahan konten terbaru, ekspor ulang database setelah pengerjaan selesai.

Jika hasil ekspor berisi akun, email, atau pesan pribadi, jangan unggah ke repositori publik tanpa mengamankan data tersebut.

### Repositori Proyek

[URL REPOSITORI GITHUB]

## 12. Uji Pemulihan (Recovery Test)

Tujuan pengujian ini adalah membuktikan bahwa proyek dapat dipulihkan dari repositori GitHub.

**Gunakan database pengujian terpisah agar data asli tidak terganggu.**

### Langkah 1 — Clone proyek

Buka terminal pada folder `htdocs`:

```bash
git clone https://github.com/USERNAME/NAMA_REPO.git literasi-uji
```

### Langkah 2 — Membuat database pengujian

Buka phpMyAdmin.

Impor:

```text
db/database_uji.sql
```

File tersebut disiapkan untuk database:

```text
etika_sosmed_uji
```

Jangan menggunakan `db/database.sql` untuk menguji pemulihan jika database utama masih digunakan.

### Langkah 3 — Konfigurasi koneksi

Salin `config.local.example.php` menjadi `config.local.php` pada folder hasil clone.

Sesuaikan:

```php
'DB_NAME' => 'etika_sosmed_uji'
```

Gunakan akun database yang memiliki izin pada database pengujian.

### Langkah 4 — Membuat admin pengujian

Jika database uji belum memiliki admin, jalankan:

```powershell
D:\xampp\php\php.exe admin/create-admin.php
```

Jalankan perintah dari folder `literasi-uji`.

### Langkah 5 — Pengujian

Buka:

<http://localhost/literasi-uji/>

Periksa:

- Halaman beranda tampil.
- Artikel dapat dibuka.
- Kategori dan tag tampil.
- Gambar artikel tersedia.
- Navigasi berfungsi.
- Login admin berhasil.
- Dashboard dapat diakses.
- Formulir kontak dapat menyimpan pesan.

Ambil screenshot sebagai bukti uji pemulihan.

Catatan: Database contoh digunakan untuk menguji pemulihan situs awal. Pemulihan seluruh data terbaru membutuhkan hasil ekspor database aktual dan backup folder uploads.

## 13. Pengujian Fungsional

Berikut daftar pengujian yang perlu dilakukan sebelum pengumpulan:

- [ ] Beranda berjalan tanpa error.
- [ ] Halaman Tentang Saya tampil.
- [ ] Halaman Kontak tampil.
- [ ] Formulir kontak berhasil mengirim pesan.
- [ ] Pesan tersimpan dan tampil di dashboard admin.
- [ ] Minimal tiga artikel memiliki 200+ kata.
- [ ] Setiap artikel memiliki kategori dan minimal dua tag.
- [ ] Gambar, alt text, dan sumber gambar tampil.
- [ ] Pencarian artikel berfungsi.
- [ ] Navigasi desktop dan mobile berfungsi.
- [ ] Login dan logout admin berhasil.
- [ ] CRUD artikel berhasil diuji.
- [ ] Data existing tetap aman.
- [ ] File rahasia tidak masuk GitHub.
- [ ] Minimal lima commit tersedia.
- [ ] Recovery test berhasil.

Checklist diisi berdasarkan hasil pengujian nyata, bukan hanya berdasarkan keberadaan kode.

## 14. Dokumentasi Tugas

Dokumentasi yang disiapkan meliputi:

1. Screenshot halaman Beranda.
2. Screenshot salah satu artikel.
3. Screenshot dashboard admin.
4. Screenshot repositori dan riwayat commit GitHub.
5. Screenshot database melalui phpMyAdmin.
6. Screenshot hasil uji pemulihan.

Laporan utama disusun sebanyak 1–2 halaman yang berisi:

- Langkah pengerjaan proyek.
- Kendala yang ditemukan.
- Solusi yang dilakukan.
- Refleksi tentang etika dan keamanan digital.

Screenshot dapat ditempatkan pada bagian lampiran.

Laporan dan lampiran dikumpulkan sebagai satu file PDF sesuai ketentuan dosen.

## 15. Troubleshooting

### Website tidak dapat dibuka

Pastikan Apache aktif dan alamat localhost sesuai nama folder proyek.

### Koneksi database gagal

Periksa:

- MySQL/MariaDB aktif.
- Database sudah diimpor.
- Host dan port database sesuai.
- Username serta password benar.
- Akun database memiliki izin yang diperlukan.

### Login administrator gagal

Pastikan akun administrator sudah dibuat melalui terminal dan database yang digunakan sudah benar.

### Gambar gagal diunggah

Pastikan:

- Folder `uploads/` dapat ditulis.
- Jenis file JPEG, PNG, atau WebP.
- Ukuran gambar maksimal 2 MB.
- Ekstensi Fileinfo aktif.

### Tampilan website tidak sempurna

Pastikan koneksi internet tersedia untuk memuat Tailwind CDN dan Google Fonts.

Coba tekan `Ctrl + F5` untuk memuat ulang CSS.

### Data tidak tampil setelah clone

Periksa apakah database yang benar sudah diimpor, konfigurasi database sesuai, dan gambar unggahan ikut dicadangkan.

## 16. Etika dan Hak Cipta

Website Ruang Etika dibuat sebagai sarana edukasi mengenai etika komunikasi di media sosial.

Prinsip yang diterapkan:

- Menghormati pendapat dan martabat orang lain.
- Menghindari cyberbullying.
- Tidak menyebarkan informasi pribadi tanpa izin.
- Menghargai hak cipta.
- Mencantumkan sumber gambar.
- Tidak membagikan informasi rahasia pengguna.
- Menggunakan teknologi secara bertanggung jawab.

Gambar ilustrasi lokal menggunakan aset dari Unsplash.

Referensi lisensi:

<https://unsplash.com/license>

Sumber gambar artikel juga dicantumkan pada metadata dan halaman detail artikel.

## 17. Penutup

Ruang Etika merupakan implementasi website dinamis berbasis PHP Native dan MariaDB yang menggabungkan pengelolaan artikel, database, serta praktik keamanan digital.

Melalui proyek ini, pengembang mempraktikkan pembuatan website, pengelolaan database, version control menggunakan Git, dokumentasi proyek, dan proses pemulihan data.

Website diharapkan menjadi media edukasi sederhana yang mendorong penggunaan media sosial secara lebih sopan, bijak, dan bertanggung jawab.

---

**Dikembangkan oleh:** Fathur Rahman Misdyanto
**NIM:** 09010626011
**Program Studi:** Sistem Informasi  
**Universitas:** UIN Sunan Ampel Surabaya  
**Tahun:** 2026
