# Sistem Pendukung Keputusan Menu dan Kebutuhan Bahan Program MBG

Aplikasi web untuk operator Satuan Pelayanan Pemenuhan Gizi (SPPG). Aplikasi ini menyusun rekomendasi menu Program Makan Bergizi Gratis (MBG) dengan pembobotan Entropy dan perankingan TOPSIS, lalu menghitung kebutuhan bahan mentah berdasarkan nilai Bagian yang Dapat Dimakan (BDD).

Objek penelitian skripsi Ferdinand Wilson Pratama Sanjoto (NIM 23051204357).

## Stack

- Laravel 12, PHP 8.3
- Livewire dan Alpine.js
- SQLite (pengembangan)
- Chart.js, Leaflet

## Alur Rekomendasi

1. Operator memilih kelompok sasaran, waktu makan, dan jumlah porsi.
2. `MenuCandidateGenerator` membangkitkan kandidat menu dari bahan penukar Juknis BGN 401.1/2025. Tiap kandidat berisi satu bahan per komponen aktif: karbohidrat, protein hewani, protein nabati, sayur, buah, lemak, dan susu.
3. Kandidat yang energinya di luar rentang AKG sekali makan dibuang (gate energi). Dari sisanya diambil sampel acak ber-seed, maksimal 20 kandidat.
4. `DecisionMatrixBuilder` dan `SuitabilityScorer` membentuk matriks skor kesesuaian protein, lemak, dan karbohidrat. Skornya 1 jika di dalam rentang, `x / L` jika di bawah batas bawah, dan `U / x` jika di atas batas atas.
5. `EntropyWeighting` menghitung bobot kriteria.
6. `TopsisRanking` menyusun peringkat alternatif.
7. `MaterialRequirementService` menghitung berat kotor: `(berat bersih / BDD) x 100 x jumlah porsi`.

## Struktur Penting

| Lokasi | Isi |
|---|---|
| `app/Services/Spk/` | Entropy, TOPSIS, matriks keputusan, orkestrasi rekomendasi |
| `app/Services/Menu/` | Pembangkit kandidat, kebutuhan bahan, penyimpanan menu |
| `app/Livewire/RekomendasiMenu.php` | Halaman rekomendasi |
| `config/spk.php` | Kriteria, peta komponen, satuan penukar per kelompok sasaran |
| `database/seeders/` | Data TKPI, bahan penukar, AKG, SPPG, akun uji |
| `rencana/` | PRD, aturan kerja, panduan desain, catatan progress |
| `scripts/export_perhitungan.php` | Ekspor data contoh perhitungan untuk naskah |

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Pengaturan opsional di `.env`:

- `SPK_MAKSIMAL_KANDIDAT`: jumlah kandidat yang masuk matriks, bawaan 20.
- `SPK_MAKSIMAL_BAHAN_PER_KOMPONEN`: batas bahan per komponen, bawaan tanpa batas.

## Akun Uji

Semua akun memakai password `password`.

| Email | Peran |
|---|---|
| `admin@spk-mbg.test` | Admin |
| `operator@spk-mbg.test` | Operator SPPG 1 |
| `operator2@spk-mbg.test` | Operator SPPG 2 |

## Command

```bash
php artisan spk:coba "Siswa SD/MI/SDLB Kelas 4-6" --porsi=100   # uji generate dari terminal, tambah --simpan untuk menyimpan
php artisan spk:penukar --sp=1                                    # daftar bahan penukar
php artisan test
```

## Ekspor Data Perhitungan

```bash
php scripts/export_perhitungan.php
```

Script ini hanya membaca database. Hasilnya ditulis ke `export_perhitungan/` dan dikemas menjadi `export_perhitungan.zip`, berisi:

- `data_acuan/`: tabel bahan pangan, AKG harian, AKG sekali makan, satuan penukar, dan daftar bahan penukar.
- `a_sd_kelas_4-6/` dan `b_tk_paud/`: langkah perhitungan untuk 3000 porsi, yaitu kandidat lolos energi, matriks kesesuaian, Entropy, TOPSIS, dan kebutuhan bahan peringkat 1.
- `signatures.md`: signature method publik service, komponen Livewire, dan model.

Nilai Entropy dan TOPSIS ditulis dua kali: hasil hitung ulang manual (`*_rumus`) dan hasil class aplikasi (`*_sistem`). Keduanya harus sama.
