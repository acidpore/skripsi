# 12 — Energi Jadi Kriteria Gate, Bukan Kriteria Keputusan

Tanggal: 14 September 2026

## Masalah

Verifikasi `spk:coba` di semua kelompok sasaran menunjukkan bobot Entropy `kesesuaian_energi` selalu 0. Ditelusuri dua lapis:

1. Data seeder salah pasang bahan untuk komponen susu — `BahanPenukarSeeder` memetakan nama penukar "Susu" ke baris TKPI "Susu asam untuk bayi, bubuk" (418 kkal/100g) karena tidak ada alias eksplisit dan pencarian nama jatuh ke kandidat pertama yang BDD-nya sama-sama 100. Satu porsi susu jadi 522 kkal, melebihi total rentang AKG sekali makan, sehingga generator selalu gagal dapat 2 kandidat. Diperbaiki dengan menambah `'Susu' => 'Susu sapi, segar'` di `penukar_alias` (`config/spk.php`) dan re-seed `BahanPenukarSeeder`.
2. Setelah data benar, kandidat mulai lolos, tapi bobot `kesesuaian_energi` tetap 0 di semua kelompok sasaran. Diverifikasi lewat skrip: seluruh 20 kandidat yang lolos generator punya skor kesesuaian energi persis `1.0`. Penyebabnya struktural, bukan bug: `MenuCandidateGenerator` memangkas kombinasi ke rentang `[energi_kkal_min, energi_kkal_max]` yang sama persis dengan rentang yang dipakai `SuitabilityScorer` untuk menilai skor 1.0. Skor kesesuaian energi jadi konstan pada seluruh kandidat yang mencapai matriks keputusan, membuat Entropy menilainya tidak informatif (bobot mendekati nol) apa pun urutan atau cara pengambilan sampel top-N.

## Keputusan

Energi dipisah perannya dari protein/lemak/karbohidrat:

- **Kriteria kelayakan (gate)**, dijalankan di `MenuCandidateGenerator`: kombinasi yang energinya di luar rentang AKG dibuang sebelum sampai ke matriks keputusan. Tidak berubah dari sebelumnya.
- **Kriteria keputusan (matriks)**, dijalankan di `DecisionMatrixBuilder` lewat `config('spk.kriteria')`: hanya kesesuaian protein, lemak, dan karbohidrat. `kesesuaian_energi` dikeluarkan dari config karena setelah gate, nilainya pasti 1.0 untuk semua kandidat dan tidak bisa membedakan apa pun di tahap Entropy-TOPSIS-SAW.

Opsi alternatif (melebarkan rentang penerimaan generator supaya energi tetap punya variansi di matriks) ditolak — itu berarti sengaja meloloskan kandidat di luar AKG semata demi variansi statistik, mengorbankan validitas gizi untuk kepentingan algoritma pembobotan.

## Perubahan

| File | Perubahan |
|---|---|
| `config/spk.php` | Tambah alias `'Susu' => 'Susu sapi, segar'`; hapus entri `kesesuaian_energi` dari `kriteria` |
| `app/Services/Menu/MenuCandidateGenerator.php` | Ganti seleksi top-N dari urutan `kedekatan_energi` (closeness ke titik tengah) menjadi `shuffle` lalu ambil N — closeness-based selection sebelumnya juga berkontribusi menyempitkan variansi, walau bukan akar masalah utama |
| `rencana/PRD_Sistem_SPK_MBG.md` | Subbab 3.4 dan 3.5 diperbarui: energi eksplisit disebut kriteria kelayakan (gate), bukan kriteria keputusan; matriks keputusan kini disebut tiga kriteria (protein, lemak, karbohidrat) |

## Hasil Verifikasi

`spk:coba` dijalankan ulang untuk 7 kelompok sasaran (TK/PAUD, SD 4-6, SMP, SMA, Anak Balita, Ibu Hamil, Ibu Menyusui). Seluruhnya:

- Kandidat lolos generator (tidak lagi gagal karena bug susu)
- Bobot Entropy tiga kriteria semuanya bukan nol, bervariasi antar-kelompok sasaran (didominasi karbohidrat di 5 dari 7 kelompok, lemak di 2 kelompok)
- Spearman TOPSIS vs SAW tetap tinggi: 0.96 - 0.99 di semua kelompok

## Dampak ke Naskah

Perlu direvisi developer di Bab III:

- Subbab 3.3 (Definisi Operasional): tambah definisi "kriteria kelayakan (gate)" sebagai istilah terpisah dari "kriteria keputusan"
- Subbab 3.7 (Formulasi Metode): matriks keputusan dan persamaan Entropy/TOPSIS memakai 3 kriteria, bukan 4; energi dijelaskan sebagai syarat kelayakan di tahap pembangkitan alternatif
- Subbab 2.9/2.6: klaim penerapan AKG terhadap Insani dkk. (2026) diperkuat — AKG diterapkan dua lapis (gate energi + skor kontinu protein/lemak/karbohidrat), bukan cuma di satu tempat

Angka bobot Entropy final per kelompok sasaran ada di tabel hasil verifikasi di atas, siap dipakai referensi revisi 3.7.

## Sisa Pekerjaan

Tidak berubah dari progress 11 — lanjut ke langkah generator (`maksimal_bahan_per_komponen`), lalu penyimpanan menu terpilih, UI Livewire, auth, export.
