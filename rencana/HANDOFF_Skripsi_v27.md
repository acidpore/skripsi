# HANDOFF — Skripsi Wilson (mulai sesi baru dari sini)

Tempel file ini di awal sesi baru, lalu lampirkan `Proposal_Skripsi_final_v27.docx`. Isi file ini adalah konteks lengkap; sesi baru tidak perlu membaca riwayat chat lama.

---

## 1. Identitas

- **Mahasiswa:** Ferdinand Wilson Pratama Sanjoto (Wilson) — NIM 23051204357 — S1 Teknik Informatika, UNESA
- **Pembimbing:** Bu Santi (Martini Dwi Endah Susanti, S.Kom., M.Kom.), bidang RPL
- **Judul:** Sistem Pendukung Keputusan Penyusunan Menu dan Kebutuhan Bahan Program Makan Bergizi Gratis Berbasis Web Menggunakan Pembobotan Objektif Entropy dan TOPSIS
- **Konteks:** Wilson adalah developer platform GO MBG. Skripsi ini fokus pada **satu modul SPK** (rekomendasi menu + kebutuhan bahan), bukan seluruh platform.

## 2. Status dokumen

- **Versi terakhir:** `Proposal_Skripsi_final_v27.docx` — Bab I–III lengkap, 55 halaman
- Daftar Isi / Daftar Tabel / Daftar Gambar sudah diverifikasi halaman per halaman terhadap PDF hasil render (v27)
- Belum dikirim ke Bu Santi dalam bentuk ini (terakhir dikirim: v11, Bab I–II saja)
- Bab IV & V belum ditulis; kerja lapangan (observasi SPPG, validasi ahli gizi) belum dijalankan

## 3. Keputusan metodologi (FINAL, jangan dibuka lagi tanpa alasan kuat)

| Aspek | Keputusan |
|---|---|
| Pembobotan | Entropy (objektif) |
| Perankingan | **TOPSIS saja** — dijustifikasi Siregar dkk. (2021). SAW & Spearman **sudah dibuang** dari naskah, PRD v1.2, dan kode |
| Kriteria matriks | **3 kriteria benefit:** kesesuaian protein, lemak, karbohidrat |
| Energi | **Kriteria gate/kelayakan**, bukan kriteria keputusan (skornya selalu 1 untuk kandidat yang lolos → bobot Entropy ≈ 0). Dijelaskan di 3.3 dan 3.7.1 |
| Skor kesesuaian | 1 jika dalam [L, U]; x/L jika di bawah; U/x jika di atas |
| Pengambilan kandidat | Sampel **acak** N kandidat dari kombinasi yang lolos gate (bukan N teratas) |
| Model pengembangan | V-Model (bukan ADDIE) |
| Pengujian | Black Box — fungsional & non-fungsional (bukan ISO/IEC 25010) |
| Data | TKPI 2019, AKG Permenkes 28/2019, AKG sekali makan & satuan penukar Juknis BGN 401.1/2025 |
| Gaya sitasi | APA (sudah di-ACC Bu Santi) |

## 4. Isi Bab III yang sudah ada

3.1 Jenis Penelitian · 3.2 Lokasi, Waktu, dan Subjek · 3.3 Definisi Operasional · 3.4 Tahapan Penelitian (Gambar 3.1 V-Model, Tabel 3.1) · 3.5 Metode Pengumpulan Data · 3.6 Sumber Data dan Validasi Data (Tabel 3.2, termasuk validasi satuan penukar) · 3.7 Formulasi Metode · 3.8 Perancangan Sistem (3.8.1 Arsitektur + **Gambar 3.2 Use Case Diagram**, **Gambar 3.3 Class Diagram**; 3.8.2 Pembangkitan Kandidat; 3.8.3 Keluaran) · 3.9 Validasi Ahli · 3.10 Rancangan Pengujian (Tabel 3.3, 3.4) · 3.11 Jadwal (Tabel 3.5)

**Use Case Diagram:** actor utama "Operator SPPG (Ahli Gizi)". Area MVP: Login/Logout, Generate Rekomendasi (include: Rincian Perankingan), Simpan Menu Terpilih (extend), Lihat Kebutuhan Bahan, Export/Cetak Daftar Belanja (include). Area "di luar cakupan": Kelola Akun Operator (Admin), Menyusun HPP & Pagu Anggaran (Akuntan), Menyetujui/Menolak Menu (KA SPPG/Owner) — ini proses bisnis nyata GO MBG yang **tidak** dikodekan dan **tidak** butuh dataset tambahan.

**Class Diagram:** model Sppg, User(role), BahanPangan, AkgHarian, AkgSekaliMakan, Menu, MenuBahan, Rekomendasi (bobot_entropy & hasil_ranking_topsis JSON), KebutuhanBahan (milik Rekomendasi); service MenuCandidateGenerator, SuitabilityScorer, DecisionMatrixBuilder, EntropyWeighting, RankingMethod ← TopsisRanking, RecommendationService, MaterialRequirementService, MenuPersistenceService; Livewire RekomendasiMenu, Login; DaftarBelanjaController (cetak, csv). Sumber mermaid ada di sesi lama; kalau perlu digambar ulang, minta ulang ke sesi coding.

