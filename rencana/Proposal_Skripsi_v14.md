**SISTEM PENDUKUNG KEPUTUSAN PENYUSUNAN MENU DAN KEBUTUHAN BAHAN PROGRAM MAKAN BERGIZI GRATIS BERBASIS WEB MENGGUNAKAN PEMBOBOTAN OBJEKTIF ENTROPY DAN TOPSIS**

**PROPOSAL SKRIPSI**<img src="media/image1.png" style="width:2.80579in;height:1.575in" />

Oleh:

**FERDINAND WILSON PRATAMA SANJOTO  
NIM 23051204357**

**UNIVERSITAS NEGERI SURABAYA**

**FAKULTAS TEKNIK**

**PROGRAM STUDI S1 TEKNIK INFORMATIKA**

**2026**

# HALAMAN PERSETUJUAN PROPOSAL SKRIPSI

|                  |     |                                                                                                                                                             |
|------------------|-----|-------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Nama Mahasiswa   | :   | Ferdinand Wilson Pratama Sanjoto                                                                                                                            |
| NIM              | :   | 23051204357                                                                                                                                                 |
| Program Studi    | :   | S1 Teknik Informatika                                                                                                                                       |
| Judul Penelitian | :   | SISTEM PENDUKUNG KEPUTUSAN PENYUSUNAN MENU DAN KEBUTUHAN BAHAN PROGRAM MAKAN BERGIZI GRATIS BERBASIS WEB MENGGUNAKAN PEMBOBOTAN OBJEKTIF ENTROPY DAN TOPSIS |

Proposal ini telah disetujui dan dinyatakan memenuhi syarat untuk diajukan dalam seminar proposal skripsi.

Surabaya, 06 September 2026

Pembimbing,

Martini Dwi Endah Susanti, S.Kom., M.Kom.

# DAFTAR ISI

*Klik kanan pada daftar ini lalu pilih Update Field untuk memunculkan isinya.*

# DAFTAR TABEL

Tabel 2.1. Penelitian Terdahulu 28

# DAFTAR GAMBAR

Gambar 2.1. Bagan Kerangka Berpikir Penelitian 34

# BAB I PENDAHULUAN

## 1.1 Latar Belakang

Program Makan Bergizi Gratis (MBG) merupakan salah satu program prioritas nasional yang dijalankan pemerintah Republik Indonesia sebagai upaya perbaikan status gizi masyarakat, khususnya peserta didik, ibu hamil, ibu menyusui, dan balita. Program ini dikelola oleh Badan Gizi Nasional yang dibentuk melalui Peraturan Presiden Nomor 83 Tahun 2024, dengan unit pelaksana di tingkat lapangan berupa Satuan Pelayanan Pemenuhan Gizi (SPPG). Berdasarkan data resmi Badan Gizi Nasional per Mei 2026, tercatat sebanyak 28.390 unit SPPG telah beroperasi di seluruh provinsi di Indonesia (Kompas.com, 2026), dengan setiap satuan menyiapkan ribuan porsi makanan setiap hari.

Pada skala penyelenggaraan sebesar itu, mutu gizi menu menjadi faktor penentu keberhasilan program. Penjaminan mutu ditegaskan sebagai aspek yang bersifat mendesak dalam pelaksanaan MBG, karena tanpa mekanisme kendali yang terukur program berisiko menyalurkan makanan yang secara kualitas belum memenuhi standar (Reza, 2025). Kondisi ini menjadi lebih kritis mengingat sasaran utama program adalah kelompok usia pertumbuhan yang kebutuhan gizinya berbeda menurut kelompok umur dan kondisi fisiologis.

Pada Satuan Pelayanan Pemenuhan Gizi, proses penyusunan menu harian saat ini masih dilakukan dengan cara manual dan sangat bergantung pada pengalaman juru masak serta ketersediaan bahan di pasar setempat. Kondisi serupa juga dilaporkan pada studi kasus SPPG di Kabupaten Wonosobo, di mana pemilihan menu dilakukan secara manual sehingga sulit mempertimbangkan seluruh aspek gizi secara objektif (Insani et al., 2026). Perhitungan kandungan gizi, apabila dilakukan, umumnya terbatas pada energi dan protein, sedangkan zat gizi mikro seperti kalsium, zat besi, dan vitamin C jarang diperhitungkan secara eksplisit.

Padahal, Indonesia telah memiliki basis pengetahuan gizi yang lengkap dan otoritatif. Tabel Komposisi Pangan Indonesia (TKPI) yang diterbitkan Kementerian Kesehatan RI (2019) memuat lebih dari seribu jenis bahan pangan dalam tiga belas kelompok, dengan lebih dari dua puluh parameter zat gizi beserta nilai Bagian yang Dapat Dimakan (BDD) untuk setiap bahan. Peraturan Menteri Kesehatan Nomor 28 Tahun 2019 juga telah menetapkan Angka Kecukupan Gizi (AKG) menurut kelompok umur, jenis kelamin, dan kondisi fisiologis. Kedua sumber tersebut sesungguhnya memadai sebagai dasar penyusunan menu yang terukur, namun keduanya masih berbentuk dokumen statis yang belum terintegrasi ke dalam sistem yang dapat digunakan operator SPPG dalam pengambilan keputusan harian.

Berdasarkan penelusuran terhadap data TKPI, ditemukan persoalan yang selama ini luput dari perhatian, yaitu pengabaian faktor Bagian yang Dapat Dimakan. Menurut Kementerian Kesehatan RI (2019), nilai gizi bahan pangan dinyatakan per 100 gram bagian yang dapat dimakan, sedangkan bahan yang dibeli di pasar merupakan berat kotor yang masih mengandung kulit, tulang, dan biji. Sebagai contoh, kacang bogor goreng memiliki nilai BDD sebesar 35 persen, sehingga untuk menyajikan 50 gram bahan bersih diperlukan sekitar 143 gram berat kotor, atau hampir tiga kali lipat. Tanpa perhitungan ini, jumlah bahan yang diadakan lebih kecil daripada kebutuhan sebenarnya, sehingga menu yang di atas kertas telah memenuhi standar dapat berakhir tidak memenuhi standar pada saat disajikan.

Persoalan mendasar juga ditemukan pada sistem pendukung keputusan pemilihan menu MBG berbasis web yang dikembangkan sebelumnya menggunakan *Analytical Hierarchy Process* (AHP) dan TOPSIS (Insani et al., 2026). Sistem tersebut memperlakukan seluruh kriteria gizi termasuk lemak sebagai kriteria *benefit*, sehingga menu dengan kandungan lemak lebih tinggi memperoleh penilaian lebih baik. Angka Kecukupan Gizi hanya digunakan sebagai acuan naratif dan tidak dimasukkan ke dalam matriks keputusan, sehingga sistem tidak dapat membedakan menu yang sesuai kebutuhan dari menu yang kandungan gizinya berlebih; hal ini terlihat pada menu terbaik yang dihasilkan dengan kandungan protein 30,67 gram terhadap acuan 21 gram yang dianggap sebagai keunggulan. Selain itu, alternatif terbatas pada dua belas menu yang telah tersedia sehingga sistem berperan memilih dan bukan menyusun menu, serta tidak disertai pengujian kualitas perangkat lunak.

