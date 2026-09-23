# 11 — Pengalihan Rekomendasi ke Generator

Tanggal: 09 September 2026

Sejak progress ini, klaim bahwa sistem menyusun menu dan bukan memilih dari daftar tetap benar-benar berjalan saat aplikasi dijalankan, bukan hanya tertulis di naskah.

## Yang Dibuat

| File | Perubahan |
|---|---|
| `database/seeders/AkgHarianSeeder.php` | Baru |
| `database/seeders/AkgSekaliMakanSeeder.php` | Baru |
| `database/seeders/SppgSeeder.php` | Baru |
| `database/seeders/DatabaseSeeder.php` | Ketiganya didaftarkan |
| `app/Services/Spk/DecisionMatrixBuilder.php` | Dilepas dari Model, kini menerima daftar kandungan gizi |
| `app/Services/Spk/RecommendationService.php` | Sumber alternatif dialihkan ke `MenuCandidateGenerator` |
| `app/Services/Menu/MaterialRequirementService.php` | Membaca snapshot komposisi, bukan relasi menu |
| `app/Console/Commands/CobaRekomendasi.php` | Perintah pemeriksaan cepat satu siklus rekomendasi |

## Hasil Seeding

| Tabel | Jumlah | Sumber |
|---|---|---|
| bahan_pangan | 1.148 | TKPI, dua baris keterangan tabel dilewati |
| bahan penukar aktif | 29 | Juknis BGN, Beras dikecualikan |
| akg_harian | 26 | Permenkes 28/2019 |
| akg_sekali_makan | 12 | Juknis BGN |
| sppg | 30.244 | data induk nasional, 13 tanpa koordinat |

## Keputusan Desain

**`DecisionMatrixBuilder` dilepas dari Eloquent.** Sebelumnya ia menerima koleksi Model `Menu` dan memanggil `MenuNutritionCalculator` di dalamnya. Kini ia menerima daftar kandungan gizi berupa array dan hanya mengembalikan matriks beserta tipe kriterianya. Dengan begitu class inti SPK tidak lagi bergantung pada sumber alternatif, sehingga kandidat hasil pembangkitan maupun menu tersimpan dapat diperlakukan sama, dan pengujian akurasi perhitungan pada fase Evaluation dapat dijalankan tanpa menyentuh database.

**Perhitungan gizi kandidat tidak diulang di builder.** `MenuCandidateGenerator` sudah menghitung kandungan gizi saat menyusun kombinasi, karena angka itu dipakai untuk memangkas cabang yang energinya berlebih. Menghitung ulang di builder berarti mengerjakan hal yang sama dua kali untuk seluruh kandidat.

**Pesan penolakan menyebut jalan keluarnya.** Bila kandidat yang lolos penyaringan energi kurang dari dua, pesan yang muncul menyarankan menambah pilihan bahan tersedia atau memeriksa stok yang diinput. Operator SPPG bukan pengembang, sehingga pesan yang hanya menyatakan kegagalan tidak menolong.

**`MaterialRequirementService` membaca snapshot, bukan relasi.** Kebutuhan bahan dihitung dari `rekomendasi.komposisi_terpilih`, sehingga riwayat rekomendasi lama tetap menghasilkan daftar belanja yang sama meski TKPI di-seed ulang atau nilai satuan penukar dikoreksi. Ini konsekuensi langsung dari alasan reproduktibilitas pada progress 08.

**Perintah `spk:coba` ditambahkan.** Antarmuka Livewire belum ada, sedangkan seluruh rantai perhitungan sudah lengkap. Perintah ini menjalankan satu siklus penuh dari pembangkitan sampai daftar belanja, sehingga mesin SPK dapat diperiksa lebih dahulu tanpa menunggu antarmuka selesai.

## Temuan untuk Naskah

**Jumlah SPPG berbeda dari yang dikutip.** Latar Belakang mengutip 28.390 SPPG per Mei 2026, sedangkan data induk yang di-seed berisi 30.244, selisih 1.854. Perlu satu kalimat yang menyatakan data induk diambil pada tanggal lebih baru daripada angka yang dikutip, atau kutipannya diperbarui.

**Tiga belas SPPG tanpa koordinat** tidak akan tampil pada peta persebaran. Jumlahnya 0,04 persen, namun sebaiknya disebut di Bab IV agar tidak terbaca sebagai cacat sistem.

**Jumlah bahan TKPI 1.148**, bukan 1.146 seperti pada naskah versi lama. Batasan 10 versi 14 sudah tidak menyebut angka, sehingga tidak ada yang perlu diubah kecuali angka itu muncul lagi di bab lain.

## Cara Memeriksa

```
php artisan spk:coba "Siswa SD/MI/SDLB Kelas 4-6" --porsi=100
```

Keluaran memuat koefisien Spearman, bobot Entropy tiap kriteria, lima peringkat teratas TOPSIS beserta komposisi dan energinya, serta kebutuhan bahan dalam kilogram berat bersih dan berat kotor.

Kelompok sasaran yang dapat dicoba: Siswa TK/PAUD/TKLB, Siswa SD/MI/SDLB Kelas 4-6, Siswa SMP/MTs/SMPLB, Siswa SMA/SMK/MA/SMALB, Pendidik, Tenaga Kependidikan, Anak Balita, Ibu Hamil, Ibu Menyusui. Tiga kelompok lain akan ditolak dengan pesan yang menyebut ketiadaan standar makanan resmi.

## Sisa Pekerjaan

- Antarmuka Livewire belum ada sama sekali
- Autentikasi dan pengikatan operator ke SPPG belum dibuat
- Export daftar belanja belum dibuat
- Menyimpan kandidat terpilih sebagai menu tetap SPPG belum dibuat, `menu_terpilih_id` masih selalu null
- Peninjauan ulang `maksimal_bahan_per_komponen` dapat dilakukan setelah `spk:coba` dijalankan dan jumlah kandidat yang lolos terlihat
