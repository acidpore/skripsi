# 08 — Satuan Penukar & Tujuh Komponen

Tanggal: 09 September 2026

Restrukturisasi pembangkit menu setelah Standar Makan Siang Juknis BGN 401.1/2025 terbaca. Juknis tidak memberi gramasi tetap melainkan sistem satuan penukar.

## Yang Dibuat

| File | Perubahan |
|---|---|
| `2026_09_09_000010_add_satuan_penukar_and_snapshot_kandidat.php` | Kolom `berat_per_sp_gram` pada `bahan_pangan`, kolom `komposisi_terpilih` pada `rekomendasi` |
| `app/Models/BahanPangan.php` | Kolom baru masuk fillable dan cast |
| `app/Models/Rekomendasi.php` | Kolom snapshot masuk fillable dan cast array |
| `config/spk.php` | `gramasi_standar` diganti `satuan_penukar`, komponen bertambah dua, batas bahan per komponen diturunkan |
| `app/Services/Menu/MenuCandidateGenerator.php` | Ditulis ulang mengikuti model satuan penukar dan pemangkasan bertahap |

## Keputusan Desain

**Berat per satuan penukar disimpan sebagai kolom pada `bahan_pangan`, bukan di config.** Nilai per SP melekat pada bahan, bukan pada kelompok sasaran. Menyimpannya di database membuat penambahan bahan penukar baru cukup lewat seeder atau antarmuka admin, tanpa menyentuh config. Gramasi kandidat dihitung `jumlah_sp x berat_per_sp_gram`.

**Kolom `berat_per_sp_gram` sekaligus menggantikan penyaringan `jenis = Tunggal`.** Sebelumnya bahan yang layak menjadi komponen menu disaring lewat kolom `jenis`, sebuah heuristik yang saya turunkan sendiri dari isi data. Kini penyaringnya adalah keanggotaan pada daftar bahan penukar Juknis, karena hanya bahan yang terdaftar di sana yang memiliki nilai per SP. Ini penyaring bersumber, bukan heuristik, sehingga lebih kuat dipertanggungjawabkan sekaligus menghapus kekhawatiran dodol atau kerupuk masuk sebagai komponen karbohidrat. Penyaringan `jenis` dihapus dari config.

**Komponen menjadi tujuh.** Ditambahkan `sumber_lemak` yang dipetakan ke kelompok Lemak dan `susu` ke kelompok Susu. Komponen yang tidak dicantumkan pada `satuan_penukar` suatu kelompok sasaran otomatis tidak dibangkitkan, sehingga susu yang hanya dipakai sebagian kelompok tidak memerlukan penanganan khusus.

**Pemangkasan dipindahkan ke dalam proses kombinasi.** Sebelumnya seluruh kombinasi dibentuk lebih dahulu lalu disaring energinya di akhir. Dengan tujuh komponen cara itu tidak lagi aman: batas lima bahan per komponen berarti 5 pangkat 7 yaitu 78.125 baris pada kasus terburuk. Kini setiap kali satu komponen ditambahkan, cabang yang energinya sudah melewati batas atas langsung dibuang, karena komponen berikutnya hanya menambah energi dan tidak mungkin mengoreksinya turun. Penyaringan batas bawah tetap dilakukan di akhir, karena kombinasi yang energinya masih kurang bisa saja terpenuhi oleh komponen berikutnya. `maksimal_bahan_per_komponen` diturunkan dari 8 menjadi 5 mengikuti bertambahnya komponen.

**Ibu Hamil dan Ibu Menyusui tidak dialiaskan.** Keduanya berbagi satu gambar standar pada Juknis, jadi kesamaan itu berasal dari BGN sendiri, bukan keputusan kita. Keduanya cukup diisi dengan nilai satuan penukar yang sama pada config. Alias tetap dipakai hanya untuk Pendidik dan Tenaga Kependidikan, yang memang tidak memiliki gambar sendiri.

**Snapshot komposisi disimpan pada `rekomendasi.komposisi_terpilih`.** Kolom berisi id bahan, kode, nama, jumlah SP, berat bersih, dan nilai gizi terhitung, sehingga riwayat rekomendasi tetap dapat ditelusuri persis meski data TKPI di-reseed atau nilai satuan penukar dikoreksi kemudian. `menu_terpilih_id` tetap nullable dan hanya terisi bila operator menyimpan kandidat sebagai menu tetap.

## Cacat Sumber Primer yang Ditangani di Kode

**Angka gram tercetak tidak dipakai sama sekali.** Seluruh gramasi dihitung dari jumlah SP dikali berat per SP. Ini menetralkan kesalahan pada Gambar 16, yang mencantumkan SP 1,75 namun gram karbohidratnya tersalin dari tabel SP 1,5 tanpa diskalakan. Konsekuensi lain yang menguntungkan, aturan perhitungan menjadi seragam untuk seluruh kelompok sasaran.

**SD kelas 1-3 diperlakukan sebagai kelompok tidak didukung.** Gambar 14 terpotong pada baris Sayur sehingga baris Buah dan Lemak tidak tercetak. Menebak nilainya dengan analogi ke TK akan melemahkan klaim bahwa seluruh gramasi bersumber dari dokumen resmi. Penanganannya tidak memerlukan kode tambahan, cukup dengan tidak mencantumkan kelompok itu pada `satuan_penukar`, dan `didukung` akan mengembalikan false.

Dengan demikian kelompok sasaran yang tidak dapat dibangkitkan menjadi tiga: Siswa SD/MI/SDLB Kelas 1-3, Anak Balita usia 13-59 bulan, dan Balita usia 6-11 bulan.

**Seluruh tabel berjudul makan siang.** Juknis tidak menyediakan standar makanan untuk waktu distribusi pagi, termasuk untuk TK/PAUD yang justru menerima MBG pagi. Ini tidak ditangani di kode dan memang tidak perlu, karena satuan penukar hanya menentukan besar porsi sedangkan penilaian tetap memakai rentang AKG sesuai waktu distribusi masing-masing. Porsi yang berlebih untuk sasaran pagi akan tertangkap sebagai skor kesesuaian rendah, bukan tersembunyi. Perlu dinyatakan di Batasan Masalah.

## Yang Masih Diblokir

`config/spk.satuan_penukar` masih kosong. Format yang diharapkan, dengan kunci kelompok sasaran sama persis dengan kolom `kelompok_sasaran` pada CSV BGN dan kunci komponen sama persis dengan `peta_komponen`:

```php
'satuan_penukar' => [
    'Siswa SD/MI/SDLB Kelas 4-6' => [
        'karbohidrat' => 1.5,
        'lauk_hewani' => 1,
        'lauk_nabati' => 1,
        'sayur' => 1,
        'buah' => 1,
        'sumber_lemak' => 1,
    ],
],
```

Nilai `berat_per_sp_gram` per bahan juga belum tersedia. Angka yang sudah terverifikasi dari lima tabel yang saling konsisten adalah beras 50, nasi 100, telur 55, daging ayam 40, tempe 50, tahu 110, dan sayur 100. Seluruh daftar penukar perlu ditranskrip lengkap sebelum seeder dibuat, karena bahan tanpa nilai ini tidak akan pernah ikut dibangkitkan.

## Sisa Pekerjaan

- Transkripsi nilai satuan penukar per kelompok sasaran dan berat per SP per bahan
- Pengalihan `RecommendationService` dari tabel `menu` ke `MenuCandidateGenerator`, prioritas pertama setelah data di atas masuk
- Tahap 0 belum dieksekusi: Livewire belum terpasang, residu VARK belum dihapus
