# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Sistem Pendukung Keputusan Penyusunan Menu dan Kebutuhan Bahan Program MBG

Ferdinand Wilson Pratama Sanjoto — NIM 23051204357 — v1.2 — 16 September 2026

---

## 1. Ringkasan & Tujuan Produk

Sistem ini adalah aplikasi web yang membantu operator Satuan Pelayanan Pemenuhan Gizi (SPPG) menyusun rekomendasi menu Program Makan Bergizi Gratis (MBG) yang terukur secara gizi, serta menghitung kebutuhan bahan mentah secara akurat. Rekomendasi dihasilkan lewat pembobotan kriteria objektif (Entropy) dan perankingan TOPSIS, dipilih tunggal berdasarkan justifikasi Siregar dkk. (2021). Produk ini adalah objek penelitian skripsi — PRD ini menjadi blueprint teknis yang juga menjadi dasar isi Bab III (Analysis & Design pada V-Model).

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
| Operator SPPG (Ahli Gizi) | Pengguna utama. Mengelola profil SPPG-nya sendiri, memilih kelompok sasaran & waktu makan, menginput porsi/bahan tersedia, menjalankan generate rekomendasi menu, melihat hasil ranking & gizi, menghitung kebutuhan bahan, mengekspor daftar belanja. Hanya bisa melihat data SPPG miliknya sendiri (multi-tenant). |
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
- Alternatif dibangkitkan sistem dari basis bahan pangan, bukan dipilih dari daftar menu tetap. Tiap kandidat menu terdiri atas satu bahan per komponen aktif: karbohidrat, protein hewani, protein nabati, sayur, buah, sumber lemak, dan susu
- Peran komponen tiap bahan diturunkan dari kelompok TKPI, bukan penilaian manual per bahan
- Bahan yang dapat menjadi komponen dibatasi pada daftar bahan penukar Juknis BGN 401.1/2025, yaitu bahan yang memiliki nilai berat bersih per satuan penukar. TKPI tetap menjadi sumber nilai gizi dan BDD bagi seluruh bahan
- Gramasi dihitung sebagai jumlah satuan penukar dikali berat bersih per satuan penukar, bukan diambil dari angka gram yang tercetak pada Juknis
- Tujuh komponen: karbohidrat, protein hewani, protein nabati, sayur, buah, sumber lemak, dan susu. Komponen yang tidak dicantumkan pada standar suatu kelompok sasaran tidak dibangkitkan
- Kelompok sasaran tanpa standar makanan resmi tidak dilayani pembangkitan, yaitu SD kelas 1-3, Anak Balita usia 13-59 bulan, dan Balita usia 6-11 bulan
- Penyaringan bertahap sebelum matriks keputusan dibentuk: batasi bahan per komponen, saring dengan bahan yang tersedia di SPPG, buang kombinasi yang energinya di luar rentang AKG (kesesuaian energi berperan sebagai kriteria kelayakan/gate di tahap ini, bukan kriteria keputusan), lalu ambil sampel acak N kandidat dari yang lolos
- Pagu biaya per porsi juga berperan sebagai kriteria kelayakan/gate, sejajar dengan gate energi, begitu data harga bahan primer tersedia (lihat Batasan subbab 7 dan Pertanyaan Terbuka subbab 9): pagu kecil Rp10.000 untuk kelompok Ibu Hamil, Ibu Menyusui, Anak Balita, Siswa TK/PAUD/TKLB, dan Siswa SD/MI/SDLB Kelas 1-3; pagu besar Rp15.000 untuk Siswa SD/MI/SDLB Kelas 4-6 sampai Siswa SMA/SMK/MA/SMALB
- CRUD menu tetap tersedia agar operator dapat menyimpan kandidat terpilih sebagai menu tetap SPPG-nya

### 3.5 Mesin Rekomendasi SPK (inti sistem)
1. Operator memilih kelompok sasaran + waktu makan, input jumlah porsi & bahan tersedia
2. Sistem membentuk matriks keputusan dari alternatif menu yang relevan. Kolom matriks bukan nilai gizi mentah, melainkan skor kesesuaian terhadap rentang AKG satu kali MBG: kesesuaian protein, lemak, dan karbohidrat. Ketiganya bertipe benefit. Kesesuaian energi tidak dimasukkan ke matriks — ia sudah berperan sebagai kriteria kelayakan (gate) pada tahap pembangkitan alternatif (subbab 3.4), sehingga setiap kandidat yang mencapai matriks sudah pasti sesuai rentang energi dan tidak ada lagi yang bisa dibedakan darinya di tahap ranking. Skor kesesuaian dihitung dengan rumus bertahap, bernilai 1 bila nilai gizi berada dalam rentang, `x / L` bila kurang dari batas bawah, dan `U / x` bila melebihi batas atas, sehingga selalu berada pada rentang 0 sampai 1, tidak pernah negatif, dan menghukum kelebihan maupun kekurangan secara setara
3. Sistem menghitung bobot tiap kriteria dengan Entropy
4. Sistem menjalankan TOPSIS sebagai satu-satunya metode perankingan
5. Sistem menampilkan menu terbaik, tabel ranking, dan grafik pemenuhan gizi vs rentang AKG

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
| rekomendasi | sppg_id, akg_sekali_makan_id, menu_terpilih_id, jumlah_porsi, bobot_entropy (JSON), hasil_ranking_topsis (JSON), dibuat_pada |
| kebutuhan_bahan | rekomendasi_id, bahan_pangan_id, berat_kotor_gram — hasil kalkulasi BDD, dasar daftar belanja |

