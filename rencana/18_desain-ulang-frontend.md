# 18 - Desain Ulang Frontend

## Masalah

Tampilan sebelumnya memakai pola generik: kartu putih rounded-2xl di mana-mana, gradient radial biru, badge angka bulat, ikon sparkle, loader titik memantul, semuanya rata tengah. Halaman `/` masih welcome bawaan Laravel, halaman 404 punya konfigurasi font dan warna sendiri yang berbeda dari layout utama.

## Yang dibuat

Satu sistem desain di `resources/views/components/layouts/app.blade.php`, dipakai seluruh halaman:

- Warna: kertas hangat (`paper`), tinta (`ink`), garis (`line`), aksen hijau `leaf`, peringatan `ember`. Tanpa gradient, tanpa shadow.
- Tipografi: Fraunces untuk judul, IBM Plex Sans untuk isi, IBM Plex Mono untuk angka dan label kecil (`.num`, `.eyebrow`).
- Komponen CSS ringkas: `.field`, `.btn-ink`, `.btn-line`, `.rule`, `.rule-strong`, `.dotted-bg`, `.sweep` (loading).
- Radius maksimal 2px. Pemisah antar bagian memakai garis tipis, bukan kartu bersarang.
- Header menampilkan navigasi Rekomendasi dan Cara hitung dengan garis bawah hijau pada halaman aktif. Footer mencantumkan sumber acuan.

Halaman yang ditulis ulang: layout, welcome (landing ringkas pengganti bawaan Laravel), login, rekomendasi-menu, dokumentasi metode (dengan daftar isi sticky), 404 (kini memakai layout), cetak daftar belanja (tipografi disamakan).

Halaman rekomendasi kini menampilkan per kandidat: empat zat gizi makro dengan rentang AKG dan penanda merah jika di luar rentang, gramasi per komponen (collapsible), skor TOPSIS, bobot Entropy dalam tabel, dan koefisien Spearman sebagai angka.

## Keputusan desain

- Nuansa "lembar kerja gizi" institusional dipilih agar produk terasa ditulis untuk operator SPPG, bukan template SaaS.
- Angka selalu monospace tabular agar kolom sejajar dan mudah dibandingkan.
- Tidak ada ikon dekoratif. Satu-satunya tanda merek adalah kotak hijau kecil di samping wordmark.

## Revisi setelah umpan balik owner

- Angka terasa kabur: `antialiased` dibuang dari body, `.num` diberi bobot 500, angka gizi dibesarkan ke `text-xl`, rentang AKG ke `text-sm`, `.eyebrow` naik ke 12px bobot 500.
- Teks tipis menyatu dengan latar: `ink-2` dan `ink-3` digelapkan (#3F3B34, #5C574D).
- Ikon: ditambah set ikon inline `.ico` (stroke 1.75) pada brand, tiga isian form, tombol, status rentang gizi, empty state, dan tiga poin landing. Tetap tanpa library ikon.
- Panduan desain dipindah ke `rencana/DESIGN.md` dan dirujuk dari `CLAUDE.md` agar model apa pun mengikuti sistem yang sama.

## Sisa pekerjaan

- Grafik Chart.js pemenuhan gizi vs AKG (PRD 3.5 butir 6) belum dibuat; nilai gizi saat ini ditampilkan sebagai angka dengan rentang.
- Dashboard dan peta Leaflet belum ada halamannya.
