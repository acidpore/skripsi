# 02 — Rencana Kerja & To-Do List

Tanggal: 09 September 2026

## Kondisi Aktual Project

Hasil pemeriksaan isi project sebelum menyusun rencana:

| Temuan | Keterangan |
|---|---|
| Laravel 12, PHP 8.2 | Terpasang, server berjalan di `127.0.0.1:8000` |
| Livewire | **Belum terpasang.** Padahal PRD Bagian 5 mewajibkannya |
| Sisa project lain | `app/Models/{Module, Material, QuestionnaireDataset, QuizResult, AccessLog}.php`, `app/Services/LearningStyleService.php`, 4 Console Command VARK, dan 5 migration `2026_01_17_*` adalah residu project prediksi gaya belajar VARK, bukan bagian SPK MBG |
| `rubix/ml` + `rubix/tensor` | Dependency machine learning milik project VARK. Tidak dipakai SPK MBG (Entropy/TOPSIS/SAW cukup PHP native) |
| Model & migration SPK | Belum ada satupun |

Artinya project ini bukan lahan kosong, melainkan Laravel bekas project lain yang harus dibersihkan lebih dulu.

## To-Do List

### Tahap 0 — Bersih-bersih & Fondasi

- [ ] Hapus residu project VARK: 5 Model, `LearningStyleService`, 4 Console Command, dan 5 migration `2026_01_17_*`
- [ ] Lepas dependency `rubix/ml` dan `rubix/tensor`
- [ ] Pasang Livewire 3
- [ ] Pastikan `.env` mengarah ke SQLite dan `database/database.sqlite` ada
- [ ] Siapkan layout dasar Blade (Tailwind atau Bootstrap via CDN, tanpa build step)

### Tahap 1 — Keputusan yang Memblokir (harus tuntas sebelum coding inti)

- [ ] **K1.** Periksa `TKPI.xlsx`: apakah kalsium, besi, dan vitamin C tersedia? Menentukan jumlah kriteria matriks keputusan
- [ ] **K2.** Tentukan daftar kriteria final beserta sifat benefit/cost tiap kriteria
- [ ] **K3.** Tentukan top-N alternatif menu per kelompok sasaran
- [ ] **K4.** Petakan `template_alternatif_menu.csv` dari nama masakan ke kode bahan TKPI + gram per porsi

K4 adalah blocker paling kritis. Tanpa keterkaitan menu ke bahan, kandungan gizi menu tidak dapat dihitung, sehingga matriks keputusan tidak dapat dibentuk dan seluruh mesin SPK tidak bisa diuji.

### Tahap 2 — Skema Data

- [ ] Migration + Model: `bahan_pangan`, `akg_harian`, `akg_sekali_makan`, `sppg`, `menu`, `menu_bahan`, `rekomendasi`, `kebutuhan_bahan`
- [ ] Kolom `sppg_id` pada `users` untuk multi-tenant
- [ ] Global scope multi-tenant agar operator hanya melihat data SPPG miliknya

### Tahap 3 — ETL & Seeder

- [ ] Seeder `bahan_pangan` dari `tkpi_bdd.csv` (lewati baris sampah ke-2, konversi koma desimal ke titik)
- [ ] Seeder `akg_harian` dan `akg_sekali_makan`
- [ ] Seeder `sppg` dari `sppg_data_with_coords_clean.csv`
- [ ] Seeder `menu` + `menu_bahan` dari hasil pemetaan K4

### Tahap 4 — Mesin SPK

- [ ] Interface `RankingMethod`
- [ ] `DecisionMatrixBuilder` — susun matriks dari alternatif menu dan acuan AKG
- [ ] `EntropyWeighting` — bobot objektif tiap kriteria
- [ ] `TopsisRanking` dan `SawRanking`
- [ ] `SpearmanCorrelation` — uji konsistensi dua ranking
- [ ] `RecommendationService` — orkestrasi seluruh alur dan penyimpanan hasil

### Tahap 5 — Kebutuhan Bahan

- [ ] `MaterialRequirementService` — berat kotor = berat bersih dibagi BDD dikali 100 dikali jumlah porsi
- [ ] Agregasi kebutuhan lintas menu terpilih
- [ ] Export daftar belanja

### Tahap 6 — Antarmuka

- [ ] Autentikasi dan profil SPPG
- [ ] Halaman basis bahan pangan (pencarian, filter kelompok, detail gizi)
- [ ] CRUD menu
- [ ] Halaman generate rekomendasi: form input, tabel ranking, grafik Chart.js gizi vs rentang AKG
- [ ] Halaman kebutuhan bahan dan daftar belanja
- [ ] Dashboard dan peta persebaran SPPG (Leaflet)
- [ ] Panel admin: kelola data acuan nasional dan akun SPPG

### Tahap 7 — Pengujian

- [ ] 30-40 skenario black box
- [ ] Uji akurasi gizi dan konsistensi ranking
- [ ] Pengujian ISO/IEC 25010:2023 sesuai PRD Bagian 6

## Rencana Sub Agent

Dua sub agent dijalankan paralel hanya setelah Tahap 2 selesai, karena keduanya bergantung pada skema data yang sudah terkunci.

**Agent 1 — Data & ETL**
Menangani Tahap 3 seluruhnya, ditambah verifikasi K1 terhadap `TKPI.xlsx`. Pekerjaannya berat di sisi pembacaan file dan parsing, tetapi keluarannya sedikit. Cocok didelegasikan agar isi CSV yang panjang tidak membanjiri konteks utama.

**Agent 2 — Mesin SPK**
Menangani Tahap 4, khusus class perhitungan murni. Seluruh class menerima array dan mengembalikan array, tanpa menyentuh database sama sekali, sehingga dapat dikerjakan terpisah tanpa risiko bentrok dengan pekerjaan Agent 1.

**Yang dikerjakan di thread utama**
Tahap 0, 1, 2, 5, 6, dan 7. Bagian ini menyangkut keputusan desain, integrasi antar modul, dan antarmuka yang perlu ditinjau langsung bersama developer.

Pembagian ini dipilih agar dua agent tidak pernah menyentuh file yang sama: Agent 1 hanya menulis di `database/seeders/`, Agent 2 hanya menulis di `app/Services/Spk/`.

## Catatan Pengujian

Developer menjalankan `php artisan serve` dan Laragon, serta melakukan pengujian langsung di browser. Sesuai `rule.md` Pasal 6 dan 7, AI tidak menjalankan command, migration, seeding, maupun dev server. Setiap tahap yang menghasilkan perubahan struktur data atau dependency akan disertai blok command siap copy-paste.

## Sisa Pekerjaan

Menunggu keputusan developer atas dua hal sebelum Tahap 0 dieksekusi:

1. Apakah residu project VARK benar-benar boleh dihapus, atau ada bagian yang masih ingin dipertahankan.
2. Pilihan styling: Tailwind via CDN atau Bootstrap via CDN.