Apabila permasalahan tersebut tidak segera diatasi, penyimpangan kecil pada komposisi satu menu akan terakumulasi menjadi penyimpangan besar ketika dikalikan dengan ribuan porsi per hari dan ratusan hari penyelenggaraan pada setiap satuan pelayanan. Akibatnya, anggaran negara yang telah dikeluarkan tidak menghasilkan perbaikan status gizi sebagaimana diharapkan, dan tujuan Program Makan Bergizi Gratis untuk mendukung pertumbuhan serta kualitas sumber daya manusia berpotensi tidak tercapai. Selain itu, kekeliruan penetapan sifat kriteria pada sistem pendukung keputusan berisiko mengarahkan operator pada menu yang justru kurang tepat secara gizi.

Persoalan pemilihan menu terbaik dari sekumpulan kandidat dengan mempertimbangkan banyak parameter gizi sekaligus pada dasarnya merupakan persoalan pengambilan keputusan multikriteria. Salah satu metode yang paling banyak digunakan adalah *Technique for Order Preference by Similarity to Ideal Solution* (TOPSIS) karena konsepnya sederhana serta hasilnya mudah ditafsirkan (Hwang & Yoon, 1981; Pandey et al., 2023). Kelemahan penerapan TOPSIS umumnya terletak pada penentuan bobot yang bersifat subjektif. Untuk mengatasinya, pembobotan objektif dengan metode *Entropy* yang bersumber dari teori informasi dapat digunakan, yaitu bobot dihitung dari keberagaman nilai setiap kriteria dalam matriks keputusan itu sendiri (Shannon, 1948; Siregar et al., 2021).

Sebagai metode perankingan, TOPSIS bekerja dengan mengukur jarak setiap alternatif terhadap solusi ideal positif dan negatif secara Euclidean, sehingga hasil peringkatnya perlu diuji konsistensinya terhadap metode dengan pendekatan yang berbeda. *Simple Additive Weighting* (SAW) merupakan metode pengambilan keputusan multikriteria yang bekerja dengan menjumlahkan nilai ternormalisasi setiap kriteria yang telah dikalikan bobotnya (*weighted sum*), sehingga dapat digunakan sebagai pembanding independen terhadap TOPSIS (Iqbalgis & Nurochman, 2019). Konsistensi antara peringkat yang dihasilkan Entropy-TOPSIS dan SAW pada penelitian ini diuji menggunakan koefisien korelasi peringkat Spearman yang lazim digunakan untuk menganalisis konsistensi antar-teknik dalam MCDM (Aytekin, 2021), sehingga stabilitas hasil rekomendasi menu tidak hanya bergantung pada satu metode pengambilan keputusan saja.

Penerapan kombinasi *Entropy* dan TOPSIS telah terbukti menghasilkan pemeringkatan yang stabil pada data gizi anak (Tzitiridou-Chatzopoulou et al., 2024), serta mampu menggantikan penilaian pakar tanpa menurunkan kualitas keputusan (Siregar et al., 2021). Selain itu, optimasi menu makan siang sekolah yang menyeimbangkan gizi dan biaya terbukti mampu menaikkan mutu gizi hingga 19 persen sekaligus menurunkan biaya hingga 13 persen (Stern et al., 2023), sebagaimana sistem rekomendasi gizi berbasis web juga telah berhasil dikembangkan pada konteks negara berkembang (Abhari et al., 2023).

Berdasarkan pemetaan tersebut, belum ditemukan penelitian yang menerapkan pembobotan objektif pada penyusunan menu Program Makan Bergizi Gratis dengan memanfaatkan Tabel Komposisi Pangan Indonesia sebagai basis pengetahuan, memasukkan kesesuaian terhadap Angka Kecukupan Gizi sebagai kriteria keputusan, sekaligus melanjutkan keluaran sistem hingga perhitungan kebutuhan bahan mentah berdasarkan nilai Bagian yang Dapat Dimakan. Celah inilah yang menjadi ruang kebaruan penelitian ini.

Oleh karena itu, pada penelitian ini diusulkan pengembangan sistem pendukung keputusan penyusunan menu sekaligus kebutuhan bahan pada Program Makan Bergizi Gratis berbasis web, yang menggunakan metode *Entropy* sebagai pembobotan objektif dan TOPSIS sebagai metode perankingan, dengan *Simple Additive Weighting* (SAW) sebagai metode pembanding untuk menguji konsistensi hasil perankingan. Pengembangan sistem dilakukan menggunakan model ADDIE (Branch, 2009) yang diadaptasi untuk pengembangan perangkat lunak sebagaimana telah diterapkan pada pembangunan sistem informasi berbasis web di luar konteks pembelajaran (Akrom & Munggaran, 2024), sedangkan pengujian kualitas perangkat lunak mengacu pada standar ISO/IEC 25010:2023 (ISO/IEC, 2023), mengikuti praktik pengujian kualitas perangkat lunak berbasis ISO/IEC 25010 yang secara instrumental pernah diterapkan pada evaluasi perangkat lunak sejenis (Zahra & Kraugusteeliana, 2023). Sistem yang dihasilkan diharapkan dapat membantu operator SPPG menyusun menu yang terukur secara gizi sekaligus memperoleh perhitungan kebutuhan bahan mentah yang akurat berdasarkan nilai BDD.

## 1.2 Identifikasi Masalah

Berdasarkan latar belakang yang telah diuraikan, dapat diidentifikasi beberapa permasalahan sebagai berikut.

1.  Penyusunan menu harian pada SPPG masih dilakukan secara manual dan bertumpu pada pengalaman serta ketersediaan bahan, sehingga pemenuhan Angka Kecukupan Gizi tidak dapat diverifikasi secara terukur.

2.  Perhitungan kandungan gizi menu umumnya terbatas pada zat gizi makro, sementara zat gizi mikro seperti kalsium, zat besi, dan vitamin C yang penting bagi kelompok usia pertumbuhan belum diperhitungkan secara sistematis.

3.  Faktor Bagian yang Dapat Dimakan belum diperhitungkan dalam penentuan kebutuhan bahan mentah, sehingga terdapat selisih antara kandungan gizi yang direncanakan dan yang benar-benar tersaji.

4.  Tabel Komposisi Pangan Indonesia dan tabel Angka Kecukupan Gizi masih berbentuk dokumen statis yang belum terintegrasi ke dalam sistem yang dapat digunakan secara operasional oleh pengelola SPPG.

5.  Sistem pendukung keputusan menu yang telah dikembangkan pada konteks Program Makan Bergizi Gratis menggunakan pembobotan subjektif yang bergantung pada penilaian responden ahli, sehingga hasil keputusan rentan berubah apabila responden berbeda.

6.  Sistem tersebut belum membedakan sifat kriteria benefit dan cost, sehingga zat gizi yang seharusnya dibatasi seperti lemak justru dinilai semakin baik apabila semakin tinggi.

