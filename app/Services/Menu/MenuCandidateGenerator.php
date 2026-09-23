<?php

declare(strict_types=1);

namespace App\Services\Menu;

use App\Models\AkgSekaliMakan;
use App\Models\BahanPangan;
use Illuminate\Support\Collection;
use Random\Engine\Mt19937;
use Random\Randomizer;
use RuntimeException;

class MenuCandidateGenerator
{
    public function generate(AkgSekaliMakan $akg, array $bahanTersediaPerKomponen = [], ?int $seed = null): array
    {
        $satuanPenukar = $this->satuanPenukarUntuk($akg);
        $bahanPerKomponen = $this->bahanPerKomponen($satuanPenukar, $bahanTersediaPerKomponen);

        $kandidat = $this->kombinasikan($bahanPerKomponen, $satuanPenukar, $akg);

        // Pengambilan sampel acak ber-seed deterministik agar permutasi kandidat konsisten pada setiap pengulangan uji (Tabel 3.4)
        $seedNilai = $seed ?? (int) crc32((string) $akg->id);
        $randomizer = new Randomizer(new Mt19937($seedNilai));
        $kandidat = $randomizer->shuffleArray($kandidat);

        return array_slice($kandidat, 0, (int) config('spk.pembangkitan.maksimal_kandidat'));
    }

    public function didukung(AkgSekaliMakan $akg): bool
    {
        return $this->cariSatuanPenukar($akg) !== [];
    }

    private function satuanPenukarUntuk(AkgSekaliMakan $akg): array
    {
        $satuanPenukar = $this->cariSatuanPenukar($akg);

        if ($satuanPenukar === []) {
            throw new RuntimeException('Kelompok sasaran '.$akg->kelompok_sasaran.' belum memiliki standar makanan resmi pada Juknis BGN, sehingga pembangkitan menu tidak dapat dijalankan untuk kelompok ini.');
        }

        return $satuanPenukar;
    }

    // Alias dipakai untuk kelompok sasaran yang rentang AKG-nya identik dengan kelompok lain namun tidak memiliki gambar standar sendiri
    private function cariSatuanPenukar(AkgSekaliMakan $akg): array
    {
        $kelompokSasaran = config('spk.satuan_penukar_alias.'.$akg->kelompok_sasaran, $akg->kelompok_sasaran);
        $satuanPenukar = config('spk.satuan_penukar.'.$kelompokSasaran);

        return is_array($satuanPenukar) ? $satuanPenukar : [];
    }

    // Satuan penukar bernilai nol berarti komponen tidak dipakai kelompok sasaran tersebut, misalnya susu pada TK dan ibu hamil
    private function komponenAktif(array $satuanPenukar): array
    {
        return array_keys(array_filter($satuanPenukar, static fn ($jumlahSp): bool => (float) $jumlahSp > 0.0));
    }

    private function bahanPerKomponen(array $satuanPenukar, array $bahanTersediaPerKomponen): array
    {
        $petaKomponen = config('spk.peta_komponen');
        $hasil = [];

        foreach ($this->komponenAktif($satuanPenukar) as $komponen) {
            $query = BahanPangan::query()
                ->whereIn('kelompok', $petaKomponen[$komponen] ?? [])
                ->whereNotNull('berat_per_sp_gram')
                ->whereNotNull('bdd_persen');

            $tersedia = $bahanTersediaPerKomponen[$komponen] ?? [];

            if ($tersedia !== []) {
                $query->whereIn('id', $tersedia);
            }

            $batasBahan = config('spk.pembangkitan.maksimal_bahan_per_komponen');
            $bahan = $query->orderBy('nama')
                ->when($batasBahan !== null, static fn ($builder) => $builder->limit((int) $batasBahan))
                ->get();

            if ($bahan->isEmpty()) {
                throw new RuntimeException('Tidak ada bahan pangan berstatus penukar resmi yang memenuhi syarat untuk komponen '.$komponen.'.');
            }

            $hasil[$komponen] = $bahan;
        }

        return $hasil;
    }

    // Kombinasi dibentuk bertahap dan dipangkas setiap kali satu komponen ditambahkan, sehingga ruang kombinasi tidak pernah dienumerasi utuh
    private function kombinasikan(array $bahanPerKomponen, array $satuanPenukar, AkgSekaliMakan $akg): array
    {
        $kombinasi = [[]];

        foreach ($bahanPerKomponen as $komponen => $daftarBahan) {
            $kombinasi = $this->tambahKomponen($kombinasi, $komponen, $daftarBahan, (float) $satuanPenukar[$komponen], $akg);
        }

        $lolos = [];

        foreach ($kombinasi as $baris) {
            if ($baris['gizi']['energi_kal'] < $akg->energi_kkal_min) {
                continue;
            }

            $baris['kedekatan_energi'] = $this->kedekatanEnergi($baris['gizi']['energi_kal'], $akg);
            $lolos[] = $baris;
        }

        return $lolos;
    }

    private function tambahKomponen(array $kombinasi, string $komponen, Collection $daftarBahan, float $jumlahSp, AkgSekaliMakan $akg): array
    {
        $hasil = [];

        foreach ($kombinasi as $baris) {
            foreach ($daftarBahan as $bahan) {
                $gabungan = $this->gabung($baris, $komponen, $bahan, $jumlahSp);

                // Kelebihan energi tidak dapat dikoreksi komponen berikutnya, jadi cabang ini dipangkas lebih awal
                if ($gabungan['gizi']['energi_kal'] > $akg->energi_kkal_max) {
                    continue;
                }

                $hasil[] = $gabungan;
            }
        }

        return $hasil;
    }

    private function gabung(array $baris, string $komponen, BahanPangan $bahan, float $jumlahSp): array
    {
        $gizi = $baris['gizi'] ?? array_fill_keys(MenuNutritionCalculator::ZAT_GIZI, 0.0);
        $gram = $jumlahSp * $bahan->berat_per_sp_gram;
        $faktor = $gram / BahanPangan::BASIS_GIZI_GRAM;

        foreach (MenuNutritionCalculator::ZAT_GIZI as $zatGizi) {
            $gizi[$zatGizi] += ((float) ($bahan->{$zatGizi} ?? 0.0)) * $faktor;
        }

        $komposisi = $baris['komposisi'] ?? [];
        $komposisi[$komponen] = [
            'bahan_pangan_id' => $bahan->id,
            'kode' => $bahan->kode,
            'nama' => $bahan->nama,
            'jumlah_sp' => $jumlahSp,
            'berat_bersih_gram' => $gram,
        ];

        return ['komposisi' => $komposisi, 'gizi' => $gizi];
    }

    private function kedekatanEnergi(float $energi, AkgSekaliMakan $akg): float
    {
        $titikTengah = $akg->titikTengah('energi_kkal');
        $lebarSeparuh = ($akg->energi_kkal_max - $akg->energi_kkal_min) / 2;

        return $lebarSeparuh <= 0.0 ? 1.0 : 1.0 - abs($energi - $titikTengah) / $lebarSeparuh;
    }
}
