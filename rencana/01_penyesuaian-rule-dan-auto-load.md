# 01 — Penyesuaian Rule & Auto-Load Konteks

Tanggal: 09 September 2026

## Yang Dibuat

1. `rencana/rule.md` disesuaikan dari stack Travel System (TypeScript/Prisma) ke stack aktual project (Laravel + Livewire + SQLite).
2. `CLAUDE.md` di root project sebagai pintu masuk konteks otomatis.

## Keputusan Desain

**Pemetaan aturan lama ke stack Laravel**

| Aturan lama | Menjadi |
|---|---|
| `modules/[nama]/*.service.ts` | `app/Services/` |
| `types/index.ts` | Model Eloquent + Form Request |
| `components/shared/` | `resources/views/components/` |
| Prisma schema sebagai sumber kebenaran | Migration sebagai sumber kebenaran |
| Logika API di layer `api/` | Controller dan komponen Livewire hanya memanggil Service |

**Aturan baru yang ditambahkan**

- Pasal 5: komentar dibatasi maksimal satu baris. Blok komentar multi-baris, docblock panjang, dan banner dilarang. Rumus ilmiah cukup dirujuk satu baris nama rumusnya.
- Pasal 2: kewajiban interface `RankingMethod` untuk `TopsisRanking` dan `SawRanking`, sesuai justifikasi polimorfisme di PRD Bagian 5.
- Pasal 3: filter multi-tenant SPPG dijadikan aturan arsitektur, bukan sekadar fitur.
- Pasal 10: setiap progress wajib dicatat sebagai file md baru di `rencana/` dengan pola `NN_nama-progress.md`.
- Pasal 11: PRD wajib ikut diperbarui bila implementasi menyimpang.

**Mekanisme auto-load konteks**

`CLAUDE.md` di root project dibaca otomatis oleh AI assistant setiap sesi baru. File ini tidak menduplikasi isi aturan, melainkan mengimpor `rencana/rule.md` dan `rencana/PRD_Sistem_SPK_MBG.md` lewat sintaks `@path`. Dengan begitu aturan hanya punya satu sumber kebenaran, dan mengedit `rule.md` langsung berlaku di sesi berikutnya tanpa perlu menyentuh `CLAUDE.md`.

## Sisa Pekerjaan

Tiga hal berikut memblokir pembangunan mesin rekomendasi dan belum diputuskan:

1. Kriteria final matriks keputusan. `dataset/tkpi_bdd.csv` hanya memuat energi, protein, lemak, karbohidrat, dan `bdd_persen`. Kalsium, besi, dan vitamin C yang disebut PRD Bagian 3.5 belum terkonfirmasi tersedia di `TKPI.xlsx`.
2. Jumlah top-N alternatif menu per kelompok sasaran.
3. `dataset/template_alternatif_menu.csv` masih berisi nama masakan ("Ayam goreng"), belum tertaut ke kode bahan TKPI beserta gram per porsi. Tanpa keterkaitan ini matriks keputusan tidak dapat dibentuk. Ini blocker paling kritis.

Catatan teknis untuk ETL nanti: baris kedua `tkpi_bdd.csv` adalah baris sampah yang harus dilewati, dan seluruh angka gizi memakai koma sebagai pemisah desimal di dalam tanda kutip sehingga perlu dikonversi sebelum di-cast ke float.