7.  Angka Kecukupan Gizi belum dijadikan kriteria keputusan, sehingga sistem tidak dapat membedakan menu yang sesuai kebutuhan dari menu yang kandungan gizinya berlebih.

8.  Alternatif keputusan pada sistem yang ada berupa daftar menu tetap yang telah tersedia, sehingga sistem berperan memilih dan belum menyusun kombinasi menu dari basis data bahan pangan.

9.  Sistem yang ada belum disertai pengujian kualitas perangkat lunak berdasarkan standar terkini, pengujian akurasi perhitungan, maupun pengujian stabilitas hasil perankingan terhadap metode pembanding, sehingga mutu teknisnya belum dapat dipertanggungjawabkan.

## 1.3 Rumusan Masalah

Berdasarkan identifikasi masalah tersebut, rumusan masalah penelitian ini dibatasi pada dua hal berikut.

1.  Bagaimana merancang dan membangun sistem pendukung keputusan penyusunan menu dan kebutuhan bahan pada Program Makan Bergizi Gratis berbasis web dengan pembobotan objektif Entropy dan perankingan TOPSIS, memanfaatkan Tabel Komposisi Pangan Indonesia dan Angka Kecukupan Gizi sebagai basis pengetahuan?

2.  Bagaimana hasil pengujian sistem yang dikembangkan, meliputi akurasi perhitungan metode, kesesuaian gizi menu rekomendasi terhadap Angka Kecukupan Gizi, konsistensi perankingan terhadap metode Simple Additive Weighting, serta kualitas perangkat lunak berdasarkan standar ISO/IEC 25010:2023?

## 1.4 Tujuan Penelitian

Sejalan dengan rumusan masalah tersebut, penelitian ini bertujuan sebagai berikut.

1.  Merancang dan membangun sistem pendukung keputusan penyusunan menu dan kebutuhan bahan pada Program Makan Bergizi Gratis berbasis web dengan pembobotan objektif Entropy dan perankingan TOPSIS, memanfaatkan Tabel Komposisi Pangan Indonesia dan Angka Kecukupan Gizi sebagai basis pengetahuan.

2.  Menguji sistem yang dikembangkan dari sisi akurasi perhitungan metode, kesesuaian gizi menu rekomendasi terhadap Angka Kecukupan Gizi, konsistensi perankingan terhadap metode Simple Additive Weighting, serta kualitas perangkat lunak berdasarkan standar ISO/IEC 25010:2023.

## 1.5 Batasan Masalah

Agar penelitian ini terarah dan dapat diselesaikan dalam ruang lingkup yang terkendali, ditetapkan batasan masalah sebagai berikut.

1.  Basis data bahan pangan yang digunakan bersumber dari Tabel Komposisi Pangan Indonesia (TKPI) tahun 2019 dan tidak mencakup bahan pangan di luar daftar tersebut.

2.  Standar kecukupan gizi yang digunakan mengacu pada Peraturan Menteri Kesehatan Nomor 28 Tahun 2019, yang dioperasionalkan untuk satu kali makan melalui rujukan Angka Kecukupan Gizi satu kali Makan Bergizi Gratis yang diterbitkan Badan Gizi Nasional (2025), yaitu dua puluh sampai dua puluh lima persen Angka Kecukupan Gizi harian untuk waktu distribusi pagi dan tiga puluh sampai tiga puluh lima persen untuk waktu distribusi siang, sesuai kelompok sasaran penerima manfaat.

3.  Kriteria yang diperhitungkan dalam matriks keputusan dibatasi pada tingkat kesesuaian kandungan energi, protein, lemak, dan karbohidrat setiap alternatif menu terhadap rentang Angka Kecukupan Gizi satu kali Makan Bergizi Gratis, sedangkan kriteria biaya bahan tidak diperhitungkan dalam penelitian ini. Zat gizi mikro tidak dimasukkan karena rujukan Angka Kecukupan Gizi satu kali Makan Bergizi Gratis yang diterbitkan Badan Gizi Nasional hanya memuat keempat zat gizi makro tersebut, sehingga penurunan target zat gizi mikro untuk satu kali makan memerlukan asumsi di luar sumber primer.

4.  Studi kasus penelitian dibatasi pada paling banyak dua Satuan Pelayanan Pemenuhan Gizi yang bersedia menjadi lokasi penelitian, sedangkan data SPPG nasional digunakan sebagai data induk (master data) persebaran satuan pelayanan.

5.  Metode pengambilan keputusan yang dibandingkan dibatasi pada Entropy-TOPSIS dan Simple Additive Weighting, sebagai perwakilan metode berbasis jarak terhadap solusi ideal dan metode berbasis penjumlahan terbobot.

6.  Alternatif keputusan berupa kombinasi menu yang dibangkitkan sistem dari basis data bahan pangan, bukan daftar menu tetap yang dimasukkan secara manual.

7.  Sistem yang dikembangkan berupa aplikasi web yang berfungsi sebagai alat bantu pengambilan keputusan, bukan sistem yang menggantikan peran ahli gizi dalam penetapan menu akhir.

8.  Pengujian kualitas perangkat lunak mengacu pada tujuh karakteristik ISO/IEC 25010:2023, yaitu functional suitability, performance efficiency, compatibility, reliability, security, maintainability, dan flexibility, sedangkan karakteristik interaction capability dan safety tidak diukur pada penelitian ini.

9.  Nilai komposisi zat gizi dan nilai Bagian yang Dapat Dimakan pada Tabel Komposisi Pangan Indonesia dianggap berlaku untuk bahan pangan sejenis di lokasi penelitian, dan proses pengolahan makanan dianggap tidak mengubah kandungan gizi bahan secara signifikan.

10. Bahan pangan yang dapat menjadi komponen kandidat menu dibatasi pada bahan yang tercantum dalam daftar bahan penukar Petunjuk Teknis Badan Gizi Nasional (2025), karena hanya bahan tersebut yang memiliki acuan berat per satuan penukar. Tabel Komposisi Pangan Indonesia tetap digunakan sebagai sumber nilai gizi dan nilai Bagian yang Dapat Dimakan bagi seluruh bahan.

11. Pembangkitan kandidat menu hanya dilakukan bagi kelompok sasaran yang memiliki standar makanan resmi pada Petunjuk Teknis Badan Gizi Nasional (2025), sehingga kelompok Siswa SD/MI/SDLB Kelas 1-3, Anak Balita usia 13-59 bulan, dan Balita usia 6-11 bulan tidak dilayani fitur pembangkitan, meskipun rentang Angka Kecukupan Gizinya tetap tersedia dalam basis data sebagai acuan. Standar makanan yang tersedia seluruhnya diperuntukkan bagi waktu makan siang, sedangkan sebagian kelompok sasaran menerima distribusi pada waktu pagi. Dampaknya terbatas karena satuan penukar hanya menentukan besar porsi, sementara penilaian kesesuaian tetap memakai rentang Angka Kecukupan Gizi sesuai waktu distribusi masing-masing, sehingga porsi yang berlebih bagi sasaran pagi tetap tertangkap sebagai skor kesesuaian yang rendah.

