# 05 — Audit Proposal v12 terhadap Sistem yang Dibangun

Tanggal: 09 September 2026

Pemeriksaan konsistensi antara naskah `Proposal_Skripsi_v12.md`, `PRD_Sistem_SPK_MBG.md`, dan kode yang sudah ditulis. Tidak ada kode yang diubah pada progress ini.

## Temuan Kritis

### T1 — Tidak ada satupun kriteria bertipe cost, padahal naskah menjanjikannya

Bab II subbab 2.6 menyatakan penelitian ini "membedakan secara eksplisit kriteria benefit dan cost untuk menghindari kekeliruan tersebut". Identifikasi Masalah poin 6 menjadikan ketiadaan pembedaan benefit-cost sebagai salah satu masalah yang diangkat.

Setelah keputusan K2 dan K5, keempat kriteria final seluruhnya bertipe benefit. Tidak ada kriteria cost sama sekali. Secara teknis ini benar, karena kelebihan gizi sudah dihukum di dalam rumus skor kesesuaian melalui cabang `U / x`. Namun naskah belum menjelaskan hal itu, sehingga sekilas terbaca seolah janji pembedaan benefit-cost tidak dipenuhi.

Perbaikan yang disarankan pada subbab 2.6: nyatakan bahwa penelitian ini menangani persoalan lemak sebagai kriteria benefit yang keliru bukan dengan memindahkannya menjadi cost, melainkan dengan mengubah seluruh kriteria gizi menjadi skor kesesuaian terhadap rentang, karena lemak bukan zat gizi yang semakin rendah semakin baik. Batas bawah lemak juga ditetapkan BGN, sehingga memperlakukannya sebagai cost akan sama kelirunya dengan memperlakukannya sebagai benefit. Argumen ini justru memperkuat kebaruan, tetapi harus tertulis.

### T2 — Naskah menjanjikan menu dibangkitkan sistem, implementasi memilih dari daftar

Batasan Masalah poin 6 berbunyi: "Alternatif keputusan berupa kombinasi menu yang dibangkitkan sistem dari basis data bahan pangan, bukan daftar menu tetap yang dimasukkan secara manual." Identifikasi Masalah poin 8 dan Latar Belakang menjadikan sifat "memilih dan bukan menyusun" sebagai kelemahan Insani et al. (2026) yang hendak diperbaiki.

Sistem yang dibangun saat ini memakai tabel `menu` yang diisi lewat CRUD dan seeder dari `template_alternatif_menu.csv`. Artinya sistem masih memilih dari daftar menu tetap, persis kelemahan yang dikritik. Ini bukan cacat kecil, melainkan menyangkut klaim kebaruan pada Latar Belakang dan Batasan Masalah sekaligus.

Ada dua jalan keluar, dan keduanya menuntut keputusan sebelum modul rekomendasi diselesaikan.

Jalan pertama, bangun modul pembangkit kombinasi: sistem menyusun kandidat menu dengan memilih satu bahan untuk tiap komponen (karbohidrat, lauk hewani, lauk nabati, sayur, buah) dari basis TKPI, lalu menyaringnya. Ini memenuhi janji naskah, sekaligus menjelaskan mengapa PRD Bagian 3.4 menyebut pembatasan top-N "agar tidak meledak kombinatorial". Konsekuensinya, gramasi tiap komponen harus ditentukan, dan ledakan kombinasi harus dikendalikan.

Jalan kedua, turunkan klaim di naskah menjadi memilih dari daftar alternatif yang dikelola operator. Lebih cepat dikerjakan, tetapi menghapus salah satu pembeda utama terhadap penelitian terdahulu, sehingga kebaruan bertumpu pada Entropy dan BDD saja.

Rekomendasi: jalan pertama, karena naskah sudah terlanjur mengklaimnya di dua tempat dan klaim itu adalah bagian dari celah kebaruan pada paragraf penutup Latar Belakang.

