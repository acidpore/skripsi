# 22 - Pembersihan File Usang dan Konsolidasi Repositori

Tanggal: 23 September 2026

## Ringkasan Penghapusan File

Sesuai instruksi dan persetujuan developer, telah dilakukan pembersihan atas file-file usang (*dead files*) yang tidak lagi terpakai oleh sistem:

1. **Penghapusan Screenshot Usang di `rencana/screenshots/`**:
   - `01-form-hasil-ranking.jpg` (dihapus)
   - `02-kebutuhan-bahan-export.jpg` (dihapus)
   - `03-halaman-cetak-daftar-belanja.jpg` (dihapus)
   *Alasan:* Tangkapan layar warisan versi UI lama (berwarna biru) yang masih memuat artefak korelasi Spearman. Dihapus permanen agar tidak membingungkan atau tertukar dengan paket tangkapan layar baru (`01-landing-page.png` s/d `09-rekomendasi-admin-view.png`).

2. **Penghapusan Dataset Nonaktif di `dataset/`**:
   - `dataset/template_alternatif_menu.csv` (dihapus)
     *Alasan:* Format menu tetap warisan prototipe sebelum adanya generator kombinasi menu otomatis.
   - `dataset/satuan_penukar_kelompok_sasaran.csv` (dihapus)
     *Alasan:* Draf sementara satuan penukar yang seluruh datanya sudah terintegrasi resmi ke dalam `config/spk.php`.

## Status Akhir Repositori
- Folder `rencana/screenshots/` kini bersih dan hanya berisi 9 file tangkapan layar PNG resmi dengan resolusi tajam dan skenario yang selaras.
- Folder `dataset/` kini hanya memuat dataset primer aktif yang dibutuhkan oleh seeder (`tkpi_bdd.csv`, `tabel_akg_*.csv`, `bahan_penukar_juknis_bgn.csv`, `sppg_data_with_coords_clean.csv`, dan berkas acuan `TKPI.xlsx`).
- Seluruh unit & feature tests berjalan hijau (`PASS`).
