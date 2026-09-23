# 21 - Sinkronisasi Skenario SD Kelas 4-6 dan Seed Acak Deterministik

Tanggal: 23 September 2026

## Ringkasan Perubahan

Menindaklanjuti audit kritis academic writer terkait Batasan Masalah 11 dan indikator pengujian konsistensi hasil (Tabel 3.4):

1. **Pengecualian Kelompok Sasaran Batasan 11**:
   - `Anak Balita` dihapus dari `config/spk.php` (`satuan_penukar`).
   - Dengan ini, seluruh kelompok sasaran tanpa standar makanan resmi Juknis BGN (`Anak Balita`, `Anak Balita usia 13-59 bulan`, `Balita usia 6-11 bulan`, dan `Siswa SD/MI/SDLB Kelas 1-3`) otomatis memiliki status `didukung() == false`.
   - Form antarmuka Livewire `RekomendasiMenu` secara otomatis tidak menampilkan kelompok tersebut di dropdown, dan jika dikirim langsung akan ditolak dengan exception `RuntimeException`.

2. **Penerapan Seed Acak Deterministik (Uji Konsistensi Hasil)**:
   - Sebelumnya `MenuCandidateGenerator` menggunakan fungsi native `shuffle($kandidat)` tanpa seed, sehingga pengulangan menghasilkan permutasi yang berubah-ubah.
   - Diperbarui menggunakan `Random\Randomizer` dengan engine `Random\Engine\Mt19937($seed)`.
   - Seed dihitung deterministik di `RecommendationService` dari kombinasi parameter masukan: `(int) crc32(sprintf('%d-%d-%d', $sppg->id, $akg->id, $jumlahPorsi))`.
   - Hasil diverifikasi melalui script pengujian: dua pemanggilan berturut-turut dengan masukan yang sama menghasilkan bobot Entropy dan nilai preferensi TOPSIS yang 100% identik. Hal ini menjamin terpenuhinya indikator pada Tabel 3.4 naskah proposal.

3. **Pengambilan Ulang Screenshot Skenario SD Kelas 4-6 Siang**:
   - Seluruh tangkapan layar alur rekomendasi diambil ulang dengan skenario resmi yang didukung penuh: **Siswa SD/MI/SDLB Kelas 4–6 (Siang), 100 porsi**.
   - File yang diperbarui di `rencana/screenshots/`:
     - `03-rekomendasi-operator-awal.png` (Form pemilihan parameter awal Siswa SD Kelas 4–6).
     - `04-rekomendasi-hasil-ranking-topsis.png` (5 kandidat menu teratas, skor TOPSIS, dan kesesuaian nilai gizi SD Kelas 4–6).
     - `05-rekomendasi-detail-komposisi.png` (Rincian gramasi per porsi untuk tiap kandidat, tabel bobot Entropy, dan daftar belanja).
     - `06-cetak-daftar-belanja.png` (Lembar cetak resmi daftar belanja bahan pangan untuk Siswa SD Kelas 4–6).