## 1.6 Manfaat Penelitian

**1.6.1 Manfaat Teoretis**

1.  Memberikan kontribusi keilmuan mengenai penerapan pembobotan objektif Entropy pada persoalan pengambilan keputusan multikriteria di bidang gizi terapan, khususnya penyusunan menu institusional.

2.  Menjadi rujukan mengenai perbandingan perilaku metode berbasis jarak terhadap solusi ideal (TOPSIS) dan metode berbasis penjumlahan terbobot (SAW) pada data komposisi pangan, termasuk pengujian konsistensinya melalui korelasi peringkat Spearman.

3.  Tersedianya contoh penerapan model pengembangan ADDIE yang diadaptasi untuk rekayasa perangkat lunak dengan fase evaluasi berbasis standar ISO/IEC 25010:2023.

**1.6.2 Manfaat Praktis**

1.  Bagi Satuan Pelayanan Pemenuhan Gizi, melalui penelitian ini dihasilkan sistem yang dapat membantu penyusunan menu harian yang terukur secara gizi beserta perhitungan kebutuhan bahan mentah berdasarkan nilai Bagian yang Dapat Dimakan.

2.  Bagi Badan Gizi Nasional dan pemangku kepentingan program, hasil penelitian dapat menjadi bahan pertimbangan dalam merancang mekanisme penjaminan mutu gizi yang terdigitalisasi.

3.  Bagi penelitian selanjutnya, pada penelitian ini disediakan kerangka perancangan matriks keputusan, kriteria gizi, dan metrik evaluasi yang dapat direplikasi atau dikembangkan pada domain gizi lainnya.

# BAB II KAJIAN PUSTAKA

Sistematika kajian pustaka pada bab ini, sebagaimana keseluruhan sistematika proposal tugas akhir ini, disusun mengacu pada Pedoman Tugas Akhir Universitas Negeri Surabaya yang mewajibkan kajian pustaka berisi kajian teoretis terkait rumusan masalah, hasil penelitian yang relevan, serta kerangka konseptual (Universitas Negeri Surabaya, 2025).

## 2.1 Program Makan Bergizi Gratis dan Satuan Pelayanan Pemenuhan Gizi

Program Makan Bergizi Gratis (MBG) merupakan program prioritas nasional pemerintah Republik Indonesia yang bertujuan memperbaiki status gizi masyarakat, khususnya peserta didik, ibu hamil, ibu menyusui, dan balita, melalui penyediaan makanan bergizi secara gratis dan berkelanjutan. Program ini dikelola oleh Badan Gizi Nasional yang dibentuk berdasarkan Peraturan Presiden Nomor 83 Tahun 2024 tentang Badan Gizi Nasional, dengan tugas menyelenggarakan pemenuhan gizi nasional secara terintegrasi mulai dari perencanaan, pengadaan, hingga distribusi.

Unit pelaksana MBG pada tingkat lapangan disebut Satuan Pelayanan Pemenuhan Gizi (SPPG), yaitu dapur produksi yang bertanggung jawab menyiapkan dan mendistribusikan menu makan bergizi kepada penerima manfaat pada wilayah kerjanya masing-masing. Setiap SPPG menyiapkan menu harian dalam jumlah besar, sehingga kesalahan kecil pada komposisi gizi satu menu berpotensi terakumulasi menjadi penyimpangan besar apabila dikalikan dengan jumlah porsi dan hari penyelenggaraan. Penjaminan mutu gizi pada skala operasional tersebut ditegaskan sebagai aspek yang bersifat mendesak untuk dipenuhi, karena tanpa mekanisme kendali yang terukur program berisiko menyalurkan makanan yang secara kualitas belum memenuhi standar (Reza, 2025).

Pada praktiknya, penyusunan menu harian di SPPG masih banyak dilakukan secara manual dan bergantung pada pengalaman juru masak serta ketersediaan bahan di pasar setempat, sebagaimana dilaporkan pada studi kasus SPPG di Kabupaten Wonosobo (Insani et al., 2026). Kondisi inilah yang menjadi dasar kebutuhan sistem pendukung keputusan yang dapat membantu operator SPPG menyusun menu secara lebih terukur dan konsisten, sebagaimana diuraikan pada subbab-subbab berikut.

## 2.2 Tabel Komposisi Pangan Indonesia dan Bagian yang Dapat Dimakan

Tabel Komposisi Pangan Indonesia (TKPI) adalah basis data resmi yang diterbitkan Kementerian Kesehatan Republik Indonesia dan memuat kandungan zat gizi dari berbagai jenis bahan pangan yang dikonsumsi masyarakat Indonesia (Kementerian Kesehatan RI, 2019). TKPI edisi 2019 memuat lebih dari seribu jenis bahan pangan yang dikelompokkan ke dalam tiga belas kelompok pangan, dengan lebih dari dua puluh parameter zat gizi untuk setiap bahan, meliputi antara lain energi, protein, lemak, karbohidrat, kalsium, zat besi, dan vitamin C.

Salah satu parameter penting dalam TKPI adalah nilai Bagian yang Dapat Dimakan (BDD), yaitu persentase bagian suatu bahan pangan yang dapat dikonsumsi setelah dikurangi bagian yang tidak dapat dimakan seperti kulit, tulang, atau biji. Kandungan zat gizi pada TKPI dinyatakan per 100 gram bagian yang dapat dimakan, bukan per 100 gram berat kotor bahan sebagaimana dibeli di pasar (Kementerian Kesehatan RI, 2019). Dengan mengetahui nilai BDD suatu bahan, kebutuhan berat kotor dapat diperkirakan melalui persamaan (1).

