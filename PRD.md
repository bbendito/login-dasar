# PRD — Login Dasar

Product Requirements Document. Setiap fitur baru **wajib** dicatat di sini sebelum diimplementasikan.

---

## 1. Visi

Aplikasi web dengan fondasi autentikasi yang solid, aman, dan mudah dikembangkan. Menjadi dasar (base template) untuk membangun aplikasi Laravel dengan fitur tambahan secara bertahap.

## 2. Target Pengguna

- **Pengguna terdaftar**: dapat masuk ke sistem menggunakan username & password.
- **Developer/pemilik proyek**: menggunakan proyek ini sebagai kerangka awal untuk fitur-fitur berikutnya.

## 3. Status Fitur

| # | Fitur | Status | Keterangan |
|---|---|---|---|
| F-01 | Login username + password | ✅ Selesai | Rate limit 5 percobaan/menit, opsi "ingat saya" |
| F-02 | Logout | ✅ Selesai | Invalidasi session + regenerate token |
| F-03 | Dashboard (halaman setelah login) | ✅ Selesai | Proteksi middleware `auth` |
| F-04 | Register akun baru | ⬜ Belum | Lihat roadmap |
| F-05 | Reset password | ⬜ Belum | Lihat roadmap |
| F-06 | Manajemen profil | ⬜ Belum | Edit nama/username/password |

> Saat menambah fitur: tambahkan baris baru dengan kode `F-XX`, ubah status menjadi 🚧 (dikerjakan) lalu ✅.

## 4. Detail Fitur

### F-01 — Login Username & Password (Selesai)

**Deskripsi**: Pengguna memasukkan kombinasi username dan password untuk mengakses area aplikasi yang dilindungi.

**User Stories**:
- Sebagai pengguna, saya ingin login dengan username & password sehingga saya bisa mengakses dashboard.
- Sebagai pengguna, saya ingin tetap ter-login setelah browser ditutup (opsi "ingat saya").
- Sebagai pengguna, saya ingin mendapat pesan yang jelas jika kredensial salah.
- Sebagai pemilik sistem, saya ingin percobaan login dibatasi agar tidak bisa brute-force.

**Acceptance Criteria**:
- [x] Form login menerima `username` dan `password` (keduanya wajib).
- [x] Jika kredensial benar → redirect ke `/dashboard`.
- [x] Jika kredensial salah → pesan error "Username atau password salah." tanpa membocorkan field mana yang keliru.
- [x] Lebih dari 5 percobaan gagal per username/IP dalam 1 menit → diblokir sementara dengan info waktu tunggu.
- [x] Session diregenerasi setelah login sukses (anti session fixation).
- [x] Pengguna yang sudah login tidak bisa mengakses halaman login lagi (middleware `guest`).
- [x] Opsi "remember me" menjaga sesi tetap aktif lintas browser restart.

### F-02 — Logout (Selesai)

**Acceptance Criteria**:
- [x] Tombol logout tersedia di dashboard.
- [x] Setelah logout: session di-invalidate, CSRF token diregenerasi, redirect ke halaman utama.
- [x] Hanya bisa diakses oleh pengguna yang sedang login (middleware `auth`).

### F-03 — Dashboard (Selesai)

**Acceptance Criteria**:
- [x] Hanya pengguna yang login yang bisa mengakses; jika belum → redirect ke `/login`.
- [x] Menampilkan nama dan username pengguna yang sedang login.
- [x] Setelah login sukses, pengguna diarahkan ke halaman terakhir yang ia tuju (`redirect()->intended`).

## 5. Roadmap

Prioritas fitur selanjutnya:

1. **F-04 Register** — pendaftaran akun mandiri (validasi username unik).
2. **F-05 Reset password** — via email (mailer saat ini masih `log`).
3. **F-06 Manajemen profil** — edit data diri & ganti password.
4. **Role & permission** — jika aplikasi berkembang multi-peran.
5. **Ganti database** ke MySQL/PostgreSQL bila sudah produksi.

Setiap item roadmap yang mulai dikerjakan wajib dipromosikan menjadi bagian fitur lengkap (deskripsi + user stories + acceptance criteria) pada bagian 4 dokumen ini.

## 6. Batasan & Asumsi

- Tidak ada proses verifikasi email untuk saat ini.
- Database default SQLite (file lokal) — cukup untuk development.
- Bahasa antarmuka: Indonesia.
- Single guard (`web`), autentikasi berbasis session (bukan API token).

## 7. Metrik Keberhasilan (untuk fitur mendatang)

- Login sukses < 2 detik.
- Pesan error informatif tanpa membocorkan informasi sensitif.
- Semua endpoint terproteksi sesuai kebutuhan (tidak ada akses tanpa otorisasi).
