# MotorKita - Sistem Pemesanan & Manajemen Bengkel Motor

**Aplikasi Web Laravel 12 untuk Manajemen Bengkel Motor**

---

## 📋 Deskripsi

**MotorKita** adalah aplikasi web untuk bengkel motor dengan 3 role:
- **Pelanggan**: Booking servis & lihat riwayat
- **Mekanik**: Lihat task & submit laporan
- **Admin**: Kelola inventaris, billing, dan laporan

**Stack**: Laravel 12 + MySQL + Bootstrap 5

---

## 🚀 Quick Start

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL >= 5.7 atau MariaDB >= 10.3
- Node.js & NPM (untuk assets)

### 1. Clone & Install Dependencies

```bash
# Clone repository
git clone <repository-url>
cd motorKITA

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Konfigurasi Database MySQL

Edit file `.env` dan sesuaikan konfigurasi database MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=motorkita
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Penting**: Pastikan database `motorkita` sudah dibuat di MySQL sebelum menjalankan migration.

```sql
-- Buat database di MySQL
CREATE DATABASE motorkita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Jalankan Migration & Seeder

```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder (untuk data awal)
php artisan db:seed
```

### 5. Build Assets (Optional)

```bash
# Build untuk production
npm run build

# Atau jalankan dev server
npm run dev
```

### 6. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: **http://localhost:8000**

---

## 👤 Test Account

Setelah menjalankan seeder, gunakan akun berikut:

- **Admin**: 
  - Email: `admin@test.com`
  - Password: `password`

- **Mechanic**: 
  - Email: `mechanic@test.com`
  - Password: `password`

- **Customer**: 
  - Email: `customer@test.com`
  - Password: `password`

---

## 📁 Struktur Project

```
motorkita/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── CustomerController.php
│   │   ├── MechanicController.php
│   │   └── AdminController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Vehicle.php
│   │   ├── Booking.php
│   │   └── Inventory.php
│   └── Middleware/
│       └── CheckRole.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/
│   ├── customer/
│   ├── mechanic/
│   └── admin/
├── routes/web.php
├── database/
│   ├── migrations/
│   └── seeders/
└── .env
```

---

## 🗄️ Database Schema

### Tabel Utama:

1. **users** - User (Pelanggan, Mekanik, Admin)
2. **vehicles** - Kendaraan pelanggan
3. **bookings** - Pemesanan servis
4. **inventory** - Sparepart/Inventori

---

## ✨ Fitur

### Pelanggan
- ✅ Login/Register
- ✅ Booking servis
- ✅ Lihat status booking
- ✅ Riwayat servis
- ✅ Kelola kendaraan

### Mekanik
- ✅ Login
- ✅ Lihat list task hari ini
- ✅ Update status task
- ✅ Submit laporan servis

### Admin
- ✅ Login
- ✅ Manajemen sparepart (CRUD inventory)
- ✅ Lihat semua booking
- ✅ Assign mekanik ke booking
- ✅ Generate invoice/bill
- ✅ Dashboard dengan statistik

---

## 🔧 Troubleshooting

### Error: SQLSTATE[HY000] [1045] Access denied

Pastikan username dan password MySQL di file `.env` sudah benar.

### Error: SQLSTATE[HY000] [1049] Unknown database 'motorkita'

Buat database terlebih dahulu:
```sql
CREATE DATABASE motorkita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Error: PDOException - could not find driver

Install extension MySQL untuk PHP:
```bash
# Ubuntu/Debian
sudo apt-get install php-mysql

# Windows (XAMPP/WAMP)
# Aktifkan extension php_mysql.dll di php.ini
```

---

## 📝 License

MIT License

---

**Version**: 1.0  
**Created**: 2026
