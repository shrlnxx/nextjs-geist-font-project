# Sistem Keuangan Pesantren

Aplikasi web sederhana untuk mengelola keuangan internal pesantren menggunakan PHP native dan MySQL/SQLite.

## Fitur Utama

- ✅ **Form Input Transaksi**
  - Tipe transaksi: pemasukan atau pengeluaran
  - Input nominal dengan format otomatis
  - Pilihan tanggal
  - Kategori tetap: Perlengkapan, Administrasi, Pendidikan, Keamanan, Kesehatan, Kebersihan, Tim Media, Organisasi, Humas
  - Keterangan opsional

- ✅ **Laporan Keuangan**
  - Tampilan semua transaksi dalam tabel
  - Pengelompokan berdasarkan jenis transaksi dan kategori
  - Perhitungan otomatis: total pemasukan, total pengeluaran, dan saldo akhir
  - Filter berdasarkan:
    - Rentang tanggal bebas
    - Bulan dan tahun
    - Kategori
    - Tipe transaksi

- ✅ **Export ke Excel**
  - Download laporan dalam format .xlsx
  - Menggunakan library PhpSpreadsheet
  - Format laporan yang rapi dan profesional

- ✅ **Antarmuka Modern**
  - Desain responsif menggunakan Bootstrap 5
  - Tampilan yang bersih dan mudah digunakan
  - Tanpa sistem login (single user: bendahara)

## Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7+ atau SQLite 3
- Web server (Apache/Nginx)
- Composer (untuk install dependencies)

## Instalasi di XAMPP/Laragon

### 1. Persiapan Environment

