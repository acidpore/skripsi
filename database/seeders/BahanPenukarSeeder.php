<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BahanPangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class BahanPenukarSeeder extends Seeder
{
    private const BERKAS = 'dataset/bahan_penukar_juknis_bgn.csv';
    private const BDD_PENUH = 100.0;
    private const TOLERANSI_BDD_PERSEN = 5.0;

    // Beras dikeluarkan karena Nasi adalah bentuk yang benar-benar disajikan, mengaktifkan keduanya menghasilkan alternatif yang nyaris kembar
    private const DIKECUALIKAN = ['Beras'];

    public function run(): void
    {
        $berkas = base_path(self::BERKAS);

        if (!is_readable($berkas)) {
            throw new RuntimeException('Berkas '.self::BERKAS.' tidak ditemukan atau tidak dapat dibaca.');
        }

        $petaKomponen = config('spk.peta_komponen');
        $pointer = fopen($berkas, 'rb');
        $judul = fgetcsv($pointer);
        $tersimpan = 0;

        while (($baris = fgetcsv($pointer)) !== false) {
            $data = @array_combine($judul, $baris);

            if ($data === false || in_array($data['nama_penukar'], self::DIKECUALIKAN, true)) {
                continue;
            }

            $bahan = $this->cariBahan($data, $petaKomponen[$data['komponen']] ?? []);

            if ($bahan === null) {
                $this->command->warn('Tidak ada padanan TKPI untuk penukar '.$data['nama_penukar'].'.');

                continue;
            }

            $bahan->update([
                'berat_per_sp_gram' => (float) $data['berat_bersih_per_sp_gram'],
                // TKPI menandai sebagian BDD dengan tanda hubung, Juknis melengkapinya lewat rasio berat kotor terhadap berat bersih
                'bdd_persen' => $bahan->bdd_persen ?? (float) $data['bdd_tersirat_persen'],
            ]);
            $this->periksaBdd($bahan, (float) $data['bdd_tersirat_persen'], $data['nama_penukar']);
            $tersimpan++;
        }

        fclose($pointer);

        $this->command->info('Bahan penukar tersimpan: '.$tersimpan.'.');
    }

    private function cariBahan(array $data, array $kelompok): ?BahanPangan
    {
        $nama = config('spk.penukar_alias.'.$data['nama_penukar'], $data['nama_penukar']);

        $kandidat = BahanPangan::query()
            ->whereIn('kelompok', $kelompok)
            ->where('nama', 'like', $nama.'%')
            ->get();

        if ($kandidat->isEmpty()) {
            return null;
        }

        $cocokPersis = $kandidat->firstWhere('nama', $nama);

        if ($cocokPersis !== null) {
            return $cocokPersis;
        }

        return $this->terdekatMenurutBdd($kandidat, (float) $data['bdd_tersirat_persen']);
    }

    // Nama penukar yang generik seperti Ikan segar memetakan ke banyak baris TKPI, BDD tersirat dipakai sebagai penentu agar pemetaan berbasis angka
    private function terdekatMenurutBdd(Collection $kandidat, float $bddTersirat): BahanPangan
    {
        return $kandidat->sortBy(static fn (BahanPangan $bahan): float => abs($bahan->bdd_persen - $bddTersirat))->first();
    }

    private function periksaBdd(BahanPangan $bahan, float $bddTersirat, string $namaPenukar): void
    {
        if ($bddTersirat >= self::BDD_PENUH) {
            return;
        }

        $selisih = abs($bahan->bdd_persen - $bddTersirat);

        if ($selisih > self::TOLERANSI_BDD_PERSEN) {
            $this->command->warn(
                'Selisih BDD pada '.$namaPenukar.' terhadap '.$bahan->nama.
                ': TKPI '.$bahan->bdd_persen.' persen, Juknis '.$bddTersirat.' persen.'
            );
        }
    }
}
