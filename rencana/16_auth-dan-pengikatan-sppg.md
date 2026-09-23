# 16 — Auth dan Pengikatan Operator ke SPPG

Tanggal: 14 September 2026

## Yang Dibuat

| File | Isi |
|---|---|
| `app/Livewire/Auth/Login.php` + `resources/views/livewire/auth/login.blade.php` | Form login pakai `Auth::attempt()` bawaan Laravel — tidak ada package tambahan (Breeze/Fortify/Jetstream), karena `laravel/framework` sudah menyediakan sistem auth session lengkap tanpa scaffolding, dan menambah package berarti `composer require` + kemungkinan `npm install` yang di luar wewenang AI (rule.md poin 7) |
| `routes/web.php` | `GET /login` (middleware `guest`), `POST /logout`, `GET /rekomendasi` dipindah ke dalam grup middleware `auth` |
| `bootstrap/app.php` | `redirectGuestsTo('/login')` supaya pengunjung tanpa sesi diarahkan ke halaman login, bukan 403 default |
| `resources/views/components/layouts/app.blade.php` | Header ditambah info user login (nama + peran) dan tombol keluar, hanya tampil kalau `@auth` |
| `app/Livewire/RekomendasiMenu.php` | `mount()`: kalau `Auth::user()->isOperator()`, `sppgId` dikunci ke `sppg_id` miliknya dan pencarian SPPG disembunyikan. Guard kedua di `generate()`: operator yang tetap kirim `sppgId` lain (lewat DevTools/manipulasi request) ditolak dengan pesan error, bukan cuma disembunyikan di UI |
| `resources/views/livewire/rekomendasi-menu.blade.php` | Kotak SPPG jadi teks statis read-only kalau `sppgTerkunci`, form pencarian tetap ada buat admin |
| `database/seeders/UserSeeder.php` | Akun uji: `admin@spk-mbg.test` (role admin, `sppg_id` null) dan `operator@spk-mbg.test` (role operator, terikat ke `Sppg::first()`), password sama-sama `password` — untuk pengembangan saja, wajib diganti sebelum dipakai di luar itu |

## Keputusan Desain

**Tidak pakai Breeze/Fortify.** Kebutuhan auth di langkah ini sempit: login, logout, proteksi route, kunci tenant. `Illuminate\Auth` bawaan Laravel 12 sudah cukup tanpa scaffolding tambahan — sesuai prinsip pakai yang sudah ada dulu sebelum nambah dependency baru.

**Registrasi mandiri SPPG (PRD 3.1) ditunda.** PRD sebut "Registrasi & login SPPG" sebagai satu modul, tapi langkah 5 yang diminta secara spesifik cuma "auth + pengikatan operator ke SPPG". Alur registrasi (bikin `Sppg` baru + `User` sekaligus, siapa yang boleh mendaftar) adalah keputusan produk tersendiri yang belum dibahas — untuk sekarang akun dibuat lewat seeder/tinker oleh admin, konsisten dengan tabel peran PRD Bagian 2 yang bilang **Admin** yang "mengelola akun SPPG".

**Guard tenant dobel: UI dan server.** Menyembunyikan input SPPG di form saja tidak cukup (rule.md poin 3 eksplisit soal filter per tenant) — kalau operator memanipulasi request langsung, `generate()` tetap menolak `sppgId` yang bukan miliknya sebelum memanggil service.

## Verifikasi

Diuji lewat browser:
1. Akses `/rekomendasi` tanpa login -> redirect ke `/login`
2. Login sebagai `operator@spk-mbg.test` -> masuk ke `/rekomendasi`, header tampil "Operator SPPG Kebumen Buayan Rangkah (Operator - SPPG Kebumen Buayan Rangkah)"
3. Kotak SPPG jadi teks statis "SPPG Kebumen Buayan Rangkah", tidak bisa diketik/diganti
4. Generate rekomendasi kelompok TK/PAUD tetap berjalan normal (Spearman, bobot Entropy, tabel ranking semua tampil) walau SPPG terkunci

Belum diuji manual lewat browser: guard server-side (operator kirim `sppgId` lain lewat request dimanipulasi) dan alur login admin (`admin@spk-mbg.test`, harus tetap bisa cari SPPG bebas). Kedua jalur ini logikanya sama dengan yang sudah diverifikasi di kode, tidak melibatkan pola baru.

## Sisa Pekerjaan

Langkah 6 (export daftar belanja) — developer sudah minta format ekspor (satuan berat bersih vs kotor, per bahan penukar) disinkronkan istilahnya dengan subbab 3.6.1 naskah (Berat Bersih = Jumlah SP x Berat per SP) supaya tangkapan layar hasil export bisa langsung jadi bukti implementasi di Bab IV tanpa menyimpang istilah.

Item backlog dari progress 15 (bahan tersedia picker, grafik Chart.js) masih menunggu, tidak terhalang oleh langkah ini.
