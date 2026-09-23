# 13 — Simpan Kandidat Terpilih Jadi Menu Tetap SPPG

Tanggal: 14 September 2026

## Yang Dibuat

| File | Perubahan |
|---|---|
| `app/Services/Menu/MenuPersistenceService.php` | Baru — `simpanDariRekomendasi()` mengubah satu kandidat (default peringkat 1 TOPSIS dari `komposisi_terpilih`) jadi baris `Menu` + `MenuBahan`, lalu mengisi `Rekomendasi.menu_terpilih_id` |
| `app/Models/Menu.php` | `KOMPONEN` diperbaiki dari 5 nama lama (`lauk_hewani`, `lauk_nabati`, tanpa lemak/susu) jadi 7 nama yang benar-benar dipakai generator (`protein_hewani`, `protein_nabati`, + `lemak`, `susu`) — konstanta ini tidak dipakai di manapun sebelumnya, jadi tidak ada regresi |
| `app/Console/Commands/CobaRekomendasi.php` | Tambah opsi `--simpan` untuk uji coba manual siklus penuh sampai tersimpan jadi menu |

## Keputusan Desain

**Parameter `komposisi` opsional, bukan wajib peringkat 1.** `simpanDariRekomendasi()` menerima array komposisi eksplisit sebagai override, dengan fallback ke `komposisi_terpilih` bawaan rekomendasi. Ini disiapkan untuk UI Livewire nanti, di mana operator bisa memilih peringkat mana pun dari `hasil_ranking_topsis`/`hasil_ranking_saw` untuk disimpan, bukan cuma peringkat 1.

**Kode menu dibangkitkan acak (`MN-XXXXXXXX`).** Tidak ada aturan penomoran di PRD, jadi dipakai kode acak yang cukup unik alih-alih skema penomoran berurutan yang butuh keputusan desain terpisah dan belum diminta.

## Verifikasi

`spk:coba "Siswa SD/MI/SDLB Kelas 4-6" --porsi=100 --simpan` dijalankan: 7 baris `MenuBahan` tersimpan sesuai 7 komponen, `Rekomendasi.menu_terpilih_id` terisi dan bisa ditelusuri balik dari `Menu` ke `Rekomendasi`.

## Sisa Pekerjaan

UI Livewire, auth + pengikatan operator ke SPPG, export daftar belanja — urutan tidak berubah dari progress 11.