1. **Install XAMPP atau Laragon**
   - Download dan install XAMPP dari [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Atau install Laragon dari [https://laragon.org/](https://laragon.org/)

2. **Pastikan PHP dan MySQL berjalan**
   - Jalankan Apache dan MySQL dari XAMPP Control Panel
   - Atau start services dari Laragon

### 2. Setup Database

#### Opsi A: Menggunakan MySQL (Recommended)

1. **Buka phpMyAdmin**
   - Akses `http://localhost/phpmyadmin`

2. **Import Database**
   - Buat database baru bernama `pesantren_keuangan`
   - Import file `db/pesantren_keuangan.sql`

3. **Konfigurasi Database**
   - Buka file `config.php`
   - Pastikan konfigurasi MySQL sudah benar:
   ```php
   define('DB_TYPE', 'mysql');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'pesantren_keuangan');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Kosong untuk XAMPP default
   ```

#### Opsi B: Menggunakan SQLite

1. **Ubah Konfigurasi**
   - Buka file `config.php`
   - Ubah `DB_TYPE` menjadi `sqlite`:
   ```php
   define('DB_TYPE', 'sqlite');
   ```

2. **Database SQLite akan otomatis dibuat** saat aplikasi pertama kali dijalankan

### 3. Install Dependencies

1. **Install Composer** (jika belum ada)
   - Download dari [https://getcomposer.org/](https://getcomposer.org/)

2. **Install PhpSpreadsheet**
   ```bash
   cd pesantren-keuangan
   composer install
   ```

### 4. Deploy Aplikasi

1. **Copy folder aplikasi**
   - Copy folder `pesantren-keuangan` ke dalam folder `htdocs` (XAMPP) atau `www` (Laragon)

2. **Set Permissions** (jika di Linux/Mac)
   ```bash
   chmod -R 755 pesantren-keuangan/
   chmod -R 777 pesantren-keuangan/db/
   ```

### 5. Akses Aplikasi

1. **Buka browser** dan akses:
   ```
   http://localhost/pesantren-keuangan
   ```

2. **Aplikasi siap digunakan!**

## Struktur Folder

```
pesantren-keuangan/
├── index.php              # Halaman utama & form input
├── laporan.php            # Halaman laporan & filter
├── export_excel.php       # Export ke Excel
├── config.php             # Konfigurasi database
├── koneksi.php            # Koneksi database & fungsi
├── composer.json          # Dependencies
├── README.md              # Dokumentasi
├── assets/
│   ├── css/
│   │   └── style.css      # Custom CSS
│   └── js/
│       └── script.js      # Custom JavaScript
├── db/
│   └── pesantren_keuangan.sql  # File SQL database
└── vendor/                # Dependencies (auto-generated)
```

## Cara Penggunaan

### 1. Input Transaksi Baru

1. Akses halaman utama (`index.php`)
2. Pilih tipe transaksi (pemasukan/pengeluaran)
3. Masukkan nominal (tanpa titik/koma)
4. Pilih tanggal transaksi
5. Pilih kategori dari dropdown
6. Tambahkan keterangan (opsional)
7. Klik "Simpan Transaksi"

### 2. Melihat Laporan

1. Klik menu "Laporan Keuangan" atau akses `laporan.php`
2. Gunakan filter untuk menyaring data:
   - **Rentang Tanggal**: Pilih tanggal dari dan sampai
   - **Bulan/Tahun**: Filter berdasarkan bulan dan tahun tertentu
   - **Kategori**: Filter berdasarkan kategori tertentu
   - **Tipe**: Filter pemasukan atau pengeluaran saja
3. Klik "Filter" untuk menerapkan filter

### 3. Export ke Excel

1. Di halaman laporan, atur filter sesuai kebutuhan
2. Klik tombol "Export Excel"
3. File .xlsx akan otomatis terdownload

### 4. Print Laporan

1. Di halaman laporan, klik tombol "Print"
2. Gunakan fungsi print browser untuk mencetak

## Fitur Tambahan

### Auto-Save Form
- Data form otomatis tersimpan di browser
- Data akan dipulihkan jika browser tertutup tidak sengaja

### Format Otomatis
- Nominal otomatis diformat dengan separator ribuan
- Validasi input di client dan server side

### Keyboard Shortcuts
- `Ctrl + N`: Buka halaman input transaksi
- `Ctrl + L`: Buka halaman laporan
- `Ctrl + E`: Export Excel (di halaman laporan)

## Troubleshooting

### Error "PhpSpreadsheet tidak ditemukan"
```bash
cd pesantren-keuangan
composer install
```

### Error koneksi database MySQL
1. Pastikan MySQL service berjalan
2. Periksa konfigurasi di `config.php`
3. Pastikan database `pesantren_keuangan` sudah dibuat

### Error permission denied (Linux/Mac)
```bash
chmod -R 755 pesantren-keuangan/
chmod -R 777 pesantren-keuangan/db/
```

### Aplikasi tidak bisa diakses
1. Pastikan Apache service berjalan
2. Periksa folder sudah berada di `htdocs` atau `www`
3. Akses dengan URL yang benar: `http://localhost/pesantren-keuangan`

## Backup Data

### MySQL
```bash
mysqldump -u root -p pesantren_keuangan > backup_keuangan.sql
```

### SQLite
Copy file `db/pesantren_keuangan.db` ke lokasi backup

## Keamanan

- Aplikasi ini dirancang untuk penggunaan internal/offline
- Tidak ada sistem autentikasi (single user)
- Gunakan prepared statements untuk mencegah SQL injection
- Validasi input di client dan server side

## Support

Jika mengalami masalah atau butuh bantuan:
1. Periksa file log error PHP
2. Pastikan semua persyaratan sistem terpenuhi
3. Periksa konfigurasi database di `config.php`

## Changelog

### v1.0.0
- ✅ Form input transaksi
- ✅ Laporan keuangan dengan filter
- ✅ Export ke Excel
- ✅ Antarmuka Bootstrap 5
- ✅ Support MySQL dan SQLite
- ✅ Validasi input
- ✅ Auto-save form
- ✅ Print laporan

---

**Sistem Keuangan Pesantren v1.0.0**  
Dikembangkan untuk memudahkan pengelolaan keuangan internal pesantren.
