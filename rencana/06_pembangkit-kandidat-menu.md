# 06 — Pembangkit Kandidat Menu

Tanggal: 09 September 2026

Menindaklanjuti temuan T2 pada progress 05. Sistem kini membangkitkan kandidat menu dari basis bahan pangan, bukan memilih dari daftar menu tetap.

## Yang Dibuat

| File | Perubahan |
|---|---|
| `config/spk.php` | Ditambah `peta_komponen`, `jenis_bahan_diizinkan`, `gramasi_standar`, dan `pembangkitan` |
| `app/Services/Menu/MenuCandidateGenerator.php` | Pembangkit kandidat tiga tahap |
| `rencana/PRD_Sistem_SPK_MBG.md` | Bagian 3.4 ditulis ulang menjadi Pembangkitan Alternatif Menu, Bagian 6 dan 7 disesuaikan ke istilah ISO/IEC 25010:2023 |

## Temuan dari Pemeriksaan Data TKPI

Pemeriksaan `dataset/tkpi_bdd.csv` menemukan dua hal yang menentukan desain pembangkit.

**Kolom `kelompok` memakai nama pendek**, yaitu Serealia, Umbi, Kacang, Sayur, Buah, Daging, Ikan dsb, Telur, Susu, Lemak, Gula, Bumbu. Penamaan ini berbeda dari nama panjang pada dokumen TKPI cetak, sehingga pemetaan komponen harus memakai nama pendek tersebut, bukan nama pada naskah.

**Kolom `jenis` membedakan Tunggal dan Olahan**, dan temuan ini penting. Kelompok Serealia berjenis Olahan memuat dodol bali, kue lumpur, wingko babat, dan biskuit. Kelompok Umbi berjenis Olahan memuat keripik singkong dan kerupuk udang goreng. Bila pembangkit mengambil seluruh bahan tanpa membedakan jenis, sistem akan menghasilkan kandidat menu seperti nasi dengan dodol sebagai komponen karbohidrat. Pembangkitan karena itu dibatasi pada bahan berjenis Tunggal saja.

Pembatasan ini juga memperkuat Batasan Masalah poin 10 pada naskah, karena penyaringan bahan tidak hanya berdasarkan ketersediaan nilai BDD, melainkan juga berdasarkan jenis bahan.

## Keputusan Desain

**Peran komponen diturunkan dari kelompok TKPI.** `peta_komponen` memetakan Serealia dan Umbi menjadi karbohidrat, Daging, Ikan dsb, dan Telur menjadi lauk hewani, Kacang menjadi lauk nabati, sedangkan Sayur dan Buah mengikuti namanya. Kelompok Susu, Lemak, Gula, dan Bumbu tidak dipetakan sehingga otomatis tidak ikut dibangkitkan. Pemetaan memakai taksonomi TKPI sendiri, sehingga merupakan keputusan bersumber dan bukan penilaian pribadi peneliti.

**Kombinasi tidak pernah dienumerasi seluruhnya.** Urutan penyaringan mengikuti tiga tahap: batasi kandidat bahan per komponen, saring dengan bahan yang tersedia di SPPG bila operator menginputnya, lalu buang kombinasi yang energinya berada di luar rentang AKG sebelum kandidat masuk matriks keputusan.

**Top-N dijawab lewat config, bukan angka tebakan.** `maksimal_bahan_per_komponen` bernilai 8 dan `maksimal_kandidat` bernilai 20. Dengan lima komponen, batas pertama menahan ruang kombinasi pada 8 pangkat 5 yaitu 32.768 baris sebagai batas atas terburuk, dan itu terjadi hanya bila operator tidak menginput bahan tersedia sama sekali. Penyaringan energi memangkasnya jauh lebih kecil sebelum matriks dibentuk. Kedua angka ini adalah keluaran desain penyaringan, sehingga menjawab pertanyaan terbuka K3 pada PRD Bagian 9.

**Kandidat diurutkan berdasarkan kedekatan energi terhadap titik tengah rentang** sebelum dipotong menjadi top-N. Penyaringan awal ini sengaja hanya memakai energi, karena energi adalah kendala paling mengikat. Penilaian menyeluruh atas keempat zat gizi tetap dikerjakan Entropy, TOPSIS, dan SAW pada matriks keputusan, bukan pada tahap penyaringan.

**Gramasi wajib ada, bila kosong pembangkitan ditolak.** Bila `gramasi_standar` untuk suatu kelompok sasaran belum diisi, service melempar exception dengan pesan yang menyebut kelompok sasarannya. Ini lebih baik daripada membangkitkan kandidat dengan gramasi tebakan yang hasilnya tidak dapat dipertanggungjawabkan.

## Yang Diblokir dan Menunggu Developer

`config/spk.php` bagian `gramasi_standar` sengaja dikosongkan. Isinya harus ditranskrip dari Standar Makan Siang pada Juknis BGN 401.1/2025 Gambar 11 sampai 17, yang berbentuk gambar sehingga tidak dapat diekstrak otomatis dari PDF.

Format yang diharapkan, dengan kunci kelompok sasaran yang sama persis dengan kolom `kelompok_sasaran` pada `tabel_akg_satu_kali_mbg_BGN.csv`:

```php
'gramasi_standar' => [
    'Siswa SD/MI/SDLB Kelas 4-6' => [
        'karbohidrat' => 150,
        'lauk_hewani' => 50,
        'lauk_nabati' => 50,
        'sayur' => 75,
        'buah' => 100,
    ],
],
```

Angka pada contoh di atas adalah ilustrasi format, bukan nilai sebenarnya. Jangan dipakai sebelum diganti dengan angka dari Juknis.

## Sisa Pekerjaan

- Transkripsi gramasi standar dari Juknis BGN, memblokir seluruh pengujian pembangkit dengan data nyata
- `RecommendationService` masih membaca alternatif dari tabel `menu`, perlu dialihkan ke `MenuCandidateGenerator` setelah gramasi tersedia
- Perilaku `menu_terpilih_id` perlu diputuskan ulang: kandidat hasil pembangkitan belum tersimpan sebagai baris `menu`, sehingga perlu disimpan lebih dahulu saat operator memilihnya
- T6 pada progress 05 belum diverifikasi, yaitu angka 990 dari 1.146 bahan
- Tahap 0 belum dieksekusi: Livewire belum terpasang, residu VARK belum dihapus
