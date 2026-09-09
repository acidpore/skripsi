# 04 — Kriteria Final & Orkestrasi SPK

Tanggal: 09 September 2026

## Keputusan yang Masuk

| Kode | Keputusan |
|---|---|
| K1 | Kalsium, besi, dan vitamin C dikeluarkan dari matriks. Tabel AKG satu kali MBG dari BGN hanya memuat empat zat gizi makro |
| K2 | Empat kriteria final, seluruhnya benefit: kesesuaian energi, protein, lemak, karbohidrat |
| K5 | Kriteria selisih AKG diganti menjadi skor kesesuaian bertipe benefit |
| Styling | Bootstrap 5 via CDN |
| Residu VARK | Dihapus |

Sumber tabel AKG satu kali MBG dikoreksi menjadi Lampiran I Keputusan Kepala BGN Nomor 401.1 Tahun 2025, bukan Pedoman Umum Sistem dan Tata Kelola BGN.

K3 (batas top-N alternatif) dan K4 (pemetaan menu ke bahan TKPI) masih kosong.

## Yang Dibuat

| File | Peran |
|---|---|
| `config/spk.php` | Definisi empat kriteria, tipe, dan pemetaan ke kolom gizi serta kolom rentang AKG |
| `app/Services/Spk/SuitabilityScorer.php` | Skor kesesuaian terhadap rentang AKG |
| `app/Services/Menu/MenuNutritionCalculator.php` | Kandungan gizi satu porsi menu dari komposisi bahan |
| `app/Services/Spk/DecisionMatrixBuilder.php` | Matriks keputusan dari daftar menu dan acuan AKG |
| `app/Services/Spk/RecommendationService.php` | Orkestrasi Entropy, TOPSIS, SAW, Spearman, dan penyimpanan hasil |
| `app/Services/Menu/MaterialRequirementService.php` | Konversi BDD dan agregasi kebutuhan bahan |

PRD Bagian 3.5, 4, dan 7 disesuaikan mengikuti perubahan ini.

## Keputusan Desain

**Kriteria disimpan di `config/spk.php`, bukan dikeraskan di dalam class.** Penambahan kalsium, besi, atau vitamin C kelak cukup menambah satu entri config beserta kolom rentangnya, tanpa menyentuh `DecisionMatrixBuilder` maupun `RecommendationService`. Ini juga membuat daftar kriteria yang dikutip di Bab III punya satu sumber kebenaran yang dapat ditunjuk langsung.

**Rumus kesesuaian diletakkan pada class tersendiri.** `SuitabilityScorer` tidak menyentuh model apapun, sehingga rumus yang menjadi kontribusi utama penelitian dapat diuji terpisah dari database.

**Skor nol untuk nilai gizi nol.** Bila menu tidak memuat suatu zat gizi sama sekali, skornya nol, bukan pembagian tak terdefinisi.

**`RecommendationService` menyimpan skor kriteria dan kandungan gizi tiap alternatif ke dalam JSON hasil ranking.** Grafik pemenuhan gizi pada PRD Bagian 3.5 butuh angka gizi mentah, sedangkan penelusuran ulang perhitungan saat sidang butuh skor kriteria. Menyimpan keduanya saat generate menghindari perhitungan ulang ketika hasil lama dibuka kembali.

**Perankingan menolak jalan bila alternatif kurang dari dua.** Entropy memakai `1 / ln(m)` dengan m adalah jumlah alternatif, sehingga satu alternatif membuat pembagian dengan nol. Penolakan dilakukan lebih awal dengan pesan yang jelas, bukan dibiarkan gagal di dalam perhitungan.

**BDD kosong atau nol diperlakukan sebagai 100 persen.** Bahan olahan seperti nasi memang tidak memiliki bagian terbuang. Tanpa penjagaan ini konversi berat kotor akan membagi dengan nol.

**`batas_alternatif` bernilai null.** K3 belum diputuskan, dan null berarti seluruh alternatif ikut diranking. Mengisi config ini kelak tidak menuntut perubahan kode.

## Perilaku yang Perlu Ditulis di Bab IV

Bila seluruh alternatif menu masuk rentang AKG, kolom kriteria tersebut bernilai 1 seluruhnya, variansnya nol, dan Entropy memberi bobot nol pada kriteria itu. Ini benar secara metode, karena kriteria yang tidak membedakan alternatif memang tidak layak diberi bobot. Perlu dinyatakan eksplisit agar tidak terbaca sebagai kesalahan hitung.

## Peringatan Sebelum Penghapusan Residu VARK

Project ini **bukan git repository**. Saran mencommit lebih dulu sebelum penghapusan massal tidak dapat dijalankan apa adanya, dan tanpa itu penghapusan tidak dapat dibatalkan. Jalankan inisialisasi terlebih dahulu.

```
cd C:\laragon\www\skripsi
git init
git add -A
git commit -m "chore: snapshot sebelum pembersihan residu project VARK"
```

Setelah snapshot tersimpan, hapus residu VARK.

```
git rm app/Models/Module.php app/Models/Material.php app/Models/QuestionnaireDataset.php app/Models/QuizResult.php app/Models/AccessLog.php
git rm app/Services/LearningStyleService.php
git rm app/Console/Commands/GenerateVarkDataset.php app/Console/Commands/TrainVarkModel.php app/Console/Commands/EvaluateVarkModel.php app/Console/Commands/TestVarkPrediction.php
git rm database/migrations/2026_01_17_000001_create_modules_table.php database/migrations/2026_01_17_000002_create_materials_table.php database/migrations/2026_01_17_000003_create_questionnaire_datasets_table.php database/migrations/2026_01_17_000004_create_quiz_results_table.php database/migrations/2026_01_17_000005_create_access_logs_table.php
```

Lepas dependency machine learning milik project VARK, lalu pasang Livewire.

```
composer remove rubix/ml rubix/tensor
composer require livewire/livewire
```

Bangun ulang database dari nol, karena struktur lama sudah tidak dipakai.

```
php artisan migrate:fresh
```

Migration `users` bawaan project VARK kemungkinan masih memuat kolom `vark_score_v` sampai `predicted_style`. Kolom itu tidak lagi dirujuk Model `User`, jadi tidak merusak jalannya aplikasi, namun sebaiknya dibersihkan sekalian agar metrik SonarQube tidak terganggu.

## Sisa Pekerjaan

- K3 dan K4 masih menunggu keputusan. K4 tetap blocker terberat karena seeder menu tidak dapat dibuat tanpa pemetaan nama masakan ke kode bahan TKPI beserta gramasinya
- Seeder seluruh dataset belum dibuat
- Antarmuka belum dimulai, menunggu Livewire terpasang
