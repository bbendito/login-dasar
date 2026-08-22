# Login Dasar

Aplikasi web dengan sistem autentikasi berbasis **username & password**, dibangun dengan Laravel. Dokumen ini adalah panduan utama proyek. Untuk kebutuhan produk dan desain teknis, lihat:

- [`PRD.md`](PRD.md) — Product Requirements Document (kebutuhan produk, fitur, roadmap)
- [`design.md`](design.md) — Design document (arsitektur teknis, database, UI/UX)

> **Aturan penting:** Setiap penambahan atau perubahan fitur wajib mengacu dan memperbarui `PRD.md` serta `design.md` agar dokumentasi tetap sinkron dengan kode.

---

## Tech Stack

| Layer | Teknologi | Versi |
|---|---|---|
| Backend | PHP | ^8.3 |
| Framework | Laravel | ^13.17 |
| Database | SQLite (default, bisa diganti MySQL/PostgreSQL via `.env`) | - |
| Frontend | Blade Template + Tailwind CSS | v4 |
| Asset bundler | Vite | ^8 |
| Testing | PHPUnit | ^12 |

## Instalasi

```bash
# 1. Install dependensi backend
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrasi + seed data awal
php artisan migrate --seed

# 4. Install dependensi frontend & build asset
npm install
npm run build
```

Atau sekali jalan: `composer setup`

## Menjalankan Aplikasi

```bash
composer dev          # server dev + queue + vite (all-in-one)
```

atau manual terpisah:

```bash
php artisan serve     # aplikasi -> http://localhost:8000
npm run dev           # vite dev server (hot reload)
```

## Akun Demo

Setelah `db:seed`:

| Username | Password |
|---|---|
| `testuser` | `password` |

## Struktur Proyek

```
login-dasar/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Controller.php        # Base controller
│   │       └── LoginController.php   # Auth: create/store/destroy
│   ├── Models/
│   │   └── User.php                  # Authenticatable + kolom username
│   └── Providers/
│       └── AppServiceProvider.php    # Registrasi komponen blade anonim
├── bootstrap/app.php                 # Konfigurasi routing/middleware/exceptions
├── config/                           # Konfigurasi framework
├── database/
│   ├── factories/UserFactory.php
│   ├── migrations/                   # users (+username), cache, jobs
│   └── seeders/DatabaseSeeder.php    # User demo
├── public/                           # Entry point + hasil build vite
├── resources/
│   ├── css/app.css                   # Tailwind entry
│   ├── js/app.js
│   └── views/
│       ├── auth/login.blade.php      # Halaman login
│       ├── dashboard.blade.php       # Halaman setelah login
│       ├── welcome.blade.php         # Landing page bawaan
│       └── components/app-layout.blade.php  # Layout dasar (dipakai via <x-app-layout>)
├── routes/web.php                    # Semua route web
└── tests/                            # Feature & Unit tests
```

## Perintah Berguna

```bash
composer test              # jalankan seluruh test
vendor/bin/pint            # code style fixer
php artisan migrate:fresh --seed   # reset database + data demo
php artisan route:list     # lihat semua route
```

## Konvensi Pengembangan

1. **Bahasa**: Kode & identifier dalam Inggris, konten UI & pesan error dalam Bahasa Indonesia.
2. **Dokumentasi**: Fitur baru = update `PRD.md` (kebutuhan) + `design.md` (implementasi).
3. **Testing**: Fitur baru wajib disertai minimal 1 feature test.
4. **Style**: Ikuti PSR-12, verifikasi dengan Pint.
5. **Keamanan**: Password selalu hashed (`cast 'hashed'` di model), form pakai `@csrf`, validasi di controller.