Berat Kotor = (Berat Bersih */* BDD) × 100 (1)

Tanpa memperhitungkan nilai BDD, jumlah bahan pangan yang diadakan berisiko lebih kecil daripada kebutuhan sebenarnya, sehingga menu yang secara perhitungan telah memenuhi standar gizi dapat berakhir tidak memenuhi standar pada saat benar-benar disajikan kepada penerima manfaat.

## 2.3 Angka Kecukupan Gizi

Angka Kecukupan Gizi (AKG) adalah kecukupan rata-rata zat gizi setiap hari bagi hampir semua orang menurut kelompok umur, jenis kelamin, dan kondisi fisiologis tertentu, seperti kehamilan dan menyusui, untuk mencapai derajat kesehatan yang optimal. Nilai AKG bagi masyarakat Indonesia ditetapkan secara resmi melalui Peraturan Menteri Kesehatan Republik Indonesia Nomor 28 Tahun 2019 tentang Angka Kecukupan Gizi yang Dianjurkan untuk Masyarakat Indonesia, yang mencakup kebutuhan energi, protein, lemak, karbohidrat, serta berbagai vitamin dan mineral.

Dalam konteks penyusunan menu makan siang seperti pada Program Makan Bergizi Gratis, AKG harian perlu dikonversikan menjadi acuan kebutuhan gizi per satu kali makan, karena satu porsi menu hanya menyumbang sebagian dari total kebutuhan gizi harian seseorang, sebagaimana juga diatur dalam prinsip gizi seimbang pada Peraturan Menteri Kesehatan Nomor 41 Tahun 2014 tentang Pedoman Gizi Seimbang. AKG berperan sebagai acuan pembanding untuk menilai kesesuaian gizi suatu menu, sekaligus dapat dijadikan salah satu kriteria dalam pengambilan keputusan multikriteria pada penyusunan menu.

## 2.4 Sistem Pendukung Keputusan dan Pengambilan Keputusan Multikriteria

Sistem Pendukung Keputusan (SPK) adalah sistem informasi berbasis komputer yang interaktif dan fleksibel, dirancang untuk membantu pengambil keputusan memanfaatkan data dan model dalam memecahkan masalah yang bersifat semi-terstruktur maupun tidak terstruktur (Turban et al., 2011). SPK tidak dimaksudkan untuk menggantikan peran pengambil keputusan, melainkan sebagai alat bantu yang memperluas kemampuan pengambil keputusan dalam mengolah informasi dan mempertimbangkan berbagai alternatif secara lebih sistematis.

Salah satu kelompok persoalan yang umum ditangani SPK adalah pengambilan keputusan multikriteria atau *Multi-Criteria Decision Making* (MCDM), yaitu proses pemilihan alternatif terbaik dari sejumlah alternatif yang tersedia berdasarkan beberapa kriteria yang saling bertentangan atau memiliki satuan yang berbeda-beda (Triantaphyllou, 2000). Penyusunan menu gizi merupakan salah satu bentuk persoalan MCDM, karena pemilihan menu terbaik perlu mempertimbangkan banyak parameter gizi sekaligus, seperti energi, protein, lemak, dan karbohidrat, yang masing-masing memiliki rentang kecukupan tersendiri dan tidak jarang saling bertentangan satu sama lain.

Metode MCDM secara umum dibedakan berdasarkan cara penentuan bobot kriteria, yaitu metode pembobotan subjektif yang bergantung pada penilaian pakar atau responden, dan metode pembobotan objektif yang bobotnya dihitung murni dari data itu sendiri tanpa melibatkan penilaian subjektif (Kusumadewi et al., 2006). Penerapan MCDM pada ranah gizi dan kesehatan di Indonesia telah dibuktikan pula oleh Hadikurniawati et al. (2023) dalam seleksi ibu hamil berisiko stunting, yang menunjukkan bahwa pendekatan pengambilan keputusan multikriteria dapat diterapkan dengan baik pada persoalan gizi terapan.

Penelitian ini menggunakan pendekatan pembobotan objektif dengan metode *Entropy*, dipadukan dengan metode perankingan TOPSIS sebagai metode utama dan *Simple Additive Weighting* (SAW) sebagai metode pembanding, sebagaimana diuraikan pada subbab 2.5 hingga 2.7.

## 2.5 Metode Entropy

Metode *Entropy* merupakan metode pembobotan objektif yang bersumber dari teori informasi (Shannon, 1948), yang pada mulanya dikembangkan untuk mengukur derajat ketidakpastian atau keberagaman suatu sumber informasi. Dalam konteks MCDM, prinsip tersebut diadaptasi menjadi metode pembobotan kriteria yang bobotnya dihitung berdasarkan tingkat keberagaman nilai suatu kriteria di dalam matriks keputusan, tanpa memerlukan penilaian subjektif dari pengambil keputusan atau pakar (Siregar et al., 2021).

Logika dasar metode *Entropy* adalah semakin beragam nilai suatu kriteria antar-alternatif, semakin besar kemampuan kriteria tersebut untuk membedakan alternatif satu dengan yang lain, sehingga kriteria tersebut layak diberi bobot lebih besar. Sebaliknya, kriteria yang nilainya cenderung seragam pada seluruh alternatif dianggap kurang informatif dan diberi bobot lebih kecil. Tahapan perhitungan bobot *Entropy* secara umum meliputi normalisasi matriks keputusan, perhitungan nilai entropy setiap kriteria, dan perhitungan bobot dari derajat diversifikasi (1 dikurangi nilai entropy), dengan rincian formulasi disajikan pada Bab III subbab 3.6.

Metode *Entropy* telah terbukti efektif dipadukan dengan TOPSIS untuk menghasilkan pemeringkatan yang stabil pada data gizi anak (Tzitiridou-Chatzopoulou et al., 2024), serta mampu menggantikan penilaian pakar tanpa menurunkan kualitas keputusan pada kasus seleksi penerima beasiswa berbasis kriteria objektif (Siregar et al., 2021). Kedua penelitian tersebut menjadi dasar penerapan metode *Entropy* pada penelitian ini untuk menghitung bobot kriteria gizi secara objektif dari data komposisi pangan, alih-alih dari penilaian ahli sebagaimana dilakukan pada sistem terdahulu (Insani et al., 2026).

## 2.6 Metode Technique for Order Preference by Similarity to Ideal Solution

*Technique for Order Preference by Similarity to Ideal Solution* (TOPSIS) adalah metode MCDM yang dikembangkan berdasarkan konsep bahwa alternatif terbaik adalah alternatif yang memiliki jarak terpendek terhadap solusi ideal positif (kombinasi nilai terbaik dari seluruh kriteria) dan jarak terjauh terhadap solusi ideal negatif (kombinasi nilai terburuknya) (Hwang & Yoon, 1981). Metode ini banyak digunakan dalam berbagai bidang penerapan karena konsepnya relatif sederhana, perhitungannya efisien secara komputasi, dan hasilnya mudah ditafsirkan secara intuitif (Pandey et al., 2023).

Tahapan umum TOPSIS meliputi normalisasi matriks keputusan, pembobotan matriks ternormalisasi, penentuan kedua titik solusi ideal tersebut, perhitungan jarak Euclidean setiap alternatif terhadapnya, serta perhitungan nilai preferensi sebagai dasar perankingan akhir, dengan formulasi lengkap disajikan pada Bab III subbab 3.6.

Salah satu tahap penting dalam TOPSIS adalah penentuan sifat setiap kriteria sebagai kriteria *benefit* (semakin tinggi nilainya semakin baik) atau kriteria *cost* (semakin rendah nilainya semakin baik). Penentuan sifat kriteria yang keliru dapat menyebabkan kesalahan penilaian alternatif, sebagaimana ditemukan pada sistem pendukung keputusan menu MBG terdahulu yang memperlakukan seluruh kriteria termasuk lemak sebagai kriteria *benefit*, sehingga menu dengan kandungan lemak lebih tinggi justru memperoleh penilaian lebih baik (Insani et al., 2026). Namun demikian, dikotomi *benefit* dan *cost* tidak sepenuhnya memadai untuk kriteria gizi yang memiliki rentang kecukupan. Zat gizi seperti lemak tidak dapat diperbaiki dengan memindahkannya menjadi kriteria *cost*, karena Badan Gizi Nasional (2025) menetapkan batas bawah sekaligus batas atas bagi zat gizi tersebut, sehingga kandungan yang terlalu rendah sama tidak sesuainya dengan kandungan yang terlalu tinggi. Penelitian ini karena itu tidak menilai kandungan zat gizi secara langsung, melainkan mengubahnya terlebih dahulu menjadi tingkat kesesuaian terhadap rentang Angka Kecukupan Gizi satu kali makan, sehingga seluruh kriteria bersifat *benefit* dengan penyimpangan ke arah kekurangan maupun kelebihan sama-sama menurunkan nilai alternatif.

## 2.7 Metode Simple Additive Weighting

*Simple Additive Weighting* (SAW) adalah metode MCDM yang bekerja dengan menjumlahkan nilai ternormalisasi setiap kriteria yang telah dikalikan dengan bobotnya (*weighted sum*), sehingga nilai preferensi akhir setiap alternatif diperoleh secara langsung tanpa memerlukan perhitungan jarak terhadap solusi ideal sebagaimana pada TOPSIS (Iqbalgis & Nurochman, 2019). Proses normalisasi pada SAW dilakukan dengan membagi nilai suatu kriteria terhadap nilai maksimum pada kriteria *benefit*, atau dengan membagi nilai minimum terhadap nilai suatu kriteria pada kriteria *cost*.

Karena logika perhitungannya berbeda dari TOPSIS, yaitu berbasis penjumlahan terbobot langsung dan bukan berbasis jarak ke solusi ideal, SAW dapat digunakan sebagai metode pembanding yang independen untuk menguji konsistensi hasil perankingan yang dihasilkan oleh Entropy-TOPSIS. Apabila kedua metode dengan pendekatan perhitungan yang berbeda menghasilkan urutan peringkat yang serupa, hal tersebut menjadi indikasi bahwa rekomendasi menu yang dihasilkan bersifat stabil dan tidak semata-mata bergantung pada karakteristik satu metode pengambilan keputusan saja.

## 2.8 Korelasi Peringkat Spearman

Korelasi peringkat Spearman (*Spearman rank correlation*) adalah ukuran statistik nonparametrik yang digunakan untuk menilai kekuatan dan arah hubungan antara dua variabel yang berbentuk peringkat (*rank*), dengan nilai koefisien berkisar antara -1 hingga +1. Semakin mendekati +1, semakin kuat kesesuaian arah peringkat antara dua variabel yang dibandingkan. Dalam konteks MCDM, korelasi peringkat Spearman lazim digunakan untuk menganalisis konsistensi hasil peringkat yang dihasilkan oleh dua teknik pengambilan keputusan yang berbeda (Aytekin, 2021).

Pada penelitian ini, korelasi peringkat Spearman digunakan untuk menguji konsistensi antara peringkat menu yang dihasilkan oleh Entropy-TOPSIS sebagai metode utama dan Simple Additive Weighting sebagai metode pembanding. Nilai koefisien korelasi yang tinggi antara kedua hasil peringkat tersebut menjadi salah satu indikator bahwa rekomendasi menu yang dihasilkan sistem bersifat andal, dengan formulasi perhitungan dan kriteria interpretasi nilai koefisien disajikan pada Bab III subbab 3.9.

## 2.9 Model ADDIE

ADDIE merupakan model pengembangan yang terdiri atas lima fase, yaitu *Analysis* (analisis kebutuhan), *Design* (perancangan), *Development* (pengembangan), *Implementation* (implementasi), dan *Evaluation* (evaluasi) (Branch, 2009). Model ini pada mulanya banyak dikenal dan diterapkan dalam konteks desain pembelajaran (*instructional design*), namun kelima fasenya bersifat generik dan tidak eksklusif untuk pengembangan media pembelajaran, sehingga dapat pula diadaptasi untuk pengembangan sistem informasi dan perangkat lunak pada domain lain.

Sifat generik ADDIE tersebut ditunjukkan pada penerapannya dalam pembangunan sistem informasi manajemen aset berbasis web di luar konteks pembelajaran, yang mengadaptasi kelima fase ADDIE untuk tahapan analisis kebutuhan pengguna, perancangan basis data dan antarmuka, pengkodean sistem, implementasi pada lingkungan pengguna, serta evaluasi fungsionalitas sistem yang dibangun (Akrom & Munggaran, 2024). Preseden tersebut menjadi dasar penggunaan ADDIE sebagai kerangka pengembangan pada penelitian ini, yang juga bersifat pembangunan sistem informasi dan bukan media pembelajaran.

Karakteristik ADDIE yang bersifat iteratif turut menjadi pertimbangan pemilihannya, karena hasil evaluasi pada fase akhir dapat memberikan umpan balik terhadap fase analisis atau perancangan untuk perbaikan berkelanjutan, berbeda dengan model pengembangan yang bersifat satu arah kaku seperti model *Waterfall* (Pressman, 2015). Pada penelitian ini, kelima fase ADDIE diadaptasi masing-masing untuk analisis kebutuhan SPPG dan data gizi, perancangan arsitektur serta matriks keputusan, pengembangan sistem, implementasi pada lingkungan uji, dan evaluasi meliputi akurasi perhitungan, kesesuaian AKG, konsistensi perankingan, serta kualitas perangkat lunak, dengan rincian tahapan disajikan pada Bab III subbab 3.3.

## 2.10 Standar Kualitas Perangkat Lunak ISO/IEC 25010:2023

ISO/IEC 25010 merupakan standar internasional yang menetapkan model kualitas produk perangkat lunak, yang digunakan sebagai acuan dalam perencanaan maupun evaluasi kualitas perangkat lunak secara formal. Standar ini merupakan bagian dari rangkaian *Systems and software Quality Requirements and Evaluation* (SQuaRE). Pada tahun 2023, ISO/IEC menerbitkan revisi standar tersebut dengan judul yang berubah dari “System and software quality models” pada edisi 2011 menjadi “Product quality model” pada edisi 2023 (International Organization for Standardization, 2023).

Revisi ISO/IEC 25010:2023 turut mengubah dan menambah karakteristik kualitas produk perangkat lunak, dari delapan karakteristik pada edisi 2011 menjadi sembilan karakteristik pada edisi 2023. Karakteristik *usability* pada edisi lama diganti namanya menjadi *interaction capability*, karakteristik *portability* diganti menjadi *flexibility*, serta ditambahkan satu karakteristik baru, yaitu *safety*, yang menilai kemampuan produk menghindari kondisi yang membahayakan manusia, properti, atau lingkungan pada konteks penggunaan tertentu.

Kesembilan karakteristik ISO/IEC 25010:2023 meliputi *functional suitability*, *performance efficiency*, *compatibility*, *interaction capability*, *reliability*, *security*, *maintainability*, *flexibility*, dan *safety*. Penelitian ini menguji tujuh dari sembilan karakteristik tersebut, yaitu *functional suitability*, *performance efficiency*, *compatibility*, *reliability*, *security*, *maintainability*, dan *flexibility*, sedangkan *interaction capability* dan *safety* tidak diukur karena berada di luar ruang lingkup penelitian, dengan alasan pengecualian dijelaskan pada Bab I subbab 1.5.

Pengujian kualitas perangkat lunak berbasis karakteristik ISO/IEC 25010, meskipun masih mengacu pada istilah edisi 2011 seperti *usability* dan *portability*, telah diterapkan secara instrumental pada evaluasi kualitas aplikasi *digital banking* (Zahra & Kraugusteeliana, 2023). Penelitian tersebut menunjukkan preseden penerapan kerangka ISO/IEC 25010 pada evaluasi kualitas aplikasi berbasis web dan mobile di luar konteks sistem pendukung keputusan gizi, sehingga menjadi salah satu rujukan metodologis pengujian kualitas perangkat lunak pada penelitian ini, dengan tetap mengacu pada istilah dan karakteristik edisi 2023 yang berlaku saat ini.

## 2.11 Penelitian Terdahulu

Beberapa penelitian terdahulu yang relevan dengan penelitian ini dirangkum pada Tabel 2.1 sebagai dasar penentuan posisi dan kebaruan penelitian.

**Tabel 2.1. Penelitian Terdahulu**

| **Sumber**                            | **Fokus Penelitian**                                                                                                                                             | **Keterkaitan dengan Penelitian Ini**                                                                                                                                                                                      |
|---------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Insani et al. (2026)                  | SPK pemilihan menu MBG berbasis web menggunakan AHP dan TOPSIS, dengan seluruh kriteria diperlakukan sebagai benefit dan alternatif terbatas pada 12 menu tetap. | Menjadi rujukan utama sekaligus pembanding: penelitian ini memperbaiki kelemahan pembedaan kriteria benefit-cost, menambahkan AKG sebagai kriteria, dan menyusun menu dari basis data alih-alih memilih dari daftar tetap. |
| Siregar et al. (2021)                 | Pendekatan hibrid Entropy-TOPSIS untuk penentuan penerima beasiswa prestasi berbasis kriteria objektif.                                                          | Menunjukkan Entropy mampu menggantikan pembobotan pakar tanpa menurunkan kualitas keputusan; menjadi dasar penerapan Entropy pada domain gizi dalam penelitian ini.                                                        |
| Tzitiridou-Chatzopoulou et al. (2024) | Penerapan Entropy-weight dan TOPSIS untuk mengevaluasi praktik malnutrisi pada data gizi anak.                                                                   | Membuktikan kombinasi Entropy-TOPSIS efektif pada domain gizi dan menghasilkan peringkat yang stabil, mendukung kelayakan pendekatan serupa untuk penyusunan menu MBG.                                                     |
| Stern et al. (2023)                   | Optimasi multi-objektif menu makan siang sekolah yang menyeimbangkan gizi, biaya, konsumsi, dan dampak lingkungan.                                               | Menunjukkan optimasi menu berbasis data dapat menaikkan mutu gizi sekaligus menurunkan biaya, memperkuat urgensi pendekatan sistematis pada penyusunan menu institusional.                                                 |
| Abhari et al. (2023)                  | Perancangan dan evaluasi sistem rekomendasi gizi berbasis web untuk meningkatkan ketahanan pangan di negara berkembang.                                          | Menjadi preseden penerapan sistem rekomendasi gizi berbasis web pada konteks negara berkembang yang relevan dengan konteks Indonesia.                                                                                      |
| Hadikurniawati et al. (2023)          | Penerapan MADM berbasis bilangan fuzzy segitiga untuk seleksi ibu hamil berisiko stunting.                                                                       | Menunjukkan penerapan metode MCDM pada domain gizi dan kesehatan ibu-anak di Indonesia, memperkuat relevansi pendekatan MCDM pada persoalan gizi nasional.                                                                 |
| Zahra & Kraugusteeliana (2023)        | Evaluasi kualitas aplikasi digital banking menggunakan kerangka ISO 25010.                                                                                       | Menjadi rujukan metodologis pengujian kualitas perangkat lunak berbasis ISO/IEC 25010, dengan penelitian ini mengacu pada edisi 2023 yang lebih mutakhir.                                                                  |
| Akrom & Munggaran (2024)              | Penerapan model ADDIE dalam membangun sistem informasi manajemen aset berbasis web di luar konteks pembelajaran.                                                 | Menjadi preseden penggunaan ADDIE untuk pengembangan sistem informasi non-pembelajaran, mendasari pemilihan ADDIE sebagai kerangka pengembangan pada penelitian ini.                                                       |

## 2.12 Kerangka Berpikir

Berdasarkan kajian teori dan penelitian terdahulu yang telah diuraikan, disusun kerangka berpikir penelitian sebagaimana diilustrasikan pada Gambar 2.1. Permasalahan berawal dari penyusunan menu harian SPPG yang masih dilakukan secara manual, ditambah dengan sistem pendukung keputusan menu MBG terdahulu yang menggunakan pembobotan subjektif dan belum membedakan kriteria benefit-cost secara tegas (Insani et al., 2026). Kondisi tersebut menimbulkan kebutuhan akan pendekatan pembobotan yang lebih objektif dan metode perankingan yang teruji konsistensinya.

<img src="media/image2.png" style="width:4in;height:5.14053in" />

**Gambar 2.1. Bagan Kerangka Berpikir Penelitian**

Kebutuhan tersebut dijawab dengan mengintegrasikan basis pengetahuan gizi resmi, yaitu Tabel Komposisi Pangan Indonesia beserta nilai BDD (Kementerian Kesehatan RI, 2019) dan Angka Kecukupan Gizi (Peraturan Menteri Kesehatan Nomor 28 Tahun 2019), ke dalam matriks keputusan yang diberi bobot secara objektif menggunakan metode Entropy (Shannon, 1948), kemudian diranking menggunakan TOPSIS (Hwang & Yoon, 1981) dengan kriteria benefit dan cost yang dibedakan secara eksplisit. Hasil perankingan TOPSIS selanjutnya diuji konsistensinya terhadap hasil Simple Additive Weighting (Iqbalgis & Nurochman, 2019) menggunakan korelasi peringkat Spearman (Aytekin, 2021).

Sistem pendukung keputusan yang mengintegrasikan seluruh komponen tersebut dikembangkan menggunakan kerangka ADDIE yang diadaptasi untuk rekayasa perangkat lunak (Branch, 2009; Akrom & Munggaran, 2024), dan diuji kualitasnya berdasarkan tujuh karakteristik ISO/IEC 25010:2023 (International Organization for Standardization, 2023). Keluaran akhir kerangka berpikir ini adalah sistem pendukung keputusan penyusunan menu dan kebutuhan bahan Program Makan Bergizi Gratis berbasis web, yang diharapkan menghasilkan rekomendasi menu yang terukur secara gizi, konsisten hasil perankingannya, dan memenuhi standar kualitas perangkat lunak yang berlaku.

# DAFTAR PUSTAKA

Abhari, S., Lankarani, K. B., Azadbakht, L., Niakan Kalhori, S. R., Safdari, R., Karbasi, Z., & Zakerabasali, S. (2023). Designing and evaluating a nutrition recommender system for improving food security in a developing country. *Archives of Iranian Medicine*, *26*(11), 629–641. [https://doi.org/10.34172/aim.2023.93](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.34172%2Faim.2023.93)

Akrom, M. A., & Munggaran, L. C. (2024). Penerapan metode ADDIE dalam membangun sistem informasi manajemen aset berbasis web pada unit kerja khusus Pusat Pengembangan Kedokteran Indonesia. *Jurnal Teknologi Sistem Informasi dan Aplikasi*, *7*(3), 1080–1089. https://doi.org/10.32493/jtsi.v7i3.41849

Aytekin, A. (2021). Comparative analysis of the normalization techniques in the context of MCDM problems. *Decision Making: Applications in Management and Engineering*, *4*(2), 1–25. https://doi.org/10.31181/dmame210402001a

Badan Gizi Nasional. (2025). *Petunjuk teknis penyelenggaraan Program Makan Bergizi Gratis Tahun Anggaran 2026* (Lampiran I Keputusan Kepala Badan Gizi Nasional Nomor 401.1 Tahun 2025). Badan Gizi Nasional.

Branch, R. M. (2009). *Instructional Design: The ADDIE Approach*. Springer.

Hadikurniawati, W., Hartomo, K. D., Sembiring, I., Purnomo, H. D., & Iriani, A. (2023). Triangular fuzzy numbers-based MADM for selecting pregnant mothers at risk of stunting. *Jurnal RESTI (Rekayasa Sistem dan Teknologi Informasi)*, *7*(3), 579–585. [https://doi.org/10.29207/resti.v7i3.4966](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.29207%2Fresti.v7i3.4966)

Hwang, C. L., & Yoon, K. (1981). *Multiple Attribute Decision Making: Methods and Applications*. Springer-Verlag.

Insani, F. A., Asnawi, M. F., & Utomo, D. P. (2026). Sistem pendukung keputusan berbasis web untuk pemilihan menu gizi seimbang pada program makan bergizi gratis (MBG) menggunakan metode AHP dan TOPSIS. *TEKTONIK: Jurnal Ilmu Teknik*, *3*(4), 47–57. [https://doi.org/10.62017/tektonik](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.62017%2Ftektonik)

International Organization for Standardization. (2023). *ISO/IEC 25010:2023 Systems and software engineering — Systems and software Quality Requirements and Evaluation (SQuaRE) — Product quality model*. ISO.

Iqbalgis, H., & Nurochman, N. (2019). Aplikasi metode Simple Additive Weighting (SAW) dalam pengembangan sistem pencarian toko batik berbasis Android. *JISKa (Jurnal Informatika Sunan Kalijaga)*, *4*(2), 125–137. https://doi.org/10.14421/jiska.2019.42-07

Kementerian Kesehatan Republik Indonesia. (2019). *Tabel Komposisi Pangan Indonesia*. Direktorat Jenderal Kesehatan Masyarakat, Kementerian Kesehatan Republik Indonesia.

Kompas.com. (2026, 13 Mei). *Bakom: Ada 28.390 SPPG di Indonesia, lebih dari 50 persen sudah kantongi sertifikat*. Diakses pada 6 September 2026, dari [https://nasional.kompas.com/read/2026/05/13/11424131/bakom-ada-28390-sppg-di-indonesia-lebih-dari-50-persen-sudah-kantongi](https://www.google.com/url?sa=E&q=https%3A%2F%2Fnasional.kompas.com%2Fread%2F2026%2F05%2F13%2F11424131%2Fbakom-ada-28390-sppg-di-indonesia-lebih-dari-50-persen-sudah-kantongi)

Kusumadewi, S., Hartati, S., Harjoko, A., & Wardoyo, R. (2006). *Fuzzy Multi-Attribute Decision Making (Fuzzy MADM)*. Graha Ilmu.

Pandey, V., Komal, & Dincer, H. (2023). A review on TOPSIS method and its extensions for different applications with recent development. *Soft Computing*, *27*(23), 18011–18039. [https://doi.org/10.1007/s00500-023-09011-0](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.1007%2Fs00500-023-09011-0)

Peraturan Menteri Kesehatan Republik Indonesia Nomor 28 Tahun 2019 tentang Angka Kecukupan Gizi yang Dianjurkan untuk Masyarakat Indonesia.

Peraturan Menteri Kesehatan Republik Indonesia Nomor 41 Tahun 2014 tentang Pedoman Gizi Seimbang.

Peraturan Presiden Republik Indonesia Nomor 83 Tahun 2024 tentang Badan Gizi Nasional.

Pressman, R. S. (2015). *Software Engineering: A Practitioner’s Approach* (8th ed.). McGraw-Hill Education.

Reza, I. F. (2025). Urgensi penjaminan mutu dalam pelaksanaan program makan bergizi gratis. *Jurnal Analis Kebijakan*, *9*(2), 67–72.

Shannon, C. E. (1948). A mathematical theory of communication. *The Bell System Technical Journal*, *27*(3), 379–423.

Siregar, M. U., Nasiroh, T., & Mustakim, M. (2021). Suatu pendekatan hibrid menggunakan TOPSIS-Entropi pada penentuan siswa penerima beasiswa prestasi berbasiskan kriteria objektif. *Jurnal Teknologi Informasi dan Ilmu Komputer*, *8*(1), 61–70. [https://doi.org/10.25126/jtiik.0814261](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.25126%2Fjtiik.0814261)

Stern, A. L., Levine, S., Richardson, S. A., Blackstone, N. T., Economos, C., & Griffin, T. S. (2023). Improving school lunch menus with multi-objective optimisation: Nutrition, cost, consumption and environmental impacts. *Public Health Nutrition*, *26*(8), 1715–1727. [https://doi.org/10.1017/S1368980023000927](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.1017%2FS1368980023000927)

Triantaphyllou, E. (2000). *Multi-Criteria Decision Making Methods: A Comparative Study*. Springer.

Turban, E., Sharda, R., & Delen, D. (2011). *Decision Support and Business Intelligence Systems* (9th ed.). Pearson.

Tzitiridou-Chatzopoulou, M., Zournatzidou, G., Orovou, E., Lithoxopoulou, M., Drogouti, E., Sklavos, G., Antoniou, E., & Tsakalidis, C. (2024). Evaluating malnutrition practices and mother's education on children failure to thrive symptoms using entropy-weight and TOPSIS method. *Children*, *11*(8), 903. [https://doi.org/10.3390/children11080903](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.3390%2Fchildren11080903)

Universitas Negeri Surabaya. (2025). *Pedoman Tugas Akhir Universitas Negeri Surabaya (Peraturan Rektor Nomor 38 Tahun 2025)*. Direktorat Pendidikan dan Transformasi Pendidikan.

Zahra, M. N., & Kraugusteeliana, K. (2023). Analisis kualitas performa aplikasi digital banking X menggunakan framework ISO 25010. *Jurnal Teknologi Informasi dan Ilmu Komputer*, *10*(3), 621–630. [https://doi.org/10.25126/jtiik.2023106326](https://www.google.com/url?sa=E&q=https%3A%2F%2Fdoi.org%2F10.25126%2Fjtiik.2023106326)