---

## 5. Kebutuhan Teknis

| Komponen | Pilihan & Justifikasi |
|---|---|
| Backend | Laravel (PHP) — struktur OOP native (Model, Controller, Service). Tiap metode SPK diimplementasikan sebagai class tersendiri (mis. `EntropyWeighting`, `TopsisRanking`), mengimplementasikan interface bersama (mis. `RankingMethod`) untuk polimorfisme. |
| Frontend | Laravel Livewire + Alpine.js — komponen interaktif ditulis di PHP/Blade (tanpa build step JS terpisah, tanpa `npm run dev`); Alpine.js (via CDN) untuk interaksi ringan sisi klien (toggle, modal, tab) |
| Database | SQLite untuk pengembangan (cukup untuk skala ±1.150 bahan pangan + puluhan ribu baris SPPG); disebutkan di Bab III dapat dimigrasikan ke MySQL/PostgreSQL saat deployment produksi |
| Visualisasi | Chart.js untuk grafik pemenuhan gizi vs AKG |
| Peta | Leaflet untuk dashboard persebaran SPPG |
| Styling | Bootstrap atau Tailwind CSS via CDN/build ringan — opsional, tidak mengikat siklus development Livewire |
| Testing | Black Box Testing (fungsional & non-fungsional), manual oleh developer |

---

## 6. Kebutuhan Non-Fungsional (Black Box Testing)

| Aspek | Kriteria Penerimaan |
|---|---|
| Fungsional | Seluruh kasus uji black box (30–40 skenario) lolos; metrik kelengkapan fungsi mendekati 1 |
| Performa | Perhitungan Entropy-TOPSIS atas seluruh alternatif selesai dalam waktu respons yang wajar untuk aplikasi web (diukur, target ditentukan saat pengujian) |
| Keandalan | Sistem tetap stabil pada uji beban bertingkat tanpa crash |
| Keamanan | Tidak ditemukan kerentanan SQL Injection/XSS kritis pada pengujian |
| Kompatibilitas | Tampilan konsisten di browser utama & berbagai OS/resolusi layar; basis data dapat dipindahkan dari SQLite ke MySQL/PostgreSQL tanpa perubahan logika |

---

## 7. Batasan (Out of Scope)

- Kriteria biaya/harga bahan tidak dimasukkan ke matriks keputusan pada versi ini. Ambang pagu per porsi sudah diketahui (Rp10.000 pagu kecil, Rp15.000 pagu besar, lihat subbab 3.4), tetapi penerapannya sebagai gate ditunda sampai sumber harga bahan primer (PIHPS/Bapanas) tersedia dan bisa diverifikasi
- Kalsium, besi, dan vitamin C dikeluarkan dari matriks keputusan pada versi ini karena tabel AKG satu kali MBG dari BGN hanya memuat empat zat gizi makro. Penambahan kelak dimungkinkan dengan mengalikan AKG harian Permenkes 28/2019 dengan persentase pada kolom persentase_akg, namun menuntut ekstraksi ketiga kolom tersebut dari sumber aslinya
- Tidak mencakup fitur logistik/pengiriman (rute distribusi, armada) — di luar cakupan data yang tersedia
- Autentikasi lanjutan (SSO, 2FA) tidak menjadi prioritas versi awal

---

## 8. Milestone Pengembangan (dipetakan ke V-Model)

| Tahap V-Model | Milestone |
|---|---|
| Requirement Analysis | PRD ini + hasil ETL seluruh dataset (Bagian 4) — selesai |
| System Design | ERD final, use case & activity diagram, desain kriteria & bobot awal, wireframe UI/UX |
| Module Design & Coding | Implementasi seluruh modul di Bagian 3, ETL TKPI.xlsx ke database, modul Entropy-TOPSIS |
| Unit & Integration Testing | Migrasi & seeding data (bahan pangan + SPPG), integrasi penuh antar-modul |
| System & Acceptance Testing | Black Box Testing penuh (Bagian 6) + uji akurasi & kesesuaian gizi |

---

## 9. Pertanyaan Terbuka

- Berapa top-N alternatif menu per kelompok sasaran yang realistis agar tidak meledak kombinatorial? (perlu diputuskan sebelum coding modul rekomendasi)
- Ambang pagu (Rp10.000/Rp15.000, subbab 3.4) sudah diketahui; apakah kriteria biaya akan diaktifkan sebagai gate di iterasi berikutnya jika sumber harga resmi (PIHPS/Bapanas) sudah bisa diverifikasi primer?
- Menurut operator, dalam praktik satu SPPG memasak menu yang sama dalam sehari untuk seluruh kelompok sasaran yang dilayani; yang berbeda antar pagu kecil dan pagu besar hanyalah jumlah/ukuran porsi, bukan komposisi menunya. Ini berpotongan dengan desain saat ini di subbab 3.4-3.5 yang membangkitkan dan meranking menu terpisah per kelompok sasaran (tiap kelompok sasaran punya rentang AKG sekali makan sendiri di `akg_sekali_makan`). Perlu diputuskan: apakah rekomendasi tetap dihasilkan per kelompok sasaran (operator lalu memilih satu menu yang sama-sama cocok untuk kedua pagu), atau alur generate perlu diubah supaya eksplisit menghasilkan satu menu yang dievaluasi kecocokannya terhadap AKG kedua pagu sekaligus.
- Perlu konfirmasi ke dosen pembimbing: apakah struktur entitas ini sudah cukup granular untuk kebutuhan Bab III?
