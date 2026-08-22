# Design Document — Login Dasar

Dokumen desain teknis. Implementasi fitur baru **wajib** mengikuti arsitektur & pola yang dijelaskan di sini, dan dokumen ini diperbarui setiap ada perubahan teknis.

---

## 1. Arsitektur Sistem

Aplikasi monolit Laravel (MVC) dengan server-side rendering Blade.

```
┌─────────────────────────────────────────────────────────┐
│                      Browser                            │
│         (Blade + Tailwind CSS, form POST standar)       │
└───────────────┬─────────────────────────────────────────┘
                │ HTTP
┌───────────────▼─────────────────────────────────────────┐
│                  Laravel (PHP ^8.3)                     │
│                                                         │
│  routes/web.php ──► Middleware ──► Controller ──► Model │
│      (guest/auth)                 (LoginController)     │
│                                         │               │
│                              Auth::attempt() ─► users   │
└───────────────┬─────────────────────────────────────────┘
                │
┌───────────────▼─────────────────────────────────────────┐
│              SQLite (database/database.sqlite)          │
│        users / sessions / cache / jobs                  │
└─────────────────────────────────────────────────────────┘
```

Keputusan arsitektur utama:

| Keputusan | Alasan |
|---|---|
| Monolit SSR (bukan SPA/API) | Proyek "dasar", sederhana & mudah dikembangkan |
| Session-based auth | Standar Laravel untuk web app, aman secara default |
| SQLite | Zero-config untuk development; ganti via `.env` bila perlu |
| Tailwind v4 utility classes langsung di Blade | Tanpa framework JS tambahan |

## 2. Alur Autentikasi

### 2.1 Login

```
GET /login (guest)
   └─► LoginController@create ──► view auth/login

POST /login (guest)                    [throttle: max 5 percobaan/menit per username|IP]
   ├─ Validasi: username required, password required
   ├─ Gagal rate-limit ──► error "Terlalu banyak percobaan..."
   ├─ Auth::attempt(username, password, remember)
   │    ├─ gagal ──► hit RateLimiter ──► error "Username atau password salah."
   │    └─ sukses
   │         ├─ clear RateLimiter
   │         ├─ session()->regenerate()
   │         └─ redirect()->intended('/dashboard')
   ▼
GET /dashboard (auth)
   └─ tampilkan nama + @username, tombol logout
```

### 2.2 Logout

```
POST /logout (auth)
   ├─ Auth::guard('web')->logout()
   ├─ session()->invalidate()
   ├─ session()->regenerateToken()
   └─ redirect '/'
```

## 3. Desain Database

Tabel `users`:

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint PK | |
| **username** | string, unique | Dipakai untuk login (bukan email) |
| name | string | Nama lengkap, tampil di dashboard |
| email | string, unique | Masih ada dari skeleton; belum dipakai login |
| email_verified_at | timestamp nullable | Belum dipakai |
| password | string | Auto-hash via cast `hashed` di model |
| remember_token | string | Untuk "ingat saya" |
| created_at / updated_at | timestamps | |

Tabel pendukung bawaan: `sessions` (session driver database), `cache`, `jobs`.

> Konvensi migrasi: satu file migration per perubahan skema, nama deskriptif (`add_x_to_y_table`, `create_z_table`).

## 4. Route

| Method | URI | Nama | Middleware | Handler |
|---|---|---|---|---|
| GET | `/` | - | - | view welcome |
| GET | `/login` | `login` | guest | `LoginController@create` |
| POST | `/login` | `login.store` | guest | `LoginController@store` |
| POST | `/logout` | `logout` | auth | `LoginController@destroy` |
| GET | `/dashboard` | `dashboard` | auth | closure → view dashboard |

> Konvensi route: grupkan berdasarkan middleware (`guest` untuk halaman sebelum login, `auth` untuk sesudah). Beri `name()` pada semua route.

## 5. Struktur Kode & Pola

- **Controller**: method RESTful — `create` (tampilkan form), `store` (proses), `destroy` (hapus/logout).
- **Model**: fillable dideklarasikan via PHP attribute `#[Fillable([...])]`, hidden via `#[Hidden([...])]` (gaya Laravel 13).
- **Validasi**: `$request->validate()` di controller, pesan default + custom dalam Bahasa Indonesia.
- **Komponen Blade anonim**: layout di `resources/views/components/`, dipakai sebagai `<x-app-layout>` (didaftarkan manual via `Blade::anonymousComponentPath()` di AppServiceProvider karena tidak auto-register di skeleton ini).

## 6. Desain UI

### 6.1 Design System (Tailwind v4)

Warna mengikuti tema bawaan Laravel skeleton:

| Elemen | Light | Dark |
|---|---|---|
| Background | `#FDFDFC` | `#0a0a0a` |
| Surface/card | `bg-white`, border `#e3e3e0` | `#161615`, border `#3E3E3A` |
| Teks utama | `#1b1b18` | `#EDEDEC` |
| Teks sekunder | `#706f6c` | `#A1A099` |
| Aksen/error | `#F53003` (focus), red-600 (error text) | red-400 |

Tipografi: Instrument Sans (dimuat via directive `@fonts`).

### 6.2 Komponen Halaman

- **Layout** (`components/app-layout.blade.php`): body full-screen terpusat, slot tunggal.
- **Form card**: card putih rounded-lg + shadow-sm, label di atas input, error merah di bawah input.
- **Button primary**: gelap solid (light) / terang (dark), full-width di form.

> Konvensi UI: setiap elemen interaktif wajib punya style `hover:` dan mendukung dark mode (`dark:` variant).

## 7. Keamanan

- Password di-hash otomatis (cast `hashed`) — jangan pernah hash manual.
- CSRF protection aktif di semua form (`@csrf`).
- Rate limiting login: 5 percobaan gagal/menit per `username|ip` (RateLimiter).
- Session regeneration saat login, invalidation + token regeneration saat logout.
- Pesan error generik ("Username atau password salah") — tidak membocorkan keberadaan akun.
- `.env` tidak di-commit; kredensial hanya lewat environment variable.

## 8. Testing

- Framework: PHPUnit (`composer test`).
- Struktur: `tests/Feature/*Test.php` untuk alur HTTP, `tests/Unit/*Test.php` untuk logika murni.
- Test memakai `RefreshDatabase`; SQLite in-memory via konfigurasi phpunit.xml.
- Fitur baru wajib punya feature test minimal: happy path + 1 kasus gagal.

## 9. Checklist Penambahan Fitur Baru

1. Daftarkan fitur di `PRD.md` (bagian 3 & 4) lengkap dengan acceptance criteria.
2. Rancang perubahan teknis di `design.md` (route, tabel, komponen UI).
3. Buat migrasi + update model bila skema berubah.
4. Implementasi controller/route/view sesuai pola bagian 5.
5. Tulis feature test.
6. Jalankan `composer test` dan `vendor/bin/pint`.
7. Update status fitur menjadi ✅ di `PRD.md` dan catat implementasi final di `design.md`.
