# 14 — Data Pendukung Anomali Bobot TK/PAUD

Tanggal: 14 September 2026

Catatan ini bukan kesimpulan, hanya data mentah supaya penjelasan di Bab IV berbasis angka, bukan spekulasi saat ditanya penguji kenapa pola bobot TK/PAUD terbalik dari 6 kelompok sasaran lain (Lemak dominan 0,87 vs Karbohidrat dominan 0,60-0,79 di kelompok lain).

## Rentang AKG Sekali Makan (Waktu Pagi, per `akg_sekali_makan`)

| Kelompok | Protein (g) | Lemak (g) | Karbohidrat (g) |
|---|---|---|---|
| Siswa TK/PAUD/TKLB (Pagi) | 5 - 6,3 | 10 - 12,5 | 44 - 55 |
| Siswa SD/MI/SDLB Kelas 4-6 (Siang, pembanding) | 15,8 - 18,4 | 19,5 - 22,8 | 87 - 101,5 |

## Kandungan Gizi Mentah 5 Kandidat Pertama (sebelum ditransformasi jadi skor kesesuaian)

**TK/PAUD:**

| # | Protein (g) | Lemak (g) | Karbohidrat (g) |
|---|---|---|---|
| 1 | 17,6 | 7,8 | 54,5 |
| 2 | 17,2 | 7,4 | 55,4 |
| 3 | 17,3 | 7,4 | 57,0 |
| 4 | 16,6 | 7,5 | 57,3 |
| 5 | 17,2 | 7,3 | 56,0 |

**SD 4-6 (pembanding):**

| # | Protein (g) | Lemak (g) | Karbohidrat (g) |
|---|---|---|---|
| 1 | 22,6 | 18,8 | 82,9 |
| 2 | 24,2 | 17,2 | 87,0 |
| 3 | 17,9 | 20,4 | 83,3 |
| 4 | 19,3 | 23,5 | 72,7 |
| 5 | 23,9 | 30,7 | 86,0 |

## Skor Kesesuaian Seluruh 20 Kandidat (setelah transformasi persamaan 3)

**TK/PAUD:**

- kesesuaian_protein: min 0,350 — max 0,395 (rentang sempit, seluruhnya jauh di bawah 1 karena `x > U`, dihukum rumus `U/x`)
- kesesuaian_lemak: min 0,615 — max 0,832 (rentang relatif lebih lebar)
- kesesuaian_karbohidrat: min 0,935 — max 1 (rentang sangat sempit, mepet ke 1)

**SD 4-6 (pembanding):**

- kesesuaian_protein: min 0,651 — max 1
- kesesuaian_lemak: min 0,625 — max 1
- kesesuaian_karbohidrat: min 0,322 — max 1 (rentang paling lebar di antara ketiganya, cocok dengan bobot karbohidrat yang dominan di 6 kelompok sasaran lain)

## Pengamatan Berbasis Angka (bukan kesimpulan final)

1. Pada TK/PAUD, kandungan protein kandidat (16,6-17,6 g) jauh melampaui batas atas rentang AKG protein sekali makan (6,3 g) — hampir 3 kali lipat. Ini konsisten di seluruh 20 kandidat, sehingga skor kesesuaian protein seluruhnya terjebak di rentang sempit 0,35-0,40 (dihukum rumus `U/x` karena kelebihan, bukan karena mendekati sesuai).
2. Kesesuaian karbohidrat TK/PAUD juga sangat sempit (0,935-1) karena satuan penukar karbohidrat kelompok ini (1,25 SP) menghasilkan kandungan karbohidrat yang kebetulan hampir selalu jatuh dekat batas atas rentang AKG (55 g).
3. Kesesuaian lemak adalah satu-satunya dari ketiga kriteria TK/PAUD yang variasinya relatif lebar (0,615-0,832) dibanding dua kriteria lain yang sama-sama sempit — pola inilah yang membuat Entropy menilai lemak paling informatif dan memberinya bobot dominan.
4. Pada SD 4-6 dan lima kelompok sasaran sisanya, kandungan gizi dari komposisi standar Juknis relatif lebih dekat ke rentang AKG di ketiga kriteria (skor mendekati atau menyentuh 1 di ujung atas), tapi karbohidrat punya sebaran skor terluas (turun sampai 0,322 di beberapa kandidat), sehingga karbohidrat yang dominan.

## Yang Perlu Diperiksa Lebih Lanjut (bukan tugas kode, ini soal data/asumsi gizi)

Poin 1 di atas berpotongan langsung dengan rencana validasi di PRD subbab 3.6.1 (validasi konsistensi internal satuan penukar terhadap rentang AKG). Kandungan protein standar makan siang TK/PAUD Juknis BGN tampak jauh melebihi rentang AKG protein sekali makan Permenkes 28/2019 yang dipakai sebagai acuan skor — ini bisa jadi temuan penelitian yang sah (dua sumber resmi tidak saling konsisten untuk kelompok ini), bukan kesalahan implementasi. Perlu dikonfirmasi ke ahli gizi/dosen pembimbing sebagaimana direncanakan di subbab 3.9, bukan diasumsikan sebagai bug.
