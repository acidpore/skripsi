# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Sistem Pendukung Keputusan Penyusunan Menu dan Kebutuhan Bahan Program MBG

Ferdinand Wilson Pratama Sanjoto — NIM 23051204357 — v1.1 — 09 September 2026

---

## 1. Ringkasan & Tujuan Produk

Sistem ini adalah aplikasi web yang membantu operator Satuan Pelayanan Pemenuhan Gizi (SPPG) menyusun rekomendasi menu Program Makan Bergizi Gratis (MBG) yang terukur secara gizi, serta menghitung kebutuhan bahan mentah secara akurat. Rekomendasi dihasilkan lewat pembobotan kriteria objektif (Entropy) dan dua metode perankingan (TOPSIS sebagai utama, SAW sebagai pembanding) yang konsistensinya diuji dengan korelasi Spearman. Produk ini adalah objek penelitian skripsi — PRD ini menjadi blueprint teknis yang juga menjadi dasar isi Bab III (Analysis & Design pada ADDIE).

### 1.1 Masalah yang Diselesaikan

- Penyusunan menu SPPG masih manual dan intuitif, bergantung pengalaman juru masak.
- Nilai Bagian yang Dapat Dimakan (BDD) diabaikan, menyebabkan kebutuhan bahan mentah salah hitung.
- Sistem pendukung keputusan menu MBG sebelumnya memakai bobot subjektif dan tidak membedakan kriteria benefit/cost secara tegas.

### 1.2 Tujuan Produk

1. Menghasilkan rekomendasi menu yang terukur gizi dan konsisten hasil perankingannya.
2. Menghitung kebutuhan bahan mentah secara akurat berdasarkan nilai BDD.

---

## 2. Pengguna & Peran (Roles)

| Peran | Deskripsi & Hak Akses |
|---|---|
| Operator SPPG | Pengguna utama. Mengelola profil SPPG-nya sendiri, memilih kelompok sasaran & waktu makan, menginput porsi/bahan tersedia, menjalankan generate rekomendasi menu, melihat hasil ranking & gizi, menghitung kebutuhan bahan, mengekspor daftar belanja. Hanya bisa melihat data SPPG miliknya sendiri (multi-tenant). |
| Admin | Mengelola data acuan nasional (basis bahan pangan TKPI, tabel AKG), mengelola akun SPPG, memantau seluruh SPPG terdaftar lewat dashboard peta persebaran. |

---

## 3. Modul & Kebutuhan Fungsional

### 3.1 Autentikasi & Manajemen Akun
- Registrasi & login SPPG
- Reset password
- Manajemen profil SPPG (nama, wilayah, alamat — mengacu ke entitas sppg)

### 3.2 Basis Bahan Pangan (TKPI)
- Import/seed data bahan pangan dari TKPI.xlsx (±1.150 bahan, 20+ zat gizi + BDD per bahan)
- Pencarian & filter bahan per kelompok (serealia, sayuran, daging & unggas, dst.)
- Tampilkan detail nilai gizi per 100 g BDD untuk satu bahan

### 3.3 Acuan Gizi (AKG)
- Tabel AKG harian per kelompok umur (referensi, Permenkes 28/2019)
- Tabel AKG satu kali makan per kelompok sasaran MBG & waktu makan — dipakai langsung sebagai acuan kesesuaian gizi (BGN)

### 3.4 Pembangkitan Alternatif Menu
- Alternatif dibangkitkan sistem dari basis bahan pangan, bukan dipilih dari daftar menu tetap. Tiap kandidat menu terdiri atas satu bahan per komponen: karbohidrat, lauk hewani, lauk nabati, sayur, buah
- Peran komponen tiap bahan diturunkan dari kelompok TKPI, bukan penilaian manual per bahan
- Bahan yang dapat menjadi komponen dibatasi pada daftar bahan penukar Juknis BGN 401.1/2025, yaitu bahan yang memiliki nilai berat bersih per satuan penukar. TKPI tetap menjadi sumber nilai gizi dan BDD bagi seluruh bahan
- Gramasi dihitung sebagai jumlah satuan penukar dikali berat bersih per satuan penukar, bukan diambil dari angka gram yang tercetak pada Juknis
- Tujuh komponen: karbohidrat, lauk hewani, lauk nabati, sayur, buah, sumber lemak, dan susu. Komponen yang tidak dicantumkan pada standar suatu kelompok sasaran tidak dibangkitkan
- Kelompok sasaran tanpa standar makanan resmi tidak dilayani pembangkitan, yaitu SD kelas 1-3, Anak Balita usia 13-59 bulan, dan Balita usia 6-11 bulan
- Penyaringan bertahap sebelum matriks keputusan dibentuk: batasi bahan per komponen, saring dengan bahan yang tersedia di SPPG, buang kombinasi yang energinya di luar rentang AKG, lalu ambil top-N kandidat
- CRUD menu tetap tersedia agar operator dapat menyimpan kandidat terpilih sebagai menu tetap SPPG-nya

