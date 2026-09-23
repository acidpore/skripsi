# 19 - TOPSIS-only, sinkron PRD dengan thesis

## Apa yang dibuat

- PRD (`rencana/PRD_Sistem_SPK_MBG.md`) naik ke v1.2: SAW dan Spearman dibuang dari subbab 3.5, entitas `rekomendasi`, dan Bagian 5. Kerangka pengujian ISO/IEC 25010:2023 diganti Black Box Testing (Bagian 6), model pengembangan ADDIE diganti V-Model (Bagian 8). Role di Bagian 2 jadi "Operator SPPG (Ahli Gizi)" biar konsisten dengan Use Case Diagram.
- Kode dibongkar mengikuti PRD baru:
  - `RecommendationService`: hapus dependency `SawRanking`/`SpearmanCorrelation`, hapus pemanggilan dan field `hasil_ranking_saw`/`koefisien_spearman` saat `Rekomendasi::create`.
  - `Rekomendasi` model: hapus dua kolom itu dari `$fillable` dan `casts()`.
  - Migration `create_rekomendasi_table`: hapus kolom `hasil_ranking_saw` dan `koefisien_spearman`.
  - `resources/views/livewire/rekomendasi-menu.blade.php`: hapus paragraf yang menampilkan korelasi Spearman ke UI.
  - `CobaRekomendasi` command: hapus baris log Spearman.
  - Hapus file `app/Services/Spk/SawRanking.php` dan `app/Services/Spk/SpearmanCorrelation.php` (dead code).
  - `resources/views/dokumentasi/metode.blade.php`: hapus section 04 (SAW) dan 05 (Spearman) beserta entri navigasinya, renumber jadi 01-04, update kalimat pengantar.
  - `resources/views/welcome.blade.php`: hapus klaim "SAW mengujinya, Spearman mengukur kesepakatan" di landing page.
  - `tests/Spk/SpkSelfCheck.php`: hapus import dan pemanggilan `SawRanking`/`SpearmanCorrelation` (fatal error kalau dijalankan, class sudah dihapus); `checkDominantAlternativeRanksFirst` dan `checkCostCriterionInvertsPreference` sekarang cuma uji TOPSIS.

## Keputusan desain

- TOPSIS jadi satu-satunya metode ranking, dijustifikasi Siregar dkk. (2021) sesuai naskah skripsi v26 — bukan penilaian baru, PRD lama saja yang belum diupdate mengikuti keputusan v16.

## Sisa pekerjaan / catatan

- Migration ini kemungkinan sudah pernah dijalankan di `database/database.sqlite` developer. AI tidak menjalankan migration (rule.md sec 7) — developer perlu `php artisan migrate:fresh --seed` (atau migration baru drop kolom) secara manual agar skema database sinkron dengan migration file.
- Ditemukan gap lama di luar scope task ini: `Rekomendasi::$fillable` punya `komposisi_terpilih` tapi migration `create_rekomendasi_table` tidak punya kolom itu — insert akan gagal kalau kolom belum ada di skema aktual. Perlu dicek terpisah.
