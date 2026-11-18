SIM-Lab Keperawatan - SMK Kesehatan Surya Medika
============================================================

Aplikasi web untuk mengelola laboratorium keperawatan SMK Kesehatan Surya
Medika: inventaris alat dan bahan, peminjaman, penggunaan bahan habis pakai,
pengadaan, serta pencatatan kerusakan alat.

Repositori ini berisi aplikasi sekolah, bukan distribusi framework CodeIgniter.

Fitur
-----

- **Dashboard:** ringkasan inventaris, alat medis, bahan habis pakai,
  peminjaman aktif, pengajuan tertunda, dan stok menipis. Guru dan siswa juga
  mendapatkan ringkasan peminjaman miliknya.
- **Inventaris:** pengelolaan barang, kode barcode, kategori alat/bahan,
  stok, kondisi, dan lokasi penyimpanan.
- **Peminjaman alat:** pengajuan, perubahan status, pembatalan, dan
  pengembalian dengan pencatatan kondisi alat.
- **Penggunaan BHP:** pencatatan pengajuan dan penggunaan bahan habis pakai.
- **Pengadaan:** pengajuan kebutuhan barang dan proses persetujuan.
- **Kerusakan alat:** pencatatan kerusakan dan pembaruan status perbaikan.
- **Pengguna:** pengelolaan akun, peran, dan status aktif oleh admin.
- **Profil:** pembaruan data pribadi, kata sandi, dan foto profil.
- **Laporan:** ekspor Excel untuk inventaris, peminjaman, penggunaan BHP,
  pengadaan, kerusakan alat, dan pengguna.

Peran pengguna
--------------

Peran yang tersedia pada database: ``admin``, ``laboran``, ``guru``,
``siswa``, dan ``kepsek``. Menu dan tindakan yang tersedia bergantung pada
peran serta status transaksi.

- Manajemen pengguna dibatasi untuk admin.
- Modul kerusakan alat dibatasi untuk admin dan laboran.
- Modul pengadaan tidak dapat diakses siswa.
- Halaman persetujuan pada modul ``Admin`` dapat diakses admin, laboran,
  dan kepala sekolah.

Teknologi dan kebutuhan
-----------------------

- **Backend:** PHP dan CodeIgniter **3.1.13**, dengan pola MVC.
- **Database:** MySQL/MariaDB melalui driver PHP ``mysqli``.
- **Antarmuka:** Tailwind CSS dan Font Awesome 6.4.0 melalui CDN.
- **Server lokal:** Apache, misalnya melalui WampServer atau XAMPP.
  Aktifkan ``mod_rewrite`` dan izinkan ``.htaccess`` jika menggunakan URL
  tanpa ``index.php``.
- Ekstensi PHP ``mysqli`` harus aktif. Direktori sesi PHP dan direktori
  unggahan harus dapat ditulis oleh proses web server.
- Akses internet diperlukan untuk memuat aset CDN.

Kode aplikasi menggunakan sintaks PHP 7+, termasuk operator ``??``.
Persyaratan PHP pada ``composer.json`` merupakan metadata bawaan framework,
bukan jaminan kompatibilitas aplikasi pada semua versi PHP. Uji versi PHP
yang akan digunakan sebelum deployment.

Framework sudah tersedia di direktori ``system/`` dan Composer autoload
dinonaktifkan pada konfigurasi saat ini. Instalasi dasar tidak memerlukan
``composer install``, Node.js, atau proses build frontend.

Instalasi lokal
---------------

1. Jalankan Apache dan MySQL/MariaDB.

2. Clone repositori ke document root web server. Contoh pada WampServer:

   .. code-block:: console

      cd C:\wamp64\www
      git clone https://github.com/lovelyvaseline-sketch/silabsmksurmed.git silabsmksurmed

   Pada XAMPP, gunakan direktori ``htdocs``.

3. Buat database bernama ``db_lab_keperawatan``, lalu impor
   ``db_lab_keperawatan.sql`` melalui phpMyAdmin atau klien database.
   Dump memuat struktur tabel **dan data**, bukan skema kosong.
   Gunakan database baru agar tidak menimpa data instalasi lain.

4. Sesuaikan koneksi pada ``application/config/database.php``:

   .. code-block:: php

      // Sesuaikan nilai berikut pada konfigurasi $db['default'].
      'hostname' => 'localhost',
      'username' => 'pengguna_database_lokal',
      'password' => 'kata_sandi_database_lokal',
      'database' => 'db_lab_keperawatan',
      'dbdriver' => 'mysqli',

5. Sesuaikan alamat aplikasi pada ``application/config/config.php``:

   .. code-block:: php

      $config['base_url'] = 'http://localhost/silabsmksurmed/';

   Konfigurasi saat ini menggunakan ``$config['index_page'] = 'index.php';``.
   Jika ingin menghilangkannya dari URL, kosongkan nilainya dan pastikan
   aturan rewrite pada ``.htaccess`` diproses Apache.

6. Pastikan ``uploads/profile/``, ``uploads/users/``, serta lokasi penyimpanan
   sesi PHP dapat ditulis. Driver sesi saat ini menggunakan file;
   ``sess_save_path`` masih ``NULL``, sehingga konfigurasi sesi PHP server
   perlu diperhatikan.

7. Buka ``http://localhost/silabsmksurmed/index.php/auth``.
   Controller awal aplikasi adalah ``auth``.

Akses akun
----------

Login menggunakan akun aktif pada tabel ``tb_users`` dan kata sandi yang
sesuai. Autentikasi menggunakan ``password_verify()``; kata sandi pada
database berupa hash, bukan kata sandi yang dapat dibaca.

README ini tidak menetapkan username atau kata sandi default. Jika kata
sandi akun tidak diketahui, pemilik instalasi perlu mengaturnya kembali
dengan hash yang dibuat melalui ``password_hash()``.

Struktur proyek
---------------

.. code-block:: text

   application/
     config/        Konfigurasi aplikasi, database, autoload, dan routing
     controllers/   Autentikasi dan modul aplikasi
     models/        Akses data
     views/         Halaman dan template antarmuka
   assets/css/      Stylesheet lokal
   system/          Framework CodeIgniter 3.1.13
   uploads/         Berkas foto pengguna/profil
   db_lab_keperawatan.sql
   index.php        Entry point aplikasi
   .htaccess        Aturan rewrite Apache

Database memuat enam tabel utama: ``tb_users``, ``tb_inventaris``,
``tb_peminjaman``, ``tb_penggunaan_bhp``, ``tb_pengadaan``, dan
``tb_kerusakan_alat``.

Catatan sebelum deployment
--------------------------

- Repositori menyertakan dump database, foto profil, dan konfigurasi yang
  berisi encryption key. Tinjau data pribadi sebelum membagikan repositori
  atau menggunakan dump sebagai data awal.
- Ganti encryption key dan kredensial database untuk instalasi sendiri.
  Rotasi key yang sudah dibagikan jika dipakai pada sistem lain.
- Jangan izinkan dump SQL dan berkas konfigurasi diunduh melalui web server.
- Entry point saat ini memakai environment ``development`` jika ``CI_ENV``
  tidak disetel. Atur ``CI_ENV=production`` pada konfigurasi server produksi.
- Sesuaikan ``base_url``, HTTPS, pengaturan cookie, lokasi sesi, dan izin
  unggahan dengan lingkungan deployment. CSRF protection sudah aktif
  pada konfigurasi aplikasi.

Lisensi framework
-----------------

Lisensi CodeIgniter yang disertakan tersedia pada ``license.txt``.
