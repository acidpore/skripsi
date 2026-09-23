# Panduan Desain Frontend

Wajib dibaca sebelum menyentuh file di `resources/views/`. Semua token ada di `resources/views/components/layouts/app.blade.php`.

## Arah

Nuansa "lembar kerja gizi" institusional: kertas hangat, tinta gelap, garis tipis, angka monospace. Bukan template SaaS. Bukan kartu putih bertumpuk.

## Token

| Token | Nilai | Pakai untuk |
|---|---|---|
| `paper`, `paper-2`, `paper-3` | #F4F1EA, #ECE8DE, #E3DED2 | latar halaman, latar sekunder |
| `ink`, `ink-2`, `ink-3` | #1B1A17, #3F3B34, #5C574D | teks utama, teks pendukung, label |
| `line`, `line-strong` | #D6D0C2, #B8B1A0 | garis pemisah, border input |
| `leaf`, `leaf-light`, `leaf-dark` | #1E5A3C, #E1ECE4, #153F2A | aksen, status baik, tautan |
| `ember`, `ember-light` | #B4471F, #F5E4DC | galat, nilai di luar rentang |

Font: `font-display` (Fraunces) untuk h1/h2 dan nama menu, `font-sans` (IBM Plex Sans) untuk isi, `.num` (IBM Plex Mono 500, tabular) untuk semua angka.

## Kelas utilitas

- `.eyebrow` label kecil kapital monospace di atas judul atau header tabel.
- `.num` setiap angka, satuan, kode.
- `.field` input, select, textarea.
- `.btn .btn-ink` aksi utama (satu per bagian). `.btn .btn-line` aksi sekunder.
- `.rule` garis tipis atas. `.rule-strong` garis tebal 2px penanda awal bagian.
- `.ico` svg ikon 18px, stroke 1.75, `viewBox="0 0 24 24"`, tanpa fill.
- `.sweep` teks tombol saat loading. `.dotted-bg` latar empty state.

## Larangan

- Tidak ada `rounded-lg` ke atas, `shadow-*`, gradient, `backdrop-blur`, warna di luar token.
- Tidak ada ikon dari library eksternal. Ikon ditulis inline sebagai path sederhana dengan kelas `.ico`, dipakai secukupnya: kotak 36px berlatar putih berbingkai `line-strong` untuk penanda bagian, inline di tombol, dan penanda status.
- Tidak ada `antialiased` pada body (membuat teks tipis kabur di Windows).
- Tidak ada emoji atau karakter dekoratif non-ASCII. Entitas HTML seperti `&ndash;` boleh.
- Tidak rata tengah kecuali halaman satu kolom pendek (login, 404).

## Pola halaman

1. Header halaman: `.eyebrow` lalu `h1.font-display`. Kalimat pengantar maksimal dua baris di kanan atau bawah.
2. Form: tiap isian `grid grid-cols-[3rem_1fr]` dengan ikon kotak di kiri, dipisah `.rule`, tombol utama di bawah `.rule`.
3. Data: tabel dengan header `border-b border-ink` dan `.eyebrow`, baris `divide-line`, angka `.num` rata kanan.
4. Status: blok `border-l-2` warna `leaf` atau `ember` dengan label `.eyebrow` di kiri, bukan toast.
5. Halaman standalone (cetak) mengikuti font dan warna yang sama lewat CSS manual.