### T3 — Zat gizi mikro disebut sebagai masalah tetapi tidak diselesaikan

Identifikasi Masalah poin 2 menyatakan kalsium, zat besi, dan vitamin C belum diperhitungkan secara sistematis, dan Latar Belakang mengulang keluhan yang sama. Namun setelah keputusan K1, Batasan Masalah poin 3 membatasi kriteria hanya pada empat zat gizi makro.

Akibatnya naskah mengidentifikasi sebuah masalah lalu mengecualikannya dari ruang lingkup tanpa penjelasan. Perlu satu kalimat pada Batasan Masalah poin 3 yang menyatakan alasannya, yaitu tabel Angka Kecukupan Gizi satu kali Makan Bergizi Gratis yang diterbitkan BGN hanya menetapkan rentang untuk empat zat gizi makro, sehingga kriteria kesesuaian hanya dapat dibentuk untuk keempatnya. Alternatifnya, kembalikan ketiga zat gizi mikro ke dalam matriks dengan menurunkan acuannya sendiri dari AKG harian Permenkes 28/2019 dikali persentase kontribusi BGN.

### T4 — PRD masih memakai istilah ISO/IEC 25010 edisi 2011

Proposal Batasan Masalah poin 8 dan subbab 2.10 sudah memakai istilah edisi 2023: tujuh karakteristik yang diuji mencakup *flexibility*, sedangkan *interaction capability* dan *safety* dikecualikan.

PRD Bagian 6 masih mencantumkan *Portability* dan *Compatibility* sebagai karakteristik terpisah, dan Bagian 7 masih menyebut pengecualian *Usability*. Keduanya istilah edisi 2011. PRD perlu disesuaikan agar tidak menjadi dua dokumen yang saling bertentangan saat dilampirkan.

### T5 — Subbab 2.4 masih menyebut biaya sebagai kriteria

Kalimat pada subbab 2.4 masih mendaftar "energi, protein, lemak, karbohidrat, kalsium, zat besi, vitamin C, biaya, dan kesesuaian terhadap AKG" sebagai parameter yang dipertimbangkan. Kriteria biaya sudah dikeluarkan, dan susunan kriteria sudah berubah menjadi empat skor kesesuaian. Kalimat ini perlu diselaraskan dengan Batasan Masalah poin 3.

### T6 — Batasan Masalah poin 10 perlu diverifikasi ulang terhadap data

Poin 10 menyebut 990 dari 1.146 bahan memiliki nilai BDD. Angka ini belum diverifikasi terhadap `dataset/tkpi_bdd.csv` yang benar-benar akan di-seed. Verifikasi menuntut penghitungan baris pada berkas tersebut, yang akan dilakukan pada tahap ETL. Bila angkanya berbeda, naskah harus mengikuti data, bukan sebaliknya.

## Yang Sudah Sinkron

Rumus berat kotor pada persamaan (1) di subbab 2.2 sama persis dengan implementasi pada `MaterialRequirementService`. Batasan Masalah poin 2 sudah mengacu pada rentang BGN per waktu distribusi, sesuai struktur tabel `akg_sekali_makan`. Entri Badan Gizi Nasional (2025) sudah masuk Daftar Pustaka. Penggunaan Spearman sebagai penguji konsistensi TOPSIS terhadap SAW sudah terimplementasi utuh.

## Sisa Pekerjaan

Prioritas pertama adalah keputusan atas T2, karena menentukan apakah modul pembangkit kombinasi menu perlu dibangun. Keputusan itu juga menjawab K4 sekaligus: bila menu dibangkitkan sistem, pemetaan manual dari nama masakan ke kode bahan TKPI tidak lagi diperlukan, dan yang dibutuhkan justru daftar gramasi standar per komponen.

T1, T3, T4, dan T5 adalah perbaikan naskah dan PRD, dapat dikerjakan kapan saja sebelum revisi berikutnya diserahkan. T6 menunggu tahap ETL.
