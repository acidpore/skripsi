# Skripsi — Sistem SPK Menu MBG

Aturan kerja wajib dibaca sebelum mengerjakan apapun di project ini:

@rencana/rule.md

Panduan desain frontend, wajib dibaca sebelum menyentuh `resources/views/`:

@rencana/DESIGN.md

Acuan kebutuhan produk dan model data:

@rencana/PRD_Sistem_SPK_MBG.md

## Kebiasaan Kerja Tambahan

- Repo: https://github.com/acidpore/skripsi (branch `master`). Commit dan push hanya jika owner minta.
- Jangan pernah mencantumkan nama AI di mana pun: tanpa `Co-Authored-By`, tanpa "Generated with", commit atas nama `acidpore`.
- Data contoh perhitungan untuk naskah dibuat lewat script terpisah di `scripts/` yang read-only terhadap database, tanpa mengubah kode aplikasi. Hasil hitung ulang manual (`*_rumus`) ditulis berdampingan dengan hasil class aplikasi (`*_sistem`) sebagai bukti kecocokan.
- Ekspor perhitungan: `php scripts/export_perhitungan.php`, hasil di `export_perhitungan/` dan `export_perhitungan.zip`.
- PHP Laragon tidak ada di PATH bash: `C:\laragon\bin\php\php-8.3.29-Win32-vs16-x64\php.exe`.
- README, catatan, dan teks UI ditulis dalam Bahasa Indonesia.
