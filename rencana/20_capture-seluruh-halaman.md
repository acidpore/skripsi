# 20 - Capture Nyata Seluruh Halaman Project via Chrome

Tanggal: 23 September 2026

## Ringkasan Eksekusi

Sesuai instruksi owner, dilakukan proses pemahaman menyeluruh terhadap arsitektur project, domain SPK MBG, aturan kerja (`rencana/rule.md`, `rencana/DESIGN.md`, `rencana/PRD_Sistem_SPK_MBG.md`), serta eksekusi tangkapan layar nyata (live capture) menggunakan Google Chrome binary asli (`C:\Program Files\Google\Chrome\Application\chrome.exe`) melalui automasi Playwright Python pada server lokal yang berjalan di port 8000.

Seluruh file tangkapan layar disimpan di folder `rencana/screenshots/`.

---

## Daftar Tangkapan Layar yang Dihasilkan

| No | File | Halaman / Rute | Keterangan & State |
|---|---|---|---|
| 01 | `01-landing-page.png` | `GET /` | Halaman landing welcome dengan tipografi Fraunces & IBM Plex Sans, pengenalan sistem SPK MBG berbasis TKPI & standar AKG, serta acuan Juknis BGN. |
| 02 | `02-login-page.png` | `GET /login` | Halaman autentikasi sistem untuk Operator SPPG dan Admin. |
| 03 | `03-rekomendasi-operator-awal.png` | `GET /rekomendasi` | Kondisi awal saat Operator SPPG login (SPPG otomatis terkunci ke SPPG operator bersangkutan). |
| 04 | `04-rekomendasi-hasil-ranking-topsis.png` | `GET /rekomendasi` | Hasil kalkulasi rekomendasi menu setelah form disubmit (5 kandidat menu teratas, skor kedekatan relatif TOPSIS, dan kesesuaian nilai gizi makro vs rentang AKG). |
| 05 | `05-rekomendasi-detail-komposisi.png` | `GET /rekomendasi` | Detail breakdown komposisi gramasi 7 komponen penukar untuk tiap kandidat, visualisasi tabel bobot objektif Entropy, serta tabel kebutuhan bahan baku & BDD. |
| 06 | `06-cetak-daftar-belanja.png` | `GET /rekomendasi/{id}/cetak` | Halaman siap cetak/ekspor PDF lembar kerja resmi Daftar Belanja Bahan Pangan dengan kolom Berat Bersih dan Berat Kotor. |
| 07 | `07-dokumentasi-metode.png` | `GET /dokumentasi/metode` | Dokumentasi metode matematis ilmiah: Komponen Penukar BGN, Standar AKG Permenkes, Pagu Biaya, Bobot Entropi Shannon, dan Jarak Solusi Ideal TOPSIS. |
| 08 | `08-halaman-404.png` | `GET /halaman-tidak-ada-404` | Halaman error 404 berdesain kertas institusional yang konsisten dengan tema layout utama. |
| 09 | `09-rekomendasi-admin-view.png` | `GET /rekomendasi` | Kondisi saat Admin SPK MBG login, di mana kolom SPPG aktif dan dapat mencari seluruh SPPG di sistem. |

---

## Pemahaman Arsitektur & Aturan Project

1. **Stack Aktual**:
   - Backend: Laravel 12 (PHP 8.3 Laragon), SQLite lokal (`database/database.sqlite`).
   - Frontend: Laravel Livewire 3/4 + Alpine.js, Tailwind CSS via CDN token kustom di `app.blade.php`.
   - Engine SPK: Pembangkitan kandidat menu berbasis 7 komponen penukar (Juknis BGN 401.1/2025), filter energi (gate), pembobotan objektif Entropi Shannon (protein, lemak, karbohidrat), dan perangkingan tunggal TOPSIS.
2. **Kepatuhan Desain (`DESIGN.md`)**:
   - Palet warna: `paper`, `ink`, `line`, `leaf`, `ember`.
   - Tipografi: Fraunces (judul), IBM Plex Sans (body), IBM Plex Mono (angka `.num` tabular).
   - Tanpa rounded besar, tanpa shadow, tanpa gradien, tanpa emoji/karakter non-ASCII.
3. **Autentikasi & Multi-Tenant**:
   - Operator SPPG terikat ke satu instansi SPPG miliknya.
   - Admin memiliki akses global untuk memilih dan menyusun menu untuk SPPG manapun.