### 3.5 Mesin Rekomendasi SPK (inti sistem)
1. Operator memilih kelompok sasaran + waktu makan, input jumlah porsi & bahan tersedia
2. Sistem membentuk matriks keputusan dari alternatif menu yang relevan. Kolom matriks bukan nilai gizi mentah, melainkan skor kesesuaian terhadap rentang AKG satu kali MBG: kesesuaian energi, protein, lemak, dan karbohidrat. Keempatnya bertipe benefit. Skor dihitung dengan rumus bertahap, bernilai 1 bila nilai gizi berada dalam rentang, `x / L` bila kurang dari batas bawah, dan `U / x` bila melebihi batas atas, sehingga selalu berada pada rentang 0 sampai 1, tidak pernah negatif, dan menghukum kelebihan maupun kekurangan secara setara
3. Sistem menghitung bobot tiap kriteria dengan Entropy
4. Sistem menjalankan TOPSIS (ranking utama) dan SAW (ranking pembanding) memakai bobot yang sama
5. Sistem menghitung korelasi Spearman antara kedua hasil ranking
6. Sistem menampilkan menu terbaik, tabel ranking, dan grafik pemenuhan gizi vs rentang AKG

### 3.6 Kebutuhan Bahan & Daftar Belanja
- Hitung kebutuhan berat kotor per bahan: berat kotor = (berat bersih ÷ BDD) × 100 × jumlah porsi
- Agregasi kebutuhan bahan lintas menu terpilih dalam satu jadwal produksi
- Export daftar belanja (PDF/Excel)

### 3.7 Dashboard & Peta Persebaran SPPG
- Ringkasan aktivitas SPPG (jumlah menu tersimpan, riwayat rekomendasi)
- Peta persebaran SPPG nasional (Leaflet, dari data koordinat)

---

## 4. Model Data (Entitas Inti)

| Entitas | Atribut Kunci & Relasi |
|---|---|
| bahan_pangan | kode, nama, kelompok, energi, protein, lemak, karbohidrat, kalsium, besi, vitamin_c, ..., bdd_persen — sumber: TKPI.xlsx |
| akg_harian | kelompok_umur, berat_badan, tinggi_badan, energi, protein, lemak, karbohidrat — referensi, sumber: Permenkes 28/2019 |
| akg_sekali_makan | kelompok_sasaran, waktu_distribusi, persentase_akg, rentang energi/protein/lemak/karbohidrat (min-max) — dasar seluruh kriteria, sumber: Lampiran I Keputusan Kepala BGN Nomor 401.1 Tahun 2025 tentang Petunjuk Teknis Penyelenggaraan Program MBG Tahun Anggaran 2026 |
| sppg | nama, provinsi, kab_kota, kecamatan, kelurahan_desa, alamat, latitude, longitude — entitas multi-tenant |
| menu | kode, nama, akg_sekali_makan_id (memuat kelompok sasaran sekaligus waktu distribusi), sppg_id (null berarti menu acuan nasional), relasi many-to-many ke bahan_pangan lewat menu_bahan (berat bersih gram per porsi) |
| rekomendasi | sppg_id, akg_sekali_makan_id, menu_terpilih_id, jumlah_porsi, bobot_entropy (JSON), hasil_ranking_topsis (JSON), hasil_ranking_saw (JSON), koefisien_spearman, dibuat_pada |
| kebutuhan_bahan | rekomendasi_id, bahan_pangan_id, berat_kotor_gram — hasil kalkulasi BDD, dasar daftar belanja |

---

## 5. Kebutuhan Teknis