## 5. Status Item Terbuka (Update 23 Sep 2026)

| # | Item | Status | Catatan |
|---|---|---|---|
| 1 | **Rancangan UI/UX & Screenshots** | **SELESAI** | Seluruh screenshot baru (.png) di `rencana/screenshots/` sudah diambil via Chrome asli dengan skenario seragam: **Siswa SD/MI/SDLB Kelas 4–6 Siang, 100 porsi** (Gambar 3.4 s/d 3.8). Screenshot lama (.jpg) sudah dihapus permanen. |
| 2 | **Sampel Acak vs Uji Konsistensi (Tabel 3.4)** | **SELESAI** | Diimplementasikan seed deterministik via `Randomizer(Mt19937)` berbasis `(sppg_id, akg_id, porsi)`. Terbukti 100% konsisten & identik pada setiap pengulangan. |
| 3 | **Batasan Masalah 11 (Kelompok Sasaran)** | **SELESAI** | Kelompok sasaran tanpa standar makanan resmi (`Anak Balita`, `Balita 13-59 bln`, `Balita 6-11 bln`, `SD Kelas 1-3`) dikunci total dari kode & config, tidak muncul di UI. |
| 4 | **3.8.3 menyebut "grafik pemenuhan gizi"** | Menunggu naskah | Chart.js belum diimplementasi di UI. Kalimat di naskah disarankan disesuaikan ke format angka & penanda rentang AKG terkini. |
| 5 | **Activity/sequence diagram alur generate** | Sedang | Tanyakan saat bimbingan ke Bu Santi apakah perlu diagram tambahan. |
| 6 | **Lampiran instrumen validasi ahli** | Sedang | Disebut di 3.5.2 tapi belum ada lampirannya. |
| 7 | **Temuan anomali bobot TK/PAUD** | Bahan bimbingan | Porsi Juknis BGN TK/PAUD menghasilkan protein 16,6–17,6 g vs AKG 5–6,3 g. Layak dijadikan bahan diskusi Bab IV. |

## 6. Sisi Kode & Lingkungan (Update 23 Sep 2026)

- Stack: Laravel 12 + Livewire, Tailwind CDN, SQLite (`database/database.sqlite`), PHP 8.3 Laragon.
- Kredensial uji: `operator@spk-mbg.test` (terikat SPPG Kebumen, id 1) / `admin@spk-mbg.test`, password `password`.
- Generator & SPK: `MenuCandidateGenerator` ber-seed deterministik, `DecisionMatrixBuilder`, `EntropyWeighting`, `TopsisRanking`.
- Repositori bersih: file `.jpg` lama dan dataset tidak terpakai (`template_alternatif_menu.csv`, `satuan_penukar_kelompok_sasaran.csv`) telah dibersihkan. Seluruh tes `php artisan test` hijau (PASS).

## 8. Cara kerja yang disepakati (WAJIB diikuti sesi baru)

1. **Edit .docx lewat XML mentah**, bukan python-docx — supaya field sitasi Mendeley (`w:sdt`) tidak rusak. Alur: unzip → edit `word/document.xml` → zip `-X` → `validate.py --original` → render `soffice` ke PDF → cek `pdftotext`/`pdftoppm`.
2. **Setiap edit yang mengubah panjang konten → verifikasi ulang SEMUA nomor halaman** Daftar Isi/Tabel/Gambar. Metode: ekstrak tiap halaman PDF satu per satu, ambil baris terakhir sebagai nomor cetak, cari heading/caption di halaman fisik, cocokkan. Jangan pakai tebakan.
3. **Jangan klaim "selesai" sebelum dicek sendiri** (render + lihat visual). Klaim agen/sesi lain diaudit ulang, tidak dipercaya mentah.
4. Referensi: jurnal nasional minimal SINTA 3 dengan DOI aktif (dicek benar-benar resolve). Buku dikecualikan dari syarat DOI. Isu lama yang sudah diputuskan (Kompas, Reza 2025, Shannon 1948 tanpa DOI, Insani dkk. 2026) jangan diangkat ulang.
5. Format pedoman UNESA Edisi III 2025: A5, Book Antiqua 10pt, margin atas/kiri 2,5 cm & kanan/bawah 2 cm, spasi 1,15. Urutan Bab I: Latar Belakang → Identifikasi → **Batasan** → **Rumusan** → Tujuan → Manfaat.
6. Bahasa Indonesia, langsung, ringkas.

## 9. Langkah pertama yang disarankan di sesi baru

1. Bawa v27 ke Bu Santi; tanyakan item #3, #4, dan temuan TK/PAUD.
2. Sementara itu: putuskan item #2 (grafik vs ubah kalimat) dan kumpulkan screenshot UI baru untuk item #1.
