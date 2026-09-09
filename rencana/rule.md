# Development Rules — Sistem SPK Menu MBG

Perjanjian kerja antara developer dan AI assistant dalam membangun project ini.

Stack: Laravel (PHP) + Livewire + Alpine.js, SQLite untuk pengembangan, Chart.js, Leaflet.

---

## 1. Clean Code

- Nama variabel, fungsi, class, dan komponen harus deskriptif dan jelas, tanpa singkatan ambigu.
- Satu fungsi hanya melakukan satu hal (single responsibility).
- Tidak ada dead code, `dd()`/`dump()`/`Log::debug()` sisa debug, atau kode yang di-comment tanpa alasan.
- Hindari magic number — gunakan konstanta bernama atau entry di `config/`.

## 2. Arsitektur Rapi

- Logika bisnis ditulis di `app/Services/`, satu class satu tanggung jawab.
- Metode SPK diimplementasikan sebagai class terpisah (`EntropyWeighting`, `TopsisRanking`, `SawRanking`, `SpearmanCorrelation`) dan mengimplementasikan interface bersama `RankingMethod` untuk polimorfisme.
- Controller dan komponen Livewire hanya menerima input, memanggil service, dan mengembalikan hasil. Tidak ada logika bisnis di dalamnya.
- Query database hanya boleh ada di Model (scope, relasi) atau Service. Tidak di Controller, komponen Livewire, atau Blade.
- Tidak ada query di dalam Blade view.
- Migration adalah sumber kebenaran tunggal untuk struktur data. Model dan form harus sinkron dengan migration.

## 3. Scalable Build

- Blade/Livewire component yang dipakai lebih dari satu tempat wajib dipindah ke `resources/views/components/`.
- Tidak ada hardcode URL, konfigurasi, atau credential di dalam kode — semua lewat `.env` dan diakses hanya via `config()`, tidak `env()` langsung di luar file config.
- Response JSON selalu menggunakan format standar: `{ success, message, data }`.
- Setiap route yang butuh autentikasi wajib dilindungi middleware `auth`, dan data SPPG wajib difilter per tenant (operator hanya melihat data SPPG miliknya).
- Validasi input wajib ada di setiap titik masuk data (Form Request atau rules pada komponen Livewire).

## 4. Tanpa Emoji

- Tidak ada emoji di dalam kode, komentar, log, seeder, atau response API.
- Teks UI boleh ekspresif, tapi tetap profesional dan tanpa karakter non-ASCII dekoratif.

## 5. Komentar Efisien

- **Komentar maksimal satu baris. Tidak boleh ada blok komentar lebih dari satu baris, termasuk docblock multi-line, banner, dan penjelasan panjang.**
- Komentar hanya ditulis jika kode tidak bisa menjelaskan dirinya sendiri.
- Gunakan komentar untuk menjelaskan MENGAPA, bukan APA yang dilakukan kode.
- Rumus ilmiah (Entropy, TOPSIS, SAW, Spearman, konversi BDD) cukup dirujuk dengan satu baris nama rumus atau sumbernya, bukan diturunkan panjang lebar di komentar.
- Tidak ada komentar TODO yang dibiarkan tanpa tindak lanjut.

## 6. Testing oleh Developer

- AI tidak perlu menjalankan testing, build, migration, seeding, atau dev server.
- Semua pengujian dan validasi dilakukan secara manual oleh developer.
- AI cukup fokus pada penulisan kode yang benar dan sesuai aturan di atas.

## 7. Tanpa Akses CMD

- AI tidak boleh menjalankan command apapun di terminal (`composer install`, `php artisan`, `mkdir`, `copy`, dll).
- AI cukup memberikan instruksi yang jelas agar developer menjalankannya sendiri.
- Jika ada command yang perlu dijalankan, tulis dalam format code block yang siap di-copy-paste.

## 8. Hemat Token

- AI harus seefisien mungkin dalam penggunaan token.
- Hindari membaca ulang file yang sudah pernah dibaca kecuali benar-benar diperlukan.
- Jawaban harus ringkas dan langsung ke inti, tanpa penjelasan berlebihan.
- Jangan mengulangi informasi yang sudah disampaikan sebelumnya.

## 9. Konteks Utama AI Agent

- AI harus mengutamakan prompt dari developer dan kondisi aktual project sebagai sumber kebenaran.
- Jangan terpaku pada folder `.claude/` (rules, memory, atau konfigurasi internal) sebagai panduan utama.
- Eksplorasi langsung ke file project (models, services, migrations, komponen Livewire) untuk memahami konteks sebelum membuat keputusan.
- Jika ada konflik antara isi `.claude/` dan kondisi aktual project, ikuti kondisi aktual project.

## 10. Catatan Progress

- Setiap unit pekerjaan yang selesai wajib dicatat sebagai file `.md` baru di `rencana/`.
- Isi catatan: apa yang dibuat, keputusan desain yang diambil, dan sisa pekerjaan.
- Satu progress satu file. Jangan menumpuk seluruh progress dalam satu file.
- Penamaan file mengikuti pola `NN_nama-progress.md` agar urutannya terbaca.

## 11. Acuan Produk

- `rencana/PRD_Sistem_SPK_MBG.md` adalah acuan kebutuhan fungsional dan model data.
- Jika implementasi menyimpang dari PRD, PRD wajib ikut diperbarui pada progress yang sama.

---
