# Sistem Inventaris Sekolah

Project aplikasi web Sistem Inventaris Sekolah berbasis **Laravel 11**, **Laravel Breeze (Blade Stack)**, dan **MySQL**. Aplikasi ini dirancang untuk mempermudah pengelolaan data barang inventaris sekolah, kategori, proses permohonan dan persetujuan peminjaman, pencatatan pengembalian barang beserta pemulihan stok otomatis, serta audit log aktivitas pengguna.

---

## 🚀 Tech Stack & Spesifikasi

* **Framework**: Laravel 11.57.0+
* **View Engine**: Blade Template + Tailwind CSS
* **Autentikasi**: Laravel Breeze (Blade stack)
* **Database**: MySQL (`school_inventory`)
* **OR/M**: Eloquent Relationship & Soft Deletes
* **Visualisasi**: Chart.js (Grafik Peminjaman Bulanan)
* **Konvensi Naming**: Bahasa Inggris (Models, Controllers, Migrations, Routes, Variables)

---

## 👥 Role & Fitur Sistem

### 👑 1. Role Admin
* **Dashboard Analytics**: Statistik total akun, kategori, barang, permohonan aktif, serta **Grafik Peminjaman Bulanan** (Chart.js) dan Activity Log terbaru.
* **Manajemen Kategori Barang (`/categories`)**: Tambah, edit, hapus, dan cari kategori. Validasi menggunakan `StoreCategoryRequest` & `UpdateCategoryRequest`.
* **Manajemen Data Barang (`/items`)**: Tambah, edit, hapus barang inventaris beserta kode barang, stok, dan kondisi. Validasi menggunakan `StoreItemRequest` & `UpdateItemRequest`.
* **Verifikasi Peminjaman (`/loans`)**: Memverifikasi peminjaman barang (`Approve` / `Reject`) menggunakan `DB::transaction()` untuk pengurangan stok secara otomatis.
* **Verifikasi Pengembalian (`/returns`)**: Menginput tanggal pengembalian dan kondisi barang pada `return_logs` serta secara otomatis **mengembalikan stok barang**.
* **Audit Log Aktivitas (`/activity-logs`)**: Melihat seluruh daftar riwayat aktivitas sistem dengan filter pengguna dan tipe aksi.
* **Manajemen Pengguna (`/users`)**: Menambah, mengedit role/password, dan menghapus akun pengguna (Admin/User).

### 👤 2. Role User
* **Dashboard User**: Menampilkan ringkasan total pinjaman pribadi, pinjaman aktif, dan stok barang tersedia.
* **Katalog Barang Tersedia (`/items`)**: Melihat daftar inventaris barang yang tersedia dan fitur **Request Loan**.
* **Pengajuan & Riwayat Peminjaman (`/loans`)**: Mengajukan peminjaman barang dan memantau status peminjaman pribadi (`pending`, `approved`, `returned`, `rejected`, `overdue`).

---

## 🔑 Data Seeder & Kredensial Login

Aplikasi telah dilengkapi dengan Seeder otomatis. Berikut adalah kredensial login default:

| Role | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@school.com` | `password` | Pengelola Seluruh Sistem |
| **User 1** | `budi@school.com` | `password` | Pengguna / Siswa |
| **User 2** | `siti@school.com` | `password` | Pengguna / Siswa |

---

## 🛠️ Langkah Instalasi & Pengoperasian

### 1. Clone & Install Dependencies
```bash
composer install
npm install
npm run build
```

### 2. Konfigurasi Environment (`.env`)
Pastikan pengaturan koneksi database MySQL di file `.env` sudah sesuai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_inventory
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi Database & Seeder
Jalankan perintah berikut untuk membuat database dan mengisi data sampel:
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 4. Menjalankan Command Penjadwalan Terlambat (Overdue Check)
Untuk menguji pemeriksaan otomatis peminjaman yang melewati jatuh tempo:
```bash
php artisan loans:check-overdue
```

### 5. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **http://127.0.0.1:8000**

---

## 📑 Catatan Pengembangan
* Seluruh form memiliki `placeholder` dan penanda field wajib (`*`).
* Seluruh format tanggal menggunakan format standar Indonesia (`d M Y` / `d M Y H:i WIB`).
* Seluruh format angka stok menggunakan `number_format(..., 0, ',', '.')`.
* Soft Delete diimplementasikan pada tabel `loans` dan `return_logs`.
