# 07 — Cakupan Gramasi per Kelompok Sasaran

Tanggal: 09 September 2026

## Persoalan

Tabel AKG satu kali MBG memuat dua belas kelompok sasaran, sedangkan Standar Makan Siang pada Juknis BGN 401.1/2025 hanya tersedia untuk tujuh kelompok, yaitu ibu hamil dan menyusui dalam satu gambar, balita, PAUD/TK, SD kelas 1-3, SD kelas 4-6, SMP, dan SMA. Empat kelompok tidak memiliki gambar standar sendiri: Pendidik, Tenaga Kependidikan, Anak Balita usia 13-59 bulan, dan Balita usia 6-11 bulan.

Tanpa penanganan, pembangkitan menu untuk keempat kelompok itu akan gagal tanpa penjelasan yang jelas bagi operator.

## Yang Dibuat

| File | Perubahan |
|---|---|
| `config/spk.php` | Ditambah `gramasi_alias` |
| `app/Services/Menu/MenuCandidateGenerator.php` | Resolusi alias pada `gramasiUntuk`, ditambah `didukung` |

## Keputusan Desain

**Pendidik dan Tenaga Kependidikan dialihkan ke gramasi SMA lewat alias, bukan disalin.** Rentang AKG kedua kelompok itu identik dengan Siswa SMA/SMK/MA/SMALB pada seluruh empat zat gizi, yaitu energi 713 sampai 831 kkal, protein 21,0 sampai 24,5 g, lemak 22,5 sampai 26,3 g, dan karbohidrat 105,0 sampai 122,5 g. Menyalin angka gramasi ke tiga tempat membuka peluang ketiganya berbeda diam-diam bila kelak ada koreksi, sedangkan alias memastikan ketiganya selalu merujuk satu sumber. Pilihan ini juga membuat alasannya terbaca langsung di config, sehingga dapat dikutip di Bab III.

**Dua kelompok balita yang tersisa tidak dialiaskan.** Anak Balita usia 13-59 bulan berada pada waktu distribusi Pagi dengan rentang energi 270 sampai 338 kkal, sedangkan Balita usia 6-11 bulan berada pada waktu distribusi Siang dengan rentang 240 sampai 280 kkal. Keduanya berbeda dari Anak Balita yang rentangnya 405 sampai 473 kkal pada waktu Siang. Mengalihkannya ke gramasi Anak Balita berarti menyajikan porsi yang jauh melebihi kebutuhan, dan menebak gramasi bayi tanpa sumber resmi jauh lebih berbahaya daripada menolak melayani kelompok itu.

**Pesan penolakan menyebut sebabnya, bukan sekadar config kosong.** Exception yang dilempar berbunyi bahwa kelompok sasaran tersebut belum memiliki standar makanan resmi pada Juknis BGN. Perbedaan ini penting karena penyebabnya memang keterbatasan sumber primer, bukan kesalahan konfigurasi sistem.

**Ditambahkan `didukung` sebagai pemeriksaan tanpa exception.** Antarmuka nanti perlu menyembunyikan atau menandai kelompok sasaran yang tidak dapat dibangkitkan sebelum operator menekan tombol generate. Memaksa antarmuka menangkap exception untuk keperluan itu akan mencampur alur kesalahan dengan alur tampilan biasa.

## Konsekuensi ke Naskah

Batasan Masalah perlu menyatakan bahwa pembangkitan kandidat menu dibatasi pada kelompok sasaran yang memiliki Standar Makan Siang resmi pada Juknis BGN 401.1/2025, sehingga Anak Balita usia 13-59 bulan dan Balita usia 6-11 bulan tidak dilayani oleh fitur pembangkitan. Kedua kelompok itu tetap ada dalam basis data sebagai acuan AKG, hanya tidak dapat dibangkitkan menunya.

Pembatasan ini bukan kelemahan yang perlu disembunyikan. Menolak menghasilkan rekomendasi ketika sumber primer tidak menyediakan acuan justru sejalan dengan posisi sistem sebagai alat bantu keputusan, bukan pengganti ahli gizi, sebagaimana dinyatakan pada Batasan Masalah poin 7.

## Sisa Pekerjaan

Blocker utama tidak berubah: angka gramasi belum tersedia. Setelah masuk, urutan pengerjaan berikutnya adalah mengalihkan `RecommendationService` dari tabel `menu` ke `MenuCandidateGenerator`, karena selama pengalihan itu belum terjadi, klaim bahwa sistem menyusun menu belum benar-benar berjalan saat aplikasi dijalankan.

Pengalihan tersebut menuntut satu keputusan yang belum diambil: kandidat hasil pembangkitan bukan baris pada tabel `menu`, sedangkan `rekomendasi.menu_terpilih_id` merujuk ke tabel itu. Pilihannya adalah menyimpan seluruh kandidat sebagai baris `menu` milik SPPG saat rekomendasi dibuat, atau menyimpan komposisi kandidat terpilih saja ke dalam kolom JSON pada `rekomendasi` dan menjadikan penyimpanan sebagai menu tetap sebagai tindakan terpisah yang dipicu operator. Pilihan kedua lebih ringan dan menghindari tabel `menu` membengkak oleh kandidat yang tidak pernah dipakai.
