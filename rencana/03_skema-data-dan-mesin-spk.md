# 03 — Skema Data & Mesin SPK

Tanggal: 09 September 2026

Dikerjakan paralel: skema data di thread utama, class perhitungan SPK oleh sub agent.

## Yang Dibuat

### Migration (9 file, prefix `2026_09_09_`)

| File | Tabel |
|---|---|
| `000001` | `sppg` |
| `000002` | `bahan_pangan` |
| `000003` | `akg_harian` |
| `000004` | `akg_sekali_makan` |
| `000005` | `menu` |
| `000006` | `menu_bahan` |
| `000007` | `rekomendasi` |
| `000008` | `kebutuhan_bahan` |
| `000009` | tambah `sppg_id` dan `role` pada `users` |

### Model

`Sppg`, `BahanPangan`, `AkgHarian`, `AkgSekaliMakan`, `Menu`, `MenuBahan`, `Rekomendasi`, `KebutuhanBahan`, dan `User` yang ditulis ulang.

### Service SPK di `app/Services/Spk/`

`RankingMethod` (interface), `EntropyWeighting`, `TopsisRanking`, `SawRanking`, `SpearmanCorrelation`. Seluruh class menerima array dan mengembalikan array, tanpa menyentuh database. Self-check berbasis `assert()` ada di `tests/Spk/SpkSelfCheck.php`.

## Keputusan Desain

**Kelompok sasaran tidak disimpan sebagai teks bebas.** `menu` dan `rekomendasi` merujuk ke `akg_sekali_makan_id`. Pada `tabel_akg_satu_kali_mbg_BGN.csv` setiap kelompok sasaran muncul tepat satu kali dengan satu waktu distribusi, sehingga satu foreign key sudah memuat kelompok sasaran sekaligus waktu makan. Ini juga menghindari pencocokan string saat query, mengingat label pada `template_alternatif_menu.csv` ("SD Kelas 4-6") berbeda dari label BGN ("Siswa SD/MI/SDLB Kelas 4-6"). Pencocokan label cukup dilakukan sekali saat seeding.

**Kolom kalsium, besi, dan vitamin C dibuat nullable.** Keputusan K1 belum diambil, dan membuat kolomnya nullable sejak awal berarti kedua kemungkinan hasil verifikasi `TKPI.xlsx` tidak menimbulkan migration ulang.

**`menu.sppg_id` boleh null.** Null berarti menu acuan nasional yang dapat dipakai seluruh SPPG, sedangkan menu dengan `sppg_id` terisi hanya milik SPPG tersebut. Scope `tersediaUntukSppg` menggabungkan keduanya.

**`menu_bahan.berat_bersih_gram`** disebut berat bersih, bukan sekadar gram, karena TKPI menghitung gizi per 100 gram bagian yang dapat dimakan. Penamaan ini mencegah tertukar dengan berat kotor hasil konversi BDD.

**`rekomendasi` menyimpan `bobot_entropy`** selain kedua hasil ranking. PRD Bagian 4 belum mencantumkannya, namun bobot wajib tersimpan agar hasil perankingan dapat ditelusuri ulang saat sidang. PRD perlu menyesuaikan pada revisi berikutnya.

**`User` ditulis ulang.** Model lama memuat kolom dan relasi milik project VARK (`vark_score_v` sampai `predicted_style`, relasi ke `QuestionnaireDataset`, `QuizResult`, `AccessLog`). File ini harus berubah untuk menampung `sppg_id`, jadi sekalian dibersihkan. File VARK lainnya belum disentuh, masih menunggu persetujuan penghapusan.

## Temuan yang Perlu Keputusan

**K5 — normalisasi kriteria cost pada SAW bernilai nol.** Rumus baku SAW untuk kriteria cost adalah `min / x`. Bila `x` bernilai nol, pembagian tidak terdefinisi, dan implementasi saat ini mengembalikan 0.0 sebagai penalti. Untuk sistem ini konsekuensinya keliru arah: kriteria "selisih terhadap AKG" bersifat cost, dan selisih nol justru berarti menu tepat sasaran, seharusnya mendapat skor terbaik, bukan terburuk. Perlu diputuskan salah satu:

1. Tambahkan epsilon kecil pada seluruh nilai kriteria cost sebelum normalisasi.
2. Ganti normalisasi cost menjadi `1 - x / max`, konsisten untuk nilai nol tetapi menyimpang dari rumus SAW yang lazim dikutip.
3. Definisikan ulang kriteria menjadi benefit, misalnya "tingkat kesesuaian AKG" dengan nilai lebih besar berarti lebih sesuai.

Opsi 3 paling aman secara metodologis karena tidak mengubah rumus SAW yang akan dikutip di Bab II.

**Entropy menuntut nilai non-negatif.** Proporsi negatif dilewati dalam perhitungan, sehingga kriteria selisih AKG wajib disimpan sebagai nilai mutlak, bukan selisih bertanda. Ini menguatkan opsi 3 di atas.

## Sisa Pekerjaan

- Menunggu keputusan K1 sampai K5
- Tahap 0 belum dieksekusi: Livewire belum terpasang, residu VARK belum dihapus
- `DecisionMatrixBuilder` dan `RecommendationService` belum dibuat, keduanya menunggu keputusan kriteria final
- Seeder belum dibuat

## Command untuk Developer

Belum perlu dijalankan sekarang. Migration baru aman dijalankan hanya setelah keputusan penghapusan residu VARK diambil, karena migration `2026_01_17_*` masih aktif dan akan ikut berjalan.
