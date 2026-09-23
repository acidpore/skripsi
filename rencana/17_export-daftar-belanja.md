# 17 — Export Daftar Belanja (PDF/Excel)

Tanggal: 14 September 2026

Langkah terakhir dari daftar "Sisa Pekerjaan" yang disusun sejak progress 11. Rantai fungsional MVP (generate -> SPK -> simpan menu -> auth -> export) selesai dari sisi kode.

## Yang Dibuat

| File | Isi |
|---|---|
| `app/Http/Controllers/DaftarBelanjaController.php` | `cetak()` — halaman HTML print-friendly; `csv()` — unduh CSV via `response()->streamDownload()`. Keduanya cuma terima input (route model binding), panggil `MaterialRequirementService`, kembalikan hasil — tidak ada logika bisnis baru (rule.md poin 2) |
| `resources/views/daftar-belanja/cetak.blade.php` | View mandiri (bukan lewat layout Livewire), tombol `window.print()`, CSS `@media print` menyembunyikan tombol saat benar-benar dicetak |
| `routes/web.php` | `GET /rekomendasi/{rekomendasi}/cetak` dan `GET /rekomendasi/{rekomendasi}/csv`, dua-duanya di dalam grup middleware `auth` |
| `resources/views/livewire/rekomendasi-menu.blade.php` | Tautan "Unduh CSV" dan "Cetak / PDF" di panel Kebutuhan Bahan |

## Keputusan Desain

**Tidak ada package composer baru.** PRD 3.6 sebut "Export daftar belanja (PDF/Excel)". Tidak ditambah `barryvdh/dompdf` atau `maatwebsite/excel` supaya tidak perlu `composer require` (aksi yang lebih berat, di luar kebutuhan langkah ini):
- **"Excel"** dipenuhi lewat CSV native (`fputcsv`, tanpa dependency) — CSV dibuka langsung oleh Excel, dan ini format yang sudah cukup untuk operator mengimpor ke spreadsheet.
- **"PDF"** dipenuhi lewat halaman HTML cetak-friendly + tombol `window.print()` bawaan browser ("Simpan sebagai PDF" adalah opsi print standar Chrome/Edge), bukan PDF yang di-generate di server.

Kalau nanti dibutuhkan PDF yang benar-benar di-generate server-side (misal buat lampiran otomatis, bukan aksi manual operator), itu perlu `composer require` yang harus dijalankan developer sendiri — dicatat sebagai kemungkinan pekerjaan lanjutan, bukan blocker sekarang.

**Format dan istilah disinkronkan ke subbab 3.6.1.** Kolom "Berat Bersih (kg)" dan "Berat Kotor (kg)" dipakai identik di CSV, halaman cetak, dan tabel Kebutuhan Bahan di form — semuanya bersumber dari `KebutuhanBahan.berat_bersih_gram`/`berat_kotor_gram` yang sudah dihitung `MaterialRequirementService` memakai rumus persamaan (1) di naskah (Berat Kotor = Berat Bersih / BDD x 100). Halaman cetak juga menyertakan kalimat penjelas rumus itu di footer, supaya tangkapan layar langsung membawa konteks metodologinya kalau dipakai sebagai bukti implementasi di Bab IV.

**Otorisasi tenant dobel-cek lagi di sini.** `DaftarBelanjaController::otorisasi()` mengulang pola yang sama dari `RekomendasiMenu::generate()` (progress 16) — operator cuma bisa export `Rekomendasi` milik SPPG-nya, dicek di server bukan cuma disembunyikan di UI, karena kedua route ini diakses lewat URL langsung (bisa di-bypass UI kalau tidak dicek ulang).

## Verifikasi

Diuji lewat browser sebagai operator (SPPG Kebumen Buayan Rangkah):
1. Generate rekomendasi SD 4-6, scroll ke panel Kebutuhan Bahan -> tautan "Unduh CSV" dan "Cetak / PDF" muncul
2. `/rekomendasi/35/cetak` dibuka di tab baru -> halaman render benar: judul, info SPPG/kelompok sasaran/porsi/tanggal, tabel bahan dengan kolom Berat Bersih/Berat Kotor, kalimat penjelas rumus BDD di bawah
3. Isi CSV diverifikasi lewat pemanggilan logika yang sama di luar HTTP (karena unduhan file tidak bisa diperiksa langsung di browser sandbox) — persis cocok dengan tabel di halaman cetak, 7 baris bahan

Belum diuji manual: guard 403 kalau operator akses rekomendasi SPPG lain lewat URL langsung (logikanya identik dengan guard `generate()` yang sudah diverifikasi di progress 16).

## Status MVP

Delapan progress (11-17) menutup seluruh rantai fungsional yang direncanakan sejak awal: pembangkitan kandidat menu, mesin SPK Entropy-TOPSIS-SAW-Spearman, kriteria gate-vs-keputusan, penyimpanan menu tetap, UI Livewire, auth + tenant, dan export. Sisa item backlog yang sengaja ditunda (bukan mendesak untuk MVP): picker "bahan tersedia per komponen" manual, grafik Chart.js pemenuhan gizi vs AKG, registrasi mandiri SPPG (PRD 3.1).

Developer sudah minta jeda dari sisi kode setelah ini, untuk menyinkronkan progress implementasi ke naskah Bab III (khususnya perubahan kriteria gate-vs-keputusan dari progress 12, dan temuan anomali TK/PAUD dari progress 14) sebelum menambah fitur baru.
