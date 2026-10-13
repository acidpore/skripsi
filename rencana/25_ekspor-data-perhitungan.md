# 25 - Ekspor Data Contoh Perhitungan

Tanggal: 13 Oktober 2026

## Dibuat

- `scripts/export_perhitungan.php`: ekspor data acuan, langkah Entropy-TOPSIS untuk SD Kelas 4-6 (Siang) dan TK/PAUD (Pagi) dengan 3000 porsi, kebutuhan bahan peringkat 1, dan `signatures.md`. Hasil di `export_perhitungan/` dan `export_perhitungan.zip`.
- README ditulis ulang untuk project ini.
- CLAUDE.md ditambah bagian kebiasaan kerja.

## Keputusan

- Script read-only, tidak membuat baris rekomendasi baru. Seed memakai SPPG id 1, sama dengan rumus di `RecommendationService`.
- Nilai Entropy dan TOPSIS dihitung ulang manual (`*_rumus`) berdampingan dengan hasil class (`*_sistem`). Kedua kasus cocok.

## Sisa pekerjaan

- Uji ulang browser setelah refactor (dari progress 24) masih tertunda.
