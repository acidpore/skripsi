# 23 - Perbaikan Hasil Audit Alur Generate

Tanggal: 24 September 2026

## Dasar

Audit baca-kode atas alur generate (dua sub agent, read-only) atas permintaan academic writer, untuk menyamakan class diagram, ERD, activity diagram, dan sequence diagram dengan implementasi.

## Fakta untuk diagram

- BDD: kolom `bahan_pangan.bdd_persen` (decimal 5,2, nullable). Bahan ber-BDD null tidak ikut dibangkitkan.
- Komponen menu: bukan kolom di `bahan_pangan`. Diturunkan dari `bahan_pangan.kelompok` lewat `config/spk.php` `peta_komponen`, lalu disimpan per baris di `menu_bahan.komponen`.
- Penanda bahan penukar: `bahan_pangan.berat_per_sp_gram` (nullable).
- `rekomendasi.komposisi_terpilih` (json, nullable) ada di migration `000010`. Gap lama pada catatan 19 sudah tertutup.
- Urutan sebenarnya: validasi (rules, cek SPPG) -> seed crc32 -> generator -> minimal 2 kandidat -> matriks -> Entropy -> TOPSIS -> simpan Rekomendasi -> hitung dan simpan KebutuhanBahan (kandidat peringkat 1) dalam satu transaksi -> simpan menu (opsional, hitung ulang kebutuhan mengikuti menu pilihan).
- Cabang masukan tidak valid dan kandidat kurang dari dua sama-sama ditangani (banner pesanError).
- Bahan penukar aktif per komponen (hasil tinker): karbohidrat 5, protein hewani 4, protein nabati 3, sayur 8, buah 5, lemak 3, susu 1.

## Perbaikan kode

1. IDOR: `rekomendasiId` di `RekomendasiMenu` diberi `#[Locked]`, dan computed `rekomendasi()` mengembalikan null bila operator bukan pemilik SPPG.
2. `simpan()` idempoten: bila `menu_terpilih_id` sudah terisi, ditolak dengan pesan. Banner sukses lama dibersihkan sebelum pesan penolakan tampil.
3. `porsi` menjadi `?int` agar input kosong lolos ke validasi `required`, bukan TypeError.
4. Kebutuhan bahan dihitung dan disimpan di `RecommendationService::generate()` dan saat `simpan()`. Livewire, cetak, dan csv hanya membaca lewat `MaterialRequirementService::forRekomendasi()`.
5. Cetak dan csv untuk rekomendasi tanpa kebutuhan bahan menjawab 404 dengan pesan "belum memiliki kebutuhan bahan", berbeda dari id tidak ada (404 bawaan).
6. `maksimal_kandidat` dari `.env` (`SPK_MAKSIMAL_KANDIDAT`, bawaan 20). `maksimal_bahan_per_komponen` dari `.env` (`SPK_MAKSIMAL_BAHAN_PER_KOMPONEN`, bawaan null yaitu tanpa batas). Keduanya agar uji waktu respons Tabel 3.10 bisa divariasikan.
7. Test baru `tests/Feature/RekomendasiOtorisasiTest.php`: 3 tes, 8 assertion, lulus (cetak SPPG lain 403, porsi 0 dan 100001 ditolak server, `rekomendasiId` terkunci).

## Keputusan

- Parameter stok bahan tersedia tidak dihubungkan ke UI (naskah v30 ke atas tidak lagi mengklaimnya).
- Ketidakkonsistenan bentuk `komposisi_terpilih` (kandidat utuh saat generate, tanpa `kedekatan_energi` setelah simpan) ditunda, konsumennya hanya memakai `['komposisi']`.
- Batas bahan per komponen tidak diaktifkan: semua komponen di bawah 30 bahan, sehingga kalimat naskah "dari seluruh bahan pada daftar bahan penukar" tetap benar.

## Hasil uji manual di Chrome (operator SPPG Kebumen, SD Kelas 4-6 Siang, 100 porsi)

- Generate normal: lima kandidat tampil, tanpa galat.
- Porsi kosong: pesan "wajib diisi", tanpa TypeError. Porsi 0 dicegat atribut `min=1` browser, penolakan server dibuktikan tes.
- Simpan menu peringkat 1 berhasil. Simpan kedua pada rekomendasi yang sama ditolak.
- Csv rekomendasi 8: 200, tujuh bahan, berat kotor sesuai BDD (semangka 18 kg bersih menjadi 39,13 kg kotor).
- Cetak rekomendasi 1, 2, 5, 6, 7, 8: 200. Tidak ada 403 karena seluruh data milik SPPG 1.
- Rekomendasi id 3 dan 4 (tanpa kebutuhan_bahan) dihapus dari DB. Id 9 memang tidak ada.

## Sisa pekerjaan

- Bab IV: ambil screenshot 403 lintas SPPG (KF-01). Akun uji sudah dibuat: `operator2@spk-mbg.test` / `password`, terikat SPPG id 2 (Tanah Laut Tambang Ulang Sungai Pinang). Login sebagai akun ini lalu buka `/rekomendasi/8/cetak` (milik SPPG 1), hasil yang benar 403.

## Pembersihan dead code

- Method `MenuNutritionCalculator::calculate(Menu)` dihapus (tidak ada pemanggil di app, tests, resources, routes). Konstanta `ZAT_GIZI` dipertahankan, dipakai `MenuCandidateGenerator`.
- `php artisan test` setelah penghapusan: 5 lulus, 10 assertion.
