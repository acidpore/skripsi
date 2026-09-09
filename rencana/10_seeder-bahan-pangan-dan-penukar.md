# 10 — Seeder Bahan Pangan & Bahan Penukar

Tanggal: 09 September 2026

Kedua CSV satuan penukar sudah tersedia di `dataset/`. Config terisi, dua seeder pertama dibuat.

## Yang Dibuat

| File | Peran |
|---|---|
| `config/spk.php` | `satuan_penukar` terisi untuk tujuh kelompok sasaran, kunci `peta_komponen` diselaraskan dengan nama komponen pada CSV |
| `database/seeders/BahanPanganSeeder.php` | ETL TKPI ke tabel `bahan_pangan` |
| `database/seeders/BahanPenukarSeeder.php` | Mengisi `berat_per_sp_gram` dari daftar penukar Juknis |
| `app/Services/Menu/MenuCandidateGenerator.php` | Komponen dengan satuan penukar nol dilewati |

## Keputusan Desain

**Nama komponen mengikuti CSV, bukan penamaan sebelumnya.** Kunci `peta_komponen` diubah dari `lauk_hewani`, `lauk_nabati`, dan `sumber_lemak` menjadi `protein_hewani`, `protein_nabati`, dan `lemak`. Penamaan CSV berasal dari dokumen sumber, dan menyeragamkannya menghapus satu lapis penerjemahan antara berkas dan kode.

**Nilai satuan penukar nol dipertahankan di config, bukan dihapus.** CSV mencantumkan susu bernilai 0 untuk TK, Ibu Hamil, dan Ibu Menyusui. Menyalinnya apa adanya membuat config tetap setia pada sumber dan perbedaan antar-kelompok tetap terbaca. Generator yang menyaringnya lewat `komponenAktif`, bukan config yang memangkasnya.

**K4 diselesaikan lewat kode, bukan tabel pemetaan manual.** `BahanPenukarSeeder` mencari padanan TKPI berdasarkan nama, dibatasi kelompok TKPI yang sesuai komponennya. Bila ada kecocokan nama persis, baris itu yang dipakai. Bila tidak, dipilih kandidat yang `bdd_persen`-nya paling mendekati BDD tersirat dari CSV. Dengan begitu nama generik seperti Ikan segar dan Jeruk terpetakan berbasis angka, dan tidak ada berkas pemetaan manual yang harus dipelihara terpisah.

**Verifikasi BDD hanya berjalan untuk bahan yang BDD tersiratnya di bawah 100 persen.** Dua belas dari tiga puluh bahan memiliki berat kotor sama dengan berat bersih sehingga BDD tersiratnya 100 persen dan tidak memberi informasi. Memeriksanya hanya akan menghasilkan peringatan palsu untuk bahan seperti tempe dan minyak. Toleransi ditetapkan 5 persen, dan pelanggaran dicatat sebagai peringatan tanpa menggagalkan seeding.

**Beras dikeluarkan di seeder, bukan di generator.** `berat_per_sp_gram` pada baris Beras tidak pernah diisi, sehingga penyaring `whereNotNull` yang sudah ada pada generator otomatis mengeluarkannya. Tidak ada kode tambahan di sisi generator.

**Baris sampah TKPI dikenali lewat pola kode, bukan lewat nomor baris.** Baris kedua berkas sumber berisi keterangan tabel. Menyaringnya dengan pola `^[A-Z]{2}\d+$` pada kolom kode lebih tahan banting daripada melewati baris kedua secara harfiah, karena berkas sumber dapat berubah urutannya saat diekspor ulang.

## Catatan Data yang Perlu Diketahui

Kolom `catatan` pada CSV penukar mencatat bahwa Gambar 11 menulis Jeruk 100 g sedangkan lima gambar lain menulis 110 g. Seeder memakai nilai kolom `berat_bersih_per_sp_gram`, yaitu 110 g. Perbedaan ini perlu disebut di Bab III bila ditanyakan penguji.

Susu tercatat 200 g per satuan penukar, sedangkan Anak Balita dan SD kelas 4-6 memakai 0,625 SP yang setara 125 g. Perhitungan ini berjalan sendiri lewat perkalian SP dengan berat per SP, tidak perlu penanganan khusus.

## Sisa Pekerjaan

Seeder `akg_harian`, `akg_sekali_makan`, dan `sppg` belum dibuat. Ketiganya lebih sederhana karena struktur CSV-nya sudah rapi.

`RecommendationService` masih membaca alternatif dari tabel `menu` dan belum dialihkan ke `MenuCandidateGenerator`. Pengalihan ini menjadi prioritas berikutnya, namun menuntut seeder `akg_sekali_makan` lebih dahulu karena generator memerlukan baris AKG sebagai acuan.

Tahap 0 masih belum dieksekusi: Livewire belum terpasang, residu VARK belum dihapus, dan migrasi belum pernah dijalankan.
