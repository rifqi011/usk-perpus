# Sistem Informasi Perpustakaan USK

Sistem Informasi Perpustakaan berbasis web menggunakan Laravel 11 dan Filament 3.

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default](#akun-default)
- [Struktur Database](#struktur-database)
- [Fitur Aplikasi](#fitur-aplikasi)
- [Troubleshooting](#troubleshooting)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## 🚀 Fitur Utama

### Manajemen User
- **Admin Management**: Kelola admin dengan role (Admin & Super Admin)
- **Member Management**: Kelola anggota perpustakaan dengan sistem registrasi
- **Profile Management**: Admin dapat mengelola profil mereka sendiri

### Master Data
- **Kategori Buku**: Klasifikasi buku berdasarkan kategori
- **Genre Buku**: Sistem genre dengan relasi many-to-many (1 buku bisa punya banyak genre)
- **Penulis**: Data penulis lengkap dengan sosial media
- **Penerbit**: Data penerbit buku
- **Rak Buku**: Lokasi penyimpanan buku
- **Buku**: Data buku lengkap dengan SKU, stok awal, cover, dan multiple authors/genres

### Transaksi
- **Peminjaman**: Sistem peminjaman dengan multiple books (1 transaksi bisa pinjam banyak buku)
- **Pengembalian**: Proses pengembalian dengan perhitungan denda otomatis
- **Reservasi**: Sistem reservasi buku
- **Denda**: Tracking dan management denda

### Pengaturan
- **Aturan Peminjaman**: Multiple loan rules (default, mahasiswa, dosen, dll)
- **Site Settings**: Konfigurasi nama perpustakaan, logo, tagline, dll

## 🛠 Teknologi yang Digunakan

- **Framework**: Laravel 11.x
- **Admin Panel**: Filament 3.x
- **Database**: MySQL 8.0+
- **PHP**: 8.2+
- **Frontend**: Livewire, Alpine.js, Tailwind CSS

## 📦 Persyaratan Sistem

Pastikan sistem Anda memenuhi persyaratan berikut:

- PHP >= 8.2
- Composer
- MySQL >= 8.0 atau MariaDB >= 10.3
- Node.js >= 18.x (untuk asset compilation)
- NPM atau Yarn
- Git

### Extension PHP yang Diperlukan

```
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD atau Imagick
- Zip
```

## 📥 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/rifqi011/usk-perpus.git
cd usk-perpus/library-app
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_usk
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Buat Database

Buat database baru di MySQL:

```sql
CREATE DATABASE perpustakaan_usk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau menggunakan command line:

```bash
mysql -u root -p -e "CREATE DATABASE perpustakaan_usk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 6. Jalankan Migration dan Seeder

```bash
# Jalankan migration dan seeder
php artisan migrate:fresh --seed
```

Command ini akan:
- Membuat semua tabel database
- Membuat role (admin, superadmin)
- Membuat akun super admin default
- Membuat loan rules default (Umum, Mahasiswa, Dosen)
- Membuat site settings default

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Compile Assets

```bash
# Development
npm run dev

# Production
npm run build
```

## ⚙️ Konfigurasi

### Konfigurasi Site Settings

Setelah login, Anda dapat mengkonfigurasi:

1. **Nama Perpustakaan**: Nama yang akan ditampilkan di aplikasi
2. **Tagline**: Deskripsi singkat perpustakaan
3. **Logo**: Upload logo perpustakaan (akan muncul di navbar)
4. **Favicon**: Icon yang muncul di browser tab

Akses: **Admin Panel → Pengaturan → Site Settings**

### Konfigurasi Loan Rules

Sistem sudah menyediakan 3 aturan peminjaman default:

1. **Aturan Peminjaman Umum** (DEFAULT)
   - Max pinjaman aktif: 3 buku
   - Durasi: 7 hari
   - Denda: Rp 1.000/hari

2. **Aturan Peminjaman Mahasiswa** (STUDENT)
   - Max pinjaman aktif: 5 buku
   - Durasi: 14 hari
   - Denda: Rp 500/hari

3. **Aturan Peminjaman Dosen** (LECTURER)
   - Max pinjaman aktif: 10 buku
   - Durasi: 30 hari
   - Denda: Rp 0/hari

Anda dapat menambah, edit, atau hapus aturan sesuai kebutuhan.

Akses: **Admin Panel → Pengaturan → Aturan Peminjaman**

## 🚀 Menjalankan Aplikasi

### Development Mode

```bash
# Terminal 1: Jalankan Laravel development server
php artisan serve

# Terminal 2: Compile assets (watch mode)
npm run dev
```

Aplikasi akan berjalan di: `http://localhost:8000`

### Production Mode

```bash
# Compile assets untuk production
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan dengan web server (Apache/Nginx)
```

## 🔐 Akun Default

Setelah menjalankan seeder, gunakan akun berikut untuk login:

### Super Admin
- **Email**: `superadmin@library.com`
- **Password**: `password`
- **Role**: Super Admin
- **Akses**: Full access ke semua fitur

### Catatan Keamanan
⚠️ **PENTING**: Segera ubah password default setelah login pertama kali!

Cara mengubah password:
1. Login ke admin panel
2. Klik avatar di pojok kanan atas
3. Pilih "Profil Saya"
4. Update password

## 🗄️ Struktur Database

### Tabel Utama

#### Users & Authentication
- `users` - Data user (admin & member)
- `roles` - Role user (admin, superadmin)
- `role_user` - Pivot table user-role
- `admin_profiles` - Profile admin
- `member_profiles` - Profile anggota

#### Master Data
- `categories` - Kategori buku
- `genres` - Genre buku
- `authors` - Data penulis
- `publishers` - Data penerbit
- `shelves` - Rak buku
- `books` - Data buku
- `book_authors` - Pivot table book-author (many-to-many)
- `book_genres` - Pivot table book-genre (many-to-many)
- `book_copies` - Salinan fisik buku

#### Transaksi
- `loans` - Header peminjaman
- `loan_details` - Detail buku yang dipinjam
- `loan_rules` - Aturan peminjaman
- `reservations` - Reservasi buku
- `fines` - Denda
- `registrations` - Registrasi anggota baru

#### Settings
- `site_settings` - Pengaturan aplikasi

### Entity Relationship Diagram (ERD)

```
users (1) ----< (M) admin_profiles
users (1) ----< (M) member_profiles
users (1) ----< (M) loans (created_by)

categories (1) ----< (M) books
publishers (1) ----< (M) books
shelves (1) ----< (M) books

books (M) ----< (M) authors (via book_authors)
books (M) ----< (M) genres (via book_genres)
books (1) ----< (M) book_copies

member_profiles (1) ----< (M) loans
loan_rules (1) ----< (M) loans
loans (1) ----< (M) loan_details
book_copies (1) ----< (M) loan_details

member_profiles (1) ----< (M) reservations
books (1) ----< (M) reservations

member_profiles (1) ----< (M) fines
loans (1) ----< (M) fines
```

## 📱 Fitur Aplikasi

### 1. Dashboard
- Statistik perpustakaan
- Grafik peminjaman
- Buku terpopuler
- Aktivitas terkini

### 2. Manajemen Admin
- Tambah/Edit/Hapus admin
- Assign role (Admin/Super Admin)
- Data identitas lengkap (NIK, tempat/tanggal lahir, foto, dll)
- Aktivasi/Deaktivasi akun

### 3. Manajemen Anggota
- Registrasi anggota baru
- Approve/Reject registrasi
- Data lengkap anggota
- Kode member otomatis (Format: M-DDMMYY-XXXXXXXX)
- Status keanggotaan (Active, Suspended, Inactive)
- Suspend/Aktivasi anggota

### 4. Manajemen Buku
- **Data Buku Lengkap**:
  - Judul, ISBN, SKU
  - Kategori & Genre (multiple)
  - Penulis (multiple)
  - Penerbit & Rak
  - Cover image
  - Deskripsi & Sinopsis
  - Harga beli & pengganti
  - Stok awal & stok tersedia

- **Fitur**:
  - Upload cover buku
  - Multiple authors per book
  - Multiple genres per book
  - Tracking stok otomatis
  - Filter & search

### 5. Peminjaman
- **Multiple Books**: 1 transaksi bisa pinjam banyak buku
- Pilih aturan peminjaman
- Kode peminjaman otomatis
- Tanggal pinjam & jatuh tempo otomatis
- Status: Borrowed, Returned, Partially Returned, Overdue
- Perhitungan denda otomatis
- Proses pengembalian dengan kondisi buku

### 6. Pengembalian
- Pilih buku yang dikembalikan (support partial return)
- Input kondisi buku saat dikembali
- Perhitungan denda otomatis:
  - Denda keterlambatan
  - Denda kerusakan (ringan/berat)
  - Denda buku hilang
- Update stok otomatis

### 7. Aturan Peminjaman
- Multiple loan rules
- Konfigurasi per rule:
  - Max pinjaman aktif
  - Durasi peminjaman
  - Masa tenggang
  - Denda per hari
  - Perpanjangan (ya/tidak & max berapa kali)
  - Denda kerusakan
  - Denda buku hilang (fixed/harga buku)

### 8. Penulis
- Data lengkap penulis
- Foto penulis
- Biografi
- Kontak:
  - Email
  - Telepon
  - Website
- Sosial Media:
  - Facebook
  - Twitter/X
  - Instagram
  - LinkedIn

### 9. Genre
- Manajemen genre buku
- 1 buku bisa punya banyak genre
- Deskripsi genre
- Tracking jumlah buku per genre

### 10. Site Settings
- Nama perpustakaan
- Tagline
- Logo (muncul di navbar)
- Favicon

### 11. Profile Management
- Admin bisa edit profil sendiri
- Update:
  - Nama akun
  - Email
  - Password
  - Foto profil (muncul sebagai avatar di navbar)

## 🔧 Troubleshooting

### Error: "Class not found"

```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
```

### Error: "Storage link not found"

```bash
php artisan storage:link
```

### Error: "Permission denied" (Linux/Mac)

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Error: Database connection

1. Pastikan MySQL service berjalan
2. Cek kredensial di file `.env`
3. Pastikan database sudah dibuat
4. Test koneksi:

```bash
php artisan db:show
```

### Error: "Mix manifest not found"

```bash
npm install
npm run build
```

### Error: "419 Page Expired" saat login

```bash
php artisan config:clear
php artisan cache:clear
```

### Reset Database

Jika ingin reset database dari awal:

```bash
php artisan migrate:fresh --seed
```

⚠️ **PERINGATAN**: Command ini akan menghapus semua data!

## 📚 Dokumentasi Tambahan

### Struktur Folder

```
library-app/
├── app/
│   ├── Actions/          # Business logic actions
│   ├── Enums/           # Enum classes
│   ├── Filament/        # Filament resources & pages
│   ├── Http/            # Controllers & middleware
│   ├── Models/          # Eloquent models
│   └── Services/        # Service classes
├── database/
│   ├── migrations/      # Database migrations
│   └── seeders/         # Database seeders
├── public/              # Public assets
├── resources/
│   ├── views/          # Blade templates
│   └── css/            # CSS files
└── routes/             # Route definitions
```

### Menambah Admin Baru

1. Login sebagai Super Admin
2. Menu: **Manajemen User → Admin**
3. Klik "Tambah"
4. Isi form:
   - Kode Anggota: Auto-generate
   - Nama Akun: Nama untuk login
   - Email: Email untuk login
   - Password: Min 8 karakter
   - Role: Pilih Admin atau Super Admin
   - Data Identitas: Isi sesuai kebutuhan
5. Klik "Simpan"

### Menambah Anggota Baru

1. Menu: **Manajemen User → Anggota**
2. Klik "Tambah"
3. Isi form:
   - **Informasi Akun**:
     - Kode Anggota: Auto-generate (M-DDMMYY-XXXXXXXX)
     - Nama Akun: Nama untuk login
     - Email: Email untuk login
     - Password: Min 8 karakter
   - **Data Pribadi**:
     - Nama Lengkap: Sesuai identitas
     - NIK, Jenis Kelamin, dll
4. Klik "Simpan"

### Menambah Buku Baru

1. Menu: **Master Data → Buku**
2. Klik "Tambah"
3. Isi form:
   - **Informasi Dasar**:
     - Judul, Kategori, Penerbit
     - Penulis (bisa multiple)
     - Rak (optional)
   - **Detail Buku**:
     - ISBN, SKU
     - Tahun, Edisi, Bahasa
     - Jumlah Halaman
   - **Genre**: Pilih multiple genre
   - **Stok**: Stok awal (tidak bisa diubah setelah dibuat)
   - **Harga**: Harga beli & pengganti
   - **Deskripsi**: Upload cover, deskripsi, sinopsis
4. Klik "Simpan"

### Proses Peminjaman

1. Menu: **Transaksi → Peminjaman**
2. Klik "Tambah"
3. Isi form:
   - Pilih Anggota
   - Pilih Aturan Peminjaman
   - Tanggal Pinjam (default: hari ini)
   - Tambah Buku (bisa multiple):
     - Pilih buku dari dropdown
     - Klik "Tambah Buku" untuk menambah buku lain
   - Catatan (optional)
4. Klik "Simpan"
5. Sistem akan:
   - Generate kode peminjaman otomatis
   - Hitung tanggal jatuh tempo
   - Update stok buku
   - Update status book copy

### Proses Pengembalian

1. Menu: **Transaksi → Peminjaman**
2. Cari peminjaman yang akan dikembalikan
3. Klik tombol "Kembalikan"
4. Pilih buku yang dikembalikan:
   - Bisa partial (tidak semua buku dikembalikan)
   - Pilih kondisi buku saat dikembalikan
   - Tambah catatan jika perlu
5. Klik "Kembalikan"
6. Sistem akan:
   - Hitung denda (keterlambatan + kerusakan)
   - Update status peminjaman
   - Update stok buku
   - Generate fine record

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Project ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail.

## 👥 Tim Pengembang

- **Developer**: Rifqi
- **Repository**: [https://github.com/rifqi011/usk-perpus](https://github.com/rifqi011/usk-perpus)

## 📞 Kontak & Support

Jika ada pertanyaan atau masalah, silakan:
- Buat issue di GitHub
- Email: [email Anda]

## 🙏 Acknowledgments

- Laravel Framework
- Filament Admin Panel
- Livewire
- Alpine.js
- Tailwind CSS

---

**Dibuat dengan ❤️ untuk Perpustakaan USK**