| Komponen | Pilihan & Justifikasi |
|---|---|
| Backend | Laravel (PHP) — struktur OOP native (Model, Controller, Service). Tiap metode SPK diimplementasikan sebagai class tersendiri (mis. `EntropyWeighting`, `TopsisRanking`, `SawRanking`), mengimplementasikan interface bersama (mis. `RankingMethod`) untuk polimorfisme. |
| Frontend | Laravel Livewire + Alpine.js — komponen interaktif ditulis di PHP/Blade (tanpa build step JS terpisah, tanpa `npm run dev`); Alpine.js (via CDN) untuk interaksi ringan sisi klien (toggle, modal, tab) |
| Database | SQLite untuk pengembangan (cukup untuk skala ±1.150 bahan pangan + puluhan ribu baris SPPG); disebutkan di Bab III dapat dimigrasikan ke MySQL/PostgreSQL saat deployment produksi |
| Visualisasi | Chart.js untuk grafik pemenuhan gizi vs AKG |
| Peta | Leaflet untuk dashboard persebaran SPPG |
| Styling | Bootstrap atau Tailwind CSS via CDN/build ringan — opsional, tidak mengikat siklus development Livewire |
| Testing | PHPUnit (fungsional, termasuk komponen Livewire), k6/JMeter (performa), OWASP ZAP (keamanan), SonarQube/PHPMetrics (maintainability) — sesuai kerangka ISO/IEC 25010:2023 |

---

## 6. Kebutuhan Non-Fungsional (dipetakan ke ISO/IEC 25010:2023)

| Karakteristik | Kriteria Penerimaan |
|---|---|
| Functional Suitability | Seluruh kasus uji black box (30–40 skenario) lolos; metrik kelengkapan fungsi mendekati 1 |
| Performance Efficiency | Perhitungan Entropy-TOPSIS-SAW atas seluruh alternatif selesai dalam waktu respons yang wajar untuk aplikasi web (diukur, target ditentukan saat pengujian) |
| Reliability | Sistem tetap stabil pada uji beban bertingkat tanpa crash |
| Security | Tidak ditemukan kerentanan SQL Injection/XSS kritis pada pemindaian OWASP ZAP |
| Maintainability | Maintainability Index dalam kategori baik, duplikasi kode rendah |
| Flexibility | Tampilan konsisten di 5 browser utama, basis data dapat dipindahkan dari SQLite ke MySQL/PostgreSQL tanpa perubahan logika |
| Compatibility | Berfungsi normal di berbagai OS & resolusi layar |

Interaction capability dan safety tidak diukur, sesuai Batasan Masalah Bab I. Istilah mengikuti ISO/IEC 25010:2023, bukan edisi 2011.

---

## 7. Batasan (Out of Scope)

- Kriteria biaya/harga bahan tidak dimasukkan ke matriks keputusan pada versi ini (opsional, dapat ditambahkan kelak)
- Kalsium, besi, dan vitamin C dikeluarkan dari matriks keputusan pada versi ini karena tabel AKG satu kali MBG dari BGN hanya memuat empat zat gizi makro. Penambahan kelak dimungkinkan dengan mengalikan AKG harian Permenkes 28/2019 dengan persentase pada kolom persentase_akg, namun menuntut ekstraksi ketiga kolom tersebut dari sumber aslinya
- Karakteristik interaction capability dan safety pada ISO/IEC 25010:2023 dikecualikan dari pengujian formal (dinyatakan di Batasan Masalah Bab I)
- Tidak mencakup fitur logistik/pengiriman (rute distribusi, armada) — di luar cakupan data yang tersedia
- Autentikasi lanjutan (SSO, 2FA) tidak menjadi prioritas versi awal

---

## 8. Milestone Pengembangan (dipetakan ke fase ADDIE)

| Fase ADDIE | Milestone |
|---|---|
| Analysis | PRD ini + hasil ETL seluruh dataset (Bagian 4) — selesai |
| Design | ERD final, use case & activity diagram, desain kriteria & bobot awal, wireframe UI/UX |
| Development | Implementasi seluruh modul di Bagian 3, ETL TKPI.xlsx ke database, modul Entropy-TOPSIS-SAW-Spearman |
| Implementation | Deployment, migrasi & seeding data (bahan pangan + SPPG), integrasi penuh antar-modul |
| Evaluation | Pengujian ISO/IEC 25010:2023 penuh (Bagian 6) + uji akurasi & kesesuaian gizi |

---

## 9. Pertanyaan Terbuka

- Berapa top-N alternatif menu per kelompok sasaran yang realistis agar tidak meledak kombinatorial? (perlu diputuskan sebelum coding modul rekomendasi)
- Apakah kriteria biaya akan ditambahkan di iterasi berikutnya jika sumber harga resmi (PIHPS/Bapanas) sudah bisa diverifikasi primer?
- Perlu konfirmasi ke dosen pembimbing: apakah struktur entitas ini sudah cukup granular untuk kebutuhan Bab III?
