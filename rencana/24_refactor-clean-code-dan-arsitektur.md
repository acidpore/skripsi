# 24 - Refactor Clean Code dan Arsitektur

Tanggal: 24 September 2026

## Tujuan

Merapikan kode menurut rule.md (query hanya di Model atau Service, tanpa logika bisnis di Livewire dan Controller, tanpa magic number) sebagai persiapan pengujian black box. Rancangan SPK tidak berubah.

## Perubahan

- `RekomendasiFormService` (baru, `app/Services/Spk`): seluruh query form Livewire.
- `RecommendationService::generateUntuk()`: cek SPPG milik pengguna dan pencarian Sppg/Akg pindah dari Livewire ke service.
- `MenuPersistenceService::simpanPeringkat()`: simpan menu, timpa komposisi terpilih, hitung ulang kebutuhan bahan dalam satu transaksi. Menerima `MaterialRequirementService` lewat constructor.
- `User::dapatMengaksesSppg()`: satu aturan multi-tenant, dipakai `RekomendasiFormService` dan `DaftarBelanjaController`.
- Scope model: `Sppg::cariNama`, `AkgSekaliMakan::berurutan`.
- `config/spk.php`: `porsi.bawaan/minimal/maksimal`, `pencarian_sppg_maksimal`. Atribut `min` dan `max` di blade membaca config.
- Rute: `LogoutController` invokable, `/` memakai `Route::view`, `strict_types` ditambahkan.
- `MenuNutritionCalculator::calculate()` dihapus (dead code), konstanta `ZAT_GIZI` tetap.
- Tes contoh dirapikan, `tests/Unit/ExampleTest.php` dihapus.
- Halaman 403 bertema (`resources/views/errors/403.blade.php`), seragam dengan 404.

## Signature method (untuk diagram)

- `RecommendationService::generateUntuk(User $pengguna, int $sppgId, int $akgId, int $jumlahPorsi): Rekomendasi`
- `RecommendationService::generate(Sppg $sppg, AkgSekaliMakan $akg, int $jumlahPorsi, array $bahanTersediaPerKomponen = []): Rekomendasi`
- `RekomendasiFormService::cariSppg(string $kataKunci): Collection`
- `RekomendasiFormService::daftarAkgDidukung(): Collection`
- `RekomendasiFormService::sppg(?int $sppgId): ?Sppg`
- `RekomendasiFormService::rekomendasiMilik(?int $rekomendasiId, User $pengguna): ?Rekomendasi`
- `MenuPersistenceService::simpanPeringkat(Rekomendasi $rekomendasi, int $indeksPeringkat, string $nama): Menu`
- `MenuPersistenceService::simpanDariRekomendasi(Rekomendasi $rekomendasi, string $nama, ?array $komposisi = null): Menu`
- `MaterialRequirementService::forRekomendasi(Rekomendasi): Collection`, `calculateForRekomendasi(Rekomendasi): Collection`, `hitung(array $komposisi, int $jumlahPorsi): array`
- `User::dapatMengaksesSppg(int $sppgId): bool`
- `MenuNutritionCalculator`: hanya konstanta `ZAT_GIZI`
- Livewire `RekomendasiMenu`: `generate()`, `simpan(int $indeksPeringkat)`, `pilihSppg(int $sppgId)`

## Keputusan

- Throttle login tidak ditambahkan (pengujian keamanan di luar Batasan Masalah butir 8).
- Tes fungsional alur generate tidak dibuat, alur diuji manual lewat black box.

## Verifikasi

- `php artisan test`: 4 lulus, 9 assertion.
- Bukti KF-01 di browser: operator 2 mendapat 403 pada cetak dan csv rekomendasi 8.

## Sisa pekerjaan

- Uji ulang browser: generate, simpan, simpan kedua, csv dan cetak setelah refactor (butuh login manual).
- Ambil ulang screenshot 403 setelah halaman bertema.
