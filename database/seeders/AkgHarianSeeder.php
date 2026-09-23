<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AkgHarian;
use Illuminate\Database\Seeder;
use RuntimeException;

class AkgHarianSeeder extends Seeder
{
    private const BERKAS = 'dataset/tabel_akg_harian_permenkes28_2019.csv';

    public function run(): void
    {
        $berkas = base_path(self::BERKAS);

        if (!is_readable($berkas)) {
            throw new RuntimeException('Berkas '.self::BERKAS.' tidak ditemukan atau tidak dapat dibaca.');
        }

        $pointer = fopen($berkas, 'rb');
        $judul = fgetcsv($pointer);
        $tersimpan = 0;

        while (($baris = fgetcsv($pointer)) !== false) {
            $data = @array_combine($judul, $baris);

            if ($data === false || trim((string) $data['Kelompok_Umur']) === '') {
                continue;
            }

            AkgHarian::updateOrCreate(
                [
                    'kelompok' => trim($data['Kelompok']),
                    'kelompok_umur' => trim($data['Kelompok_Umur']),
                ],
                [
                    'berat_badan_kg' => (float) $data['Berat_Badan_kg'],
                    'tinggi_badan_cm' => (float) $data['Tinggi_Badan_cm'],
                    'energi_kkal' => (float) $data['Energi_kkal'],
                    'protein_g' => (float) $data['Protein_g'],
                    'lemak_total_g' => (float) $data['Lemak_Total_g'],
                    'karbohidrat_g' => (float) $data['Karbohidrat_g'],
                ]
            );

            $tersimpan++;
        }

        fclose($pointer);

        $this->command->info('AKG harian tersimpan: '.$tersimpan.'.');
    }
}
