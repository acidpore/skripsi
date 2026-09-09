# 09 — Catatan Pemetaan Bahan Penukar

Tanggal: 09 September 2026

## Status

Kedua berkas CSV yang disebut sudah dibuat namun belum tersimpan di direktori `dataset/`. Isi direktori masih tujuh berkas lama. Seeder belum dapat dibuat sampai keduanya tersedia.

Naskah `Proposal_Skripsi_v14.md` sudah masuk. Batasan Masalah poin 10 sudah menyatakan pembatasan pada daftar bahan penukar Juknis, dengan TKPI tetap berperan sebagai sumber nilai gizi dan BDD. PRD Bagian 3.4 sudah disamakan dengan rumusan itu.

## Yang Dibuat

`rencana/PRD_Sistem_SPK_MBG.md` Bagian 3.4 diperbarui: penyaring `jenis = Tunggal` dihapus dan diganti pembatasan pada daftar penukar, ditambahkan aturan perhitungan gramasi dari satuan penukar, daftar tujuh komponen, serta tiga kelompok sasaran yang tidak dilayani pembangkitan.

## Konsekuensi Ruang Kombinasi

Daftar penukar berisi tiga puluh bahan, dengan rincian enam karbohidrat, empat protein hewani, tiga protein nabati, delapan sayur, lima buah, tiga lemak, dan satu susu. Ruang kombinasi maksimum menjadi 8.640 baris, dan sekitar 4.500 dengan batas lima bahan per komponen.

Ruang kombinasi penuh dapat dihitung persis tanpa menunggu pengukuran: lima karbohidrat setelah Beras dikeluarkan, empat protein hewani, tiga protein nabati, delapan sayur, lima buah, tiga lemak, dan satu susu, menghasilkan 7.200 kombinasi sebagai batas atas mutlak sebelum penyaringan stok maupun energi.

Karena itu `maksimal_bahan_per_komponen` disetel null alias nonaktif. Pada daftar sekarang batas tersebut tidak punya alasan performa dan hanya membuang keragaman kandidat. Parameternya tetap dipertahankan sebagai katup pengaman bila daftar penukar diperluas kelak, misalnya bila daftar bahan penukar Permenkes 41/2014 ikut ditarik. Pemangkasan inkremental pada progress 08 juga dipertahankan dengan alasan yang sama, karena tidak menambah biaya.

## Tiga Besaran yang Tidak Boleh Tertukar di Bab IV

Tiga puluh bahan penukar, 7.200 ruang kombinasi maksimum, dan jumlah kandidat yang benar-benar masuk matriks keputusan adalah tiga besaran berbeda. Yang stabil dan dapat dikutip sebagai angka baku hanya ruang kombinasi maksimum. Jumlah kandidat berubah tiap kali sistem dijalankan karena bergantung pada stok yang diinput operator, sehingga dilaporkan per skenario uji, bukan sebagai angka tunggal.

## Jebakan Beras dan Nasi

Beras dan Nasi keduanya ada dalam daftar penukar dan merupakan bahan yang sama dalam keadaan berbeda. Bila keduanya aktif, generator akan menghasilkan pasangan kandidat yang secara gizi hampir kembar, dan itu merugikan dua kali: keragaman kandidat berkurang tanpa manfaat, dan Entropy membaca kolom yang nilainya berpasangan sebagai kurang membedakan.

Keputusan: hanya Nasi yang diaktifkan, karena itu bentuk yang benar-benar disajikan kepada penerima manfaat. Penanganannya cukup dengan mengosongkan `berat_per_sp_gram` pada baris Beras saat seeding, tanpa kode tambahan.

Koreksi atas perumusan awal: Beras dan Nasi menghasilkan alternatif yang nyaris kembar, bukan kolom yang nilainya berpasangan. Jalur pengaruhnya ke Entropy adalah proporsi `p_ij` yang dihitung dengan membagi tiap nilai terhadap jumlah kolomnya, sehingga menambah alternatif yang nyaris identik membuat sebaran kolom sedikit lebih rata, entropinya naik, dan bobotnya turun. Perumusan ini yang dipakai bila kalimat tersebut masuk Bab III.

## Verifikasi Silang lewat BDD Tersirat

Kolom berat kotor per SP pada CSV memungkinkan pemeriksaan otomatis: BDD tersirat dihitung sebagai berat bersih dibagi berat kotor dikali 100, lalu dibandingkan dengan `bdd_persen` dari TKPI. Selisih besar menandakan salah transkrip atau salah pemetaan kode bahan.

Pemeriksaan ini akan dijalankan di dalam seeder dan mencatat peringatan, bukan menggagalkan seeding, karena sebagian selisih wajar terjadi akibat pembulatan angka pada Juknis.

BDD tersirat punya peran kedua yang lebih berharga, yaitu sebagai penentu saat memetakan nama penukar yang generik. Dua belas dari tiga puluh bahan memiliki berat kotor sama dengan berat bersih sehingga BDD tersiratnya 100 persen dan tidak memberi informasi. Sebaliknya nama seperti Ikan segar, Jeruk, dan Daging sapi memetakan ke banyak baris TKPI dengan BDD berbeda-beda. Untuk nama semacam itu, baris TKPI yang dipilih adalah yang nilai `bdd_persen`-nya paling mendekati BDD tersirat, sehingga pemetaan menjadi keputusan berbasis angka dan bukan penilaian selera. Untuk nama spesifik seperti tempe atau kangkung, BDD tersirat tetap diperlakukan sebagai peringatan saja.

## Sisa Pekerjaan

1. Simpan kedua CSV ke `dataset/`
2. Petakan tiga puluh nama penukar ke kode TKPI, sisa terakhir K4
3. Buat seeder bahan penukar beserta pemeriksaan BDD tersirat
4. Alihkan `RecommendationService` dari tabel `menu` ke `MenuCandidateGenerator`
5. Tinjau ulang `maksimal_bahan_per_komponen`
6. Tahap 0 masih belum dieksekusi: Livewire belum terpasang, residu VARK belum dihapus
