# 15 — UI Livewire Rekomendasi Menu

Tanggal: 14 September 2026

## Yang Dibuat

| File | Isi |
|---|---|
| `resources/views/components/layouts/app.blade.php` | Layout dasar: Tailwind CDN, `@livewireStyles`/`@livewireScripts` |
| `app/Livewire/RekomendasiMenu.php` | Full-page Livewire component: form SPPG/kelompok sasaran/porsi, `generate()`, `simpan()` |
| `resources/views/livewire/rekomendasi-menu.blade.php` | View: form, tabel 5 peringkat teratas TOPSIS, kebutuhan bahan |
| `routes/web.php` | `GET /rekomendasi` -> `RekomendasiMenu::class` |

## Keputusan Desain

**Tailwind lewat CDN, bukan build Vite.** `public/build/` belum pernah di-generate (`node_modules` juga belum ter-install) — menyiapkan pipeline asset build itu kerjaan tersendiri di luar lingkup UI SPK ini. PRD sendiri mengizinkan opsi CDN untuk styling. Migrasi ke build Vite bisa jadi progress terpisah nanti kalau developer sudah `npm install`.

**Pencarian SPPG, bukan dropdown penuh.** 30 ribu baris `sppg` tidak mungkin di-render sebagai satu `<select>`. Dipakai input teks dengan `wire:model.live.debounce.400ms`, computed property query `LIKE` dibatasi 10 hasil, operator klik salah satu buat pilih.

**Kelompok sasaran difilter ke yang didukung generator saja.** Dropdown AKG dibangun dari `AkgSekaliMakan::all()->filter(fn ($akg) => $generator->didukung($akg))`, supaya operator tidak bisa memilih kelompok yang pasti gagal (SD kelas 1-3, Balita 13-59bln, Balita 6-11bln — sesuai batasan PRD 3.4).

**Simpan menu per baris peringkat, bukan cuma top-1.** Tiap baris tabel ranking punya input nama + tombol simpan sendiri, memanggil `simpan(int $indeksPeringkat)` yang mengambil `komposisi` dari `hasil_ranking_topsis[$indeks]`, dilempar ke parameter opsional `MenuPersistenceService::simpanDariRekomendasi()` yang memang disiapkan untuk kasus ini di progress 13.

## Ditunda (Dicatat, Bukan Dihilangkan Diam-diam)

- **Input "bahan tersedia per komponen"** — generator sudah punya mode default (array kosong = semua bahan dipakai), sudah merupakan perilaku valid. Picker manual 7 komponen adalah UI tersendiri yang lebih kompleks, ditunda ke iterasi berikutnya.
- **Grafik Chart.js pemenuhan gizi vs AKG** (PRD 3.5 poin 6) — perlu wiring aset JS terpisah dari CDN Tailwind yang dipakai sekarang, ditunda supaya alur inti selesai dan teruji browser dulu.
- **Auth/pengikatan operator ke SPPG** — itu langkah 5. SPPG dipilih manual dulu di form, tanpa filter tenant.

## Verifikasi

Diuji langsung lewat browser (Chrome automation):
1. `/rekomendasi` render bersih, tidak ada error di console
2. Cari SPPG "a" -> 10 hasil muncul, klik salah satu -> "Terpilih: ..." tampil
3. Dropdown kelompok sasaran isi 9 opsi (7 kelompok + 2 alias), sesuai filter `didukung()`
4. Pilih "Siswa SD/MI/SDLB Kelas 4-6 - Siang", porsi 100, klik "Buat Rekomendasi" -> Spearman, bobot Entropy 3 kriteria, tabel 5 peringkat, dan kebutuhan bahan semua tampil benar
5. Simpan peringkat 2 (bukan default top-1) dengan nama "Menu Uji Peringkat 2" -> pesan sukses tampil
6. Verifikasi lewat query DB langsung: `Menu` + `MenuBahan` tersimpan cocok persis dengan `hasil_ranking_topsis[1]['komposisi']`, bukan `komposisi_terpilih` (top-1) — membuktikan parameter override di `MenuPersistenceService` jalan benar dari UI

## Sisa Pekerjaan

Auth + pengikatan operator ke SPPG (langkah 5), lalu export daftar belanja (langkah 6). Bahan tersedia picker dan grafik Chart.js jadi item backlog terpisah, tidak wajib sebelum langkah 5/6.
